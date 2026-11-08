<?php

namespace App\Http\Middleware;

use App\Http\Controllers\VerrouillageController;
use App\Support\Journal;
use App\Support\Reglages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Inactivité : au lieu de fermer la session, on la VERROUILLE. L'utilisateur reste identifié, mais plus aucune
 * page ni action ne répond tant qu'il n'a pas ressaisi son mot de passe (écran de verrouillage).
 * Un verrouillage trop long ferme la session. À placer après le middleware « auth ».
 */
class VerrouillageInactivite
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! Reglages::get('securite.verrouillage_actif')) {
            return $next($request);
        }

        $session = $request->session();

        // Session déjà verrouillée : rien ne passe, sauf si le verrouillage a trop duré (alors : déconnexion complète).
        if ($session->has(VerrouillageController::CLE)) {
            if (VerrouillageController::verrouillageTropLong($request)) {
                return $this->fermer($request, 'verrouillage de plus de '.Reglages::get('securite.ecran_max_minutes').' minutes');
            }

            return $this->refuser($request);
        }

        $derniere = (int) $session->get('derniere_activite', 0);
        $limite = (int) Reglages::get('securite.inactivite_minutes') * 60;

        if ($derniere > 0 && now()->timestamp - $derniere > $limite) {
            VerrouillageController::verrouiller($request, 'inactivité de '.Reglages::get('securite.inactivite_minutes').' minutes');

            return $this->refuser($request);
        }

        $session->put('derniere_activite', now()->timestamp);

        return $next($request);
    }

    /** Page demandée pendant le verrouillage : écran de verrouillage (ou 423 pour un appel Ajax). */
    private function refuser(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Session verrouillée.', 'verrouille' => true, 'redirect' => route('verrouillage.show')], 423);
        }

        return redirect()->route('verrouillage.show');
    }

    private function fermer(Request $request, string $motif): Response
    {
        Journal::noter('session', 'deconnexion_inactivite', 'Session fermée : '.$motif, $request->user());

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Session expirée.', 'redirect' => route('login')], 401);
        }

        return redirect()->route('login')->with('status', 'Votre session a expiré. Reconnectez-vous.');
    }
}
