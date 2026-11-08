<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Support\ExportExcel;
use App\Support\ImportPersonnel;
use App\Support\Journal;
use App\Support\PersonnelExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Import et export Excel du personnel (modèle, prévisualisation sans écriture, import « tout ou rien »). Import : ADMIN. */
class RhImportController extends Controller
{
    private const DOSSIER = 'imports_personnel';

    private function chemin(string $token, Request $request): ?string
    {
        if (! preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
            return null;
        }
        $relatif = self::DOSSIER.'/'.$request->user()->id.'_'.$token.'.xlsx';

        return Storage::disk('local')->exists($relatif) ? Storage::disk('local')->path($relatif) : null;
    }

    private function nettoyer(): void
    {
        $disque = Storage::disk('local');
        foreach ($disque->files(self::DOSSIER) as $fichier) {
            if ($disque->lastModified($fichier) < now()->subDay()->getTimestamp()) {
                $disque->delete($fichier);
            }
        }
    }

    public function index(Request $request)
    {
        return view('rh.import', ['peutImporter' => $request->user()->peut('rh_import.importer'), 'avecMotDePasse' => (bool) session('rh_comptes_crees'), 'resultat' => session('rh_import')]);
    }

    public function modele()
    {
        return PersonnelExcel::modele();
    }

    /** Export des agents dans le format du modèle : à corriger dans Excel, puis à réimporter. */
    public function export(Request $request)
    {
        $agents = Personnel::nonArchives()->with(['service.sousDirection.direction', 'service.directionDirecte', 'civilite', 'genre', 'statutMatrimonial', 'typeAgent', 'emploi', 'grade', 'classeAgent', 'echelon', 'user:id,personnel_id',
            'mandats' => fn ($q) => $q->actifA()->with(['direction', 'sousDirection.direction', 'service.sousDirection.direction'])])->orderBy('nom')->orderBy('prenom')->get();
        Journal::noter('exports', 'export', 'Export Excel du personnel : '.$agents->count().' agent(s)');

        return ExportExcel::telecharger(
            'personnel_'.now()->format('Ymd_His').'.xlsx', 'Personnel', ['Édité le '.now()->format('d/m/Y à H:i').' par '.$request->user()->name.' · '.$agents->count().' agent(s) · même format que le modèle d\'import'],
            PersonnelExcel::entetes(), PersonnelExcel::lignes($agents)
        );
    }

    public function apercu(Request $request)
    {
        Gate::authorize('rh_import.importer');
        $request->validate(['fichier' => ['required', 'file', 'max:5120', 'extensions:xlsx']], [
            'fichier.required' => 'Choisissez le fichier Excel à importer.', 'fichier.extensions' => 'Seuls les fichiers .xlsx sont acceptés (utilisez le modèle).',
            'fichier.max' => 'Le fichier dépasse 5 Mo.', 'fichier.uploaded' => "Le fichier n'a pas pu être envoyé (taille maximale du serveur dépassée ?).",
        ]);

        $this->nettoyer();
        $token = Str::random(40);
        $request->file('fichier')->storeAs(self::DOSSIER, $request->user()->id.'_'.$token.'.xlsx', 'local');
        $analyse = ImportPersonnel::analyser($this->chemin($token, $request));

        return view('rh.import_apercu', ['analyse' => $analyse, 'token' => $token, 'nomFichier' => $request->file('fichier')->getClientOriginalName()]);
    }

    public function appliquer(Request $request)
    {
        Gate::authorize('rh_import.importer');
        $chemin = $this->chemin((string) $request->input('token'), $request);
        if (! $chemin) {
            return redirect()->route('rh.import')->withErrors(['fichier' => "Le fichier n'est plus disponible : renvoyez-le."]);
        }

        // Le fichier est ré-analysé : la prévisualisation n'est jamais considérée comme fiable
        $analyse = ImportPersonnel::analyser($chemin);
        if ($analyse['erreurs_fichier'] || ($analyse['resume']['erreurs'] ?? 1) > 0) {
            return redirect()->route('rh.import')->withErrors(['fichier' => "Le fichier contient des erreurs : corrigez-le et renvoyez-le. Rien n'a été importé."]);
        }

        $r = ImportPersonnel::appliquer($analyse, $request->user());
        Storage::disk('local')->delete(self::DOSSIER.'/'.$request->user()->id.'_'.$request->input('token').'.xlsx');
        if ($r['comptes']) {
            session(['rh_comptes_crees' => $r['comptes']]);
        }

        return redirect()->route('rh.import')->with('rh_import', ['crees' => $r['crees'], 'maj' => $r['maj'], 'comptes' => count($r['comptes'])]);
    }
}
