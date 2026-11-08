<?php

namespace App\Console\Commands;

use App\Models\ActionCle;
use App\Models\ActionMesurable;
use App\Models\Axe;
use App\Models\SituationTemporelle;
use App\Models\StatutExecution;
use App\Models\Tache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportMatriceCommand extends Command
{
    /**
     * php artisan import:matrice storage/app/imports/matrice.xlsx
     */
    protected $signature = 'import:matrice
        {chemin : Chemin vers le fichier .xlsx}
        {--sheet=Plan_Révisé (lisible) : Nom exact de la feuille à importer}
        {--dry-run : Affiche ce qui serait importé sans rien écrire en base}';

    protected $description = "Importe la matrice de la feuille de route DGEPN (axes, actions clés, actions mesurables, tâches)";

    // Compteurs pour la colonne `ordre`, remis à zéro à chaque nouveau parent
    private int $ordreAxe = 0;
    private array $ordreActionCle = [];
    private array $ordreActionMesurable = [];
    private array $ordreTache = [];

    public function handle(): int
    {
        $chemin = $this->argument('chemin');

        if (! file_exists($chemin)) {
            $this->error("Fichier introuvable : {$chemin}");
            return self::FAILURE;
        }

        $nomFeuille = $this->option('sheet');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Lecture de \"{$nomFeuille}\" dans {$chemin}...");

        $spreadsheet = IOFactory::load($chemin);
        $feuille = $spreadsheet->getSheetByName($nomFeuille);

        if (! $feuille) {
            $this->error("Feuille \"{$nomFeuille}\" introuvable. Feuilles disponibles : "
                .implode(', ', $spreadsheet->getSheetNames()));
            return self::FAILURE;
        }

        $lignes = $this->extraireLignes($feuille);

        $this->info(count($lignes).' lignes de données trouvées (hors en-tête).');

        $stats = ['axes' => 0, 'actions_cles' => 0, 'actions_mesurables' => 0, 'taches' => 0];

        // état du "forward-fill" (report des cellules fusionnées/vides)
        $axeCourant = null;
        $resultatCourant = null;
        $actionCleTexteCourant = null;
        $actionMesurableTexteCourant = null;

        $axeModele = null;
        $actionCleModele = null;
        $actionMesurableModele = null;

        DB::beginTransaction();

        try {
            foreach ($lignes as $i => $ligne) {
                [$axeTexte, $resultatTexte, $actionCleTexte, $actionMesurableTexte, $tacheTexte,
                    $indicateur, $livrable, $responsableTexte, $dateLimite, $statutTexte] = $ligne;

                // Ligne totalement vide : on l'ignore
                if (! array_filter($ligne, fn ($v) => $v !== null && $v !== '')) {
                    continue;
                }

                // Garde-fou : une cellule "Axe" renseignée mais qui ne suit pas le motif
                // "Axe N — ..." est une note/commentaire résiduel de la matrice, pas un vrai axe.
                if ($axeTexte !== null && $axeTexte !== '' && ! preg_match('/^Axe\s*\d+/ui', $axeTexte)) {
                    $this->warn("Ligne ".($i + 2).' ignorée (ne ressemble pas à un axe) : '.\Illuminate\Support\Str::limit($axeTexte, 60));
                    continue;
                }

                // --- Forward-fill des colonnes hiérarchiques ---
                if ($axeTexte !== null && $axeTexte !== '') {
                    $axeCourant = $axeTexte;
                    $resultatCourant = $resultatTexte;
                    $axeModele = null; // sera recréé/récupéré ci-dessous
                }
                if ($actionCleTexte !== null && $actionCleTexte !== '') {
                    $actionCleTexteCourant = $actionCleTexte;
                    $actionCleModele = null;
                }
                if ($actionMesurableTexte !== null && $actionMesurableTexte !== '') {
                    $actionMesurableTexteCourant = $actionMesurableTexte;
                    $actionMesurableModele = null;
                }

                if (! $axeCourant || ! $actionCleTexteCourant || ! $actionMesurableTexteCourant) {
                    $this->warn('Ligne '.($i + 2).' ignorée (hiérarchie incomplète, aucun axe/action clé/action mesurable actif).');
                    continue;
                }

                // --- Axe ---
                if (! $axeModele) {
                    [$axeNumero, $axeLibelle] = $this->parserAxe($axeCourant);
                    $axeModele = Axe::firstOrCreate(
                        ['libelle' => $axeLibelle],
                        [
                            'resultat_attendu' => $resultatCourant,
                            'ordre' => ++$this->ordreAxe,
                        ]
                    );
                    if (! $axeModele->wasRecentlyCreated) {
                        $this->ordreAxe--; // on n'a pas consommé un nouveau numéro d'ordre
                    } else {
                        $stats['axes']++;
                    }
                }

                // --- Action clé ---
                if (! $actionCleModele) {
                    [$codeActionCle, $libelleActionCle] = $this->parserActionCle($actionCleTexteCourant);
                    $this->ordreActionCle[$axeModele->id] ??= 0;
                    $this->ordreActionCle[$axeModele->id]++;
                    $actionCleModele = ActionCle::firstOrCreate(
                        ['axe_id' => $axeModele->id, 'libelle' => $libelleActionCle],
                        [
                            'code' => $codeActionCle,
                            'ordre' => $this->ordreActionCle[$axeModele->id],
                        ]
                    );
                    if ($actionCleModele->wasRecentlyCreated) {
                        $stats['actions_cles']++;
                    }
                }

                // --- Action mesurable ---
                if (! $actionMesurableModele) {
                    $this->ordreActionMesurable[$actionCleModele->id] ??= 0;
                    $this->ordreActionMesurable[$actionCleModele->id]++;
                    $actionMesurableModele = ActionMesurable::firstOrCreate(
                        ['action_cle_id' => $actionCleModele->id, 'libelle' => $actionMesurableTexteCourant],
                        ['ordre' => $this->ordreActionMesurable[$actionCleModele->id]]
                    );
                    if ($actionMesurableModele->wasRecentlyCreated) {
                        $stats['actions_mesurables']++;
                    }
                }

                // --- Tâche ---
                // Si "Tâche afférente" est vide, l'action mesurable EST la tâche (pas de découpage).
                $libelleTache = ($tacheTexte !== null && $tacheTexte !== '')
                    ? $tacheTexte
                    : $actionMesurableTexteCourant;

                $estRealise = $statutTexte !== null && trim(mb_strtolower($statutTexte)) === 'réalisé';
                $statutId = $estRealise ? StatutExecution::TERMINEE : StatutExecution::NON_DEMARREE;
                $situationId = $estRealise ? SituationTemporelle::TERMINEE : SituationTemporelle::NORMAL;

                $dateFin = $this->convertirDate($dateLimite);

                $this->ordreTache[$actionMesurableModele->id] ??= 0;
                $this->ordreTache[$actionMesurableModele->id]++;

                $tacheModele = Tache::firstOrCreate(
                    ['action_mesurable_id' => $actionMesurableModele->id, 'libelle' => $libelleTache],
                    [
                        'indicateur' => $indicateur,
                        'livrable' => $livrable,
                        'ordre' => $this->ordreTache[$actionMesurableModele->id],
                        'date_fin' => $dateFin,
                        'statut_execution_id' => $statutId,
                        'situation_temporelle_id' => $situationId,
                        'avancement_pourcentage' => $estRealise ? 100 : 0,
                        // Responsable indiqué dans la matrice source : conservé en clair pour
                        // faciliter l'affectation manuelle dans l'application (pas d'auto-création
                        // de Personnel/Affectation à l'import).
                        'observations' => ($responsableTexte !== null && trim($responsableTexte) !== '')
                            ? 'Responsable indiqué dans la matrice source : '.$this->normaliserNomResponsable($responsableTexte)
                            : null,
                    ]
                );
                if ($tacheModele->wasRecentlyCreated) {
                    $stats['taches']++;
                }
            }

            if ($dryRun) {
                $this->warn('Mode --dry-run : annulation de la transaction, rien n\'a été écrit en base.');
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Erreur pendant l\'import : '.$e->getMessage());
            $this->error($e->getFile().':'.$e->getLine());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Import terminé'.($dryRun ? ' (simulation)' : '').' :');
        foreach ($stats as $label => $count) {
            $this->line("  - {$label} : {$count} créé(s)");
        }

        return self::SUCCESS;
    }

    /**
     * Convertit la feuille en tableau de lignes brutes (sans l'en-tête),
     * une ligne = tableau indexé 0..9 dans l'ordre des colonnes Excel.
     */
    private function extraireLignes($feuille): array
    {
        $donnees = $feuille->toArray(null, true, true, false);
        // Ligne 0 = en-têtes, on les retire
        array_shift($donnees);

        return $donnees;
    }

    /**
     * "Axe 1 — Harmonisation du cadre..." -> ['AXE1', 'Harmonisation du cadre...']
     */
    private function parserAxe(string $texte): array
    {
        if (preg_match('/^Axe\s*(\d+)\s*[—-]\s*(.+)$/ui', trim($texte), $m)) {
            return ['AXE'.$m[1], trim($m[2])];
        }

        return [null, trim($texte)];
    }

    /**
     * "R06 — Régime des jetons de présence..." -> ['R06', 'Régime des jetons de présence...']
     */
    private function parserActionCle(string $texte): array
    {
        if (preg_match('/^(R\d+)\s*[—-]\s*(.+)$/ui', trim($texte), $m)) {
            return [$m[1], trim($m[2])];
        }

        return [null, trim($texte)];
    }

    /**
     * "M.BAMBA Arnaud" -> "M. BAMBA Arnaud" (ajoute l'espace manquant après "M.",
     * normalise les espaces multiples). La casse elle-même est rapprochée
     * séparément via une comparaison LOWER() au moment du firstOrCreate.
     */
    private function normaliserNomResponsable(string $texte): string
    {
        $texte = trim($texte);
        $texte = preg_replace('/^M\.\s*/ui', 'M. ', $texte);
        $texte = preg_replace('/\s+/', ' ', $texte);

        return $texte;
    }

    /**
     * PhpSpreadsheet peut retourner soit un objet DateTime, soit un nombre de série Excel,
     * soit null, selon la lecture. On gère les trois cas.
     */
    private function convertirDate(mixed $valeur): ?string
    {
        if ($valeur === null || $valeur === '') {
            return null;
        }

        if ($valeur instanceof \DateTimeInterface) {
            return $valeur->format('Y-m-d');
        }

        if (is_numeric($valeur)) {
            return ExcelDate::excelToDateTimeObject($valeur)->format('Y-m-d');
        }

        // Chaîne déjà formatée
        try {
            return \Carbon\Carbon::parse($valeur)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
