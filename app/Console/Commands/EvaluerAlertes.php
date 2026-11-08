<?php

namespace App\Console\Commands;

use App\Support\MoteurAlertes;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class EvaluerAlertes extends Command
{
    protected $signature = 'alertes:evaluer
        {--simuler : Affiche ce qui serait envoyé, sans rien envoyer ni enregistrer}
        {--date= : Date d\'évaluation AAAA-MM-JJ (par défaut : aujourd\'hui)}';

    protected $description = "Met à jour les situations temporelles et envoie les alertes d'échéance du jour";

    public function handle(): int
    {
        try {
            $date = $this->option('date')
                ? Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay()
                : today();
        } catch (Throwable $e) {
            $this->error('Date invalide : utilisez le format AAAA-MM-JJ.');

            return self::FAILURE;
        }

        $simuler = (bool) $this->option('simuler');
        $detail = $simuler || $this->getOutput()->isVerbose();

        $this->info(($simuler ? '[SIMULATION] ' : '').'Évaluation des alertes du '.$date->format('d/m/Y'));

        $stats = (new MoteurAlertes($date, $simuler, function (string $texte, string $niveau) use ($detail) {
            if ($niveau === 'warn') {
                $this->warn($texte);
            } elseif ($detail) {
                $this->line($texte);
            }
        }))->executer();

        $this->table(['Indicateur', 'Valeur'], [
            ['Règles actives avec un canal exploitable', $stats['regles']],
            ['Tâches déclenchées', $stats['taches_declenchees']],
            [$simuler ? 'Alertes à envoyer' : 'Alertes envoyées', $stats['envoyees']],
            ['Déjà envoyées (anti-doublon)', $stats['deja_envoyees']],
            ['Ignorées (pas de contact)', $stats['ignorees']],
            ['Relances en pause (justification)', $stats['en_pause']],
            ['Échecs', $stats['echecs']],
            [$simuler ? 'Situations à modifier' : 'Situations modifiées', $stats['situations_modifiees']],
        ]);

        if ($stats['regles_sans_canal']) {
            $this->warn('Règles sans canal actif (ignorées) : '.implode(', ', $stats['regles_sans_canal']));
        }

        return $stats['echecs'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
