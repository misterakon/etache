<?php

namespace App\Support;

use App\Models\TachePonctuelle;
use App\Models\User;

/** Chiffres des tâches ponctuelles pour le tableau de bord et le pilotage (jamais mêlés à l'avancement de la feuille de route). */
class PonctuellesResume
{
    /**
     * @param  bool  $moi  true : seulement les tâches de l'utilisateur ; false : tout ce qu'il a le droit de voir
     * @param  int|null  $directionId  limite à une direction (pilotage)
     * @return array{ouvertes: int, en_retard: int, terminees_30j: int, a_valider: int, liste: list<array<string, mixed>>}
     */
    public static function pour(User $user, bool $moi = false, ?int $directionId = null): array
    {
        $q = TachePonctuelle::query()->visiblePar($user)->with('responsable');
        if ($moi) {
            $q->where('responsable_personnel_id', $user->personnel_id ?: 0);
        }
        if ($directionId) {
            $q->where('direction_id', $directionId);
        }

        $aValider = TachePonctuelle::query()->visiblePar($user)->where('validation', TachePonctuelle::VALIDATION_ATTENTE)
            ->when(! $user->hasRole('ADMIN|DG'), fn ($w) => $w->whereJsonContains('superieurs', (int) $user->personnel_id))
            ->when($directionId, fn ($w) => $w->where('direction_id', $directionId))->count();

        $liste = (clone $q)->ouvertes()->orderByRaw('date_fin IS NULL')->orderBy('date_fin')->limit(4)->get()->map(function (TachePonctuelle $t) use ($user) {
            return [
                'id' => $t->id, 'code' => $t->code, 'libelle' => $t->libelle, 'avancement' => (int) $t->avancement_pourcentage,
                'echeance' => $t->date_fin?->format('d/m/Y'), 'retard' => $t->estEnRetard(),
                'jours' => $t->date_fin ? (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false) : null,
                'agent' => (int) $t->responsable_personnel_id === (int) $user->personnel_id ? null : $t->responsable?->nomComplet(),
                'url' => route('ponctuelle.index', ['ouvrir' => $t->id]),
            ];
        })->all();

        return [
            'ouvertes' => (clone $q)->ouvertes()->count(), 'en_retard' => (clone $q)->enRetard()->count(),
            'terminees_30j' => (clone $q)->where('statut', TachePonctuelle::TERMINEE)->where('terminee_le', '>=', now()->subDays(30))->count(),
            'a_valider' => $aValider, 'liste' => $liste,
        ];
    }
}
