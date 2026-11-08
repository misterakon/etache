<?php

namespace App\Console\Commands;

use App\Models\ProfilAcces;
use App\Models\User;
use App\Support\Journal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Remet l'application à neuf en vidant les données (jamais les référentiels ni la structure du code).
 *
 *   php artisan etache:reinitialiser --simuler                      (aperçu, rien n'est supprimé)
 *   php artisan etache:reinitialiser --niveau=travail               (tâches, courrier, journal, notifications…)
 *   php artisan etache:reinitialiser --niveau=personnel             (+ agents, postes, dossiers, comptes non administrateurs)
 *   php artisan etache:reinitialiser --niveau=tout                  (+ directions, services, exercices)
 *
 * Conservés dans tous les cas : profils d'accès, types d'agent, familles et emplois, rôles d'affectation, priorités, statuts,
 * situations, canaux, règles d'alerte, listes de valeurs, fonctions, grades, classes, échelons, types, urgences, instructions
 * et délais du courrier, et les comptes administrateurs. Les réglages ne sont vidés qu'avec --reglages.
 */
class ReinitialiserApplication extends Command
{
    protected $signature = 'etache:reinitialiser
        {--niveau=travail : travail | personnel | tout}
        {--garder-admin=* : e-mail(s) des administrateurs à conserver (par défaut : tous les administrateurs)}
        {--reglages : vide aussi les réglages (retour aux valeurs par défaut)}
        {--simuler : affiche ce qui serait supprimé, sans rien supprimer}
        {--force : sans confirmation interactive (obligatoire en production)}';

    protected $description = "Remet l'application à neuf : vide les données de travail (et, selon le niveau, le personnel et l'organisation)";

    /** Données de travail : feuille de route, courrier, traces. */
    private const TRAVAIL = [
        'tache_justifications', 'tache_historiques', 'tache_dependance', 'alertes_envoyees', 'affectations', 'sous_taches', 'taches', 'actions_mesurables', 'action_cle_direction', 'actions_cles', 'axes',
        'diligence_alertes', 'diligence_actions', 'diligence_delais', 'reponse_visas', 'reponses_courrier', 'diligence_avis', 'imputation_destinataires',
        'diligences', 'imputation_instruction', 'imputations', 'departs', 'courriers', 'courrier_sequences', 'syntheses_pilotage', 'instantanes_pilotage', 'rh_alertes', 'notifications', 'journal_activites',
    ];

    private const PERSONNEL = ['dossier_infos', 'personnel_competences', 'personnel_formations', 'mandats_responsables', 'personnel'];

    private const ORGANISATION = ['postes_imputation', 'services', 'sous_directions', 'directions', 'exercices'];

    public function handle(): int
    {
        $niveau = $this->option('niveau');
        if (! in_array($niveau, ['travail', 'personnel', 'tout'], true)) {
            $this->error('Niveau inconnu : choisissez travail, personnel ou tout.');

            return self::FAILURE;
        }
        if (app()->environment('production') && ! $this->option('force') && ! $this->option('simuler')) {
            $this->error("Environnement de production : relancez avec --force si vous êtes certain (et après une sauvegarde de la base).");

            return self::FAILURE;
        }

        $tables = self::TRAVAIL;
        if (in_array($niveau, ['personnel', 'tout'], true)) {
            $tables = array_merge($tables, self::PERSONNEL);
        }
        if ($niveau === 'tout') {
            $tables = array_merge($tables, self::ORGANISATION);
        }
        if ($this->option('reglages')) {
            $tables[] = 'reglages';
        }
        $tables = array_values(array_filter($tables, fn ($t) => Schema::hasTable($t)));

        // Administrateurs conservés (niveaux personnel et tout)
        $gardes = collect();
        $supprimerComptes = $niveau !== 'travail';
        if ($supprimerComptes) {
            $emails = array_map('mb_strtolower', (array) $this->option('garder-admin'));
            $gardes = User::whereHas('profilAcces', fn ($p) => $p->where('slug', ProfilAcces::ADMINISTRATEUR))
                ->when($emails !== [], fn ($q) => $q->whereIn(DB::raw('LOWER(email)'), $emails))->get();
            if ($gardes->isEmpty()) {
                $this->error("Aucun compte administrateur à conserver : l'opération est annulée (vous ne pourriez plus vous connecter).");

                return self::FAILURE;
            }
        }

        $pieces = Schema::hasTable('pieces_jointes')
            ? DB::table('pieces_jointes')->when($niveau === 'travail', fn ($q) => $q->whereNotIn('attachable_type', ['personnel', 'App\\Models\\Personnel']))->get(['id', 'chemin'])
            : collect();

        $this->newLine();
        $this->info("Niveau « {$niveau} » : voici ce qui sera vidé.");
        $this->table(['Table', 'Lignes'], collect($tables)->map(fn ($t) => [$t, DB::table($t)->count()])->all());
        $this->line('Fichiers joints à supprimer : '.$pieces->count());
        if ($supprimerComptes) {
            $this->line('Comptes supprimés : '.User::whereNotIn('id', $gardes->pluck('id'))->count().' ; conservés : '.$gardes->pluck('email')->implode(', '));
        }
        $this->line('Conservés : référentiels, paramètres de courrier, profils d\'accès'.($this->option('reglages') ? '' : ', réglages').($niveau !== 'tout' ? ', organisation' : '').'.');

        if ($this->option('simuler')) {
            $this->warn('Simulation : rien n\'a été supprimé.');

            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            if (! $this->confirm('Avez-vous fait une sauvegarde de la base de données et des fichiers ?', false)) {
                $this->warn('Opération annulée : faites d\'abord une sauvegarde.');

                return self::FAILURE;
            }
            if ($this->ask('Cette opération est IRRÉVERSIBLE. Tapez REINITIALISER pour confirmer') !== 'REINITIALISER') {
                $this->warn('Confirmation incorrecte : opération annulée.');

                return self::FAILURE;
            }
        }

        // --- Fichiers
        $disque = Storage::disk(config('pieces_jointes.disque'));
        foreach ($pieces as $p) {
            if ($p->chemin) {
                $disque->delete($p->chemin);
            }
        }
        $pieces->pluck('id')->chunk(500)->each(fn ($lot) => DB::table('pieces_jointes')->whereIn('id', $lot)->delete());
        foreach (['courriers', 'imports_matrice', 'imports_personnel'] as $dossier) {
            Storage::disk('local')->deleteDirectory($dossier);
            $disque->deleteDirectory($dossier);
        }
        if ($niveau === 'tout') {
            $disque->deleteDirectory('archives_exercices');
        }
        if ($supprimerComptes && is_dir(public_path('uploads/personnel'))) {
            File::cleanDirectory(public_path('uploads/personnel'));
        }

        // --- Tables
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        // --- Comptes
        if ($supprimerComptes) {
            $supprimes = User::whereNotIn('id', $gardes->pluck('id'))->pluck('id');
            User::whereIn('id', $supprimes)->delete();
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->whereIn('user_id', $supprimes)->delete();
            }
            User::whereIn('id', $gardes->pluck('id'))->update(['personnel_id' => null, 'mot_de_passe_provisoire' => false]);
        }

        Cache::flush();
        Journal::noter('reglages', 'modification', "Réinitialisation de l'application (niveau {$niveau})", null, null, ['niveau' => $niveau, 'tables' => count($tables)]);

        $this->newLine();
        $this->info('Application remise à neuf. La numérotation (A26-0001, D26-0001, DL26-0001, TA26-001…) repart de 1.');
        if ($niveau !== 'tout') {
            $this->line('Pensez à vérifier que l\'exercice actif existe toujours (Paramètres > Exercice).');
        }

        return self::SUCCESS;
    }
}
