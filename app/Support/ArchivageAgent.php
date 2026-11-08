<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Archivage d'un agent : jamais de suppression. L'agent quitte les listes courantes, ses postes prennent fin,
 * son compte est désactivé (et ses sessions fermées) ; tout son historique reste consultable.
 */
class ArchivageAgent
{
    public const MOTIFS = ['Retraite', 'Démission', 'Fin de contrat', 'Mutation hors de la structure', 'Décès', 'Doublon de fiche', 'Autre'];

    /** @return array{mandats: int, comptes: int, taches: int} */
    public static function archiver(Personnel $agent, string $motif, User $par): array
    {
        if ($agent->estArchive()) {
            throw new DomainException('Cet agent est déjà archivé.');
        }
        if ((int) $par->personnel_id === (int) $agent->id) {
            throw new DomainException('Vous ne pouvez pas archiver votre propre fiche.');
        }

        $mandats = MandatResponsable::where('personnel_id', $agent->id)->actifA()->get();
        $comptes = User::where('personnel_id', $agent->id)->get();

        DB::transaction(function () use ($agent, $motif, $par, $mandats, $comptes) {
            $agent->update(['actif' => false, 'archive_le' => now(), 'archive_motif' => $motif, 'archive_par' => $par->id]);
            foreach ($mandats as $mandat) {
                $fin = today()->subDay();
                $mandat->update(['date_fin' => $fin->lt($mandat->date_debut) ? $mandat->date_debut->copy() : $fin, 'motif_fin' => "Archivage de l'agent : {$motif}"]);
            }
            foreach ($comptes as $compte) {
                $compte->update(['actif' => false]);
            }
            if ($comptes->isNotEmpty() && Schema::hasTable('sessions')) {
                DB::table('sessions')->whereIn('user_id', $comptes->pluck('id'))->delete();
            }
        });

        try {
            AffectationPoste::synchroniserFonction($agent, today()->addDay());
        } catch (\Throwable $e) {
            report($e);
        }

        $taches = Tache::whereHas('affectations', fn ($a) => $a->where('personnel_id', $agent->id))
            ->whereHas('statutExecution', fn ($s) => $s->whereNotIn('code', [StatutExecution::TERMINEE, StatutExecution::ANNULEE]))->count();

        Journal::noter('personnel', 'archivage', "Agent {$agent->nomComplet()} archivé : {$motif}", $agent, $agent, [
            'motif' => $motif, 'postes_termines' => $mandats->count(), 'comptes_desactives' => $comptes->count(), 'taches_ouvertes' => $taches,
        ]);

        return ['mandats' => $mandats->count(), 'comptes' => $comptes->count(), 'taches' => $taches];
    }

    /** Restauration : la fiche redevient active. Les comptes restent désactivés (réactivation volontaire). */
    public static function restaurer(Personnel $agent, User $par): void
    {
        if (! $agent->estArchive()) {
            throw new DomainException("Cet agent n'est pas archivé.");
        }
        $agent->update(['actif' => true, 'archive_le' => null, 'archive_motif' => null, 'archive_par' => null]);
        Journal::noter('personnel', 'restauration', "Agent {$agent->nomComplet()} restauré depuis les archives", $agent, $agent);
    }
}
