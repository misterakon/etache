<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\DelaiCourrier;
use App\Models\InstructionCourrier;
use App\Models\JournalActivite;
use App\Models\PieceJointe;
use App\Models\TypeCourrier;
use App\Models\UrgenceCourrier;
use App\Support\CourrierDroits;
use App\Support\DestinatairesImputation;
use App\Support\ExportExcel;
use App\Support\Journal;
use App\Support\NumerotationCourrier;
use App\Support\Reglages;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourrierController extends Controller
{
    private function disque()
    {
        return Storage::disk(config('pieces_jointes.disque'));
    }

    private function exigerRegistre(Request $request): void
    {
        abort_unless(CourrierDroits::peutConsulterRegistre($request->user()), 403, "Vous n'avez pas accès au registre du courrier.");
    }

    /** Requête du registre arrivée avec les filtres de l'écran (partagée par la liste et l'export). */
    private function requeteRegistre(Request $request)
    {
        $q = Courrier::arrivee()->visiblePar($request->user())
            ->with(['type', 'urgence', 'enregistrePar', 'scans'])
            ->orderByDesc('date_reception')->orderByDesc('id');

        if ($request->filled('q')) {
            $motif = '%'.$request->query('q').'%';
            $q->where(fn ($w) => $w->where('numero', 'like', $motif)->orWhere('expediteur', 'like', $motif)
                ->orWhere('objet', 'like', $motif)->orWhere('reference_courrier', 'like', $motif));
        }
        $q->when($request->filled('du'), fn ($x) => $x->whereDate('date_reception', '>=', $request->query('du')))
            ->when($request->filled('au'), fn ($x) => $x->whereDate('date_reception', '<=', $request->query('au')))
            ->when($request->filled('type_id'), fn ($x) => $x->where('type_courrier_id', $request->integer('type_id')))
            ->when($request->filled('urgence_id'), fn ($x) => $x->where('urgence_id', $request->integer('urgence_id')))
            ->when($request->filled('statut') && isset(Courrier::STATUTS[$request->query('statut')]), fn ($x) => $x->where('statut', $request->query('statut')))
            ->when($request->query('confidentiel') === '1', fn ($x) => $x->where('confidentialite', Courrier::CONFIDENTIEL));

        return $q;
    }

    public function index(Request $request)
    {
        $this->exigerRegistre($request);
        $user = $request->user();

        $base = Courrier::arrivee()->visiblePar($user);
        $kpis = [
            'mois' => (clone $base)->whereYear('date_reception', now()->year)->whereMonth('date_reception', now()->month)->count(),
            'annee' => (clone $base)->whereYear('date_reception', now()->year)->count(),
            'a_imputer' => (clone $base)->where('statut', 'enregistre')->count(),
            'confidentiels' => (clone $base)->where('confidentialite', Courrier::CONFIDENTIEL)->count(),
        ];

        return view('courrier.registre', [
            'courriers' => $this->requeteRegistre($request)->paginate(20)->withQueryString(),
            'kpis' => $kpis,
            'types' => TypeCourrier::orderBy('ordre')->get(),
            'urgences' => UrgenceCourrier::orderBy('ordre')->get(),
            'peutEnregistrer' => CourrierDroits::peutEnregistrer($user),
        ]);
    }

    public function export(Request $request)
    {
        $this->exigerRegistre($request);
        $courriers = $this->requeteRegistre($request)->limit(Reglages::get('journal.export_max_lignes'))->get();

        $lignes = $courriers->map(fn ($c) => [
            $c->numero, $c->date_reception->format('d/m/Y'), optional($c->date_courrier)->format('d/m/Y'), $c->reference_courrier, $c->expediteur, $c->objet,
            $c->type?->libelle, $c->urgence?->libelle, $c->estConfidentiel() ? 'Oui' : 'Non', $c->libelleModeTransmission(), $c->nombre_pieces, $c->libelleOrigine(), $c->libelleStatut(), $c->enregistrePar?->name,
        ])->all();

        Journal::noter('exports', 'export', 'Export du registre du courrier arrivée : '.count($lignes).' courrier(s)', null, null, ['filtres' => $request->query()]);

        return ExportExcel::telecharger(
            'registre_courrier_arrivee_'.now()->format('Ymd_His').'.xlsx',
            'Registre du courrier arrivée',
            ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.count($lignes).' courrier(s)'],
            ['N°', 'Reçu le', 'Date du courrier', 'Référence', 'Expéditeur', 'Objet', 'Type', 'Urgence', 'Confidentiel', 'Mode de transmission', 'Pièces jointes', 'Origine', 'Statut', 'Enregistré par'],
            $lignes
        );
    }

    /** Vérification en direct d'un doublon (même expéditeur, même référence, même date de courrier). */
    public function doublon(Request $request)
    {
        $this->exigerRegistre($request);
        $existant = $this->chercherDoublon($request->query('expediteur'), $request->query('reference'), $request->query('date_courrier'), $request->integer('ignore') ?: null, $request->user());

        return response()->json($existant
            ? ['existe' => true, 'numero' => $existant->numero, 'recu_le' => $existant->date_reception->format('d/m/Y'), 'url' => route('courrier.show', $existant->id)]
            : ['existe' => false]);
    }

    private function chercherDoublon(?string $expediteur, ?string $reference, ?string $dateCourrier, ?int $ignorerId, $user): ?Courrier
    {
        if (! filled($expediteur) || ! filled($reference) || ! filled($dateCourrier)) {
            return null;
        }

        return Courrier::arrivee()->visiblePar($user)
            ->whereRaw('LOWER(expediteur) = ?', [mb_strtolower(trim($expediteur))])
            ->whereRaw('LOWER(reference_courrier) = ?', [mb_strtolower(trim($reference))])
            ->whereDate('date_courrier', $dateCourrier)
            ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
            ->first();
    }

    /** Enregistrement d'un nouveau courrier arrivée, ou modification (id_). Le scan est déposé par glisser-déposer. */
    public function store(Request $request)
    {
        $user = $request->user();
        $id = $request->input('id_');
        $courrier = $id ? Courrier::findOrFail($id) : null;

        abort_unless(
            $courrier ? CourrierDroits::peutModifier($courrier, $user) : CourrierDroits::peutEnregistrer($user),
            403, "Vous n'avez pas le droit d'enregistrer ou de modifier ce courrier."
        );

        $extensions = array_intersect_key(config('pieces_jointes.extensions'), array_flip(Reglages::get('courrier.scan_extensions')));
        $scanRequis = ! $courrier && Reglages::get('courrier.scan_obligatoire');

        $validator = Validator::make($request->all(), [
            'id_' => ['nullable', 'integer', 'exists:courriers,id'],
            'origine' => ['required', Rule::in(array_keys(Courrier::ORIGINES))],
            'mode_transmission' => ['required', Rule::in(array_keys(Courrier::MODES_TRANSMISSION))],
            'mode_transmission_precision' => ['required_if:mode_transmission,autre', 'nullable', 'string', 'max:150'],
            'nombre_pieces' => ['required', 'integer', 'min:0', 'max:999'],
            'date_reception' => ['required', 'date', 'before_or_equal:today'],
            'date_courrier' => ['nullable', 'date', 'before_or_equal:date_reception'],
            'reference_courrier' => ['nullable', 'string', 'max:100'],
            'expediteur' => ['required', 'string', 'max:255'],
            'objet' => ['required', 'string', 'max:500'],
            'type_courrier_id' => ['required', Rule::exists('types_courrier', 'id')->where('actif', true)],
            'urgence_id' => ['required', Rule::exists('urgences_courrier', 'id')->where('actif', true)],
            'confidentialite' => ['nullable', 'in:confidentiel'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'scan' => [$scanRequis ? 'required' : 'nullable', 'file', 'max:'.Reglages::get('courrier.scan_taille_max_ko'), 'extensions:'.implode(',', array_keys($extensions))],
        ], [
            'origine.required' => 'Indiquez qui enregistre le courrier.',
            'mode_transmission.required' => 'Indiquez le mode de transmission du courrier.',
            'mode_transmission_precision.required_if' => 'Précisez le mode de transmission.',
            'nombre_pieces.required' => 'Indiquez le nombre de pièces jointes (0 s\'il n\'y en a pas).',
            'date_reception.required' => 'La date de réception est obligatoire.',
            'date_reception.before_or_equal' => 'La date de réception ne peut pas être dans le futur.',
            'date_courrier.before_or_equal' => 'La date du courrier ne peut pas être postérieure à sa date de réception.',
            'expediteur.required' => "L'expéditeur est obligatoire.",
            'objet.required' => "L'objet est obligatoire.",
            'type_courrier_id.required' => 'Choisissez le type de courrier.',
            'urgence_id.required' => "Choisissez l'urgence.",
            'scan.required' => 'Joignez le scan du courrier.',
            'scan.uploaded' => "Le scan n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
            'scan.max' => 'Le scan dépasse la taille maximale autorisée (:max Ko).',
            'scan.extensions' => 'Type de fichier non accepté pour le scan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $donnees = $validator->validated();

        // Avertissement de doublon : on demande confirmation avant d'enregistrer quand même
        if (! $request->boolean('confirmer_doublon')) {
            $doublon = $this->chercherDoublon($donnees['expediteur'], $donnees['reference_courrier'] ?? null, $donnees['date_courrier'] ?? null, $courrier?->id, $user);
            if ($doublon) {
                return redirect()->back()->withInput()->with('courrier_doublon', [
                    'numero' => $doublon->numero, 'recu_le' => $doublon->date_reception->format('d/m/Y'), 'url' => route('courrier.show', $doublon->id),
                ]);
            }
        }

        $fichier = $request->file('scan');
        $ext = $fichier ? strtolower($fichier->getClientOriginalExtension()) : null;
        if ($fichier && ! in_array($fichier->getMimeType(), $extensions[$ext] ?? [], true)) {
            return redirect()->back()->withErrors(['scan' => "Le contenu du fichier ne correspond pas à son extension (.{$ext})."])->withInput();
        }

        $champs = [
            'origine' => $donnees['origine'],
            'mode_transmission' => $donnees['mode_transmission'],
            'mode_transmission_precision' => $donnees['mode_transmission'] === 'autre' ? trim($donnees['mode_transmission_precision']) : null,
            'nombre_pieces' => (int) $donnees['nombre_pieces'],
            'date_reception' => $donnees['date_reception'],
            'date_courrier' => $donnees['date_courrier'] ?? null,
            'reference_courrier' => $donnees['reference_courrier'] ?? null,
            'expediteur' => trim($donnees['expediteur']),
            'objet' => trim($donnees['objet']),
            'type_courrier_id' => $donnees['type_courrier_id'],
            'urgence_id' => $donnees['urgence_id'],
            'confidentialite' => ! empty($donnees['confidentialite']) ? Courrier::CONFIDENTIEL : Courrier::NORMAL,
            'observations' => $donnees['observations'] ?? null,
        ];

        $chemin = null;
        $modifies = [];
        $nouvelleVersion = null;
        $creation = $courrier === null;

        try {
            DB::transaction(function () use (&$courrier, $champs, $fichier, $ext, &$chemin, &$modifies, &$nouvelleVersion, $user, $creation) {
                if ($creation) {
                    $annee = (int) now()->year;
                    [$numero, $sequence] = NumerotationCourrier::prochain(Courrier::SENS_ARRIVEE, $annee);
                    $courrier = Courrier::create($champs + [
                        'sens' => Courrier::SENS_ARRIVEE, 'numero' => $numero, 'annee' => $annee, 'sequence' => $sequence,
                        'statut' => 'enregistre', 'enregistre_par' => $user->id,
                    ]);
                } else {
                    $courrier->fill($champs);
                    $modifies = array_keys($courrier->getDirty());
                    $courrier->save();
                }

                if ($fichier) {
                    $chemin = $fichier->storeAs('courriers/'.now()->format('Y'), Str::random(40).'.'.$ext, config('pieces_jointes.disque'));
                    if (! $chemin) {
                        throw new \RuntimeException("Le scan n'a pas pu être enregistré.");
                    }
                    $ancien = $courrier->scans()->first();
                    $nouvelleVersion = PieceJointe::create([
                        'attachable_type' => $courrier->getMorphClass(), 'attachable_id' => $courrier->id, 'categorie' => 'scan',
                        'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''), 'chemin' => $chemin, 'extension' => $ext,
                        'mime' => $fichier->getMimeType(), 'taille' => $fichier->getSize(),
                        'version' => $ancien ? $ancien->version + 1 : 1, 'racine_id' => $ancien?->racine(), 'actuelle' => true, 'depose_par' => $user->id,
                    ]);
                    $ancien?->update(['actuelle' => false]);
                }
            });
        } catch (\Throwable $e) {
            if ($chemin) {
                $this->disque()->delete($chemin);
            }
            throw $e;
        }

        if ($creation) {
            Journal::noter('courrier', 'enregistrement', "Courrier {$courrier->numero} enregistré : {$courrier->expediteur} — {$courrier->objet}", $courrier, $courrier, [
                'numero' => $courrier->numero, 'origine' => $courrier->origine, 'confidentiel' => $courrier->estConfidentiel(),
            ]);
        } elseif ($modifies) {
            Journal::noter('courrier', 'modification', "Courrier {$courrier->numero} modifié (".implode(', ', $modifies).')', $courrier, $courrier, ['champs' => $modifies]);
        }
        if ($nouvelleVersion) {
            Journal::noter('courrier', 'scan', "Scan du courrier {$courrier->numero} ".($nouvelleVersion->version > 1 ? 'remplacé (v'.$nouvelleVersion->version.')' : 'déposé').' : '.$nouvelleVersion->nom_original, $nouvelleVersion, $courrier);
        }

        return redirect()->route('courrier.show', $courrier->id)->with('success', $creation ? "Courrier {$courrier->numero} enregistré." : 'Courrier modifié.');
    }

    public function show(Request $request, $id)
    {
        $courrier = Courrier::with(['type', 'urgence', 'enregistrePar', 'scans'])->findOrFail($id);
        abort_unless(CourrierDroits::peutVoir($courrier, $request->user()), 403, "Vous n'avez pas accès à ce courrier.");

        if ($courrier->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', "Courrier confidentiel {$courrier->numero} consulté", $courrier, $courrier);
        }

        $parcours = JournalActivite::where('parent_type', $courrier->getMorphClass())->where('parent_id', $courrier->id)
            ->whereNotIn('evenement', ['consultation', 'telechargement'])
            ->orderBy('created_at')->orderBy('id')->get();

        $peutImputer = CourrierDroits::peutImputer($request->user()) && ! in_array($courrier->statut, ['solde', 'archive'], true);

        return view('courrier.detail', [
            'courrier' => $courrier,
            'scan' => $courrier->scan(),
            'parcours' => $parcours,
            'peutModifier' => CourrierDroits::peutModifier($courrier, $request->user()),
            'peutImputer' => $peutImputer,
            'imputations' => $courrier->imputations()->with(['auteur', 'instructions', 'diligences.responsable', 'destinatairesAssocies.diligence'])->get(),
            'groupes' => $peutImputer ? DestinatairesImputation::groupes() : null,
            'instructions' => $peutImputer ? InstructionCourrier::where('actif', true)->orderBy('ordre')->get() : collect(),
            'delais' => $peutImputer ? DelaiCourrier::where('actif', true)->orderBy('ordre')->get() : collect(),
        ]);
    }

    /** Fiche de transmission du courrier (PDF), reprenant le modèle papier avec les cases cochées. */
    public function fiche(Request $request, $id)
    {
        $courrier = Courrier::with(['type', 'urgence', 'imputations.instructions', 'imputations.destinatairesAssocies', 'imputations.diligences'])->findOrFail($id);
        abort_unless(CourrierDroits::peutVoir($courrier, $request->user()), 403, "Vous n'avez pas accès à ce courrier.");

        $diligences = $courrier->diligences;
        $imputee = fn (string $type, int $idDest) => $diligences->contains(fn ($d) => $d->destinataire_type === $type && (int) $d->destinataire_id === $idDest)
            || $courrier->imputations->flatMap->destinatairesAssocies->contains(fn ($destinataire) => $destinataire->destinataire_type === $type && (int) $destinataire->destinataire_id === $idDest);
        $groupes = DestinatairesImputation::groupes();
        $derniere = $courrier->imputations->last();

        Journal::noter('courrier', 'telechargement', "Fiche de transmission du courrier {$courrier->numero} générée", null, $courrier);

        return Pdf::loadView('courrier.fiche_pdf', [
            'courrier' => $courrier, 'groupes' => $groupes, 'imputee' => $imputee, 'derniere' => $derniere,
            'agents' => $diligences->where('destinataire_type', 'agent'),
            'instructions' => InstructionCourrier::where('actif', true)->orderBy('ordre')->get(),
            'delais' => DelaiCourrier::where('actif', true)->orderBy('ordre')->get(),
            'ministere' => Reglages::get('courrier.fiche_ministere'), 'direction' => Reglages::get('courrier.fiche_direction'),
        ])->setPaper('a4', 'portrait')->stream('fiche_'.$courrier->numero.'.pdf');
    }

    public function scanApercu(Request $request, $id)
    {
        return $this->servirScan($request, $id, 'apercu');
    }

    public function scanTelecharger(Request $request, $id)
    {
        return $this->servirScan($request, $id, 'telechargement');
    }

    /** Aperçu (dans la visionneuse de l'application) ou téléchargement du scan. */
    private function servirScan(Request $request, $id, string $mode)
    {
        $courrier = Courrier::with('scans')->findOrFail($id);
        abort_unless(CourrierDroits::peutVoir($courrier, $request->user()), 403, "Vous n'avez pas accès à ce courrier.");

        $scan = $courrier->scan();
        abort_unless($scan && $this->disque()->exists($scan->chemin), 404, 'Aucun scan disponible pour ce courrier.');

        if ($courrier->estConfidentiel()) {
            Journal::noter('courrier', 'consultation', "Scan du courrier confidentiel {$courrier->numero} ".($mode === 'apercu' ? 'consulté' : 'téléchargé'), $scan, $courrier);
        } elseif ($mode !== 'apercu') {
            Journal::noter('courrier', 'telechargement', "Scan du courrier {$courrier->numero} téléchargé", $scan, $courrier);
        }

        if ($mode === 'apercu') {
            return response()->file($this->disque()->path($scan->chemin), [
                'Content-Type' => $scan->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.addslashes($courrier->numero.'.'.$scan->extension).'"',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN', // affichable dans la visionneuse de l'application
            ]);
        }

        return $this->disque()->download($scan->chemin, $courrier->numero.'.'.$scan->extension);
    }
}
