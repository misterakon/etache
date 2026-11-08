<?php

namespace App\Support;

use App\Models\MandatResponsable;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\User;
use App\Notifications\ClotureTacheNotification;
use DomainException;

/**
 * Validation de la clôture d'une tâche par le supérieur hiérarchique (Réglages > Clôture des tâches).
 *  - désactivé : rien ne change ;
 *  - activé : « Terminée » demandée par le responsable (ou dernière sous-tâche faite) = clôture SOUMISE ; la tâche reste « En cours »
 *    (99 % au plus) et n'est pas comptée comme terminée tant que le supérieur n'a pas validé ; refus = motif obligatoire, retour « En cours » ;
 *  - ADMIN, DG et le supérieur du responsable clôturent directement ;
 *  - la date de soumission sert au calcul du retard : le délai du valideur n'est pas imputé au responsable.
 */
class ClotureTache
{
    public static function active(): bool
    {
        return (bool) Reglages::get('cloture_taches.validation_active');
    }

    /** ADMIN, DG, ou supérieur hiérarchique du responsable de la tâche. */
    public static function peutValider(Tache $tache, ?User $user): bool
    {
        return $user !== null && SousTacheDroits::peutValider($tache, $user);
    }

    public static function champsSoumission(?int $userId): array
    {
        return [
            'cloture_validation' => 'a_valider', 'cloture_soumise_le' => now(), 'cloture_soumise_par' => $userId,
            'cloture_validee_le' => null, 'cloture_validee_par' => null, 'cloture_motif_refus' => null, 'cloture_relance_le' => null,
        ];
    }

    public static function champsValidation(?int $userId, $soumiseLe = null): array
    {
        return [
            'cloture_validation' => 'validee', 'cloture_soumise_le' => $soumiseLe ?? now(), 'cloture_validee_le' => now(), 'cloture_validee_par' => $userId,
            'cloture_motif_refus' => null, 'cloture_relance_le' => null,
        ];
    }

    public static function champsRetrait(): array
    {
        return [
            'cloture_validation' => null, 'cloture_soumise_le' => null, 'cloture_soumise_par' => null, 'cloture_validee_le' => null,
            'cloture_validee_par' => null, 'cloture_motif_refus' => null, 'cloture_relance_le' => null,
        ];
    }

    /** Changement de statut fait par quelqu'un qui valide lui-même : marque la clôture validée, ou retire une clôture devenue sans objet. */
    public static function pourStatutDirect(Tache $tache, int $statutId, ?User $user): array
    {
        if (! self::active()) {
            return [];
        }
        $terminee = (int) $statutId === (int) StatutExecution::where('code', StatutExecution::TERMINEE)->value('id');
        if ($terminee && $tache->cloture_validation !== 'validee') {
            return self::champsValidation($user?->id, $tache->cloture_validation === 'a_valider' ? $tache->cloture_soumise_le : null);
        }
        if (! $terminee && $tache->cloture_validation !== null) {
            return self::champsRetrait();
        }

        return [];
    }

    private static function utilisateursDe(iterable $personnels)
    {
        $ids = collect($personnels)->pluck('id')->filter()->all();

        return $ids ? User::where('actif', true)->whereIn('personnel_id', $ids)->get() : collect();
    }

    private static function notifier($utilisateurs, Tache $tache, string $evenement, ?string $detail, ?int $sauf = null): void
    {
        try {
            foreach ($utilisateurs as $u) {
                if ($sauf && (int) $u->id === $sauf) {
                    continue;
                }
                $u->notify(new ClotureTacheNotification($tache, $evenement, $detail));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Responsables de la tâche (utilisateurs), pour les informer d'une décision. */
    private static function responsables(Tache $tache)
    {
        return self::utilisateursDe($tache->responsables()->with('personnel')->get()->pluck('personnel')->filter());
    }

    /** À appeler après avoir écrit la soumission : journal et alerte des valideurs. */
    public static function apresSoumission(Tache $tache, ?int $userId, string $origine): void
    {
        Journal::noter('taches', 'modification', "Clôture de la tâche {$tache->code} soumise à la validation du supérieur hiérarchique ({$origine})", $tache);
        self::notifier(self::utilisateursDe(SousTacheDroits::validateurs($tache)), $tache, ClotureTacheNotification::A_VALIDER, null, $userId);
    }

    /** @throws DomainException */
    public static function valider(Tache $tache, User $user): void
    {
        if ($tache->cloture_validation !== 'a_valider') {
            throw new DomainException("Cette tâche n'est pas en attente de validation de clôture.");
        }
        if (! self::peutValider($tache, $user)) {
            throw new DomainException("Seul le supérieur hiérarchique du responsable, le DG ou l'administrateur peut valider cette clôture.");
        }
        $avant = JournalTache::instantane($tache);
        $tache->update([
            'statut_execution_id' => StatutExecution::where('code', StatutExecution::TERMINEE)->value('id'), 'avancement_pourcentage' => 100,
            'derniere_mise_a_jour_le' => now(),
        ] + self::champsValidation($user->id, $tache->cloture_soumise_le));
        JournalTache::enregistrer($tache, $avant, $user->id);
        Journal::noter('taches', 'validation', "Clôture de la tâche {$tache->code} validée par {$user->name}", $tache);

        $destinataires = self::responsables($tache)->merge($tache->cloture_soumise_par ? User::where('id', $tache->cloture_soumise_par)->get() : collect())->unique('id');
        self::notifier($destinataires, $tache, ClotureTacheNotification::VALIDEE, null, $user->id);
    }

    /** @throws DomainException */
    public static function refuser(Tache $tache, User $user, string $motif): void
    {
        if ($tache->cloture_validation !== 'a_valider') {
            throw new DomainException("Cette tâche n'est pas en attente de validation de clôture.");
        }
        if (! self::peutValider($tache, $user)) {
            throw new DomainException("Seul le supérieur hiérarchique du responsable, le DG ou l'administrateur peut décider de cette clôture.");
        }
        $motif = trim($motif);
        if (mb_strlen($motif) < 5) {
            throw new DomainException('Le motif du refus est obligatoire (5 caractères minimum).');
        }
        $tache->update([
            'cloture_validation' => 'refusee', 'cloture_motif_refus' => $motif, 'cloture_validee_le' => now(), 'cloture_validee_par' => $user->id,
            'derniere_mise_a_jour_le' => now(),
        ]);
        Journal::noter('taches', 'refus', "Clôture de la tâche {$tache->code} refusée par {$user->name} : {$motif}", $tache, null, ['motif' => $motif]);

        $destinataires = self::responsables($tache)->merge($tache->cloture_soumise_par ? User::where('id', $tache->cloture_soumise_par)->get() : collect())->unique('id');
        self::notifier($destinataires, $tache, ClotureTacheNotification::REFUSEE, $motif, $user->id);
    }

    /** Nouvelle soumission après un refus (responsable de la tâche ou administrateur). @throws DomainException */
    public static function soumettre(Tache $tache, User $user): void
    {
        if ($tache->cloture_validation !== 'refusee') {
            throw new DomainException("Cette tâche n'a pas de clôture refusée à soumettre de nouveau.");
        }
        $estAffecte = $user->personnel_id && $tache->affectations()->where('personnel_id', $user->personnel_id)->exists();
        if (! $estAffecte && ! $user->hasRole('ADMIN|DG')) {
            throw new DomainException('Seule une personne affectée à la tâche peut la soumettre de nouveau.');
        }
        $tache->update(self::champsSoumission($user->id) + ['derniere_mise_a_jour_le' => now()]);
        self::apresSoumission($tache, $user->id, 'nouvelle soumission');
    }

    /** Relances : valideur après N jours, puis DG après 2 N jours. Retourne le nombre de tâches relancées. */
    public static function relancer(): int
    {
        if (! self::active()) {
            return 0;
        }
        $jours = max(1, (int) Reglages::get('cloture_taches.relance_jours'));
        $n = 0;
        Tache::exerciceOuvert()->where('cloture_validation', 'a_valider')->where('cloture_soumise_le', '<=', now()->subDays($jours))
            ->where(fn ($q) => $q->whereNull('cloture_relance_le')->orWhere('cloture_relance_le', '<=', now()->subDays($jours)))
            ->get()->each(function (Tache $t) use ($jours, &$n) {
                $destinataires = self::utilisateursDe(SousTacheDroits::validateurs($t));
                if ($t->cloture_soumise_le->lte(now()->subDays(2 * $jours)) && ($dg = MandatResponsable::directeurGeneral())) {
                    $destinataires = $destinataires->merge(self::utilisateursDe([$dg]))->unique('id');
                }
                self::notifier($destinataires, $t, ClotureTacheNotification::RELANCE, 'Soumise le '.$t->cloture_soumise_le->format('d/m/Y').'.');
                $t->forceFill(['cloture_relance_le' => now()])->save();
                $n++;
            });

        return $n;
    }
}
