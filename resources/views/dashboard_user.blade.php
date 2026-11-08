@extends('layout.master')
@section('title', 'Tableau de bord')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/swiper/swiper-bundle.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dashboard-budget.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dashboard-design.css') }}">
@endsection

@php
  // Pile de photos des acteurs (photo, sinon initiales) : $max visibles puis "+N"
  $avatars = function ($agents, int $max = 4) {
      if ($agents->isEmpty()) {
          return '<span class="c-o-light">—</span>';
      }
      $html = '<ul class="customers">';
      foreach ($agents->take($max) as $p) {
          $nom = e($p->nomComplet());
          $html .= '<li data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="'.$nom.'">'
              .($p->photo_url
                  ? '<img class="common-circle" src="'.e($p->photo_url).'" alt="'.$nom.'">'
                  : '<div class="common-circle bg-lighter-primary">'.e($p->initiales()).'</div>')
              .'</li>';
      }
      if ($agents->count() > $max) {
          $reste = $agents->count() - $max;
          $html .= '<li data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="'.$reste.' de plus"><div class="common-circle bg-lighter-dark">+'.$reste.'</div></li>';
      }

      return $html.'</ul>';
  };
  $delai = fn (int $j) => $j === 0 ? "Aujourd'hui" : ($j === 1 ? 'Demain' : 'Dans '.$j.' j');
  $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ').' FCFA';
  // Montant abrégé pour la légende : 425 k, 1,25 M, 2,3 Md (le montant exact apparaît au survol)
  $court = function ($v) {
      $f = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ',');
      $v = (float) $v;

      return match (true) {
          $v >= 999500000 => $f($v / 1e9).' Md',
          $v >= 999500 => $f($v / 1e6).' M',
          $v >= 10000 => $f($v / 1e3).' k',
          default => number_format($v, 0, ',', ' '),
      };
  };
@endphp

@section('main-content')
<div class="page-body">
  <div class="container">        
    @include('feuille_de_route._alerte_rd_sans_mandat')
    <div class="page-title">
      <div class="grid grid-cols-12 mx-2 items-center">
        <div class="col-span-4 sm:col-span-12">
          <h3 class="ed-chip">Mon tableau de bord
          </h3>
        </div>
        <div class="col-span-4 sm:col-span-12">
          <h3 class="ed-chip" style="color: green">
            @if (isset($exercice) && $exercice)
              <i class="fa-regular fa-calendar"></i> &nbsp;Exercice {{ $exercice->annee ?? $exercice->libelle }}
            @endif
          </h3>
        </div>
        <div class="col-span-4 sm:col-span-12">
          <ol class="breadcrumb flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">                                       
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">e-Tache</li>
            <li class="breadcrumb-item active">Tableau de bord</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid starts-->
  <div class="container dashboard-12 etache-dash">
    <div class="grid grid-cols-12 card-gap"> 
      @unless (auth()->user()->personnel_id)
        <div class="col-span-12">
          <div class="card">
            <div class="card-body">
              <i class="fa-solid fa-circle-info"></i> Votre compte n'est lié à aucun agent : aucune tâche ne peut vous être affectée.
              Contactez l'administrateur pour relier votre compte à votre fiche agent.
            </div>
          </div>
        </div>
      @endunless
      <div class="col-span-7 xxl:col-span-12 box-col-12">
        <div class="grid grid-cols-12 card-gap small-stats"> 
          <div class="col-span-4 md:col-span-6 sm:col-span-12">
            <div class="card widget-hover ed-stat tl-attente">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">En attente</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['attente'] }}">{{ $stats['attente'] }}</h4><span class="ed-stat__info">{{ $stats['pctAttente'] }} % du total</span>
                  </div>
                  <div class="ed-bubble"><i class="fa-regular fa-hourglass-half"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-4 md:col-span-6 sm:col-span-12">
            <div class="card widget-hover ed-stat tl-terminee">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">Terminées</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['terminees'] }}">{{ $stats['terminees'] }}</h4><span class="ed-stat__info">{{ $stats['pctTerminees'] }} % du total</span>
                  </div>
                  <div class="ed-bubble"><i class="fa-solid fa-circle-check"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-4 md:col-span-12">
            <div class="card widget-hover ed-stat tl-encours">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">En cours</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['encours'] }}">{{ $stats['encours'] }}</h4><span class="ed-stat__info">{{ $stats['pctEncours'] }} % du total</span>
                  </div>
                  <div class="ed-bubble"><i class="fa-solid fa-bars-progress"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-4 md:col-span-6 sm:col-span-12">
            <div class="card widget-hover ed-stat tl-retard">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">En retard</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['retards'] }}">{{ $stats['retards'] }}</h4>
                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:6px;">
                      <span class="ed-stat__info">{{ $stats['pctRetards'] }} % du total</span>
                      @if ($stats['retards'] > 0)
                        <span class="ed-stat__info" style="font-weight:600;">{{ $stats['retardsNonJustifies'] }} non justifié{{ $stats['retardsNonJustifies'] > 1 ? 's' : '' }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="ed-bubble"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-4 md:col-span-6 sm:col-span-12">
            <div class="card widget-hover ed-stat tl-avenir">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">Diligence</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['diligences'] }}">{{ $stats['diligences'] }}</h4><span class="ed-stat__info">ce mois + retards</span>
                  </div>
                  <div class="ed-bubble"><i class="fa-regular fa-calendar-check"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-4 md:col-span-12">
            <div class="card widget-hover ed-stat tl-teal">
              <div class="card-body">
                <div class="common-space">
                  <div> <span class="font-medium ed-stat__lib">Total</span>
                    <h4 class="ed-compteur" data-valeur="{{ $stats['total'] }}">{{ $stats['total'] }}</h4><span class="ed-stat__info">avancement {{ $stats['avancement'] }} %</span>
                  </div>
                  <div class="ed-bubble"><i class="fa-solid fa-file-pen"></i></div>
                </div>
              </div>
            </div>
          </div>
          {{-- Priorité des taches  --}}
          <div class="col-span-12">
            <div class="card tasks-card"> 
              <div class="card-header card-no-border">
                <div class="header-top"> 
                  <h5><i class="ed-ico tl-retard fa-solid fa-flag"></i>Tâche prioritaire</h5>
                  <div class="arrow-group">
                    <div class="prev"><i class="fa-solid fa-arrow-right-long fa-flip-horizontal"></i></div>
                    <div class="next"><i class="fa-solid fa-arrow-right-long"></i></div>
                  </div>
                  <div class="card-header-right-icon">
                    <div class="dropdown icon-dropdown">
                      <button class="btn dropdown-toggle" id="priorityTask" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="priorityTask"><a class="dropdown-item" href="#!">Aujourd'hui</a><a class="dropdown-item" href="#!">Demain</a><a class="dropdown-item" href="#!">Hier</a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body pt-0"> 
                <div class="task-box">
                  <div class="swiper recent-slider">
                    <div class="swiper-wrapper">
                      @forelse ($prioritaires as $p)
                        @php $t = $p['tache']; @endphp
                        <div class="swiper-slide">
                          <div class="task-box ed-tache tl-{{ $p['tonPriorite'] }}">
                            <div class="common-space">
                              <h6><a href="{{ route('feuille_de_route.tache.details', $t->id) }}">{{ $t->code }} — {{ \Illuminate\Support\Str::limit($t->libelle, 70) }}</a></h6>
                              <span class="tl-badge tl-{{ $p['tonPriorite'] }} tl-solid whitespace-nowrap">{{ $p['priorite'] ?? 'Priorité non définie' }}</span>
                            </div>
                            <p class="project-link">{{ $p['chemin'] ?: '—' }}</p>
                            <div class="project-member">
                              <p>{{ $t->avancement_pourcentage }} % réalisé</p>
                              {!! $avatars($p['agents'], 4) !!}
                            </div>
                            <div class="progress" role="progressbar">
                              <div class="progress-bar bg-success" style="width: {{ $t->avancement_pourcentage }}%"></div>
                            </div>
                            <p>Échéance : {{ optional($t->date_fin)->format('d/m/Y') ?: '—' }}
                              @if ($p['ton'] === 'retard') <span class="tl-badge tl-retard tl-solid">{{ abs($p['jours']) }} j de retard</span> @endif</p>
                          </div>
                        </div>
                      @empty
                        <div class="swiper-slide">
                          <div class="task-box ed-tache tl-neutre">
                            <p class="mb-0">Aucune tâche prioritaire pour cet exercice.</p>
                          </div>
                        </div>
                      @endforelse
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-span-5 xxl:col-span-12 box-col-12">
        <div class="grid grid-cols-12 card-gap small-stats"> 
          <div class="col-span-12 md:col-span-6 sm:col-span-12">
            {{-- ============ Budget de MES tâches (répartition par catégorie) ============ --}}
            <div class="card widget-hover ed-stat ed-budget tl-encours">
              <div class="card-body">
                <div class="common-space">
                  <h5 class="ed-budget__titre">Budget de mes tâches</h5>
                  <div class="ed-bubble"><i class="fa-solid fa-coins"></i></div>
                </div>
                <div class="ed-budget__ligne">
                  <div class="ed-budget__gauche">
                    <div class="ed-budget__repartition">
                      <div class="ed-anneau" role="img" aria-label="Budget par catégorie : {{ $budget['pctRealise'] }} % réalisé">
                        <svg viewBox="0 0 42 42">
                          <g transform="rotate(-90 21 21)">
                            <circle class="ed-anneau__fond" cx="21" cy="21" r="15.9155" fill="none" stroke-width="5"/>
                            @foreach ($budget['arcs'] as [$tonA, $longueurA, $debutA])
                              <circle class="ed-anneau__seg ed-anneau__seg--{{ $tonA }}" cx="21" cy="21" r="15.9155" fill="none" stroke-width="5" pathLength="100" stroke-dasharray="{{ $longueurA }} {{ round(100 - $longueurA, 2) }}" stroke-dashoffset="{{ -$debutA }}"/>
                            @endforeach
                          </g>
                        </svg>
                        <div class="ed-anneau__centre"><strong>{{ $budget['pctRealise'] }}%</strong><small>réalisé</small></div>
                      </div>
                      <ul class="ed-budget__legende">
                        @foreach ($budget['segments'] as [$libelleL, $tonL, $montantL, $aideL])
                          <li title="{{ $aideL ? $aideL.' : ' : '' }}{{ $fcfa($montantL) }}"><span><span class="ed-legende__point tl-{{ $tonL }}"></span>{{ $libelleL }}</span><b>{{ $court($montantL) }}</b></li>
                        @endforeach
                      </ul>
                    </div>
                    @if ($budget['total'] > 0)
                      <p class="ed-budget__retard {{ $budget['retard'] > 0 ? 'ed-budget__retard--oui' : 'ed-budget__retard--non' }}">
                        @if ($budget['retard'] > 0)
                          <i class="fa-solid fa-triangle-exclamation"></i> Budget en retard : <b>{{ $court($budget['retard']) }}</b> ({{ $budget['pctRetard'] }} %)
                        @else
                          <i class="fa-solid fa-circle-check"></i> Aucun budget en retard
                        @endif
                      </p>
                    @endif
                  </div>
                  <div class="ed-budget__totaux">
                    <ul>
                      <li><h6>{{ $fcfa($budget['total']) }}</h6><span>Budget total</span></li>
                      <li><h6>{{ $fcfa($budget['termine']) }}</h6><span>Tâches terminées</span></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-12 md:col-span-6 sm:col-span-12">
            {{-- ============ Demandes de justification : chiffres des demandes portant sur MES tâches (non cliquable) ============ --}}
            @if ($justifications['visible'])
              @php
                // Agent : chiffres seulement. La page des demandes ne lui est pas ouverte : aucune tuile cliquable, pas de « Tout voir ».
                // [statut, libellé, couleur] : l'icône de chaque couleur est dessinée par dashboard-design.css
                $jTuiles = [
                    ['en_attente', 'En attente', 'attente'],
                    ['approuvee', 'Approuvées', 'terminee'],
                    ['refusee', 'Refusées', 'retard'],
                    ['annulee', 'Annulées', 'neutre'],
                ];
              @endphp
              {{-- Carte pleine comme le Budget (version agent : chiffres seulement) --}}
              <div class="card widget-hover ed-stat ed-just">
                <div class="card-body">
                  <div class="common-space">
                    <h5 class="ed-just__titre">Demandes de justification</h5>
                  </div>

                  <div class="ed-just__grille">
                    @foreach ($jTuiles as [$cleJ, $libelleJ, $tonJ])
                      @php $nJ = $justifications['statuts'][$cleJ]; @endphp
                      <a class="ed-just__tuile tl-{{ $tonJ }}">
                        <span class="ed-just__ico"></span>
                        <b class="ed-just__nb">{{ $nJ }}</b>
                        <span class="ed-just__lib">{{ $libelleJ }}</span>
                      </a>
                    @endforeach
                  </div>

                  @if ($justifications['total'] > 0)
                    <div class="ed-just__barre" aria-hidden="true">
                      @foreach ($jTuiles as [$cleJ, , $tonJ])
                        @if ($justifications['statuts'][$cleJ] > 0)
                          <span class="tl-{{ $tonJ }}" style="width: {{ round($justifications['statuts'][$cleJ] / $justifications['total'] * 100, 1) }}%"></span>
                        @endif
                      @endforeach
                    </div>
                    <p class="ed-just__pied">{{ $justifications['total'] }} {{ $justifications['total'] > 1 ? 'demandes' : 'demande' }} sur l'exercice</p>
                  @else
                    <p class="ed-just__pied">Aucune demande pour l'instant.</p>
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>   

      {{-- Garde-fou : le script du thème dashboard_12.js cherche #session-chart (bloc Statistique en commentaire) --}}
      <div id="session-chart" style="display:none;"></div>
      <div class="col-span-12 xxl:col-span-12 ord-xl-8 box-ord-8 box-col-12">
        <div class="card heading-space">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5><i class="ed-ico tl-attente fa-solid fa-calendar-check"></i>Mes diligences (à venir ce mois, puis retards)</h5>
              <div class="card-header-right-icon">
                <div class="dropdown icon-dropdown">
                  <button class="btn dropdown-toggle" id="recentActivity" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentActivity"><a class="dropdown-item" href="#!">Ce mois</a><a class="dropdown-item" href="#!">Mois précédent</a><a class="dropdown-item" href="#!">3 derniers mois</a><a class="dropdown-item" href="#!">6 derniers mois</a></div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 recent-activity-table">
            <div class="recent-table overflow-x-auto custom-scrollbar">
              <table class="table" id="recent-activity-table">
                <thead> 
                  <tr>
                    <th> </th>
                    <th>Tâche</th>
                    <th>Action</th>
                    <th>Responsables</th>
                    <th>Échéance</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($diligences as $d)
                    @php $t = $d['tache']; @endphp
                    <tr class="inbox-data">
                      <td><span class="ed-dot tl-{{ $d['ton'] }}"></span></td>
                      <td><a href="{{ route('feuille_de_route.tache.details', $t->id) }}"><span class="ed-code tl-{{ $d['ton'] }}">{{ $t->code }}</span> {{ \Illuminate\Support\Str::limit($t->libelle, 60) }}</a></td>
                      <td>{{ $d['chemin'] ?: '—' }}</td>
                      <td>{!! $avatars($d['agents'], 4) !!}</td>
                      <td data-order="{{ $t->date_fin->format('Y-m-d') }}">{{ $t->date_fin->format('d/m/Y') }}
                        @if ($d['ton'] === 'retard')
                          <span class="tl-badge tl-retard tl-solid">{{ abs($d['jours']) }} j de retard</span>
                        @else
                          <span class="tl-badge tl-{{ $d['jours'] <= 1 ? 'attente' : 'encours' }}">{{ $delai($d['jours']) }}</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- Container-fluid Ends-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
<script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
<script src="{{ asset('assets/js/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/dashboard/dashboard_12.js') }}"></script>
<script src="{{ asset('assets/js/general-widget.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endsection

        