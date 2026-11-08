<?php

namespace App\Console\Commands;

use App\Support\ClotureTache;
use Illuminate\Console\Command;

class ClotureTacheRelanceCommand extends Command
{
    protected $signature = 'taches:relance-validation';

    protected $description = 'Relance les valideurs des clôtures de tâches en attente (puis le DG après le double du délai)';

    public function handle(): int
    {
        $n = ClotureTache::relancer();
        $this->info($n > 0 ? "{$n} tâche(s) relancée(s)." : 'Aucune relance à envoyer.');

        return self::SUCCESS;
    }
}
