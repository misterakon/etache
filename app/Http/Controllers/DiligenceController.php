<?php

namespace App\Http\Controllers;

use App\Models\DelaiCourrier;
use App\Models\Diligence;
use App\Models\DiligenceAction;
use App\Models\DiligenceDelai;
use App\Models\Direction;
use App\Models\ReponseVisa;
use App\Models\DiligenceAvis;
use App\Models\ImputationDestinataire;
use App\Models\InstructionCourrier;
use App\Models\User; 
use App\Notifications\DiligenceNotification; 
use App\Support\AlertesDiligences;
use App\Support\ExportExcel;
use App\Support\Reglages;
use App\Support\SuiviResponsables;
use App\Support\ExportPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\CourrierDroits;
use App\Support\DestinatairesImputation;
use App\Support\DiligenceDroits;
use App\Support\Journal;
use App\Support\NotifDiligence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiligenceController extends Controller
{
    /** Requête du tableau des diligences (diligences racines) avec ses filtres : partagée par l'écran et l'export. */
    private function requete(Request $request): array
    {
        $base = Diligence::query()->whereNull('parent_diligence_id')
            ->whereHas('courrier', fn ($c) => $c->visiblePar($request->user()))
            ->when($request->filled('direction_id'), function ($q) use ($request) {
                $id = $request->integer('direction_id');
                $q->whereHas('responsable.service', fn ($s) => $s->where('direction_id', $id)->orWhereHas('sousDirection', fn ($sd) => $sd->where('direction_id', $id)));
            })
            ->when($request->filled('du'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('au')));

        $q = (clone $base)->with(['courrier', 'responsable.service'])->orderBy('echeance')->orderBy('id');
        $statut = $request->query('statut');
        if ($statut === 'en_retard') {
            $q->actives()->whereDate('echeance', '<', today());
        } elseif (in_array($statut, ['a_prendre_en_charge', 'en_cours', 'solde', 'annule'], true)) {
            $q->where('statut', $statut);
        } elseif ($statut === 'ouvertes' || $statut === null) {
            $q->actives();
        }
        if ($request->filled('q')) {
            $motif = '%'.$request->query('q').'%';
            $q->where(fn ($w) => $w->where('code', 'like', $motif)->orWhere('destinataire_libelle', 'like', $motif)
                ->orWhereHas('courrier', fn ($c) => $c->where('numero', 'like', $motif)->orWhere('expediteur', 'like', $motif)->orWhere('objet', 'like', $motif)));
        }

        return [$base, $q, $statut ?? 'ouvertes'];
    }

    /** Tableau des diligences (profils du registre) : en cours, en retard, traitées. */
    public function index(Request $request)
    {
        SuiviResponsables::actualiser();
        AlertesDiligences::executerSiNecessaire();

        abort_unless(CourrierDroits::peutConsulterRegistre($request->user()), 403, "Vous n'avez pas accès au tableau des diligences.");

        [$base, $q, $statut] = $this->requete($request);
        $kpis = [
            'ouvertes' => (clone $base)->actives()->count(),
            'a_prendre' => (clone $base)->where('statut', 'a_prendre_en_charge')->count(),
            'en_retard' => (clone $base)->actives()->whereDate('echeance', '<', today())->count(),
            'soldees_mois' => (clone $base)->where('statut', 'solde')->whereYear('solde_le', now()->year)->whereMonth('solde_le', now()->month)->count(),
        ];

        return view('courrier.diligences', [
            'diligences' => $q->paginate(20)->withQueryString(), 'kpis' => $kpis, 'statut' => $statut,
            'directions' => Direction::orderBy('nom')->get(['id', 'nom']),
        ]);
    }

    /** Export Excel des diligences par statut, direction et période (mêmes filtres que l'écran). */
    public function export(Request $request)
    {
        abort_unless(CourrierDroits::peutConsulterRegistre($request->user()), 403, "Vous n'avez pas accès au tableau des diligences.");
        [, $q, $statut] = $this->requete($request);
        $diligences = $q->limit(Reglages::get('journal.export_max_lignes'))->get();

        $lignes = $diligences->map(function (Diligence $d) {
            $suivi = $d->suivi();
            $c = $d->courrier;

            return [
                $d->code, $c->numero, $c->date_reception->format('d/m/Y'), $c->expediteur, $c->estConfidentiel() ? 'Courrier confidentiel' : $c->objet,
                $d->destinataire_libelle, $d->responsable?->nomComplet(), $suivi['bout']->responsable?->nomComplet() ?? $suivi['bout']->destinataire_libelle,
                $d->responsable?->service?->direction()?->nom, $d->created_at->format('d/m/Y'), $suivi['echeance']->format('d/m/Y'),
                $d->libelleStatut(), optional($d->pris_en_charge_le)->format('d/m/Y'), optional($d->solde_le)->format('d/m/Y'),
            ];
        })->all();

        $filtres = array_filter([
            'Statut : '.(['ouvertes' => 'ouvertes', 'toutes' => 'toutes', 'en_retard' => 'en retard', 'a_prendre_en_charge' => 'à prendre en charge', 'en_cours' => 'en cours', 'solde' => 'traitées', 'annule' => 'annulées'][$statut] ?? $statut),
            $request->filled('direction_id') ? 'Direction : '.(Direction::find($request->integer('direction_id'))?->nom ?? '—') : null,
            ($request->filled('du') || $request->filled('au')) ? 'Imputées du '.($request->query('du') ?: '…').' au '.($request->query('au') ?: '…') : null,
        ]);
        Journal::noter('exports', 'export', 'Export des diligences : '.count($lignes).' diligence(s)', null, null, ['filtres' => $request->query()]);

        return ExportExcel::telecharger(
            'diligences_'.now()->format('Ymd_His').'.xlsx', 'Diligences du courrier',
            ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.count($lignes).' diligence(s)', implode(' · ', $filtres)],
            ['Diligence', 'Courrier', 'Reçu le', 'Expéditeur', 'Objet', 'Destinataire', 'Responsable', 'Entre les mains de', 'Direction', 'Imputée le', 'Échéance', 'Statut', 'Prise en charge', 'Traitée le'],
            $lignes
        );
    }

    /** Accusé de prise en charge (PDF), à remettre au DG ou au service courrier. */
    public function accuse(Request $request, $id)
    {
        $diligence = Diligence::with(['courrier.type', 'courrier.urgence', 'responsable.service', 'imputation.auteur', 'imputation.instructions'])->findOrFail($id);
        abort_unless(DiligenceDroits::voir($diligence, $request->user()), 403, "Vous n'avez pas accès à cette diligence.");
        abort_unless($diligence->pris_en_charge_le, 404, "Cette diligence n'a pas encore été prise en charge.");
        Journal::noter('courrier', 'telechargement', "Accusé de prise en charge de la diligence {$diligence->code} généré", $diligence, $diligence->courrier);

        return ExportPdf::paginer(Pdf::loadView('courrier.accuse_pdf', [
            'diligence' => $diligence, 'ministere' => Reglages::get('courrier.fiche_ministere'), 'direction' => Reglages::get('courrier.fiche_direction'),
            'priseParNom' => $diligence->pris_en_charge_par ? \App\Models\User::find($diligence->pris_en_charge_par)?->name : null,
        ])->setPaper('a4', 'portrait'))->stream('accuse_'.$diligence->code.'.pdf');
    }

    /** « Mes courriers à traiter » : les diligences dont l'utilisateur est le responsable. */
    public function mes(Request $request)
    {
        SuiviResponsables::actualiser();
        AlertesDiligences::executerSiNecessaire();

        $user = $request->user();
        $clesCibles = $user->personnel_id
            ? DestinatairesImputation::clesAssocieesAuPersonnel((int) $user->personnel_id)
            : [];
        $avis = $user->personnel_id
            ? ImputationDestinataire::where('role', 'avis')->where(function ($query) use ($clesCibles) {
                foreach ($clesCibles as $cle) {
                    [$type, $id] = explode(':', $cle, 2);
                    $query->orWhere(fn ($cible) => $cible->where('destinataire_type', $type)->where('destinataire_id', $id));
                }
            })->with(['diligence.courrier', 'avis'])->get()
                ->filter(fn ($destinataire) => DiligenceDroits::estDestinataireAvis($destinataire, $user))
                ->filter(fn ($destinataire) => $destinataire->diligence !== null)->values()
            : collect();
        $diligences = $user->personnel_id
            ? Diligence::where('responsable_personnel_id', $user->personnel_id)
                ->with('courrier.urgence')->orderByRaw("CASE WHEN statut IN ('a_prendre_en_charge','en_cours') THEN 0 ELSE 1 END")->orderBy('echeance')->limit(200)->get()
            : collect();

        $copies = $user->personnel_id && $clesCibles !== []
            ? ImputationDestinataire::where('role', 'copie')->where(function ($query) use ($clesCibles) {
                foreach ($clesCibles as $cle) {
                    [$type, $id] = explode(':', $cle, 2);
                    $query->orWhere(fn ($cible) => $cible->where('destinataire_type', $type)->where('destinataire_id', $id));
                }
            })->with('imputation.courrier.urgence')->get()
                ->map(fn ($destinataire) => $destinataire->imputation?->courrier)
                ->filter()->unique('id')->values()
            : collect();

        // Ce qui attend une décision ou une action de l'utilisateur : visas, validations, délais, actions confiées
        $aViser = $user->personnel_id
            ? ReponseVisa::where('personnel_id', $user->personnel_id)->where('statut', 'en_attente')->whereHas('reponse', fn ($r) => $r->where('statut', 'en_visa'))
                ->with(['reponse.courrier', 'reponse.diligence', 'reponse.visas'])->get()
                ->filter(fn ($v) => $v->reponse->visaCourant()?->id === $v->id && $v->reponse->annulation_demandee_le === null)->values()
            : collect();
        $actionsAValider = DiligenceAction::where('statut_validation', 'a_valider')->whereHas('diligence', fn ($d) => $d->ouvertes())
            ->with(['diligence.courrier', 'diligence.responsable', 'agent'])->get()
            ->filter(fn ($a) => DiligenceDroits::peutValiderAction($a->diligence, $user))->values();
        $delaisADecider = DiligenceDelai::where('statut', 'en_attente')->with(['diligence.courrier', 'diligence.imputation', 'diligence.diligenceParente'])->get()
            ->filter(fn ($x) => DiligenceDroits::peutDeciderDelai($x->diligence, $user))->values();
        $mesActions = $user->personnel_id
            ? DiligenceAction::where('personnel_id', $user->personnel_id)->validees()->where('faite', false)->whereHas('diligence', fn ($d) => $d->ouvertes())->with('diligence.courrier')->orderBy('echeance')->get()
            : collect();

        // Annulations de réponse en attente d'une décision (dernier visa ; ADMIN et DG voient toutes les demandes)
        $annulationsADecider = \App\Models\ReponseCourrier::where('statut', 'en_visa')->whereNotNull('annulation_demandee_le')
            ->when(! $user->hasRole('ADMIN|DG'), fn ($q) => $q->where('annulation_decideur_id', $user->personnel_id ?: 0))
            ->with('courrier')->get();

        return view('courrier.mes_courriers', [
            'diligences' => $diligences, 'avis' => $avis, 'copies' => $copies, 'sansAgent' => ! $user->personnel_id,
            'aViser' => $aViser, 'annulationsADecider' => $annulationsADecider, 'actionsAValider' => $actionsAValider, 'delaisADecider' => $delaisADecider, 'mesActions' => $mesActions,
        ]);
    }

    public function show(Request $request, $id)
    {
        SuiviResponsables::actualiser();

        $diligence = Diligence::with([
            'courrier.type', 'courrier.urgence', 'imputation.instructions', 'imputation.auteur', 'responsable',
            'avisAssocies.avis.auteur', 'diligenceParente', 'diligencesEnfants',
        ])->findOrFail($id);
        abort_unless(DiligenceDroits::voir($diligence, $request->user()), 403, "Vous n'avez pas accès à cette diligence.");

        $courrier = $diligence->courrier;
        if ($courrier->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', "Diligence {$diligence->code} du courrier confidentiel {$courrier->numero} consultée", $diligence, $courrier);
        }

        $peutReimputer = DiligenceDroits::reimputer($diligence, $request->user());
        $destinatairesAvisPourMoi = $diligence->avisAssocies
            ->filter(fn ($destinataire) => DiligenceDroits::estDestinataireAvis($destinataire, $request->user()));

        return view('courrier.diligence', [
            'diligence' => $diligence,
            'peutAgir' => DiligenceDroits::agir($diligence, $request->user()),
            'peutAnnuler' => DiligenceDroits::annuler($diligence, $request->user()),
            'peutRenvoyer' => DiligenceDroits::renvoyer($diligence, $request->user()),
            'peutVoirCourrier' => CourrierDroits::peutVoir($courrier, $request->user()),
            'peutReimputer' => $peutReimputer,
            'destinatairesAvisPourMoi' => $destinatairesAvisPourMoi,
            'groupes' => $peutReimputer ? DestinatairesImputation::groupesPour($request->user()) : null,
            'instructions' => $peutReimputer ? InstructionCourrier::where('actif', true)->orderBy('ordre')->get() : collect(),
            'delais' => $peutReimputer ? DelaiCourrier::where('actif', true)->orderBy('ordre')->get() : collect(),
        ]);
    }

    public function avis(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        $destinataire = $diligence->avisAssocies()->findOrFail($request->input('destinataire_id'));
        abort_unless(DiligenceDroits::estDestinataireAvis($destinataire, $request->user()) && $diligence->estOuverte(), 403, 'Vous ne pouvez pas déposer un avis sur cette diligence.');

        $donnees = $request->validate([
            'destinataire_id' => ['required', 'integer'],
            'commentaire' => ['required', 'string', 'min:2', 'max:2000'],
        ]);
        $avis = DiligenceAvis::create([
            'imputation_destinataire_id' => $destinataire->id,
            'user_id' => $request->user()->id,
            'commentaire' => $donnees['commentaire'],
        ]);
        Journal::noter('courrier', 'avis_diligence', "Avis déposé sur la diligence {$diligence->code} du courrier {$diligence->courrier->numero}", $avis, $diligence->courrier);

        return redirect()->back()->with('success', 'Votre avis a été transmis au responsable de la diligence.');
    }

    public function prendre(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::agir($diligence, $request->user()), 403, 'Vous ne pouvez pas agir sur cette diligence.');

        if ($diligence->statut === 'a_prendre_en_charge') {
            $diligence->update(['statut' => 'en_cours', 'pris_en_charge_le' => now(), 'pris_en_charge_par' => $request->user()->id]);
            Journal::noter('courrier', 'prise_en_charge', "Diligence {$diligence->code} prise en charge (courrier {$diligence->courrier->numero})", $diligence, $diligence->courrier);
        }

        return redirect()->back()->with('success', "Diligence {$diligence->code} prise en charge.");
    }

    public function solder(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::agir($diligence, $request->user()), 403, 'Vous ne pouvez pas agir sur cette diligence.');

        $donnees = $request->validate(['commentaire' => ['nullable', 'string', 'max:2000']]);
        $diligence->update([
            'statut' => 'solde', 'solde_le' => now(), 'solde_par' => $request->user()->id, 'commentaire_solde' => $donnees['commentaire'] ?? null,
            'pris_en_charge_le' => $diligence->pris_en_charge_le ?? now(), 'pris_en_charge_par' => $diligence->pris_en_charge_par ?? $request->user()->id,
        ]);
        $this->remonter($diligence, $request->user()->id);
        $this->actualiserCourrier($diligence);
        Journal::noter('courrier', 'solde', "Diligence {$diligence->code} traitée (courrier {$diligence->courrier->numero})", $diligence, $diligence->courrier, ['commentaire' => $donnees['commentaire'] ?? null]);

        return redirect()->back()->with('success', "Diligence {$diligence->code} traitée.");
    }

    public function annuler(Request $request, $id)
    {
        $diligence = Diligence::with('courrier')->findOrFail($id);
        abort_unless(DiligenceDroits::annuler($diligence, $request->user()), 403, 'Vous ne pouvez pas annuler cette diligence.');

        $validator = Validator::make($request->all(), ['commentaire' => ['required', 'string', 'min:5', 'max:2000']], ['commentaire.required' => "Le motif de l'annulation est obligatoire.", 'commentaire.min' => 'Précisez le motif (5 caractères minimum).']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $diligence->update(['statut' => 'annule', 'annule_le' => now(), 'motif_annulation' => $validator->validated()['commentaire']]);
        $this->remonter($diligence, $request->user()->id);
        $this->actualiserCourrier($diligence);
        Journal::noter('courrier', 'annulation', "Diligence {$diligence->code} annulée (courrier {$diligence->courrier->numero}) : ".$validator->validated()['commentaire'], $diligence, $diligence->courrier);

        return redirect()->back()->with('success', "Diligence {$diligence->code} annulée.");
    }

    /** Dernier maillon fini : la diligence parente se solde (ou reprend la main si tout est annulé), et ainsi de suite. */
    private function remonter(Diligence $diligence, int $userId): void
    {
        $parent = $diligence->diligenceParente;
        while ($parent && $parent->statut === 'reimputee' && ! $parent->diligencesEnfants()->actives()->exists()) {
            if ($parent->diligencesEnfants()->where('statut', 'solde')->exists()) {
                $parent->update(['statut' => 'solde', 'solde_le' => now(), 'solde_par' => $userId, 'commentaire_solde' => 'Traitée automatiquement : tous les travaux transmis sont traités.']);
                Journal::noter('courrier', 'solde', "Diligence {$parent->code} traitée en cascade (courrier {$parent->courrier->numero})", $parent, $parent->courrier);
            } else {
                $parent->update(['statut' => 'en_cours']);
            }
            $parent = $parent->diligenceParente;
        }
    }

    /** Quand plus aucune diligence n'est ouverte, le courrier passe à « Traité ». */
    private function actualiserCourrier(Diligence $diligence): void
    {
        $courrier = $diligence->courrier;
        if ($courrier->statut !== 'archive' && ! $courrier->diligences()->ouvertes()->exists() && $courrier->diligences()->where('statut', 'solde')->exists()) {
            $courrier->update(['statut' => 'solde']);
        }
    }

    public function renvoyer(Request $request, $id)
    {
        $diligence = Diligence::with(['courrier', 'diligenceParente', 'imputation.auteur'])->findOrFail($id);
        abort_unless(DiligenceDroits::renvoyer($diligence, $request->user()), 403, 'Cette diligence ne peut pas être renvoyée.');
        $motif = $request->validate(['commentaire' => ['required', 'string', 'min:5', 'max:2000']], ['commentaire.required' => 'Le motif du renvoi est obligatoire.', 'commentaire.min' => 'Précisez le motif (5 caractères minimum).'])['commentaire'];
        $parent = $diligence->diligenceParente;
        $courrier = $diligence->courrier;

        $diligence->update(['statut' => 'annule', 'annule_le' => now(), 'motif_annulation' => ($parent ? 'Renvoyée au niveau supérieur : ' : "Renvoyée à l'imputeur : ").$motif]);

        if ($parent) {
            $this->remonter($diligence, $request->user()->id);
            $this->actualiserCourrier($diligence);
            Journal::noter('courrier', 'renvoi', "Diligence {$diligence->code} renvoyée au niveau supérieur (courrier {$courrier->numero}) : ".$motif, $diligence, $courrier);
            try {
                User::where('actif', true)->where('personnel_id', $parent->responsable_personnel_id)->get()
                    ->each(fn ($u) => $u->notify(new DiligenceNotification($parent, DiligenceNotification::INFORMATION)));
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('courrier.mes')->with('success', 'Dossier renvoyé au niveau supérieur.');
        }

        // Diligence reçue directement de l'imputeur (DG) : le courrier redevient « À imputer » s'il ne reste aucune diligence en cours
        if (! $courrier->diligences()->ouvertes()->exists() && in_array($courrier->statut, ['impute', 'en_traitement'], true)) {
            $courrier->update(['statut' => 'enregistre']);
        }
        Journal::noter('courrier', 'renvoi', "Diligence {$diligence->code} renvoyée à l'imputeur (courrier {$courrier->numero}) : ".$motif, $diligence, $courrier);
        try {
            NotifDiligence::versUtilisateur($diligence->imputation?->imputee_par, $diligence, DiligenceNotification::RENVOI, $motif, $request->user()->id);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('courrier.mes')->with('success', "Dossier renvoyé à celui qui l'a imputé.");
    }
}
