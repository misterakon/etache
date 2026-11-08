<?php

namespace App\Support;

use App\Models\SynthesePilotage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Export Excel d'une analyse de pilotage : une feuille par thème (synthèse, axes, directions, évolution, retards, courrier). */
class ExportPilotage
{
    private static function enteteTableau(Worksheet $f, int $ligne, array $titres): void
    {
        ExportExcel::styleEntete($f, $ligne, count($titres));
        $f->fromArray($titres, null, 'A'.$ligne);
    }

    private static function feuille(Spreadsheet $classeur, string $titre, array $titres, array $lignes, array $largeurs): void
    {
        $f = $classeur->createSheet();
        $f->setTitle($titre);
        self::enteteTableau($f, 1, $titres);
        if ($lignes) {
            $f->fromArray($lignes, null, 'A2');
        }
        foreach ($largeurs as $i => $l) {
            $f->getColumnDimensionByColumn($i + 1)->setWidth($l);
        }
        $derniere = Coordinate::stringFromColumnIndex(count($titres));
        $f->freezePane('A2');
        $f->setAutoFilter("A1:{$derniere}".max(2, count($lignes) + 1));
        $f->getStyle('A2:'.$derniere.(count($lignes) + 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        ExportExcel::corps($f, 2, max(2, count($lignes) + 1), count($titres));
        $f->setShowGridlines(false);
    }

    /** Texte de la synthèse sans balisage « ## » et « - ». */
    public static function textePlat(string $contenu): string
    {
        return preg_replace(['/^#{1,4}\s*/m', '/^[-•*]\s+/m'], ['', '• '], $contenu);
    }

    public static function excel(array $a, ?SynthesePilotage $synthese, string $editeur): StreamedResponse
    {
        $s = $a['situation'];
        $p = $a['periode_chiffres'];
        $classeur = new Spreadsheet;
        $f = $classeur->getActiveSheet();
        $f->setTitle('Synthèse');
        $f->getColumnDimension('A')->setWidth(46);
        $f->getColumnDimension('B')->setWidth(22);
        $f->getColumnDimension('C')->setWidth(22);
        $f->getColumnDimension('D')->setWidth(60);

        $f->setCellValue('A1', 'Pilotage de la feuille de route');
        ExportExcel::styleTitre($f, 'A1', 16);
        $f->setShowGridlines(false);
        foreach ([['Exercice', (string) $a['exercice']->annee], ['Période', $a['periode']['libelle']], ['Périmètre', $a['portee']], ['Édité le', now()->format('d/m/Y à H:i').' par '.$editeur]] as $i => [$k, $v]) {
            $f->setCellValue('A'.($i + 2), $k);
            $f->setCellValue('B'.($i + 2), $v);
            $f->getStyle('A'.($i + 2))->getFont()->setBold(true);
        }

        $kpis = [
            ['Tâches (hors annulées)', $s['total']], ['Terminées', $s['terminees']], ['En cours', $s['en_cours']], ['Non démarrées', $s['non_demarrees']],
            ['Bloquées', $s['bloquees']], ['Suspendues', $s['suspendues']], ['En retard', $s['en_retard']], ['Retards non justifiés', $s['non_justifies']],
            ['Avancement moyen (%)', $s['avancement']], ['Taux de réalisation (%)', $s['taux']], ['Budget estimé (FCFA)', $s['budget']], ['Budget des tâches terminées (FCFA)', $s['budget_realise']],
            ['— Sur la période —', null], ['Échéances de la période', $p['echeances']], ['Tâches terminées sur la période', $p['terminees']], ['… dont dans les délais', $p['a_temps']],
            ['Taux de respect des échéances (%)', $p['taux_respect']], ['En retard à la fin de la période', $p['retard_fin_periode']],
        ];
        $ligne = 7;
        self::enteteTableau($f, $ligne, ['Indicateur', 'Valeur']);
        foreach ($kpis as [$k, $v]) {
            $ligne++;
            $f->setCellValue('A'.$ligne, $k);
            $f->setCellValue('B'.$ligne, $v);
            if (str_starts_with($k, '—')) {
                $f->getStyle('A'.$ligne)->getFont()->setBold(true);
            }
        }
        $f->getStyle('B8:B'.$ligne)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $ligne += 2;
        $f->setCellValue('A'.$ligne, 'Synthèse'.($synthese ? ' — '.$synthese->libelleSource().' ('.$synthese->created_at->format('d/m/Y').')' : ''));
        $f->getStyle('A'.$ligne)->getFont()->setBold(true)->setSize(12);
        $ligne++;
        $texte = $synthese ? self::textePlat($synthese->contenu) : "Aucune synthèse n'a encore été générée pour ce périmètre et cette période.";
        $f->mergeCells("A{$ligne}:D{$ligne}");
        $f->setCellValue('A'.$ligne, $texte);
        $f->getStyle("A{$ligne}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $f->getRowDimension($ligne)->setRowHeight(min(409, 16 * (substr_count($texte, "\n") + (int) ceil(mb_strlen($texte) / 120) + 1)));

        $tableauEntites = fn (array $rows, bool $avecCode = false) => array_map(fn ($r) => [($avecCode ? $r['code'].' · ' : '').$r['libelle'], $r['total'], $r['terminees'], $r['en_cours'], $r['non_demarrees'], $r['en_retard'], $r['non_justifies'], $r['avancement'], $r['taux'], $r['budget']], $rows);
        $titres = ['Tâches', 'Terminées', 'En cours', 'Non démarrées', 'En retard', 'Retards non justifiés', 'Avancement moyen (%)', 'Taux de réalisation (%)', 'Budget estimé (FCFA)'];
        self::feuille($classeur, 'Par axe', array_merge(['Axe'], $titres), $tableauEntites($a['par_axe'], true), [52, 10, 11, 10, 14, 10, 14, 14, 14, 18]);
        self::feuille($classeur, 'Par direction', array_merge(['Direction'], $titres), $tableauEntites($a['par_direction']), [52, 10, 11, 10, 14, 10, 14, 14, 14, 18]);
        self::feuille($classeur, 'Évolution', ['Mois', 'Attendu (%)', 'Réalisé (%)'], array_map(fn ($c) => [$c['mois'], $c['attendu'], $c['realise']], $a['courbe']), [14, 14, 14]);
        self::feuille($classeur, 'Retards', ['Tâche', 'Libellé', 'Axe', 'Direction', 'Échéance', 'Jours de retard', 'Justification', 'Avancement (%)'],
            array_map(fn ($r) => [$r['code'], $r['libelle'], $r['axe'], $r['direction'], $r['echeance'], $r['jours'], $r['justification'], $r['avancement']], $a['retards']), [12, 60, 36, 36, 12, 12, 16, 14]);
        if ($a['courrier']) {
            $mois = array_map(fn ($m) => ucfirst(mb_substr(AnalysesPilotage::MOIS[$m], 0, 4)), range(1, 12));
            self::feuille($classeur, 'Courrier', ['Mois', 'Courriers reçus', 'Courriers expédiés'], array_map(fn ($m, $r, $d) => [$m, $r, $d], $mois, $a['courrier']['recus'], $a['courrier']['departs']), [14, 18, 18]);
        }
        $classeur->setActiveSheetIndex(0);

        $nom = 'pilotage_'.$a['exercice']->annee.'_'.preg_replace('/[^a-z0-9]+/', '_', mb_strtolower(Str::ascii($a['periode']['libelle']))).'_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(fn () => (new Xlsx($classeur))->save('php://output'), $nom, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
