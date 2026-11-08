<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\DelaiCourrier;
use App\Models\Diligence;
use App\Models\InstructionCourrier;
use App\Support\CourrierDroits;
use App\Support\DiligenceDroits;
use App\Support\ImputationCourrier;
use App\Support\Reglages;
use App\Support\DestinatairesImputation;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ImputationController extends Controller
{
    /** Imputation d'un courrier (formulaire reprenant la fiche de transmission). */
    public function store(Request $request, $id)
    {
        $courrier = Courrier::findOrFail($id);

        return $this->traiter($request, $courrier, null);
    }

    public function reimputer(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        abort_if(
            $diligence->courrier->estConfidentiel() && Reglages::get('courrier.reimputation_confidentiel_dg_seul') && ! $request->user()->hasRole('ADMIN|DG'),
            403,
            "La réimputation d'un courrier confidentiel est réservée au DG : utilisez « Renvoyer au DG » pour lui rendre le dossier."
        );
        abort_unless(DiligenceDroits::reimputer($diligence, $request->user()), 403, 'Vous ne pouvez pas réimputer cette diligence.');

        return $this->traiter($request, $diligence->courrier, $diligence);
    }

    private function traiter(Request $request, Courrier $courrier, ?Diligence $diligenceParente)
    {
        // « Archiver » / « Pour information » seuls : le courrier est classé, sans diligence
        $classement = ! $diligenceParente && self::estClassement((array) $request->input('instructions', []));

        $retour = $diligenceParente
            ? route('courrier.diligence.show', $diligenceParente->id)
            : route('courrier.show', $courrier->id);

        $autorise = $diligenceParente
            ? DiligenceDroits::reimputer($diligenceParente, $request->user())
            : CourrierDroits::peutImputer($request->user()) && CourrierDroits::peutVoir($courrier, $request->user());
        abort_unless($autorise, 403, "Vous n'avez pas le droit d'imputer ce courrier.");
        if (! $diligenceParente && in_array($courrier->statut, ['solde', 'archive'], true)) {
            return redirect()->to($retour)->withErrors(['imputation' => 'Ce courrier est soldé ou archivé : il ne peut plus être imputé.']);
        }

        $validator = Validator::make($request->all(), [
            'dest' => $classement ? ['nullable', 'array'] : ['required', 'array', 'min:1'],
            'dest.*' => ['string', 'regex:/^(direction|sous_direction|service|poste|agent):\d+$/'],
            'role' => $classement ? ['nullable', 'array'] : ['required', 'array'],
            'role.*' => ['in:principal,avis,copie'],
            'associe_a' => ['nullable', 'array'],
            'associe_a.*' => ['nullable', 'string', 'regex:/^(direction|sous_direction|service|poste|agent):\d+$/'],
            'delai_id' => $classement ? ['nullable'] : ['required', Rule::exists('delais_courrier', 'id')->where('actif', true)],
            'echeance_libre' => ['nullable', 'date', 'after_or_equal:today'],
            'instructions' => ['nullable', 'array'],
            'instructions.*' => ['integer', Rule::exists('instructions_courrier', 'id')->where('actif', true)],
            'instructions_particulieres' => ['nullable', 'string', 'max:2000'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'date_imputation' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.$courrier->date_reception->toDateString()],
            // Courrier confidentiel : le décideur confirme avoir vérifié la liste (tous les destinataires liront le contenu)
            'confirme_confidentiel' => $courrier->estConfidentiel() && ! empty($request->input('dest')) ? ['accepted'] : ['nullable'],
        ], [
            'confirme_confidentiel.accepted' => 'Courrier confidentiel : confirmez que vous avez vérifié la liste des destinataires, qui liront tous son contenu.',
            'dest.required' => 'Choisissez au moins un destinataire.',
            'delai_id.required' => 'Choisissez le délai de traitement.',
            'echeance_libre.after_or_equal' => "L'échéance ne peut pas être dans le passé.",
            'date_imputation.after_or_equal' => "La date d'imputation ne peut pas précéder la date de réception.",
            'date_imputation.before_or_equal' => "La date d'imputation ne peut pas être dans le futur.",
        ]);

        if ($validator->fails()) {
            return redirect()->to($retour)->withErrors($validator)->withInput();
        }
        $d = $validator->validated();

        if ($diligenceParente && ! $request->user()->hasRole('ADMIN|DG')) {
            $permises = DestinatairesImputation::cles(DestinatairesImputation::groupesPour($request->user()));
            if (array_diff($d['dest'], $permises)) {
                return redirect()->to($retour)->withErrors(['dest' => 'Un destinataire choisi est hors de votre hiérarchie.'])->withInput();
            }
        }

        if ($classement) {
            try {
                ImputationCourrier::classer($courrier, $request->user(), $d['dest'] ?? [], Carbon::parse($d['date_imputation']), $d['instructions'] ?? [], $d['instructions_particulieres'] ?? null, $d['observations'] ?? null);
            } catch (DomainException $e) {
                return redirect()->to($retour)->withErrors(['imputation' => $e->getMessage()])->withInput();
            }

            return redirect()->to($retour)->with('success', "Courrier {$courrier->numero} classé : aucune diligence créée.");
        }

        // Une diligence par travail indépendant ; les avis sont associés à un principal et les copies n'en créent pas.
        $destinataires = [];
        foreach (array_unique($d['dest']) as $cle) {
            [$type, $idDest] = explode(':', $cle);
            $role = $d['role'][$cle] ?? null;
            if (! $role) {
                return redirect()->to($retour)->withErrors(['role' => 'Choisissez un rôle pour chaque destinataire.'])->withInput();
            }
            $destinataires[] = [
                'type' => $type, 'id' => (int) $idDest, 'role' => $role,
                'associe_a' => $d['associe_a'][$cle] ?? null,
            ];
        }
        $clesPrincipales = collect($destinataires)->where('role', 'principal')->map(fn ($x) => $x['type'].':'.$x['id'])->values()->all();
        if (count($clesPrincipales) !== 1) {
            return redirect()->to($retour)->withErrors(['dest' => 'Désignez un seul chef de file (responsable principal).'])->withInput();
        }
        foreach ($destinataires as &$destinataire) {
            if ($destinataire['role'] === 'avis') {
                $destinataire['associe_a'] = $clesPrincipales[0]; // rattaché d'office à l'unique diligence
            }
        }
        unset($destinataire);

        // Échéance : délai choisi (en jours) ou date libre
        $delai = DelaiCourrier::findOrFail($d['delai_id']);
        if ($delai->saisie_libre) {
            if (empty($d['echeance_libre'])) {
                return redirect()->to($retour)->withErrors(['echeance_libre' => "Indiquez l'échéance."])->withInput();
            }
            $echeance = Carbon::parse($d['echeance_libre'])->startOfDay();
        } else {
            $echeance = Carbon::parse($d['date_imputation'])->startOfDay()->addDays((int) $delai->delai_jours);
        }

        if ($diligenceParente && $echeance->gt($diligenceParente->echeance)) {
            return redirect()->to($retour)->withErrors(['delai_id' => "L'échéance ne peut pas dépasser celle de la diligence parente (".$diligenceParente->echeance->format('d/m/Y').').'])->withInput();
        }

        try {
            $resultat = ImputationCourrier::imputer(
                $courrier, $request->user(), $destinataires, $delai, $echeance, Carbon::parse($d['date_imputation']),
                $d['instructions'] ?? [], $d['instructions_particulieres'] ?? null, $d['observations'] ?? null, $diligenceParente
            );
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['imputation' => $e->getMessage()])->withInput();
        }

        $message = count($resultat['diligences']).' diligence(s) principale(s) créée(s) pour le courrier '.$courrier->numero.'.';
        $redirection = redirect()->to($retour)->with('success', $message);
        if ($resultat['sans_responsable']) {
            $redirection->with('error', 'Aucun responsable en poste pour : '.implode(', ', $resultat['sans_responsable']).". La diligence existe, mais personne n'a été notifié.");
        }

        return $redirection;
    }

    private static function estClassement(array $ids): bool
    {
        $libelles = InstructionCourrier::whereIn('id', $ids)->pluck('libelle')->map(fn ($l) => Str::lower(trim($l)))->all();

        return $libelles !== [] && array_diff($libelles, ['archiver', 'pour information']) === [];
    }
}
