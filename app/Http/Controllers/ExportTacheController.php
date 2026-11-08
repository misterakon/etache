<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Support\ExportExcel;
use App\Support\ExportPdf;
use App\Support\ExportTaches;
use App\Support\FiltresTaches;
use App\Support\Journal;
use Illuminate\Http\Request;

class ExportTacheController extends Controller
{
    public function telecharger(Request $request, string $type, string $format)
    {
        abort_unless(isset(ExportTaches::TYPES[$type]) && in_array($format, ['xlsx', 'pdf'], true), 404);

        $delais = ExportTaches::joursAVenir();
        $jours = in_array($request->integer('jours'), $delais, true) ? $request->integer('jours') : ($delais[0] ?? 7);

        $taches = ExportTaches::requete($type, $request, $jours)->get();
        $colonnes = ExportTaches::colonnes($format);
        $lignes = ExportTaches::lignes($taches, array_keys($colonnes));

        $titre = ExportTaches::TYPES[$type].($type === 'a_venir' ? " ({$jours} prochains jours)" : '');
        $exercice = ($id = FiltresTaches::exerciceId($request)) ? 'Exercice '.Exercice::find($id)?->annee : 'Tous les exercices';
        $infos = [
            $exercice.' · Filtres : '.FiltresTaches::resume(FiltresTaches::depuis($request)),
            'Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.count($lignes).' tâche(s)',
        ];
        $fichier = 'taches_'.$type.'_'.now()->format('Ymd_His').'.'.$format;

        Journal::noter('exports', 'export', 'Export « '.$titre.' » ('.strtoupper($format).') : '.count($lignes).' tâche(s)', null, null, [
            'type' => $type, 'format' => $format, 'exercice' => $exercice,
            'filtres' => FiltresTaches::resume(FiltresTaches::depuis($request)), 'lignes' => count($lignes),
        ]);

        $synthese = ExportTaches::synthese($taches);

        if ($format === 'xlsx') {
            return ExportExcel::telecharger($fichier, $titre, $infos, $colonnes, $lignes, $synthese);
        }

        return ExportPdf::tableau($fichier, $titre, $infos, $colonnes, $lignes, $synthese);
    }
}
