@extends('layout.master')
@section('title', 'Mes tâches')

@include('feuille_de_route._alerte_rd_sans_mandat')

@php
  use App\Models\StatutExecution;

  // Une tâche terminée n'est plus modifiable, sauf par l'administrateur
  $validationCloture = \App\Support\ClotureTache::active();
  $verrouillee = fn ($t) => ! $estAdmin && ($t->statutExecution?->code === StatutExecution::TERMINEE || $t->cloture_validation === 'a_valider');
  $messageConfirmation = $validationCloture
      ? "Soumettre la clôture de cette tâche à la validation de votre supérieur hiérarchique ? Elle reste « En cours » jusqu'à sa décision."
      : 'Marquer cette tâche comme terminée ? Vous ne pourrez plus la modifier.';
  $couleurPoint = function ($t) {
      $code = $t->statutExecution?->code;
      if ($code === StatutExecution::TERMINEE) return 'success';
      if ($t->estEnRetard()) return 'danger';
      if ($code === StatutExecution::EN_COURS) return 'primary';
      if ($t->date_fin && $t->date_fin->diffInDays(today(), false) >= -7) return 'warning';
      return 'secondary';
  };
  $nomAgent = $personnel ? $personnel->nomComplet() : (auth()->user()->name ?? '');

  // Libellé de priorité à partir de $priorites (déjà fourni au modal)
  $prioritesParId = $priorites->keyBy('id');
  $libellePriorite = fn ($t) => optional($prioritesParId->get($t->priorite_id))->libelle;

  // Chemin Axe / Action clé / Action mesurable
  $cheminAction = fn ($t) => collect([
      $t->actionMesurable?->actionCle?->axe?->code,
      $t->actionMesurable?->actionCle?->code,
      $t->actionMesurable?->code,
  ])->filter()->implode(' / ');

  // Nombres affichés devant les onglets 1 et 2
  // ---- Onglet 1 : tâches de la journée / de la semaine en cours (10 au plus) ----
  $inclureEnRetard = true;      // false => uniquement les tâches de la semaine en cours
  $debutSemaine = today()->startOfWeek();
  $finSemaine   = today()->endOfWeek();

  $estTerminee = fn ($t) => $t->statutExecution?->code === StatutExecution::TERMINEE;
  $enRetard    = fn ($t) => ! $estTerminee($t) && $t->estEnRetard();
  $dansLaSemaine = function ($t) use ($debutSemaine, $finSemaine) {
      $debut = $t->date_debut ?? $t->date_fin;
      $fin   = $t->date_fin ?? $t->date_debut;
      if (! $debut || ! $fin) return false;
      return \Carbon\Carbon::parse($debut)->lte($finSemaine) && \Carbon\Carbon::parse($fin)->gte($debutSemaine);
  };

  $candidates = $groupes['toutes']->filter(fn ($t) => $dansLaSemaine($t) || ($inclureEnRetard && $enRetard($t)));
  $totalCandidates = $candidates->count();
  // Ordre : en retard d'abord, puis à faire (échéance la plus proche), puis terminées
  $tachesJour = $candidates->sortBy(fn ($t) =>
      (($enRetard($t) ? 0 : ($estTerminee($t) ? 2 : 1)) * 10_000_000_000)
      + ($t->date_fin?->timestamp ?? 9_999_999_999)
  )->take(10)->values();
  $nbEnRetard = $tachesJour->filter($enRetard)->count();

  // Palette de l'onglet 1 (couleurs : public/assets/css/mes-taches.css)
  $tonStatut = fn ($t) => match ($t->statutExecution?->code) {
      StatutExecution::TERMINEE => 'terminee',
      StatutExecution::EN_COURS => 'encours',
      StatutExecution::ANNULEE => 'neutre',
      default => 'attente',
  };
  $tonCarte = fn ($t) => $enRetard($t) ? 'retard' : $tonStatut($t);   // le retard prime sur le statut
  $repartition = $tachesJour->countBy($tonCarte);
  $chipsResume = [
      ['retard', 'En retard', 'fa-solid fa-triangle-exclamation'],
      ['encours', 'En cours', 'fa-solid fa-bars-progress'],
      ['attente', 'En attente', 'fa-regular fa-hourglass-half'],
      ['terminee', 'Terminées', 'fa-solid fa-circle-check'],
  ];

  // Couleur de la petite barre de progression
  $couleurBarre = fn ($t) => $estTerminee($t) ? 'success' : ($enRetard($t) ? 'danger' : 'primary');

  // ---- Onglet 4 : activité récente = dernières tâches modifiées (10 au plus) ----
  $activites = $groupes['toutes']->filter(fn ($t) => $t->updated_at)->sortByDesc('updated_at')->take(10)->values();

  $nbTachesDuJour = $tachesJour->count();
  $nbToutesTaches = $groupes['toutes']->count();

  // Colonnes de l'onglet "Statuts"
  $colonnesStatuts = [
      'attente'   => ['titre' => 'En attente', 'couleur' => 'primary', 'classe' => 'col-span-4 xl:col-span-6 md:col-span-12 xl-50 box-col-6'],
      'encours'   => ['titre' => 'En cours',   'couleur' => 'warning', 'classe' => 'col-span-4 xl:col-span-6 md:col-span-12 xl-50 box-col-6'],
      'terminees' => ['titre' => 'Terminées',  'couleur' => 'success', 'classe' => 'col-span-4 xl:col-span-12 xl-100 box-col-12'],
  ];
@endphp

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/select/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/ponctuelles.css') }}?v={{ filemtime(public_path('assets/css/ponctuelles.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/mes-taches.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/mes-taches-vues.css') }}?v={{ filemtime(public_path('assets/css/mes-taches-vues.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Mes tâches</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Tâches</li>
              <li class="breadcrumb-item active">Mes tâches</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container ep-page">
      @include('feuille_de_route._taches_a_valider')
      @php
        $tout = $groupes['toutes'];
        $mtTerminees = $tout->filter($estTerminee)->count();
        $mtRetard = $tout->filter($enRetard)->count();
        $mtAvancement = $nbToutesTaches ? (int) round($tout->avg('avancement_pourcentage')) : 0;
        $mtPctTerminees = $nbToutesTaches ? (int) round($mtTerminees / $nbToutesTaches * 100) : 0;
        $kpisMt = [
            ['Tâches du jour', $nbTachesDuJour, 'Semaine du '.$debutSemaine->translatedFormat('j F').' au '.$finSemaine->translatedFormat('j F'), 'fa-solid fa-calendar-day', '', null],
            ['En retard', $mtRetard, $mtRetard ? 'À traiter en priorité' : 'Aucun retard', 'fa-solid fa-triangle-exclamation', $mtRetard ? 'retard' : 'ok', null],
            ['Terminées', $mtTerminees.' / '.$nbToutesTaches, $mtPctTerminees.' % de mes tâches', 'fa-solid fa-circle-check', 'ok', $mtPctTerminees],
            ['Avancement moyen', $mtAvancement.' %', 'Sur toutes mes tâches', 'fa-solid fa-gauge-high', '', $mtAvancement],
        ];
        $nbPonctuelles = auth()->user()->personnel_id ? \App\Models\TachePonctuelle::ouvertes()->where('responsable_personnel_id', auth()->user()->personnel_id)->count() : 0;
        $ongletsMt = [
            1 => ['fa-calendar-day', 'Mes tâches du jour', $nbTachesDuJour],
            2 => ['fa-list-check', 'Toutes mes tâches', $nbToutesTaches],
            3 => ['fa-chart-pie', 'Statuts', null],
            4 => ['fa-clock-rotate-left', 'Activité', $activites->count()],
            5 => ['fa-bolt', 'Tâches ponctuelles', $nbPonctuelles],
        ];
      @endphp

      <x-entete-page tag="Mes tâches" titre="{{ $nomAgent ? 'Bonjour '.\Illuminate\Support\Str::before($nomAgent, ' ').', voici vos tâches' : 'Mes tâches' }}" sous="Ce qui est à faire cette semaine, les retards à traiter et l'état de toutes vos tâches." :kpis="$kpisMt">
        <x-slot:actions>
          @if (isset($exercices) && $exercices->isNotEmpty())
            <select class="ep-select" id="filtre_exercice" name="exercice_id" aria-label="Exercice" onchange="window.location.href = this.value">
              <option value="{{ request()->fullUrlWithQuery(['exercice_id' => '']) }}" @selected(! ($exerciceId ?? null))>Tous les exercices</option>
              @foreach ($exercices as $ex)
                <option value="{{ request()->fullUrlWithQuery(['exercice_id' => $ex->id]) }}" @selected(($exerciceId ?? null) == $ex->id)>Exercice {{ $ex->annee ?? $ex->libelle }}{{ $ex->id == ($exerciceDefautId ?? null) ? ' (par défaut)' : '' }}</option>
              @endforeach
            </select>
          @endif
        </x-slot:actions>
      </x-entete-page>

      <x-onglets-page :onglets="$ongletsMt" />

      <div class="ep-contenu">
        <div class="tab-content">
                <div class="tab-pan fade show active" data-tabcontent="1">
                  <div class="grid grid-cols-12 card-gap"> 
                    <div class="col-span-12 xxl:col-span-12 box-col-12 notification main-timeline">
                      <div class="card"> 
                        <div class="card-header"> 
                          <h5>Mes tâches du jour / semaine<span class="mt-compteur" style="padding-top: 4px">{{ $nbTachesDuJour }}</span></h5>
                          <p class="f-m-light mt-1" style="color: #001677;">
                            Semaine du {{ $debutSemaine->translatedFormat('j F') }} au {{ $finSemaine->translatedFormat('j F Y') }} — 10 tâches au plus{{ $inclureEnRetard ? ', retards inclus' : '' }}.
                            @if ($totalCandidates > $tachesJour->count()) {{ $totalCandidates - $tachesJour->count() }} autre(s) dans « Toutes mes tâches ». @endif
                          </p>
                          <div class="mt-resume">
                            @foreach ($chipsResume as [$tonR, $libR, $icoR])
                              <span class="mt-chip tl-{{ $tonR }}"><i class="{{ $icoR }}"></i>&nbsp; {{ $libR }} &nbsp;&nbsp;<strong>{{ $repartition[$tonR] ?? 0 }}</strong></span>
                            @endforeach
                          </div>
                        </div>
                        <div class="card-body dark-timeline basic-timeline">
                          @if ($tachesJour->isEmpty())
                            <p class="text-muted text-center" style="padding:24px 0;">Aucune tâche à traiter cette semaine.</p>
                          @else
                            <ul>
                              @foreach ($tachesJour as $t)
                                @php
                                  $tonC = $tonCarte($t);
                                  $retardT = $enRetard($t);
                                  $prio = $libellePriorite($t);
                                  $joursAvant = $t->date_fin ? (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false) : null;
                                  $tonDate = $estTerminee($t) ? 'terminee' : ($retardT ? 'retard' : (($joursAvant !== null && $joursAvant <= 2) ? 'attente' : 'teal'));
                                @endphp
                                <li class="flex" id="row-{{ $t->id }}">
                                  <div class="timeline-dot-{{ $couleurPoint($t) }}"></div>
                                  <div class="w-full ms-3">
                                    <div class="mt-carte tl-{{ $tonC }}">
                                      <div class="mt-carte__haut">
                                        <span class="mt-date tl-{{ $tonDate }}" style="color: #001a6e"><i class="fa-regular fa-calendar"></i>&nbsp;&nbsp;{{ $t->date_fin ? 'Échéance '.$t->date_fin->format('d/m/Y') : 'Sans échéance' }}</span>
                                        <span class="mt-badges">
                                          @if ($t->statutExecution) <span class="tl-badge tl-{{ $tonStatut($t) }}" style="color: #242323">{{ $t->statutExecution->libelle }}</span> @endif
                                          @include('feuille_de_route._badge_sous_taches', ['t' => $t])
                                          @if ($prio) <span class="tl-badge tl-avenir" style="color: #110101">Priorité : {{ $prio }}</span> @endif
                                          @if ($retardT) <span class="tl-badge tl-retard tl-solid" style="color: #ffffff">{{ (int) $t->date_fin->diffInDays(today()) }} j de retard</span> @endif
                                          @include('feuille_de_route._badge_justification', ['t' => $t])
                                        </span>
                                      </div>
                                      <div class="mt-carte__titre">
                                        <h6><a href="{{ route('feuille_de_route.tache.details', $t->id) }}"><span class="mt-code">{{ $t->code }}</span>{{ $t->libelle }}</a></h6>
                                        <div class="mt-actions">
                                          <a class="mt-btn mt-btn--voir" href="{{ route('feuille_de_route.tache.details', $t->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Détails"><i class="fa-regular fa-eye"></i></a>
                                          @if ($verrouillee($t))
                                            <button class="mt-btn mt-btn--verrou" type="button" disabled data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="{{ $t->cloture_validation === 'a_valider' ? 'Clôture en attente de validation' : 'Tâche terminée : verrouillée' }}"><i class="fa-solid fa-lock"></i></button>
                                          @else
                                            <button class="mt-btn mt-btn--edit btn-maj-tache" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Mettre à jour"
                                                    data-url="{{ route('feuille_de_route.tache.maj_rapide', $t->id) }}"
                                                    data-titre="{{ $t->code }} — {{ $t->libelle }}"
                                                    data-priorite="{{ $t->priorite_id }}"
                                                    data-statut="{{ $t->statut_execution_id }}"
                                                    data-avancement="{{ $t->avancement_pourcentage }}"
                                                    data-calcule="{{ $t->exerciceTache()?->avancementParSousTaches() ? 1 : 0 }}"><i class="fa-regular fa-pen-to-square"></i></button>
                                          @endif
                                          @include('feuille_de_route._btn_justifier', ['t' => $t, 'classe' => 'mt-btn mt-btn--edit'])
                                        </div>
                                      </div>
                                      @if ($cheminAction($t))
                                        <p class="mt-chemin"><i class="fa-solid fa-sitemap"></i>{{ $cheminAction($t) }}</p>
                                      @endif
                                      <div class="mt-progres">
                                        <div class="mt-meter {{ $t->avancement_pourcentage > 0 ? '' : 'mt-meter--vide' }}" style="--p: {{ $t->avancement_pourcentage }}%" role="progressbar" aria-valuenow="{{ $t->avancement_pourcentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        <span class="mt-progres__pct">{{ $t->avancement_pourcentage }}%</span>
                                      </div>
                                      @if (($t->sous_taches_count ?? 0) > 0)
                                        @php $droitsSt = \App\Support\SousTacheDroits::pour($t, auth()->user()); @endphp
                                        <details class="mt-st">
                                          <summary><i class="fa-solid fa-list-check"></i> Sous-tâches : {{ $t->sous_taches_cochees_count }}/{{ $t->sous_taches_count }}</summary>
                                          <ul>
                                            @foreach ($t->sousTaches as $s)
                                              <li>
                                                <label class="{{ $s->cochee ? 'mt-st-fait' : '' }}">
                                                  <input type="checkbox" class="form-check-input" @checked($s->cochee) @disabled(! $droitsSt['gerer']) onchange="mt_st_cocher({{ $s->id }}, this)">
                                                  {{ $s->libelle }}@if ($s->echeance) <small>(échéance {{ $s->echeance->format('d/m/Y') }})</small>@endif
                                                </label>
                                              </li>
                                            @endforeach
                                          </ul>
                                          @if ($droitsSt['raison']) <small class="mt-st-note"><i class="fa-solid fa-lock"></i> {{ $droitsSt['raison'] }}</small> @endif
                                        </details>
                                      @endif
                                    </div>
                                  </div>
                                </li>
                              @endforeach
                            </ul>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pan fade" data-tabcontent="2">
                  @php
                    $repToutes = $groupes['toutes']->countBy($tonCarte);
                    $chipsToutes = array_merge([['tous', 'Toutes', 'fa-solid fa-layer-group']], $chipsResume);
                  @endphp
                  <div class="mx-panneau">
                    <div class="mx-tete">
                      <div>
                        <h5>Toutes mes tâches <em>{{ $nbToutesTaches }}</em></h5>
                        <p>{{ $nomAgent }} — {{ $nbToutesTaches }} tâche(s)</p>
                      </div>
                      <label class="mx-recherche"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="mx_q" placeholder="Rechercher par code ou libellé…" autocomplete="off"></label>
                    </div>
                    <div class="mx-chips" id="mx_chips">
                      @foreach ($chipsToutes as [$cleChip, $libChip, $iconeChip])
                        @php $nbChip = $cleChip === 'tous' ? $nbToutesTaches : ($repToutes[$cleChip] ?? 0); @endphp
                        <button type="button" class="mx-chip tl-{{ $cleChip === 'tous' ? 'encours' : $cleChip }} {{ $loop->first ? 'actif' : '' }}" data-ton="{{ $cleChip }}"><i class="{{ $iconeChip }}"></i> {{ $libChip }} <b>{{ $nbChip }}</b></button>
                      @endforeach
                    </div>
                    <div class="mx-liste" id="mx_liste">
                      @foreach ($groupes['toutes'] as $t)
                        @include('feuille_de_route._ligne_mx', ['t' => $t])
                      @endforeach
                      <p class="mx-vide" id="mx_vide" hidden>Aucune tâche ne correspond.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pan fade" data-tabcontent="3">
                  @php
                    $onglets3 = [
                        ['attente', 'En attente', 'fa-regular fa-hourglass-half', $groupes['attente']->count()],
                        ['encours', 'En cours', 'fa-solid fa-bars-progress', $groupes['encours']->count()],
                        ['terminees', 'Terminées', 'fa-solid fa-circle-check', $groupes['terminees']->count()],
                        ['retard', 'En retard', 'fa-solid fa-triangle-exclamation', $groupes['toutes']->filter($enRetard)->count()],
                    ];
                    $actif3 = collect($onglets3)->first(fn ($o) => $o[3] > 0)[0] ?? 'attente';
                  @endphp
                  <div class="mx-panneau">
                    <div class="mx-onglets" id="mx_onglets" role="tablist">
                      @foreach ($onglets3 as [$cle3, $lib3, $ic3, $nb3])
                        <button type="button" role="tab" class="mx-onglet mx-onglet--{{ $cle3 }} {{ $cle3 === $actif3 ? 'actif' : '' }}" data-grp="{{ $cle3 }}"><i class="{{ $ic3 }}"></i> {{ $lib3 }} <em>{{ $nb3 }}</em></button>
                      @endforeach
                    </div>
                    <div class="mx-liste" id="mx_liste3">
                      @foreach (['attente', 'encours', 'terminees'] as $grp)
                        @foreach ($groupes[$grp] as $t)
                          @include('feuille_de_route._ligne_mx', ['t' => $t, 'grp' => $grp])
                        @endforeach
                      @endforeach
                      <p class="mx-vide" id="mx_vide3" hidden>Aucune tâche dans cette catégorie.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pan fade" data-tabcontent="5">
                  @include('ponctuelles._dispositif')
                </div>
                <div class="tab-pan fade" data-tabcontent="4">
                  <div class="grid grid-cols-12 card-gap">
                    <div class="col-span-12">
                      <div class="card filter-header">
                        <div class="card-body">
                          <div class="common-space">
                            <h5>Activité récente sur mes tâches</h5>
                            <div class="btn-group">
                              <button class="btn dropdown-toggle" id="activitePeriodeLabel" type="button" data-bs-toggle="dropdown" aria-expanded="false">Tout</button>
                              <ul class="dropdown-menu dropdown-block">
                                <li><a class="dropdown-item" href="#!" data-periode="jour">Aujourd'hui</a></li>
                                <li><a class="dropdown-item" href="#!" data-periode="semaine">Cette semaine</a></li>
                                <li><a class="dropdown-item" href="#!" data-periode="mois">Ce mois</a></li>
                                <li><a class="dropdown-item" href="#!" data-periode="tout">Tout</a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-span-12">
                      <div class="card project-timeline">
                        <div class="card-body notification">
                          @if ($activites->isEmpty())
                            <p class="text-muted text-center" style="padding:24px 0;">Aucune activité récente.</p>
                          @else
                            <ul id="activiteListe">
                              @foreach ($activites as $t)
                                <li class="flex" data-ts="{{ $t->updated_at->timestamp }}">
                                  <div class="activity-dot-{{ $enRetard($t) ? 'warning' : 'primary' }}"></div>
                                  <div class="w-full ms-3">
                                    <h6 class="font-normal"><a href="{{ route('feuille_de_route.tache.details', $t->id) }}">{{ $t->code }} — {{ $t->libelle }}</a></h6>
                                    <span class="date-time">{{ $t->updated_at->translatedFormat('d/m/Y H:i') }} · {{ $t->updated_at->diffForHumans() }}</span>
                                    <div class="mt-3">
                                      <div class="flex flex-wrap items-center gap-2">
                                        @if ($t->statutExecution)
                                          <span class="badge text-white rounded-full" style="background-color: {{ $t->statutExecution->couleur ?: '#5B6B84' }}; white-space:nowrap;">{{ $t->statutExecution->libelle }}</span>
                                        @endif
                                        @if ($enRetard($t))
                                          <span class="badge badge-light-danger">En retard</span>
                                        @endif
                                        <div class="progress grow" style="height:6px; max-width:200px;"><div class="progress-bar bg-{{ $couleurBarre($t) }}" style="width: {{ $t->avancement_pourcentage }}%"></div></div>
                                        <small class="c-o-light">{{ $t->avancement_pourcentage }}%</small>
                                      </div>
                                    </div>
                                  </div>
                                </li>
                              @endforeach
                            </ul>
                            <p class="text-muted text-center" id="activiteVide" style="display:none; padding:24px 0;">Aucune activité sur cette période.</p>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
        </div>
      </div>
    </div>
    <!-- Container-fluid Ends-->
  </div>

  {{-- Modal de mise à jour (onglet "Mes tâches du jour") --}}
  <div class="modal fade panel-modal" id="modalMajTache" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Mettre à jour la tâche</h4>
          <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formMajTache" method="post" action="#"
              data-code-terminee="{{ StatutExecution::TERMINEE }}"
              data-code-en-cours="{{ StatutExecution::EN_COURS }}">
          @csrf
          <div class="modal-body dark-modal">
            <p class="c-o-light mb-3" id="majTitre" style="color: #020f3a; font-weight:bold"></p>
            <div class="grid grid-cols-12 gap-3">
              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="maj_priorite"><b>Priorité</b></label>
                <select class="form-select form-select-sm" id="maj_priorite" name="priorite_id">
                  <option value="">— Aucune —</option>
                  @foreach ($priorites as $p)
                    <option value="{{ $p->id }}">{{ $p->libelle }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="maj_statut"><b>Statut d'exécution</b></label>
                <select class="form-select form-select-sm" id="maj_statut" name="statut_execution_id" required>
                  @foreach ($statutsExecution as $s)
                    <option value="{{ $s->id }}" data-code="{{ $s->code }}">{{ $s->libelle }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-span-12">
                <label class="form-label" for="maj_avancement"><b>Avancement</b> : <strong id="majAvancementValeur">0</strong> %</label>
                <input class="form-control" id="maj_avancement" name="avancement_pourcentage" type="range" min="0" max="100" step="5" value="0">
                <small id="majAvancementAide" class="text-muted" style="display:none;">Avancement calculé à partir des sous-tâches : il se modifie en les cochant.</small>
              </div>
              @unless ($estAdmin)
                <div class="col-span-12" id="majAvertissement" style="display:none;">
                  <div class="alert alert-warning mb-0">{{ $validationCloture ? "Marquer la tâche comme terminée soumet sa clôture à la validation de votre supérieur hiérarchique : elle reste « En cours » (99 % au plus) jusqu'à sa décision." : "Une fois la tâche marquée comme terminée, vous ne pourrez plus la modifier. Seul l'administrateur le pourra." }}</div>
                </div>
              @endunless
              <div class="col-span-12" id="majErreur" style="display:none;">
                <div class="alert alert-danger mb-0" id="majErreurTexte"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-primary text-white" id="btnEnregistrerMaj" type="submit">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @include('feuille_de_route._modal_justification')
  @include('feuille_de_route.js_script')
  @include('ponctuelles._modal_et_config')
@endsection

@section('script')
  <script src="{{ asset('assets/select/select2.min.js') }}"></script>
  <script src="{{ asset('assets/js/ponctuelles.js') }}?v={{ filemtime(public_path('assets/js/ponctuelles.js')) }}"></script>
  <script src="{{ asset('assets/js/timeline/custom-timeline.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  <script src="{{ asset('assets/js/touchspin_2/custom_touchspin.js') }}"></script>
  <script src="{{ asset('assets/js/custom-project.js') }}"></script>
  <script src="{{ asset('assets/js/mes-taches-vues.js') }}?v={{ filemtime(public_path('assets/js/mes-taches-vues.js')) }}"></script>
  <script>
    (function () {
      const modalEl = document.getElementById('modalMajTache');
      if (!modalEl) return;

      const form = document.getElementById('formMajTache');
      const titre = document.getElementById('majTitre');
      const priorite = document.getElementById('maj_priorite');
      const statut = document.getElementById('maj_statut');
      const avancement = document.getElementById('maj_avancement');
      const valeur = document.getElementById('majAvancementValeur');
      const avertissement = document.getElementById('majAvertissement'); // absent pour l'admin
      const erreur = document.getElementById('majErreur');
      const erreurTexte = document.getElementById('majErreurTexte');
      const btnEnregistrer = document.getElementById('btnEnregistrerMaj');
      const codeTerminee = form.dataset.codeTerminee;
      const codeEnCours = form.dataset.codeEnCours;
      const aideCalcule = document.getElementById('majAvancementAide');
      let calcule = false; // exercice en mode "par sous-tâches" : avancement non modifiable

      const codeChoisi = () => (statut.selectedOptions[0] || {dataset: {}}).dataset.code;
      const optionParCode = (code) => Array.from(statut.options).find((o) => o.dataset.code === code);

      function majAffichage() {
        valeur.textContent = avancement.value;
        if (avertissement) avertissement.style.display = codeChoisi() === codeTerminee ? '' : 'none';
      }
      function afficherErreur(message) {
        erreurTexte.textContent = message;
        erreur.style.display = '';
      }
      function ouvrirModal() {
        if (window.bootstrap) {
          bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
          jQuery(modalEl).modal('show');
        }
      }

      // Clic sur le crayon : pré-remplissage puis ouverture
      document.addEventListener('click', function (event) {
        const btn = event.target.closest('.btn-maj-tache');
        if (!btn) return;
        form.action = btn.dataset.url;
        titre.textContent = btn.dataset.titre;
        priorite.value = btn.dataset.priorite || '';
        statut.value = btn.dataset.statut;
        avancement.value = btn.dataset.avancement;
        calcule = btn.dataset.calcule === '1';
        avancement.style.pointerEvents = calcule ? 'none' : '';
        avancement.style.opacity = calcule ? '.5' : '';
        avancement.tabIndex = calcule ? -1 : 0;
        aideCalcule.style.display = calcule ? '' : 'none';
        erreur.style.display = 'none';
        majAffichage();
        ouvrirModal();
      });

      // Cohérence statut <-> avancement
      statut.addEventListener('change', function () {
        if (calcule) { majAffichage(); return; }
        if (codeChoisi() === codeTerminee) avancement.value = 100;
        majAffichage();
      });
      avancement.addEventListener('input', function () {
        if (Number(avancement.value) === 100) {
          const opt = optionParCode(codeTerminee);
          if (opt) statut.value = opt.value;
        } else if (codeChoisi() === codeTerminee) {
          const opt = optionParCode(codeEnCours);
          if (opt) statut.value = opt.value;
        }
        majAffichage();
      });

      // Enregistrement (le serveur reste seul juge du droit de modifier)
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (avertissement && codeChoisi() === codeTerminee &&
            !confirm(@json($messageConfirmation))) {
          return;
        }
        erreur.style.display = 'none';
        btnEnregistrer.disabled = true;
        try {
          const res = await fetch(form.action, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
            body: new FormData(form),
            credentials: 'same-origin',
          });
          if (res.ok) {
            window.location.reload();
            return;
          }
          let message = res.status === 403
            ? "Vous n'avez pas le droit de modifier cette tâche."
            : 'Une erreur est survenue (' + res.status + ').';
          try {
            const data = await res.json();
            if (data.errors) message = Object.values(data.errors).flat().join(' ');
            else if (data.message && res.status !== 403) message = data.message;
          } catch (e) { /* réponse non JSON */ }
          afficherErreur(message);
        } catch (e) {
          afficherErreur('Impossible de contacter le serveur.');
        } finally {
          btnEnregistrer.disabled = false;
        }
      });
    })();
  </script>
  <script>
    // Coche / décoche d'une sous-tâche depuis la carte (le serveur contrôle le droit), puis rechargement
    async function mt_st_cocher(id, caseACocher) {
      const coche = caseACocher.checked;
      try {
        const res = await fetch("{{ route('feuille_de_route.sous_tache.cocher', ':id') }}".replace(':id', id), {
          method: 'POST',
          headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/x-www-form-urlencoded'},
          body: 'cochee=' + (coche ? 1 : 0),
          credentials: 'same-origin',
        });
        if (res.ok) { window.location.reload(); return; }
        let message = 'Une erreur est survenue (' + res.status + ').';
        try { const data = await res.json(); if (data.message) message = data.message; } catch (e) { /* réponse non JSON */ }
        caseACocher.checked = !coche;
        alert(message);
      } catch (e) {
        caseACocher.checked = !coche;
        alert('Impossible de contacter le serveur.');
      }
    }
  </script>
  <style>
    .mt-st { margin-top: 10px; }
    .mt-st > summary { cursor: pointer; font-weight: 600; color: #1F2A37; }
    .mt-st ul { margin: 8px 0 0; padding: 0; list-style: none; }
    .mt-st li { padding: 4px 0; }
    .mt-st label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .mt-st-fait { text-decoration: line-through; color: #374151; }
    .mt-st-note { display: block; margin-top: 6px; color: #374151; }
    .mt-st .form-check-input:checked { background-color: #2E7D5B !important; border-color: #2E7D5B !important; }
  </style>
  <script>
    // Onglet "Activité(s)" : filtre par période (aujourd'hui / semaine / mois / tout)
    (function () {
      const liste = document.getElementById('activiteListe');
      if (!liste) return;
      const label = document.getElementById('activitePeriodeLabel');
      const vide = document.getElementById('activiteVide');

      function seuil(periode) {
        const d = new Date();
        d.setHours(0, 0, 0, 0);
        if (periode === 'tout') return 0;
        if (periode === 'semaine') d.setDate(d.getDate() - ((d.getDay() + 6) % 7)); // lundi
        if (periode === 'mois') d.setDate(1);
        return d.getTime() / 1000;
      }

      document.querySelectorAll('[data-periode]').forEach(function (lien) {
        lien.addEventListener('click', function (event) {
          event.preventDefault();
          const min = seuil(lien.dataset.periode);
          let visibles = 0;
          liste.querySelectorAll('li[data-ts]').forEach(function (li) {
            const ok = Number(li.dataset.ts) >= min;
            li.style.display = ok ? '' : 'none';
            if (ok) visibles++;
          });
          vide.style.display = visibles ? 'none' : '';
          label.textContent = lien.textContent.trim();
        });
      });
    })();
  </script>
@endsection

  