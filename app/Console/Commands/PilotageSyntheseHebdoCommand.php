<?php

namespace App\Console\Commands;

use App\Models\Exercice;
use App\Models\User;
use App\Notifications\RhNotification;
use App\Support\AnalysesPilotage;
use App\Support\RedacteurSynthese;
use App\Support\Reglages;
use Illuminate\Console\Command;

class PilotageSyntheseHebdoCommand extends Command
{
    protected $signature = 'pilotage:synthese-hebdo {--force : générer même si le réglage est désactivé}';

    protected $description = 'Génère la synthèse du mois en cours pour toute la structure (réglage « Générer une synthèse chaque lundi matin »)';

    public function handle(): int
    {
        if (! $this->option('force') && ! Reglages::get('synthese.auto_hebdo')) {
            $this->info('Génération hebdomadaire désactivée dans les Réglages.');

            return self::SUCCESS;
        }
        $exercice = Exercice::parDefaut();
        if (! $exercice) {
            $this->warn('Aucun exercice.');

            return self::SUCCESS;
        }

        $periode = AnalysesPilotage::periode('mois', (int) now()->month, $exercice);
        $r = RedacteurSynthese::generer(AnalysesPilotage::calculer($exercice, $periode), null, true);
        $lien = route('pilotage.index', ['exercice_id' => $exercice->id, 'type' => 'mois', 'valeur' => $periode['valeur'], 'portee' => 'global', 'synthese_id' => $r['synthese']->id]);
        User::where('actif', true)->whereHas('profilAcces', fn ($p) => $p->whereIn('slug', ['DG', 'ADMIN']))->get()
            ->each(fn ($u) => $u->notify(new RhNotification('pilotage_synthese', 'Synthèse hebdomadaire disponible', "La synthèse de la feuille de route ({$periode['libelle']}) est prête.", $lien, 'avenir')));
        $this->info('Synthèse générée : '.$r['synthese']->libelleSource().($r['avertissement'] ? ' ('.$r['avertissement'].')' : '').'.');

        return self::SUCCESS;
    }
}
