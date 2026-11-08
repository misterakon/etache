<?php

namespace App\Console\Commands;

use App\Models\TachePonctuelle;
use App\Notifications\PonctuelleNotification;
use App\Support\NotifPonctuelle;
use App\Support\Reglages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Alerte de retard des tâches ponctuelles : une seule notification par tâche, à l'agent et à son supérieur direct.
 * Activée par Réglages › Tâches ponctuelles › « Alerter l'agent et son supérieur en cas de retard ».
 */
class PonctuellesAlertesCommand extends Command
{
    protected $signature = 'ponctuelles:alertes {--simuler : Affiche ce qui serait envoyé sans rien envoyer}';

    protected $description = 'Prévient l’agent et son supérieur des tâches ponctuelles dont l’échéance est dépassée';

    public function handle(): int
    {
        if (! Reglages::get('taches_ponctuelles.alerte_retard')) {
            $this->info('Alertes de retard des tâches ponctuelles désactivées dans les Réglages.');

            return self::SUCCESS;
        }

        $envoyees = 0;
        foreach (TachePonctuelle::enRetard()->with('responsable')->get() as $t) {
            $deja = DB::table('notifications')->where('type', PonctuelleNotification::class)
                ->where('data', 'like', '%"type":"'.PonctuelleNotification::RETARD.'","tache_id":'.$t->id.',%')->exists();
            if ($deja) {
                continue;
            }
            $this->line($t->code.' — '.$t->libelle.' (échéance '.$t->date_fin->format('d/m/Y').')');
            if (! $this->option('simuler')) {
                $destinataires = array_filter([$t->responsable_personnel_id, NotifPonctuelle::superieurDirect($t)]);
                NotifPonctuelle::versPersonnels($destinataires, $t, PonctuelleNotification::RETARD);
            }
            $envoyees++;
        }
        $this->info(($this->option('simuler') ? '[SIMULATION] ' : '').$envoyees.' alerte(s) '.($this->option('simuler') ? 'à envoyer' : 'envoyée(s)').'.');

        return self::SUCCESS;
    }
}
