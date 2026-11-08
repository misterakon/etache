<?php

namespace App\Providers;

use App\Models\ActionCle;
use App\Models\ActionMesurable;
use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Models\Diligence;
use App\Models\DiligenceAction;
use App\Models\DiligenceDelai;
use App\Models\Exercice;
use App\Models\Imputation;
use App\Models\Personnel;
use App\Models\PieceJointe;
use App\Models\ReponseCourrier;
use App\Models\ReponseVisa;
use App\Models\SousTache;
use App\Models\SynthesePilotage;
use App\Models\Tache;
use App\Models\TachePonctuelle;
use App\Models\User;
use App\Support\Droits;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Déverrouillage de l'écran de verrouillage : 10 essais par minute et par utilisateur (en plus du plafond d'essais de la session)
        RateLimiter::for('deverrouillage', fn (Request $request) => Limit::perMinute(10)->by('deverrouillage|'.($request->user()?->id ?? $request->ip())));

        // Alias courts pour affectable_type (table `affectations`) : evite de stocker
        // le chemin de classe complet (App\Models\ActionCle...) en base, et protège
        // contre un renommage/déplacement futur des modèles.
        Relation::enforceMorphMap([
            'action_cle' => ActionCle::class,
            'action_mesurable' => ActionMesurable::class,
            'tache' => Tache::class,
            'tache_ponctuelle' => TachePonctuelle::class,
            'user' => User::class,
            'sous_tache' => SousTache::class,
            'piece_jointe' => PieceJointe::class,
            'exercice' => Exercice::class,
            'courrier' => Courrier::class,
            'imputation' => Imputation::class,
            'diligence' => Diligence::class,
            'personnel' => Personnel::class,
            'reponse_courrier' => ReponseCourrier::class,
            'depart' => CourrierDepart::class,
            'diligence_action' => DiligenceAction::class,
            'diligence_delai' => DiligenceDelai::class,
            'reponse_visa' => ReponseVisa::class,
            'synthese_pilotage' => SynthesePilotage::class,
        ]);

        // Alertes d'échéance et de retard des diligences : chaque matin (en plus du déclenchement « à la visite »)
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('courrier:alertes-diligences')->dailyAt('07:00')->withoutOverlapping();
            $schedule->command('rh:alertes')->dailyAt('07:10')->withoutOverlapping();
            $schedule->command('pilotage:instantane')->dailyAt('23:50')->withoutOverlapping();
            $schedule->command('pilotage:synthese-hebdo')->weeklyOn(1, '07:30')->withoutOverlapping();
            $schedule->command('taches:relance-validation')->dailyAt('07:20')->withoutOverlapping();
        });

        foreach (['role', 'hasrole', 'hasanyrole'] as $directive) {
            Blade::if($directive, fn (...$roles) => auth()->check() && auth()->user()->hasAnyRole(...$roles));
        }

        // Chaque droit précis du catalogue (App\Support\Droits) devient utilisable avec @can('direction.voir'),
        // middleware('can:direction.voir'), Gate::authorize(...). Complète hasRole()/ExigerProfil, sans les remplacer.
        foreach (Droits::tous() as $droit) {
            Gate::define($droit, fn (User $user) => $user->peut($droit));
        }
    }
}
