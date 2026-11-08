<?php

namespace App\Console\Commands;

use App\Support\InstantanesPilotage;
use Illuminate\Console\Command;

class PilotageInstantaneCommand extends Command
{
    protected $signature = 'pilotage:instantane';

    protected $description = 'Photo quotidienne des chiffres de la feuille de route (courbes d\'évolution du pilotage)';

    public function handle(): int
    {
        $n = InstantanesPilotage::capturer();
        $this->info($n > 0 ? "Instantané enregistré : {$n} ligne(s)." : "L'instantané du jour existe déjà.");

        return self::SUCCESS;
    }
}
