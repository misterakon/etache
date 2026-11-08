<?php

namespace App\Support;

use App\Models\AlerteEnvoyee;
use App\Models\CanalNotification;
use App\Models\Direction;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\RegleAlerte;
use App\Models\RoleAffectation;
use App\Models\SituationTemporelle;
use App\Models\StatutExecution;
use App\Models\Tache;
use App\Models\TacheJustification;
use App\Models\User;
use App\Notifications\AlerteTacheNotification;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Moteur d'alertes : appele chaque jour par la commande `alertes:evaluer`.
 *  1. met a jour taches.situation_temporelle_id ;
 *  2. pour chaque regle active x tache ouverte dont le jour de declenchement est la date evaluee
 *     (date_fin + decalage_jours = date), envoie l'alerte aux destinataires, sur les canaux ACTIFS de la regle.
 *
 * Pas de rattrapage : seul le jour exact de declenchement est traite (une regle activee tardivement
 * ne rejoue pas le passe). Anti-doublon : alertes_envoyees (tache, regle, canal, agent, echeance).
 */
class MoteurAlertes
{
    /** Une demande de justification en attente suspend les relances pendant ce nombre de jours. */
    const JOURS_PAUSE_JUSTIFICATION = 7;

    /** "Echeance proche" : echeance dans ce nombre de jours (ou moins). */
    const JOURS_ECHEANCE_PROCHE = 7;

    /** "A risque" : avancement en retard sur le temps ecoule d'au moins ce nombre de points (%). */
    const ECART_RISQUE_POINTS = 30;

    private array $stats = [
        'regles' => 0, 'regles_sans_canal' => [], 'taches_declenchees' => 0,
        'envoyees' => 0, 'deja_envoyees' => 0, 'ignorees' => 0, 'echecs' => 0,
        'en_pause' => 0, 'situations_modifiees' => 0,
    ];

    private Collection $users;           // personnel_id => User actif

    private Collection $codeRoleParId;   // role_affectation_id => code

    private ?Personnel $dg = null;

    private bool $dgCherche = false;

    private ?Collection $admins = null;

    /**
     * @param  Closure|null  $journal  fn (string $texte, string $niveau 'info'|'warn') : suivi detaille
     */
    public function __construct(
        private Carbon $date,
        private bool $simuler = false,
        private ?Closure $journal = null,
    ) {
        $this->date = $date->copy()->startOfDay();
        $this->users = User::where('actif', true)->whereNotNull('personnel_id')->get()->keyBy('personnel_id');
        $this->codeRoleParId = RoleAffectation::pluck('code', 'id');
    }

    public function executer(): array
    {
        $this->majSituations();
        $this->envoyerAlertes();

        return $this->stats;
    }

    private function ligne(string $texte, string $niveau = 'info'): void
    {
        if ($this->journal) {
            ($this->journal)($texte, $niveau);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Situation temporelle
    |--------------------------------------------------------------------------
    */

    private function majSituations(): void
    {
        $idSituation = SituationTemporelle::pluck('id', 'code');
        $horsPerimetre = StatutExecution::whereIn('code', [StatutExecution::ANNULEE, StatutExecution::SUSPENDUE])->pluck('id');

        Tache::with(['statutExecution', 'dependances.statutExecution'])
            ->exerciceOuvert()
            ->whereNotIn('statut_execution_id', $horsPerimetre)
            ->chunkById(200, function ($taches) use ($idSituation) {
                foreach ($taches as $tache) {
                    $id = $idSituation[$this->situationPour($tache)] ?? null;

                    if ($id && (int) $tache->situation_temporelle_id !== (int) $id) {
                        $this->stats['situations_modifiees']++;
                        if (! $this->simuler) {
                            // Requete directe : ne touche ni updated_at ni derniere_mise_a_jour_le (detecteur d'inactivite)
                            DB::table('taches')->where('id', $tache->id)->update(['situation_temporelle_id' => $id]);
                        }
                    }
                }
            });
    }

    /** Code de situation d'une tache : terminee > en retard > a risque > echeance proche > normal. */
    private function situationPour(Tache $tache): string
    {
        $statut = $tache->statutExecution?->code;

        if ($statut === StatutExecution::TERMINEE) {
            return SituationTemporelle::TERMINEE;
        }
        if (! $tache->date_fin) {
            return SituationTemporelle::NORMAL;
        }

        $jours = (int) $this->date->diffInDays($tache->date_fin->copy()->startOfDay(), false); // negatif = depassee
        if ($jours < 0) {
            return SituationTemporelle::EN_RETARD;
        }

        if ($statut === StatutExecution::BLOQUEE || $this->dependanceEnRetard($tache) || $this->avancementEnRetard($tache)) {
            return SituationTemporelle::A_RISQUE;
        }

        return $jours <= self::JOURS_ECHEANCE_PROCHE ? SituationTemporelle::ECHEANCE_PROCHE : SituationTemporelle::NORMAL;
    }

    private function dependanceEnRetard(Tache $tache): bool
    {
        return $tache->dependances->contains(fn ($d) => $d->date_fin
            && $d->date_fin->copy()->startOfDay()->lt($this->date)
            && ! in_array($d->statutExecution?->code, [StatutExecution::TERMINEE, StatutExecution::ANNULEE], true));
    }

    /** Temps ecoule (debut -> echeance) superieur a l'avancement de plus de ECART_RISQUE_POINTS points. */
    private function avancementEnRetard(Tache $tache): bool
    {
        if (! $tache->date_debut || ! $tache->date_fin || $tache->date_fin->lte($tache->date_debut) || $this->date->lte($tache->date_debut)) {
            return false;
        }

        $ecoule = ($this->date->timestamp - $tache->date_debut->timestamp) / ($tache->date_fin->timestamp - $tache->date_debut->timestamp) * 100;

        return ($ecoule - (int) $tache->avancement_pourcentage) >= self::ECART_RISQUE_POINTS;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Alertes
    |--------------------------------------------------------------------------
    */

    private function envoyerAlertes(): void
    {
        // Canaux actifs et geres (application, email) par regle ; SMS ignore tant qu'il n'y a pas de fournisseur
        $canauxParRegle = [];
        $regles = RegleAlerte::where('actif', true)->with('canaux')->get()->filter(function ($regle) use (&$canauxParRegle) {
            $canaux = $regle->canaux
                ->where('actif', true)
                ->whereIn('code', [CanalNotification::APPLICATION, CanalNotification::EMAIL])
                ->values();

            if ($canaux->isEmpty()) {
                $this->stats['regles_sans_canal'][] = $regle->code;

                return false;
            }
            $canauxParRegle[$regle->id] = $canaux;

            return true;
        })->values();

        $this->stats['regles'] = $regles->count();
        if ($regles->isEmpty()) {
            return;
        }

        // Echeance ciblee par chaque regle : date - decalage (J-3 => echeance dans 3 jours)
        $cible = $regles->mapWithKeys(fn ($r) => [$r->id => $this->date->copy()->subDays($r->decalage_jours)]);

        $enPause = TacheJustification::enAttente()
            ->where('created_at', '>=', $this->date->copy()->subDays(self::JOURS_PAUSE_JUSTIFICATION))
            ->pluck('tache_id')->flip();

        $horsPerimetre = StatutExecution::whereIn('code', [StatutExecution::SUSPENDUE])->pluck('id');

        Tache::with([
            'statutExecution', 'actionMesurable.actionCle',
            'affectations.personnel.service.directionDirecte', 'affectations.personnel.service.sousDirection.direction',
        ])
            ->nonTerminees()
            ->exerciceOuvert()
            ->whereNotNull('date_fin')
            ->whereNotIn('statut_execution_id', $horsPerimetre)
            ->where(function ($q) use ($cible) {
                foreach ($cible->unique(fn ($d) => $d->toDateString()) as $jour) {
                    $q->orWhereDate('date_fin', $jour);
                }
            })
            ->chunkById(200, function ($taches) use ($regles, $cible, $canauxParRegle, $enPause) {
                foreach ($taches as $tache) {
                    foreach ($regles as $regle) {
                        if (! $tache->date_fin->isSameDay($cible[$regle->id])) {
                            continue;
                        }
                        if ($enPause->has($tache->id)) {
                            $this->stats['en_pause']++;
                            $this->ligne("{$tache->code} · {$regle->code} : relance en pause (demande de justification en attente)");

                            continue;
                        }

                        $this->stats['taches_declenchees']++;
                        $destinataires = $this->destinataires($tache, $regle);
                        if ($destinataires->isEmpty()) {
                            $this->ligne("{$tache->code} · {$regle->code} : aucun destinataire", 'warn');
                        }

                        foreach ($canauxParRegle[$regle->id] as $canal) {
                            foreach ($destinataires as $agent) {
                                $this->envoyer($tache, $regle, $canal, $agent);
                            }
                        }
                    }
                }
            });
    }

    private function envoyer(Tache $tache, RegleAlerte $regle, CanalNotification $canal, Personnel $agent): void
    {
        $cle = [
            'tache_id' => $tache->id,
            'regle_alerte_id' => $regle->id,
            'canal_notification_id' => $canal->id,
            'personnel_id' => $agent->id,
            'echeance_au_envoi' => $tache->date_fin->toDateString(),
        ];
        $qui = "{$tache->code} · {$regle->code} · {$agent->nomComplet()} · {$canal->libelle}";

        if (AlerteEnvoyee::where($cle)->exists()) {
            $this->stats['deja_envoyees']++;

            return;
        }

        $parEmail = $canal->code === CanalNotification::EMAIL;
        if ($parEmail && ! filter_var($agent->email, FILTER_VALIDATE_EMAIL)) {
            $this->stats['ignorees']++;
            $this->ligne("{$qui} : ignorée (aucun email valide)", 'warn');

            return;
        }
        if (! $parEmail && ! $this->users->has($agent->id)) {
            $this->stats['ignorees']++;
            $this->ligne("{$qui} : ignorée (aucun compte utilisateur actif)", 'warn');

            return;
        }

        if ($this->simuler) {
            $this->stats['envoyees']++;
            $this->ligne("[simulation] {$qui}");

            return;
        }

        try {
            if ($parEmail) {
                Notification::route('mail', [$agent->email => $agent->nomComplet()])
                    ->notify(new AlerteTacheNotification($tache, $regle, $agent, 'mail'));
            } else {
                $this->users[$agent->id]->notify(new AlerteTacheNotification($tache, $regle, $agent, 'database'));
            }

            AlerteEnvoyee::create($cle + ['envoye_le' => now()]);
            $this->stats['envoyees']++;
        } catch (Throwable $e) {
            $this->stats['echecs']++;
            Log::warning("Alerte non envoyée ({$qui}) : ".$e->getMessage());
            $this->ligne("{$qui} : ÉCHEC — ".$e->getMessage(), 'warn');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Destinataires (cles de regles_alerte.destinataires -> agents)
    |--------------------------------------------------------------------------
    */

    private function destinataires(Tache $tache, RegleAlerte $regle): Collection
    {
        $liste = collect();

        foreach ($regle->destinataires ?? [] as $cle) {
            $liste = $liste->concat(match ($cle) {
                RegleAlerte::RESPONSABLE => $this->agentsParRole($tache, RoleAffectation::RESPONSABLE),
                RegleAlerte::PILOTE => $this->agentsParRole($tache, RoleAffectation::PILOTE),
                RegleAlerte::CONTRIBUTEUR => $this->agentsParRole($tache, RoleAffectation::CONTRIBUTEUR),
                RegleAlerte::RESPONSABLE_DIRECTION => $this->responsablesDirection($tache),
                RegleAlerte::DG => collect([$this->directeurGeneral()]),
                RegleAlerte::ADMINISTRATEUR => $this->administrateurs(),
                default => collect(),
            });
        }

        return $liste->filter(fn ($p) => $p && $p->actif)->unique('id')->values();
    }

    private function agentsParRole(Tache $tache, string $codeRole): Collection
    {
        return $tache->affectations
            ->filter(fn ($a) => ($this->codeRoleParId[$a->role_affectation_id] ?? null) === $codeRole)
            ->pluck('personnel')->filter()->values();
    }

    /** Responsable de la direction du responsable de la tache (a defaut du pilote, puis de l'action cle). */
    private function responsablesDirection(Tache $tache): Collection
    {
        $agents = $this->agentsParRole($tache, RoleAffectation::RESPONSABLE);
        if ($agents->isEmpty()) {
            $agents = $this->agentsParRole($tache, RoleAffectation::PILOTE);
        }

        $directions = $agents->map(fn ($p) => $p->service?->direction())->filter()->unique('id');

        if ($directions->isEmpty()) {
            $directionId = $tache->actionMesurable?->actionCle?->direction_id;
            $directions = collect([$directionId ? Direction::find($directionId) : null])->filter();
        }

        return $directions->map(fn ($d) => $d->responsable($this->date))->filter();
    }

    /** Directeur General : mandat en cours, a defaut l'agent lie au compte de profil DG. */
    private function directeurGeneral(): ?Personnel
    {
        if (! $this->dgCherche) {
            $this->dg = MandatResponsable::directeurGeneral($this->date)
                ?? $this->agentsDeProfil(ProfilAcces::DG)->first();
            $this->dgCherche = true;
        }

        return $this->dg;
    }

    /** Administrateurs : agents lies aux comptes de profil ADMIN (un compte sans agent ne peut pas etre journalise). */
    private function administrateurs(): Collection
    {
        return $this->admins ??= $this->agentsDeProfil(ProfilAcces::ADMINISTRATEUR);
    }

    private function agentsDeProfil(string $slug): Collection
    {
        return User::where('actif', true)->whereNotNull('personnel_id')
            ->whereHas('profilAcces', fn ($q) => $q->where('slug', $slug))
            ->with('personnel')->get()->pluck('personnel')->filter()->values();
    }
}
