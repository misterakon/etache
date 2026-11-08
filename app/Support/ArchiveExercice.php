<?php

namespace App\Support;

use App\Models\Exercice;
use App\Models\JournalActivite;
use App\Models\Tache;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Archive Excel d'un exercice, générée à la clôture : liste des tâches, structure (matrice) et journal de l'exercice.
 * Stockée sur le disque privé ; retourne le chemin relatif.
 */
class ArchiveExercice
{
    public static function generer(Exercice $exercice): string
    {
        $classeur = new Spreadsheet;

        // 1. Tâches
        $taches = Tache::dansExercice($exercice->id)
            ->with([
                'actionMesurable.actionCle.axe', 'actionMesurable.actionCle.direction', 'actionMesurable.actionCle.directionsAssociees',
                'statutExecution', 'priorite', 'affectations.personnel', 'justifications',
            ])
            ->avecCompteSousTaches()
            ->orderBy('code')
            ->get();
        $colonnes = ExportTaches::colonnes('xlsx');
        self::feuille($classeur->getActiveSheet(), 'Tâches', array_values($colonnes), ExportTaches::lignes($taches, array_keys($colonnes)));

        // 2. Structure : axes > actions clés > actions mesurables
        $lignes = [];
        $axes = $exercice->axes()->with('actionsCles.actionsMesurables.taches')->orderBy('ordre')->get();
        foreach ($axes as $axe) {
            foreach ($axe->actionsCles as $ac) {
                foreach ($ac->actionsMesurables as $am) {
                    $lignes[] = [
                        $axe->code, $axe->libelle, $ac->code, $ac->libelle, $am->code, $am->libelle,
                        $am->taches->count(), $am->taches->count() ? (int) round($am->taches->avg('avancement_pourcentage')) : '',
                    ];
                }
            }
        }
        self::feuille($classeur->createSheet(), 'Structure', ['Axe', 'Libellé de l\'axe', 'Action clé', 'Libellé de l\'action clé', 'Action mesurable', 'Libellé de l\'action mesurable', 'Nb tâches', 'Avancement moyen (%)'], $lignes);

        // 3. Journal de l'exercice (activité de ses tâches + événements de l'exercice lui-même)
        $ids = $taches->pluck('id');
        $journal = JournalActivite::where(function ($q) use ($ids, $exercice) {
            $q->where(fn ($t) => $t->where('parent_type', (new Tache)->getMorphClass())->whereIn('parent_id', $ids))
                ->orWhere(fn ($e) => $e->where('sujet_type', $exercice->getMorphClass())->where('sujet_id', $exercice->id));
        })->orderBy('created_at')->limit(50000)->get();
        self::feuille($classeur->createSheet(), 'Journal', ['Date', 'Acteur', 'Module', 'Événement', 'Résumé'], $journal->map(fn ($e) => [
            $e->created_at->format('d/m/Y H:i:s'), $e->acteur_nom ?? '—', $e->libelleModule(), $e->libelleEvenement(), $e->resume,
        ])->all());

        $classeur->setActiveSheetIndex(0);

        $chemin = 'archives_exercices/exercice_'.$exercice->annee.'_'.now()->format('Ymd_His').'.xlsx';
        $tmp = tempnam(sys_get_temp_dir(), 'arc');
        (new Xlsx($classeur))->save($tmp);
        Storage::disk(config('pieces_jointes.disque'))->put($chemin, file_get_contents($tmp));
        @unlink($tmp);

        return $chemin;
    }

    private static function feuille(Worksheet $feuille, string $titre, array $entetes, array $lignes): void
    {
        $feuille->setTitle($titre);
        $feuille->fromArray($entetes, null, 'A1');
        if ($lignes) {
            $feuille->fromArray($lignes, null, 'A2');
        }

        $derniere = Coordinate::stringFromColumnIndex(count($entetes));
        ExportExcel::styleEntete($feuille, 1, count($entetes));
        ExportExcel::corps($feuille, 2, max(count($lignes) + 1, 2), count($entetes));
        $feuille->freezePane('A2');
        $feuille->setAutoFilter("A1:{$derniere}".max(count($lignes) + 1, 2));

        foreach (array_values($entetes) as $i => $entete) {
            $max = mb_strlen((string) $entete);
            foreach ($lignes as $ligne) {
                $max = max($max, mb_strlen((string) ($ligne[$i] ?? '')));
            }
            $feuille->getColumnDimensionByColumn($i + 1)->setWidth(min(max($max + 2, 10), 50));
        }
    }
}
