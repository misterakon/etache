<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\PieceJointe;
use App\Models\Priorite;
use App\Models\TachePonctuelle;
use App\Notifications\PonctuelleNotification;
use App\Support\Journal;
use App\Support\NotifPonctuelle;
use App\Support\PonctuellesDroits;
use App\Support\Reglages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Tâches ponctuelles : page dynamique (tout passe par des appels JSON, sans rechargement).
 * Visibilité et droits : voir App\Support\PonctuellesDroits.
 */
class TachePonctuelleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('ponctuelles.index', [
            'priorites' => Priorite::where('actif', true)->orderBy('id')->get(['id', 'libelle', 'couleur']),
            'agents' => PonctuellesDroits::agentsConfiables($user)->map(fn (Personnel $p) => ['id' => $p->id, 'nom' => $p->nomComplet(), 'moi' => (int) $p->id === (int) $user->personnel_id])->values(),
            'supervise' => PonctuellesDroits::supervise($user),
            'controle' => Reglages::get('taches_ponctuelles.controle'),
            'ouvrir' => $request->integer('ouvrir') ?: null,
            'aPersonnel' => (bool) $user->personnel_id,
            'agentFiltre' => $this->agentFiltre($request),
        ]);
    }

    /** Tâches ponctuelles d'un agent précis (lien depuis sa fiche ou « Mes tâches ») : seulement si l'on a le droit d'en voir. */
    private function agentFiltre(Request $request): ?array
    {
        $id = $request->integer('agent');
        if (! $id) {
            return null;
        }
        $user = $request->user();
        $agent = Personnel::find($id);
        if (! $agent) {
            return null;
        }
        $autorise = (int) $user->personnel_id === $id || $user->hasRole('ADMIN|DG')
            || PonctuellesDroits::agentsConfiables($user)->contains(fn ($p) => (int) $p->id === $id)
            || TachePonctuelle::query()->visiblePar($user)->where('responsable_personnel_id', $id)->exists();

        return $autorise ? ['id' => $agent->id, 'nom' => $agent->nomComplet(), 'moi' => (int) $user->personnel_id === $id] : null;
    }

    /** Liste filtrée + indicateurs (JSON). portee : mes | confiees | equipe. */
    public function donnees(Request $request): JsonResponse
    {
        $user = $request->user();
        $portee = in_array($request->query('portee'), ['mes', 'confiees', 'equipe'], true) ? $request->query('portee') : 'mes';
        $moi = (int) $user->personnel_id;

        $base = TachePonctuelle::query()->visiblePar($user)->with(['responsable', 'priorite', 'createur']);
        $agentFiltre = $this->agentFiltre($request);
        $base = match (true) {
            $agentFiltre !== null => $base->where('responsable_personnel_id', $agentFiltre['id']),
            $portee === 'mes' => $base->where('responsable_personnel_id', $moi ?: 0),
            $portee === 'confiees' => $base->where('cree_par_user_id', $user->id)->where('responsable_personnel_id', '!=', $moi ?: 0),
            default => $base->where('responsable_personnel_id', '!=', $moi ?: 0),
        };

        $kpis = [
            'a_faire' => (clone $base)->actives()->where('statut', TachePonctuelle::A_FAIRE)->count(),
            'en_cours' => (clone $base)->actives()->where('statut', TachePonctuelle::EN_COURS)->count(),
            'en_retard' => (clone $base)->enRetard()->count(),
            'terminees' => (clone $base)->where('statut', TachePonctuelle::TERMINEE)->count(),
            'a_valider' => TachePonctuelle::query()->visiblePar($user)->where('validation', TachePonctuelle::VALIDATION_ATTENTE)
                ->when(! $user->hasRole('ADMIN|DG'), fn ($q) => $q->whereJsonContains('superieurs', $moi))->count(),
        ];

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            foreach (array_slice(preg_split('/\s+/u', mb_substr($q, 0, 100)) ?: [], 0, 5) as $mot) {
                $like = '%'.addcslashes($mot, '%_\\').'%';
                $base->where(fn ($w) => $w->where('libelle', 'like', $like)->orWhere('code', 'like', $like)->orWhere('description', 'like', $like)
                    ->orWhereHas('responsable', fn ($r) => $r->where('nom', 'like', $like)->orWhere('prenom', 'like', $like)));
            }
        }

        $taches = $base->orderByRaw("CASE statut WHEN 'terminee' THEN 2 WHEN 'annulee' THEN 3 ELSE 0 END")->orderByRaw('date_fin IS NULL')->orderBy('date_fin')->orderByDesc('id')->limit(300)->get();

        return response()->json(['taches' => $taches->map(fn ($t) => $this->fiche($t, $user))->values(), 'kpis' => $kpis, 'portee' => $portee]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $t = TachePonctuelle::with(['responsable', 'priorite', 'createur'])->findOrFail($id);
        abort_unless(PonctuellesDroits::voit($request->user(), $t), 403);

        return response()->json($this->fiche($t, $request->user()));
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $v = Validator::make($request->all(), [
            'libelle' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'priorite_id' => ['nullable', 'integer', 'exists:priorites,id'],
            'responsable_personnel_id' => ['nullable', 'integer', 'exists:personnel,id'],
        ], [
            'libelle.required' => 'Décrivez la tâche en quelques mots.', 'libelle.min' => 'Le libellé est trop court.',
            'date_fin.after_or_equal' => "L'échéance doit être postérieure au début.",
        ]);
        if ($v->fails()) {
            return response()->json(['message' => $v->errors()->first(), 'erreurs' => $v->errors()], 422);
        }
        $d = $v->validated();

        $cibleId = (int) ($d['responsable_personnel_id'] ?? $user->personnel_id);
        if (! $cibleId) {
            return response()->json(['message' => "Votre compte n'est lié à aucun agent : choisissez l'agent concerné."], 422);
        }
        $agent = Personnel::findOrFail($cibleId);
        if (! PonctuellesDroits::peutConfierA($user, $agent)) {
            return response()->json(['message' => "Vous ne pouvez confier une tâche qu'aux agents placés sous votre responsabilité."], 403);
        }
        if (TachePonctuelle::query()->ouvertes()->where('responsable_personnel_id', $agent->id)->count() >= (int) Reglages::get('taches_ponctuelles.max_ouvertes')) {
            return response()->json(['message' => 'Le nombre maximal de tâches ponctuelles ouvertes est atteint pour cet agent.'], 422);
        }

        $pourSoi = (int) $agent->id === (int) $user->personnel_id;
        $controle = (string) Reglages::get('taches_ponctuelles.controle');
        $validation = $pourSoi && $controle === 'validation' ? TachePonctuelle::VALIDATION_ATTENTE : 'aucune';
        $chaine = PonctuellesDroits::chaineIds($agent);

        $t = TachePonctuelle::create([
            'code' => TachePonctuelle::prochainCode(),
            'libelle' => trim($d['libelle']), 'description' => $d['description'] ?? null,
            'date_debut' => $d['date_debut'] ?? null, 'date_fin' => $d['date_fin'] ?? null,
            'priorite_id' => $d['priorite_id'] ?? null,
            'responsable_personnel_id' => $agent->id, 'cree_par_user_id' => $user->id,
            'origine' => $pourSoi ? 'agent' : 'responsable', 'validation' => $validation,
            'direction_id' => $agent->rattachement()['direction']?->id, 'superieurs' => $chaine,
        ]);

        if (! $pourSoi) {
            NotifPonctuelle::versPersonnels([$agent->id], $t, PonctuelleNotification::CONFIEE, null, $user->id);
        } elseif ($validation === TachePonctuelle::VALIDATION_ATTENTE) {
            NotifPonctuelle::versPersonnels(array_slice($chaine, 0, 1), $t, PonctuelleNotification::A_VALIDER, null, $user->id);
        } elseif ($controle === 'notification') {
            NotifPonctuelle::versPersonnels(array_slice($chaine, 0, 1), $t, PonctuelleNotification::CREEE, null, $user->id);
        }
        Journal::noter('taches_ponctuelles', 'creation', 'Tâche ponctuelle créée : '.$t->code.' — '.Str::limit($t->libelle, 80), $t);

        return response()->json($this->fiche($t->load(['responsable', 'priorite', 'createur']), $user), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $t = TachePonctuelle::with('responsable')->findOrFail($id);
        abort_unless(PonctuellesDroits::modifie($user, $t), 403, 'Vous ne pouvez pas modifier cette tâche.');
        if ($t->statut === TachePonctuelle::ANNULEE) {
            return response()->json(['message' => 'Une tâche annulée ne peut plus être modifiée.'], 422);
        }

        $v = Validator::make($request->all(), [
            'libelle' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'date_debut' => ['sometimes', 'nullable', 'date'],
            'date_fin' => ['sometimes', 'nullable', 'date'],
            'priorite_id' => ['sometimes', 'nullable', 'integer', 'exists:priorites,id'],
            'avancement_pourcentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'statut' => ['sometimes', 'in:a_faire,en_cours,terminee'],
        ]);
        if ($v->fails()) {
            return response()->json(['message' => $v->errors()->first(), 'erreurs' => $v->errors()], 422);
        }
        $d = $v->validated();

        if ($t->validation === TachePonctuelle::VALIDATION_ATTENTE && (isset($d['statut']) || isset($d['avancement_pourcentage'])) && ! PonctuellesDroits::estSuperieur($user, $t)) {
            return response()->json(['message' => 'Cette tâche attend la validation de votre supérieur : elle ne peut pas encore démarrer.'], 422);
        }
        if ($t->validation === TachePonctuelle::VALIDATION_REFUSEE) {
            return response()->json(['message' => 'Cette tâche a été refusée par votre supérieur.'], 422);
        }

        $t->fill($d);
        if (array_key_exists('statut', $d)) {
            if ($d['statut'] === TachePonctuelle::TERMINEE) {
                $t->avancement_pourcentage = 100;
                $t->terminee_le = now();
            } else {
                $t->terminee_le = null;
                if ($d['statut'] === TachePonctuelle::A_FAIRE && ! isset($d['avancement_pourcentage']) && $t->avancement_pourcentage === 100) {
                    $t->avancement_pourcentage = 0;
                }
            }
        }
        if (isset($d['avancement_pourcentage'])) {
            $p = (int) $d['avancement_pourcentage'];
            if (! isset($d['statut'])) {
                $t->statut = $p >= 100 ? TachePonctuelle::TERMINEE : ($p > 0 ? TachePonctuelle::EN_COURS : TachePonctuelle::A_FAIRE);
                $t->terminee_le = $p >= 100 ? now() : null;
            }
        }
        $t->save();
        Journal::noter('taches_ponctuelles', 'modification', 'Tâche ponctuelle mise à jour : '.$t->code.' ('.(self::STATUT_LIB[$t->statut] ?? $t->statut).', '.$t->avancement_pourcentage.' %)', $t);

        return response()->json($this->fiche($t->load(['responsable', 'priorite', 'createur']), $user));
    }

    private const STATUT_LIB = ['a_faire' => 'à faire', 'en_cours' => 'en cours', 'terminee' => 'terminée', 'annulee' => 'annulée'];

    /** Décision de la chaîne hiérarchique : valider (si en attente), refuser ou annuler, avec un motif pour les deux derniers. */
    public function decider(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $t = TachePonctuelle::with('responsable')->findOrFail($id);
        abort_unless(PonctuellesDroits::estSuperieur($user, $t), 403, 'Seule la chaîne hiérarchique de l\'agent peut décider.');

        $v = Validator::make($request->all(), ['decision' => ['required', 'in:valider,refuser,annuler'], 'motif' => ['nullable', 'string', 'max:1000']]);
        if ($v->fails()) {
            return response()->json(['message' => 'Décision invalide.'], 422);
        }
        $decision = $request->input('decision');
        $motif = trim((string) $request->input('motif', ''));
        if (in_array($decision, ['refuser', 'annuler'], true) && mb_strlen($motif) < 3) {
            return response()->json(['message' => 'Indiquez le motif : il sera communiqué à l\'agent.'], 422);
        }
        if ($decision === 'valider' && $t->validation !== TachePonctuelle::VALIDATION_ATTENTE) {
            return response()->json(['message' => "Cette tâche n'attend pas de validation."], 422);
        }

        $evenement = match ($decision) {
            'valider' => PonctuelleNotification::VALIDEE,
            'refuser' => PonctuelleNotification::REFUSEE,
            default => PonctuelleNotification::ANNULEE,
        };
        if ($decision === 'valider') {
            $t->validation = 'validee';
        } elseif ($decision === 'refuser') {
            $t->validation = TachePonctuelle::VALIDATION_REFUSEE;
            $t->statut = TachePonctuelle::ANNULEE;
            $t->annulee_le = now();
        } else {
            $t->statut = TachePonctuelle::ANNULEE;
            $t->annulee_le = now();
        }
        $t->fill(['motif_decision' => $motif ?: null, 'decide_par_user_id' => $user->id, 'decide_le' => now()])->save();

        NotifPonctuelle::versPersonnels([$t->responsable_personnel_id], $t, $evenement, $motif ?: null, $user->id);
        Journal::noter('taches_ponctuelles', $decision, 'Tâche ponctuelle '.['valider' => 'validée', 'refuser' => 'refusée', 'annuler' => 'annulée'][$decision].' : '.$t->code, $t, null, ['motif' => $motif]);

        return response()->json($this->fiche($t->load(['responsable', 'priorite', 'createur']), $user));
    }

    /** Suppression : seulement une tâche sans avancement, par son créateur ou sa chaîne hiérarchique. */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $t = TachePonctuelle::findOrFail($id);
        abort_unless((int) $t->cree_par_user_id === (int) $user->id || PonctuellesDroits::estSuperieur($user, $t), 403);
        if ($t->avancement_pourcentage > 0 || $t->piecesJointes()->exists()) {
            return response()->json(['message' => 'Une tâche déjà commencée ou avec des livrables ne se supprime pas : annulez-la.'], 422);
        }
        Journal::noter('taches_ponctuelles', 'suppression', 'Tâche ponctuelle supprimée : '.$t->code.' — '.Str::limit($t->libelle, 80), $t);
        $t->delete();

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------ livrables */

    public function livrableStore(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $t = TachePonctuelle::findOrFail($id);
        abort_unless(PonctuellesDroits::modifie($user, $t), 403);

        $extensions = array_intersect_key(config('pieces_jointes.extensions'), array_flip(Reglages::get('fichiers.extensions')));
        $v = Validator::make($request->all(), ['fichier' => ['required', 'file', 'max:'.Reglages::get('fichiers.taille_max_ko'), 'extensions:'.implode(',', array_keys($extensions))]], [
            'fichier.required' => 'Choisissez un fichier.', 'fichier.uploaded' => "Le fichier n'a pas pu être envoyé (taille maximale dépassée ?).",
            'fichier.max' => 'Le fichier dépasse la taille maximale autorisée (:max Ko).', 'fichier.extensions' => 'Type de fichier non autorisé (PDF, Word, Excel, PowerPoint, images, zip).',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => $v->errors()->first()], 422);
        }
        if ($t->piecesJointes()->count() >= (int) Reglages::get('fichiers.max_par_element')) {
            return response()->json(['message' => 'Maximum de '.Reglages::get('fichiers.max_par_element').' livrables atteint.'], 422);
        }

        $fichier = $request->file('fichier');
        $ext = strtolower($fichier->getClientOriginalExtension());
        if (! in_array($fichier->getMimeType(), $extensions[$ext] ?? [], true)) {
            return response()->json(['message' => "Le contenu du fichier ne correspond pas à son extension (.{$ext})."], 422);
        }
        $chemin = $fichier->storeAs('pieces_jointes/'.now()->format('Y/m'), Str::random(40).'.'.$ext, config('pieces_jointes.disque'));
        if (! $chemin) {
            return response()->json(['message' => "Le fichier n'a pas pu être enregistré."], 500);
        }
        $piece = PieceJointe::create([
            'attachable_type' => $t->getMorphClass(), 'attachable_id' => $t->id, 'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''),
            'chemin' => $chemin, 'extension' => $ext, 'mime' => $fichier->getMimeType(), 'taille' => $fichier->getSize(), 'version' => 1, 'actuelle' => true, 'depose_par' => $user->id,
        ]);
        Journal::noter('fichiers', 'depot', 'Livrable déposé (tâche ponctuelle '.$t->code.') : '.$piece->nom_original, $piece, $t);

        return response()->json($this->fiche($t->load(['responsable', 'priorite', 'createur']), $user), 201);
    }

    public function livrableTelecharger(Request $request, int $id, int $pieceId)
    {
        $t = TachePonctuelle::findOrFail($id);
        abort_unless(PonctuellesDroits::voit($request->user(), $t), 403);
        $piece = PieceJointe::where('attachable_type', $t->getMorphClass())->where('attachable_id', $t->id)->findOrFail($pieceId);
        $disque = Storage::disk(config('pieces_jointes.disque'));
        abort_unless($disque->exists($piece->chemin), 404, 'Fichier introuvable sur le serveur.');

        return $disque->download($piece->chemin, $piece->nom_original);
    }

    public function livrableSupprimer(Request $request, int $id, int $pieceId): JsonResponse
    {
        $user = $request->user();
        $t = TachePonctuelle::findOrFail($id);
        abort_unless(PonctuellesDroits::modifie($user, $t), 403);
        $piece = PieceJointe::where('attachable_type', $t->getMorphClass())->where('attachable_id', $t->id)->findOrFail($pieceId);
        Journal::noter('fichiers', 'suppression', 'Livrable retiré (tâche ponctuelle '.$t->code.') : '.$piece->nom_original, $piece, $t);
        $piece->delete();

        return response()->json($this->fiche($t->load(['responsable', 'priorite', 'createur']), $user));
    }

    /* ------------------------------------------------------------------ présentation */

    /** @return array<string, mixed> */
    private function fiche(TachePonctuelle $t, $user): array
    {
        [$libStatut, $couleur] = TachePonctuelle::STATUTS[$t->statut] ?? [$t->statut, '#64748B'];
        $jours = $t->date_fin ? (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false) : null;
        $r = $t->responsable;
        $modifie = PonctuellesDroits::modifie($user, $t) && $t->statut !== TachePonctuelle::ANNULEE && $t->validation !== TachePonctuelle::VALIDATION_REFUSEE;
        $superieur = PonctuellesDroits::estSuperieur($user, $t);

        return [
            'id' => $t->id, 'code' => $t->code, 'libelle' => $t->libelle, 'description' => $t->description,
            'priorite' => $t->priorite ? ['id' => $t->priorite->id, 'libelle' => $t->priorite->libelle, 'couleur' => $t->priorite->couleur] : null,
            'date_debut' => $t->date_debut?->toDateString(), 'date_fin' => $t->date_fin?->toDateString(), 'date_fin_fr' => $t->date_fin?->format('d/m/Y'),
            'jours' => $jours, 'retard' => $t->estEnRetard(), 'avancement' => (int) $t->avancement_pourcentage,
            'statut' => $t->statut, 'statut_lib' => $libStatut, 'couleur' => $couleur,
            'validation' => $t->validation, 'origine' => $t->origine, 'motif_decision' => $t->motif_decision,
            'responsable' => $r ? ['id' => $r->id, 'nom' => $r->nomComplet(), 'initiales' => $r->initiales(), 'photo' => $r->photo_url, 'moi' => (int) $r->id === (int) $user->personnel_id] : null,
            'createur' => $t->createur?->name, 'cree_le' => $t->created_at?->format('d/m/Y à H:i'),
            'peut' => [
                'modifier' => $modifie,
                'decider' => $superieur && $t->statut !== TachePonctuelle::ANNULEE && $t->validation !== TachePonctuelle::VALIDATION_REFUSEE,
                'valider' => $superieur && $t->validation === TachePonctuelle::VALIDATION_ATTENTE,
                'supprimer' => ((int) $t->cree_par_user_id === (int) $user->id || $superieur) && $t->avancement_pourcentage === 0 && ! $t->piecesJointes()->exists(),
            ],
            'livrables' => $t->piecesJointes()->get()->map(fn (PieceJointe $p) => [
                'id' => $p->id, 'nom' => $p->nom_original, 'taille' => $p->tailleLisible(), 'ext' => $p->extension, 'par' => $p->deposePar?->name,
                'url' => route('ponctuelle.livrable.telecharger', [$t->id, $p->id]), 'supprimable' => $modifie,
            ])->values(),
        ];
    }
}
