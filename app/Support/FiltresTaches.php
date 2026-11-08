<?php

namespace App\Support;

use App\Models\Axe;
use App\Models\Direction;
use App\Models\Exercice;
use Illuminate\Http\Request;

/**
 * Filtres de la page Tâches, lus depuis la requête.
 * Partagés par l'écran (liste, matrice, Gantt...) et par les exports : mêmes filtres, mêmes résultats.
 */
class FiltresTaches
{
    private const CLES = [
        'q', 'axe_id', 'action_cle_id', 'action_mesurable_id', 'statut_execution_id', 'priorite_id',
        'date_fin_du', 'date_fin_au', 'direction_id', 'direction_role', 'responsable_id', 'pilote_id', 'contributeur_id',
    ];

    public static function depuis(Request $request): array
    {
        $filtres = array_filter($request->only(self::CLES), fn ($v) => filled($v));

        if ($request->boolean('en_retard')) {
            $filtres['en_retard'] = true;
        }
        if ($request->boolean('non_justifie')) {
            $filtres['non_justifie'] = true;
        }
        // Porteuse / impliquée : sans direction choisie (ou "toutes"), le critère est inutile
        if (empty($filtres['direction_id']) || ($filtres['direction_role'] ?? '') === 'toutes') {
            unset($filtres['direction_role']);
        }

        $personnelConnecte = $request->user()?->personnel_id;
        if ($request->boolean('mes_taches') && $personnelConnecte) {
            $filtres['personnel_id'] = $personnelConnecte;
        }

        return $filtres;
    }

    /** Exercice choisi (vide = tous), sinon exercice par défaut. */
    public static function exerciceId(Request $request): ?int
    {
        return $request->has('exercice_id')
            ? ($request->integer('exercice_id') ?: null)
            : Exercice::parDefaut()?->id;
    }

    /** Texte lisible des filtres actifs (en-tête des exports). */
    public static function resume(array $f): string
    {
        $parties = [];
        $restants = $f;

        $ajouter = function (string $cle, callable $texte) use (&$parties, &$restants, $f) {
            if (! empty($f[$cle])) {
                $parties[] = $texte($f[$cle]);
                unset($restants[$cle]);
            }
        };

        $ajouter('q', fn ($v) => 'Recherche « '.$v.' »');
        $ajouter('axe_id', fn ($v) => 'Axe : '.(Axe::find($v)?->libelle ?? $v));
        $ajouter('direction_id', fn ($v) => 'Direction : '.(Direction::find($v)?->nom ?? $v));
        $ajouter('en_retard', fn () => 'En retard');
        $ajouter('non_justifie', fn () => 'En retard non justifié');
        unset($restants['direction_role']);

        if ($restants) {
            $parties[] = '+ '.count($restants).' autre(s) filtre(s)';
        }

        return $parties ? implode(' · ', $parties) : 'Aucun';
    }
}
