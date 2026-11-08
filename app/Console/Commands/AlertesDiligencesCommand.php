<?php

namespace App\Console\Commands;

use App\Support\AlertesDiligences;
use Illuminate\Console\Command;

class AlertesDiligencesCommand extends Command
{
    protected $signature = 'courrier:alertes-diligences';

    protected $description = "Alertes d'échéance proche et de retard des diligences du courrier";

    public function handle(): int
    {
        $stats = AlertesDiligences::executer();
        $this->info("Alertes émises : {$stats['echeance']} échéance(s) proche(s), {$stats['retard']} retard(s).");

        return self::SUCCESS;
    }
}
