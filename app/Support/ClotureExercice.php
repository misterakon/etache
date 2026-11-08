<?php

namespace App\Support;

use App\Models\Exercice;
use App\Models\SousTache;
use App\Models\Tache;
use App\Models\TacheJustification;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Clôture et réouverture d'un exercice.
 *  - contrôles préalables : bloquants, simples avertissements ou désactivés (Réglages > Exercice) ;
 *  - archive Excel générée AVANT la clôture : sans archive, pas de clôture ;
 *  - une fois clos, tout est verrouillé côté serveur (ProtegeParCloture) et les alertes cessent.
 */
class ClotureExercice
{
    /** @return list<array{cle: string, libelle: string, nb: int, bloquant: bool, exemples: array, precision: ?string}> */
    public static function controles(Exercice $e): array
    {
        $taches = fn () => Tache::dansExercice($e->id);
        $codes = fn ($q) => $q->orderBy('code')->limit(8)->pluck('code')->all();

        // Tâches non terminées : celles dont l'échéance dépasse la fin de l'exercice (tâches pluriannuelles) ne sont pas des retards
        $precisionNonTerminees = null;
        if ($e->date_fin) {
            $apres = $taches()->nonTerminees()->whereDate('date_fin', '>', $e->date_fin)->count();
            if ($apres > 0) {
                $precisionNonTerminees = "dont {$apres} avec une échéance après le ".$e->date_fin->format('d/m/Y')." (fin de l'exercice) : ce ne sont pas des retards.";
            }
        }

        $definitions = [
            ['justifications_en_attente', 'Demandes de justification ou de report en attente de décision',
                TacheJustification::where('statut', TacheJustification::EN_ATTENTE)->whereHas('tache', fn ($q) => $q->dansExercice($e->id)),
                fn ($q) => $q->with('tache:id,code')->limit(8)->get()->map(fn ($j) => $j->tache?->code)->filter()->all(), null],
            ['taches_non_terminees', 'Tâches non terminées et non annulées', $taches()->nonTerminees(), $codes, $precisionNonTerminees],
            ['retards_non_justifies', 'Tâches en retard non justifiées', $taches()->retardNonJustifie(), $codes, null],
            ['taches_sans_responsable', 'Tâches en cours sans responsable', $taches()->nonTerminees()->whereDoesntHave('responsables'), $codes, null],
            ['taches_a_valider', 'Tâches en attente de validation de clôture', $taches()->where('cloture_validation', 'a_valider'), $codes, null],
            ['sous_taches_non_cochees', 'Sous-tâches non cochées',
                SousTache::where('cochee', false)->whereHas('tache', fn ($q) => $q->dansExercice($e->id)),
                fn ($q) => $q->limit(8)->pluck('libelle')->all(), null],
        ];

        $controles = [];
        foreach ($definitions as [$cle, $libelle, $requete, $exemples, $precision]) {
            $niveau = Reglages::get('exercice.controle_'.$cle);
            if ($niveau === 'ignore') {
                continue;
            }
            $controles[] = [
                'cle' => $cle,
                'libelle' => $libelle,
                'nb' => (clone $requete)->count(),
                'bloquant' => $niveau === 'bloquant',
                'exemples' => $exemples(clone $requete),
                'precision' => $precision,
            ];
        }

        return $controles;
    }

    /** Contrôles qui empêchent la clôture (bloquants avec au moins une anomalie). */
    public static function bloquants(array $controles): array
    {
        return array_values(array_filter($controles, fn ($c) => $c['bloquant'] && $c['nb'] > 0));
    }

    /** @throws DomainException si la clôture est impossible (message affichable) */
    public static function cloturer(Exercice $e, User $user): void
    {
        if ($e->estClos()) {
            throw new DomainException("L'exercice {$e->annee} est déjà clos.");
        }

        $bloquants = self::bloquants(self::controles($e));
        if ($bloquants) {
            throw new DomainException('Clôture impossible : '.implode(' ; ', array_map(fn ($c) => $c['libelle'].' ('.$c['nb'].')', $bloquants)).'.');
        }

        $archive = ArchiveExercice::generer($e); // une panne d'archive interrompt la clôture

        DB::transaction(function () use ($e, $user, $archive) {
            $e->update(['cloture_le' => now(), 'cloture_par' => $user->id, 'cloture_archive' => $archive]);
        });
        ExerciceVerrou::oublier();

        Journal::noter('exercices', 'cloture', "Exercice {$e->annee} clôturé", $e, null, ['archive' => $archive]);
    }

    public static function rouvrir(Exercice $e, User $user, string $motif): void
    {
        if (! $e->estClos()) {
            throw new DomainException("L'exercice {$e->annee} n'est pas clos.");
        }

        $ancienneCloture = $e->cloture_le;
        $e->update(['cloture_le' => null, 'cloture_par' => null]); // l'archive reste conservée
        ExerciceVerrou::oublier();

        Journal::noter('exercices', 'reouverture', "Exercice {$e->annee} rouvert : {$motif}", $e, null, [
            'motif' => $motif, 'cloture_du' => $ancienneCloture?->toDateTimeString(),
        ]);
    }
}
