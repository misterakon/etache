<?php

namespace App\Http\Controllers;

use App\Support\Journal;
use App\Support\Reglages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Écran de verrouillage : après une période d'inactivité (ou sur demande), la session reste ouverte mais
 * bloquée. L'utilisateur ressaisit son mot de passe et retrouve la page où il en était.
 *
 * Garde-fous : durée maximale de verrouillage (au-delà, connexion complète), essais de mot de passe limités,
 * compte toujours actif, adresse de retour limitée à ce site, tout est inscrit au journal d'activité.
 * Les délais se règlent dans Réglages > Sécurité de la session.
 */
class VerrouillageController extends Controller
{
    public const CLE = 'ecran_verrouille';

    /** Verrouille la session de la requête en cours (sans effet si elle l'est déjà). */
    public static function verrouiller(Request $request, string $motif): void
    {
        $session = $request->session();
        if ($session->has(self::CLE)) {
            return;
        }

        $retour = $request->isMethod('GET') && ! $request->expectsJson() ? $request->fullUrl() : $request->input('retour');
        $session->put(self::CLE, [
            'depuis' => now()->timestamp,
            'essais' => 0,
            'retour' => self::retourSur($request, $retour),
        ]);

        Journal::noter('session', 'verrouillage', 'Session verrouillée : '.$motif, $request->user());
    }

    public static function verrouillageTropLong(Request $request): bool
    {
        $depuis = (int) ($request->session()->get(self::CLE.'.depuis') ?? 0);

        return $depuis > 0 && now()->timestamp - $depuis > (int) Reglages::get('securite.ecran_max_minutes') * 60;
    }

    /** L'écran de verrouillage. */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has(self::CLE)) {
            return redirect()->route('dashboard');
        }
        if (self::verrouillageTropLong($request)) {
            return $this->fermer($request, 'verrouillage de plus de '.Reglages::get('securite.ecran_max_minutes').' minutes');
        }

        $user = $request->user();
        $personnel = $user->personnel;
        $initiales = $personnel?->initiales()
            ?: collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($m) => mb_strtoupper(mb_substr($m, 0, 1)))->implode('');
        $essais = (int) $request->session()->get(self::CLE.'.essais', 0);

        return view('auth.verrouille', [
            'nom' => $personnel?->nomComplet() ?: $user->name,
            'email' => $user->email,
            'photo' => $personnel?->photo_url,
            'initiales' => $initiales ?: '?',
            'minutesInactivite' => (int) Reglages::get('securite.inactivite_minutes'),
            'minutesMax' => (int) Reglages::get('securite.ecran_max_minutes'),
            'essaisRestants' => max(0, (int) Reglages::get('securite.essais_max') - $essais),
        ]);
    }

    /** Verrouillage demandé : entrée « Verrouiller » du menu, ou minuteur d'inactivité du navigateur. */
    public function lock(Request $request): JsonResponse|RedirectResponse
    {
        self::verrouiller($request, $request->boolean('auto') ? 'inactivité détectée par le navigateur' : "demandé par l'utilisateur");

        return $request->expectsJson()
            ? response()->json(['redirect' => route('verrouillage.show')])
            : redirect()->route('verrouillage.show');
    }

    /** Déverrouillage par le mot de passe seul. */
    public function unlock(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->session()->has(self::CLE)) {
            return $this->vers($request, route('dashboard'));
        }

        $donnees = $request->validate(['password' => ['required', 'string', 'max:255']], [
            'password.required' => 'Saisissez votre mot de passe.',
        ]);

        $user = $request->user();

        if (self::verrouillageTropLong($request)) {
            return $this->fermer($request, 'verrouillage de plus de '.Reglages::get('securite.ecran_max_minutes').' minutes');
        }
        // Compte désactivé pendant l'absence : plus d'accès, même avec le bon mot de passe.
        if (! $user->actif) {
            return $this->fermer($request, 'compte désactivé');
        }

        if (! Hash::check($donnees['password'], $user->password)) {
            $essais = (int) $request->session()->get(self::CLE.'.essais', 0) + 1;
            $request->session()->put(self::CLE.'.essais', $essais);
            Journal::noter('session', 'deverrouillage_echec', 'Déverrouillage refusé : mot de passe incorrect (essai '.$essais.')', $user);

            $max = (int) Reglages::get('securite.essais_max');
            if ($essais >= $max) {
                return $this->fermer($request, "trop d'essais de déverrouillage", "Trop d'essais. Reconnectez-vous.");
            }

            throw ValidationException::withMessages(['password' => 'Mot de passe incorrect. '.($max - $essais).' essai(s) restant(s).']);
        }

        $retour = $request->session()->get(self::CLE.'.retour');

        // Nouvel identifiant de session (la session authentifiée est conservée), puis levée du verrou.
        $request->session()->regenerate();
        $request->session()->forget(self::CLE);
        $request->session()->put('derniere_activite', now()->timestamp);
        Journal::noter('session', 'deverrouillage', 'Session déverrouillée', $user);

        return $this->vers($request, $retour ?: route('dashboard'));
    }

    /** Battement de cœur : le navigateur signale que l'utilisateur est actif (le middleware rafraîchit l'horloge). */
    public function ping(): Response
    {
        return response()->noContent();
    }

    private function vers(Request $request, string $url): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() ? response()->json(['redirect' => $url]) : redirect()->to($url);
    }

    /** Ferme la session (connexion complète exigée ensuite). */
    private function fermer(Request $request, string $motif, string $message = 'Votre session a expiré. Reconnectez-vous.'): JsonResponse|RedirectResponse
    {
        Journal::noter('session', 'deconnexion_inactivite', 'Session fermée : '.$motif, $request->user());

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('status', $message);

        return $this->vers($request, route('login'));
    }

    /** Adresse de retour : acceptée seulement si elle pointe vers ce même site et pas vers une page d'authentification. */
    private static function retourSur(Request $request, ?string $url): ?string
    {
        if (! is_string($url) || $url === '' || strlen($url) > 2000) {
            return null;
        }
        $parties = parse_url($url);
        if ($parties === false || ($parties['host'] ?? null) !== $request->getHost()) {
            return null;
        }
        if (preg_match('#/(verrouille|verrouiller|login|logout|session)(/|$)#', $parties['path'] ?? '/')) {
            return null;
        }

        return $url;
    }
}
