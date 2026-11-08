<?php

namespace App\Console\Commands;

use App\Support\RhAlertes;
use Illuminate\Console\Command;

class RhAlertesCommand extends Command
{
    protected $signature = 'rh:alertes';

    protected $description = 'Alertes RH : validité des documents du dossier et sur-affectation des agents';

    public function handle(): int
    {
        $r = RhAlertes::executer();
        $this->info("Alertes émises : {$r['documents']} document(s), {$r['surcharges']} sur-affectation(s).");

        return self::SUCCESS;
    }
}
