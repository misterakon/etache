<?php

namespace App\Support;

use App\Models\Priorite;
use App\Models\RoleAffectation;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TacheHistorique;
use Illuminate\Support\Str;

/**
 * Journal des modifications d'une tache (table tache_historiques).
 *
 * Utilisation (voir FeuilleRouteControlleur::storeTache) :
 *
 *   $avant = JournalTache::instantane($tache);          // AVANT la modification
 *   ... mise a jour de la tache et de ses affectations ...
 *   JournalTache::enregistrer($tache, $avant, auth()->id());   // une ligne par champ qui a vraiment change
 *
 * Valeurs enregistrees (colonnes ancienne_valeur / nouvelle_valeur) :
 *  - dates (date_fin, date_debut) : AAAA-MM-JJ, pour pouvoir calculer les reports ; l'ecran les affiche en JJ/MM/AAAA ;
 *  - priorite, statut : le libelle ; avancement : "40 %" (comme la mise a jour rapide) ;
 *  - responsable, pilote, contributeur : les noms, separes par une virgule.
 */
class JournalTache
{
    /** Champs suivis => libelle affiche dans l'onglet "Activite". */
    const CHAMPS = [
        'date_fin' => 'Échéance',
        'date_debut' => 'Date de début',
        'priorite_id' => 'Priorité',
        'statut_execution_id' => 'Statut',
        'avancement_pourcentage' => 'Avancement',
        'responsable' => 'Responsable',
        'pilote' => 'Pilote',
        'contributeur' => 'Contributeurs',
    ];

    /** Etat actuel des champs suivis, sous forme de texte (a prendre AVANT de modifier la tache). */
    public static function instantane(Tache $tache): array
    {
        $tache->loadMissing('affectations.personnel');

        $priorites = Priorite::pluck('libelle', 'id');
        $statuts = StatutExecution::pluck('libelle', 'id');

        // id du role d'affectation => cle du champ (responsable, pilote, contributeur)
        $cleParRoleId = RoleAffectation::pluck('code', 'id')->map(fn ($code) => match ($code) {
            RoleAffectation::RESPONSABLE => 'responsable',
            RoleAffectation::PILOTE => 'pilote',
            RoleAffectation::CONTRIBUTEUR => 'contributeur',
            default => null,
        });

        $noms = ['responsable' => [], 'pilote' => [], 'contributeur' => []];
        foreach ($tache->affectations as $affectation) {
            $cle = $cleParRoleId[$affectation->role_affectation_id] ?? null;
            if ($cle && $affectation->personnel) {
                $noms[$cle][] = $affectation->personnel->nomComplet();
            }
        }
        foreach ($noms as &$liste) {
            sort($liste); // l'ordre de saisie ne doit pas compter comme une modification
        }
        unset($liste);

        return [
            'date_fin' => $tache->date_fin?->toDateString(),
            'date_debut' => $tache->date_debut?->toDateString(),
            'priorite_id' => $tache->priorite_id ? ($priorites[$tache->priorite_id] ?? null) : null,
            'statut_execution_id' => $tache->statut_execution_id ? ($statuts[$tache->statut_execution_id] ?? null) : null,
            'avancement_pourcentage' => $tache->avancement_pourcentage !== null ? $tache->avancement_pourcentage.' %' : null,
            'responsable' => $noms['responsable'] ? implode(', ', $noms['responsable']) : null,
            'pilote' => $noms['pilote'] ? implode(', ', $noms['pilote']) : null,
            'contributeur' => $noms['contributeur'] ? implode(', ', $noms['contributeur']) : null,
        ];
    }

    /**
     * Compare l'etat d'avant avec l'etat actuel (relu en base) et ecrit UNE ligne par champ modifie.
     * Retourne le nombre de lignes ecrites.
     */
    public static function enregistrer(Tache $tache, array $avant, ?int $userId = null): int
    {
        $apres = self::instantane($tache->fresh());
        $nb = 0;

        foreach (array_keys(self::CHAMPS) as $champ) {
            $ancienne = $avant[$champ] ?? null;
            $nouvelle = $apres[$champ] ?? null;

            if ((string) $ancienne === (string) $nouvelle) {
                continue;
            }

            TacheHistorique::create([
                'tache_id' => $tache->id,
                'user_id' => $userId,
                'champ' => $champ,
                'ancienne_valeur' => self::tronquer($ancienne),
                'nouvelle_valeur' => self::tronquer($nouvelle),
            ]);
            $nb++;
        }

        return $nb;
    }

    /** Evite l'erreur "Data too long" si une liste de contributeurs depasse la taille de la colonne. */
    private static function tronquer(?string $valeur): ?string
    {
        return $valeur === null ? null : Str::limit($valeur, 250, '…');
    }
}
