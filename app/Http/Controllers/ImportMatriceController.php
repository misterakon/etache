<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Support\ImportMatrice;
use App\Support\LecteurMatrice;
use App\Support\ModeleImportMatrice;
use App\Support\ReferentielImport;
use App\Support\SuiviProgression;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import de la matrice depuis un fichier Excel (ADMIN, DG).
 * Étapes : téléchargement du modèle -> prévisualisation (analyse, aucune écriture) -> import en une transaction.
 * L'import ré-analyse toujours le fichier : la prévisualisation n'est jamais considérée comme fiable.
 */
class ImportMatriceController extends Controller
{
    private const DOSSIER = 'imports_matrice';

    private const MAX_LIGNES_AFFICHEES = 1500;

    private function exiger(): void
    {
        Gate::authorize('import_matrice.importer');
    }

    private function erreur(string $message, int $code = 422, array $en_plus = []): JsonResponse
    {
        return response()->json(['status' => false, 'message' => $message] + $en_plus, $code);
    }

    /** Un exercice clos n'accepte plus de modification (colonne ajoutée avec la clôture d'exercice, tranche 7). */
    private function exerciceModifiable(Exercice $exercice): bool
    {
        return blank($exercice->getAttribute('cloture_le'));
    }

    private function chemin(string $token): ?string
    {
        if (! preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
            return null;
        }
        $relatif = self::DOSSIER.'/'.auth()->id().'_'.$token.'.xlsx';

        return Storage::disk('local')->exists($relatif) ? Storage::disk('local')->path($relatif) : null;
    }

    private function nettoyerAnciens(): void
    {
        $disque = Storage::disk('local');
        foreach ($disque->files(self::DOSSIER) as $fichier) {
            if ($disque->lastModified($fichier) < now()->subDay()->getTimestamp()) {
                $disque->delete($fichier);
            }
        }
    }

    public function index()
    {
        $this->exiger();

        return view('importation.import_matrice', [
            'exercices' => Exercice::orderByDesc('annee')->get(),
            'exerciceDefautId' => Exercice::parDefaut()?->id,
            'directionsSansCode' => ReferentielImport::directionsSansCode(),
        ]);
    }

    /** Modèle Excel rempli avec les codes de direction, statuts et priorités de la base. */
    public function modele()
    {
        $this->exiger();

        $classeur = ModeleImportMatrice::generer(
            ReferentielImport::directionsAvecCode(),
            ReferentielImport::statuts(),
            ReferentielImport::priorites()
        );

        return response()->streamDownload(function () use ($classeur) {
            IOFactory::createWriter($classeur, 'Xlsx')->save('php://output');
        }, 'modele_import_matrice.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Analyse du fichier : aucune écriture en base. */
    public function previsualiser(Request $request)
    {
        $this->exiger();
        @set_time_limit(120);

        $request->validate([
            'exercice_id' => ['required', 'exists:exercices,id'],
            'fichier' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
            'progression' => ['nullable', 'regex:/^[A-Za-z0-9]{20,40}$/'],
        ], [
            'fichier.required' => 'Choisissez un fichier Excel (.xlsx).',
            'fichier.mimes' => 'Le fichier doit être un classeur Excel (.xlsx).',
            'fichier.max' => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $exercice = Exercice::findOrFail($request->input('exercice_id'));
        if (! $this->exerciceModifiable($exercice)) {
            return $this->erreur("L'exercice {$exercice->annee} est clos : il n'accepte plus d'import.");
        }

        $suivi = SuiviProgression::pour($request->input('progression'));
        $p = $suivi?->callback();

        $this->nettoyerAnciens();
        $token = Str::random(40);
        $request->file('fichier')->storeAs(self::DOSSIER, auth()->id().'_'.$token.'.xlsx', 'local');
        $chemin = $this->chemin($token);

        $lecture = LecteurMatrice::lire($chemin, $p);
        if ($lecture['erreurs']) {
            Storage::disk('local')->delete(self::DOSSIER.'/'.auth()->id().'_'.$token.'.xlsx');

            return $this->erreur('Le fichier ne peut pas être analysé.', 422, ['erreurs_globales' => $lecture['erreurs']]);
        }

        if ($p) {
            $p('referentiel');
        }
        $referentiel = ReferentielImport::construire($exercice);
        $analyse = ImportMatrice::analyser($lecture['lignes'], $referentiel, $p);

        if ($p) {
            $p('finalisation');
        }

        // Les lignes en erreur d'abord affichées en entier ; les autres dans la limite d'affichage
        $lignes = $analyse['lignes'];
        $tronque = count($lignes) > self::MAX_LIGNES_AFFICHEES;
        if ($tronque) {
            $erreurs = array_filter($lignes, fn ($l) => $l['etat'] === 'erreur');
            $autres = array_filter($lignes, fn ($l) => $l['etat'] !== 'erreur');
            $lignes = array_values($erreurs + array_slice($autres, 0, self::MAX_LIGNES_AFFICHEES - count($erreurs), true));
            usort($lignes, fn ($a, $b) => $a['ligne'] <=> $b['ligne']);
        }

        $suivi?->terminer();

        return response()->json([
            'status' => true,
            'token' => $token,
            'exercice' => $exercice->annee,
            'importable' => $analyse['nb_erreurs'] === 0 && $analyse['resume']['taches_nouvelles'] > 0,
            'resume' => $analyse['resume'],
            'lignes' => $lignes,
            'tronque' => $tronque,
        ]);
    }

    /** Dernier état de progression d'une opération en cours (interrogé par la page toutes les ~0,4 s). */
    public function progression(string $id): JsonResponse
    {
        $this->exiger();

        return response()->json(SuiviProgression::lire($id) ?? ['etape' => null]);
    }

    /** Import réel : ré-analyse le fichier puis crée tout en une transaction (tout ou rien). */
    public function importer(Request $request)
    {
        $this->exiger();
        @set_time_limit(300);

        $donnees = $request->validate([
            'exercice_id' => ['required', 'exists:exercices,id'],
            'token' => ['required', 'string'],
            'progression' => ['nullable', 'regex:/^[A-Za-z0-9]{20,40}$/'],
        ]);

        $suivi = SuiviProgression::pour($request->input('progression'));
        $p = $suivi?->callback();

        $chemin = $this->chemin($donnees['token']);
        if (! $chemin) {
            return $this->erreur("Le fichier analysé a expiré. Recommencez l'analyse.");
        }

        $exercice = Exercice::findOrFail($donnees['exercice_id']);
        if (! $this->exerciceModifiable($exercice)) {
            return $this->erreur("L'exercice {$exercice->annee} est clos : il n'accepte plus d'import.");
        }

        $lecture = LecteurMatrice::lire($chemin, $p);
        if ($lecture['erreurs']) {
            return $this->erreur('Le fichier ne peut pas être analysé.', 422, ['erreurs_globales' => $lecture['erreurs']]);
        }

        if ($p) {
            $p('referentiel');
        }
        $referentiel = ReferentielImport::construire($exercice);
        $analyse = ImportMatrice::analyser($lecture['lignes'], $referentiel, $p);
        if ($analyse['nb_erreurs'] > 0) {
            return $this->erreur('Le fichier contient des erreurs : rien n\'a été importé.');
        }
        if ($analyse['resume']['taches_nouvelles'] === 0) {
            return $this->erreur('Aucune nouvelle tâche à importer : tout existe déjà dans cet exercice.');
        }

        $ids = ReferentielImport::idsCreation();
        if (in_array(null, [$ids['responsable'], $ids['pilote'], $ids['contributeur']], true)) {
            return $this->erreur("Les rôles d'affectation (Responsable, Pilote, Contributeur) sont introuvables : vérifiez les paramètres.");
        }

        try {
            $cree = ImportMatrice::executer($analyse['plan'], $exercice->id, $ids, $p);
        } catch (\Throwable $e) {
            report($e);

            return $this->erreur("L'import a échoué et rien n'a été enregistré : ".$e->getMessage(), 500);
        }

        Storage::disk('local')->delete(self::DOSSIER.'/'.auth()->id().'_'.$donnees['token'].'.xlsx');
        $suivi?->terminer();
        Log::info('Import de la matrice', ['user_id' => auth()->id(), 'exercice_id' => $exercice->id] + $cree);

        return response()->json([
            'status' => true,
            'message' => 'Import terminé.',
            'cree' => $cree,
            'ignorees' => $analyse['resume']['taches_existantes'],
        ]);
    }
}
