<?php

namespace App\Support;

use App\Models\DossierInfo;
use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\User;
use App\Notifications\RhNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Alertes RH, sans doublon : validité des documents du dossier (l'agent et les administrateurs) et sur-affectation
 * (le supérieur direct, une fois par semaine). Déclenchées chaque matin et « à la visite » des écrans RH.
 */
class RhAlertes
{
    public static function executerSiNecessaire(): void
    {
        if (Cache::add('rh.alertes', 1, now()->addHours(3))) {
            try {
                self::executer();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /** @return array{documents: int, surcharges: int} */
    public static function executer(): array
    {
        return ['documents' => self::documents(), 'surcharges' => self::surcharges()];
    }

    private static function premiere(string $cle): bool
    {
        return DB::table('rh_alertes')->insertOrIgnore(['cle' => $cle, 'envoye_le' => now()]) > 0;
    }

    private static function notifier(iterable $utilisateurs, RhNotification $notification): void
    {
        foreach ($utilisateurs as $utilisateur) {
            try {
                $utilisateur->notify($notification);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    private static function documents(): int
    {
        $n = 0;
        $admins = User::where('actif', true)->whereHas('profilAcces', fn ($p) => $p->where('slug', ProfilAcces::ADMINISTRATEUR))->get();

        DossierInfo::with('piece')->whereNotNull('valide_jusqu_au')->get()->each(function (DossierInfo $info) use (&$n, $admins) {
            $etat = $info->etat();
            $piece = $info->piece;
            if (! in_array($etat, ['bientot', 'expire'], true) || ! $piece || $piece->trashed()) {
                return;
            }
            if (! self::premiere("dossier:{$info->id}:{$etat}:".$info->valide_jusqu_au->toDateString())) {
                return;
            }
            $agent = Personnel::find($piece->attachable_id);
            if (! $agent || $agent->estArchive()) {
                return;
            }
            $type = ucfirst(str_replace('_', ' ', preg_replace('/^dossier_/', '', (string) $piece->categorie)));
            $quand = $etat === 'expire' ? 'a expiré le '.$info->valide_jusqu_au->format('d/m/Y') : "expire le {$info->valide_jusqu_au->format('d/m/Y')}";
            $lien = route('rh.agent', $agent->id);

            self::notifier(User::where('actif', true)->where('personnel_id', $agent->id)->get(),
                new RhNotification('rh_dossier', $etat === 'expire' ? 'Document expiré' : 'Document bientôt expiré', "Votre document « {$type} » {$quand}.", $lien, $etat === 'expire' ? 'retard' : 'attente'));
            self::notifier($admins, new RhNotification('rh_dossier', $etat === 'expire' ? 'Document expiré' : 'Document bientôt expiré', "Dossier de {$agent->nomComplet()} : « {$type} » {$quand}.", $lien, $etat === 'expire' ? 'retard' : 'attente'));
            $n++;
        });

        return $n;
    }

    private static function surcharges(): int
    {
        $n = 0;
        $semaine = now()->format('o-\WW');

        foreach (ChargeTravail::tous()->where('surcharge', true) as $ligne) {
            $agent = $ligne['personnel'];
            if (! self::premiere("surcharge:{$agent->id}:{$semaine}")) {
                continue;
            }
            $superieur = HierarchieResponsables::superieurDirect($agent);
            if (! $superieur) {
                continue;
            }
            self::notifier(User::where('actif', true)->where('personnel_id', $superieur->id)->get(), new RhNotification(
                'rh_surcharge', 'Agent en sur-affectation',
                "{$agent->nomComplet()} cumule {$ligne['charge']} éléments ouverts (seuil {$ligne['seuil']}) : {$ligne['taches']} tâche(s), {$ligne['diligences']} diligence(s), {$ligne['actions']} action(s).",
                route('rh.agent', $agent->id), 'retard'
            ));
            $n++;
        }

        return $n;
    }
}
