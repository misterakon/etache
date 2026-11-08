<?php

namespace App\Support;

use App\Models\Personnel;
use App\Models\Service;

/**
 * Affichage seulement : un agent sans service (directeur, chef de service nommé par mandat…) reçoit, pour la fiche,
 * un rattachement déduit de son poste. Rien n'est enregistré en base.
 */
class RattachementAgent
{
    public static function appliquer(Personnel $agent): array
    {
        $r = $agent->rattachement();
        if ($agent->service_id) {
            return $r;
        }

        if ($r['service']) {
            $agent->setRelation('service', $r['service']->loadMissing('sousDirection.direction', 'directionDirecte'));
        } elseif ($r['sous_direction'] || $r['direction']) {
            $virtuel = new Service;
            $virtuel->setAttribute('nom', '—');
            $virtuel->setRelation('sousDirection', $r['sous_direction']?->loadMissing('direction'));
            $virtuel->setRelation('directionDirecte', $r['direction']);
            $agent->setRelation('service', $virtuel);
        }

        return $r;
    }
}
