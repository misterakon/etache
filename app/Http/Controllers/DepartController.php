<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Models\ReponseCourrier;
use App\Support\CourrierDroits;
use App\Support\DepartDroits;
use App\Support\DepartService;
use App\Support\DiligenceDroits;
use App\Support\ExportExcel;
use App\Support\FichiersCourrier;
use App\Support\Journal;
use App\Support\Reglages;
use App\Support\ExportPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/** Registre du courrier départ : un numéro par destinataire, lien avec le courrier d'origine, archivage. */
class DepartController extends Controller
{
    private function exigerConsultation(Request $request): void
    {
        abort_unless(DepartDroits::peutConsulter($request->user()), 403, "Vous n'avez pas accès au registre du courrier départ.");
    }

    private function requete(Request $request)
    {
        $q = CourrierDepart::visiblePar($request->user())->with(['courrier', 'enregistrePar'])->orderByDesc('date_depart')->orderByDesc('id');
        if ($request->filled('q')) {
            $motif = '%'.$request->query('q').'%';
            $q->where(fn ($w) => $w->where('numero', 'like', $motif)->orWhere('destinataire', 'like', $motif)->orWhere('objet', 'like', $motif)->orWhere('numero_archives', 'like', $motif));
        }

        return $q->when($request->filled('du'), fn ($x) => $x->whereDate('date_depart', '>=', $request->query('du')))
            ->when($request->filled('au'), fn ($x) => $x->whereDate('date_depart', '<=', $request->query('au')))
            ->when(in_array($request->query('statut'), ['enregistre', 'archive'], true), fn ($x) => $x->where('statut', $request->query('statut')))
            ->when($request->query('confidentiel') === '1', fn ($x) => $x->where('confidentialite', Courrier::CONFIDENTIEL));
    }

    public function index(Request $request)
    {
        $this->exigerConsultation($request);
        $base = CourrierDepart::visiblePar($request->user());

        return view('courrier.depart_registre', [
            'departs' => $this->requete($request)->paginate(20)->withQueryString(),
            'kpis' => [
                'mois' => (clone $base)->whereYear('date_depart', now()->year)->whereMonth('date_depart', now()->month)->count(),
                'annee' => (clone $base)->whereYear('date_depart', now()->year)->count(),
                'a_classer' => (clone $base)->where('statut', 'enregistre')->count(),
                'confidentiels' => (clone $base)->where('confidentialite', Courrier::CONFIDENTIEL)->count(),
            ],
            'peutEnregistrer' => DepartDroits::peutEnregistrer($request->user()),
        ]);
    }

    public function export(Request $request)
    {
        $this->exigerConsultation($request);
        $departs = $this->requete($request)->limit(Reglages::get('journal.export_max_lignes'))->get();

        $lignes = $departs->map(fn ($d) => [
            $d->numero, $d->date_depart->format('d/m/Y'), $d->destinataire, $d->objet, $d->courrier?->numero, $d->libelleModeEnvoi(), $d->nombre_pieces,
            $d->estConfidentiel() ? 'Oui' : 'Non', $d->libelleStatut(), $d->numero_archives, optional($d->a_conserver_jusqu_au)->format('d/m/Y'), $d->enregistrePar?->name,
        ])->all();

        Journal::noter('exports', 'export', 'Export du registre du courrier départ : '.count($lignes).' courrier(s)', null, null, ['filtres' => $request->query()]);

        return ExportExcel::telecharger(
            'registre_courrier_depart_'.now()->format('Ymd_His').'.xlsx', 'Registre du courrier départ',
            ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.count($lignes).' courrier(s)'],
            ['N°', 'Date de départ', 'Destinataire', 'Objet', 'Courrier d\'origine', 'Mode d\'envoi', 'Pièces', 'Confidentiel', 'Statut', 'N° archives', 'À conserver jusqu\'au', 'Enregistré par'],
            $lignes
        );
    }

    /** Formulaire d'enregistrement (?courrier=ID ou ?reponse=ID pour pré-remplir). */
    public function create(Request $request)
    {
        abort_unless(DepartDroits::peutEnregistrer($request->user()) || $request->user()->hasRole('ADMIN|DG'), 403, "Vous n'avez pas le droit d'enregistrer un courrier départ.");

        $prefill = [];
        if ($request->filled('reponse')) {
            $reponse = ReponseCourrier::with('diligence.courrier')->findOrFail($request->integer('reponse'));
            abort_unless(DiligenceDroits::voir($reponse->diligence, $request->user()) && $reponse->statut === 'signee', 403, 'Réponse indisponible.');
            $c = $reponse->courrier;
            $prefill = ['reponse_id' => $reponse->id, 'courrier_id' => $c->id, 'objet' => $reponse->objet, 'nombre_pieces' => 1, 'confidentialite' => $c->confidentialite, 'destinataires' => [['nom' => $c->expediteur, 'precision' => '']]];
        } elseif ($request->filled('courrier')) {
            $c = Courrier::findOrFail($request->integer('courrier'));
            abort_unless(CourrierDroits::peutVoir($c, $request->user()), 403, "Vous n'avez pas accès à ce courrier.");
            $prefill = ['courrier_id' => $c->id, 'objet' => 'Réponse à votre courrier : '.$c->objet, 'confidentialite' => $c->confidentialite, 'destinataires' => [['nom' => $c->expediteur, 'precision' => '']]];
        }

        return view('courrier.depart_form', ['depart' => null, 'prefill' => $prefill] + $this->donneesFormulaire($request));
    }

    public function edit(Request $request, $id)
    {
        $depart = CourrierDepart::findOrFail($id);
        abort_unless(DepartDroits::peutModifier($depart, $request->user()), 403, 'Ce courrier départ ne peut plus être modifié.');

        return view('courrier.depart_form', ['depart' => $depart, 'prefill' => []] + $this->donneesFormulaire($request));
    }

    private function donneesFormulaire(Request $request): array
    {
        return [
            'courriersOrigine' => Courrier::arrivee()->visiblePar($request->user())->orderByDesc('id')->limit(300)->get(['id', 'numero', 'expediteur', 'objet', 'confidentialite']),
        ];
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $depart = $request->filled('id_') ? CourrierDepart::findOrFail($request->input('id_')) : null;
        abort_unless($depart ? DepartDroits::peutModifier($depart, $user) : DepartDroits::peutEnregistrer($user) || $user->hasRole('ADMIN|DG'), 403, "Vous n'avez pas le droit d'enregistrer ou de modifier ce courrier départ.");
        $retour = $depart ? route('courrier.depart.edit', $depart->id) : route('courrier.depart.create');

        $validator = Validator::make($request->all(), [
            'objet' => ['required', 'string', 'max:500'],
            'date_depart' => ['required', 'date', 'before_or_equal:today'],
            'mode_envoi' => ['nullable', Rule::in(array_keys(Courrier::MODES_TRANSMISSION))],
            'mode_envoi_precision' => ['required_if:mode_envoi,autre', 'nullable', 'string', 'max:150'],
            'nombre_pieces' => ['required', 'integer', 'min:0', 'max:999'],
            'confidentialite' => ['nullable', 'in:confidentiel'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'courrier_id' => ['nullable', 'integer', 'exists:courriers,id'],
            'reponse_id' => ['nullable', 'integer', 'exists:reponses_courrier,id'],
            'dest_nom' => ['required', 'array', 'min:1', 'max:30'],
            'dest_nom.*' => ['required', 'string', 'max:255'],
            'dest_precision' => ['nullable', 'array'],
            'dest_precision.*' => ['nullable', 'string', 'max:255'],
            'scan' => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png', 'max:'.Reglages::get('courrier.scan_taille_max_ko')],
        ], [
            'objet.required' => "L'objet est obligatoire.",
            'date_depart.required' => "La date de départ est obligatoire.",
            'date_depart.before_or_equal' => 'La date de départ ne peut pas être dans le futur.',
            'nombre_pieces.required' => "Indiquez le nombre de pièces (0 s'il n'y en a pas).",
            'mode_envoi_precision.required_if' => "Précisez le mode d'envoi.",
            'dest_nom.required' => 'Indiquez au moins un destinataire.',
            'dest_nom.*.required' => 'Chaque destinataire doit avoir un nom.',
            'scan.extensions' => 'Format accepté pour le scan : PDF, JPG ou PNG.',
            'scan.max' => 'Le scan dépasse la taille maximale autorisée (:max Ko).',
            'scan.uploaded' => "Le scan n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
        ]);
        if ($validator->fails()) {
            return redirect()->to($retour)->withErrors($validator)->withInput();
        }
        $d = $validator->validated();

        if (! empty($d['courrier_id'])) {
            $origine = Courrier::find($d['courrier_id']);
            abort_unless($origine && CourrierDroits::peutVoir($origine, $user), 403, "Vous n'avez pas accès au courrier d'origine.");
        }

        try {
            if ($depart) {
                $depart->update([
                    'destinataire' => trim($d['dest_nom'][0]), 'destinataire_precision' => $d['dest_precision'][0] ?? null, 'objet' => trim($d['objet']),
                    'date_depart' => $d['date_depart'], 'mode_envoi' => $d['mode_envoi'] ?? null,
                    'mode_envoi_precision' => ($d['mode_envoi'] ?? null) === 'autre' ? $d['mode_envoi_precision'] : null,
                    'nombre_pieces' => (int) $d['nombre_pieces'], 'confidentialite' => ! empty($d['confidentialite']) ? Courrier::CONFIDENTIEL : Courrier::NORMAL,
                    'observations' => $d['observations'] ?? null, 'courrier_id' => $d['courrier_id'] ?? null,
                ]);
                if ($request->file('scan')) {
                    FichiersCourrier::deposer($depart, $request->file('scan'), 'scan', ['pdf', 'jpg', 'jpeg', 'png'], $user, 'courriers/departs');
                }
                Journal::noter('courrier', 'depart', "Courrier départ {$depart->numero} modifié", $depart, $depart->courrier);

                return redirect()->route('courrier.depart.show', $depart->id)->with('success', 'Courrier départ modifié.');
            }

            $destinataires = collect($d['dest_nom'])->map(fn ($nom, $i) => ['nom' => $nom, 'precision' => $d['dest_precision'][$i] ?? null])->all();
            $departs = DepartService::creer([
                'objet' => $d['objet'], 'date_depart' => $d['date_depart'], 'mode_envoi' => $d['mode_envoi'] ?? null, 'mode_envoi_precision' => $d['mode_envoi_precision'] ?? null,
                'nombre_pieces' => (int) $d['nombre_pieces'], 'confidentialite' => ! empty($d['confidentialite']) ? Courrier::CONFIDENTIEL : Courrier::NORMAL,
                'observations' => $d['observations'] ?? null, 'courrier_id' => $d['courrier_id'] ?? null, 'reponse_id' => $d['reponse_id'] ?? null,
            ], $destinataires, $user, $request->file('scan'));
        } catch (DomainException $e) {
            return redirect()->to($retour)->withErrors(['scan' => $e->getMessage()])->withInput();
        }

        $numeros = collect($departs)->pluck('numero')->implode(', ');

        return redirect()->route('courrier.depart.show', $departs[0]->id)->with('success', count($departs) > 1 ? "Courriers départ enregistrés : {$numeros}." : "Courrier départ {$numeros} enregistré.");
    }

    public function show(Request $request, $id)
    {
        $depart = CourrierDepart::with(['courrier', 'reponse.diligence', 'enregistrePar'])->findOrFail($id);
        abort_unless(DepartDroits::peutVoir($depart, $request->user()), 403, "Vous n'avez pas accès à ce courrier départ.");

        if ($depart->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', "Courrier départ confidentiel {$depart->numero} consulté", $depart, $depart->courrier);
        }

        return view('courrier.depart_detail', [
            'depart' => $depart,
            'scan' => $depart->scan(),
            'groupe' => $depart->groupeComplet()->where('id', '!=', $depart->id)->get(),
            'peutModifier' => DepartDroits::peutModifier($depart, $request->user()),
            'peutClasser' => DepartDroits::peutClasser($request->user()),
            'parcours' => \App\Models\JournalActivite::where('sujet_type', $depart->getMorphClass())->where('sujet_id', $depart->id)->whereNotIn('evenement', ['consultation', 'telechargement'])->orderBy('created_at')->get(),
        ]);
    }

    public function scan(Request $request, $id)
    {
        $depart = CourrierDepart::with('reponse.fichiers')->findOrFail($id);
        abort_unless(DepartDroits::peutVoir($depart, $request->user()), 403, "Vous n'avez pas accès à ce courrier départ.");
        $scan = $depart->scan();
        abort_unless($scan, 404, 'Aucun scan joint à ce courrier départ.');

        if ($depart->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', "Scan du courrier départ confidentiel {$depart->numero} consulté", $scan, $depart->courrier);
        }

        return FichiersCourrier::servir($scan, $depart->numero, ! $request->boolean('telecharger'));
    }

    /** Fiche imprimable (modèle du registre papier). */
    public function fiche(Request $request, $id)
    {
        $depart = CourrierDepart::with(['courrier', 'enregistrePar'])->findOrFail($id);
        abort_unless(DepartDroits::peutVoir($depart, $request->user()), 403, "Vous n'avez pas accès à ce courrier départ.");
        Journal::noter('courrier', 'telechargement', "Fiche du courrier départ {$depart->numero} générée", $depart, $depart->courrier);

        return ExportPdf::paginer(Pdf::loadView('courrier.depart_fiche_pdf', [
            'depart' => $depart, 'groupe' => $depart->groupeComplet()->get(),
            'ministere' => Reglages::get('courrier.fiche_ministere'), 'direction' => Reglages::get('courrier.fiche_direction'),
        ])->setPaper('a4', 'portrait'))->stream('depart_'.$depart->numero.'.pdf');
    }
}
