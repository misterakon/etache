@extends('layout.master')
@section('title', 'Liste des tâches')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}?v={{ filemtime(public_path('assets/css/tache-list.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/budget-anneau.css') }}">
  <!-- Select2 css -->
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection

@php
  // Aides d'affichage uniquement : toute la logique est dans App\Support\ListeTachesDonnees (contrôleur)
  // Les couleurs sont définies dans public/assets/css/tache-list.css (classes tl-*)
  $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ').' FCFA';
  $ico = fn ($nom) => asset('assets/svg/icon-sprite.svg#'.$nom);
  $mq = '[@media(max-width:645px)_and_(min-width:576px)]:!py-[10px] [@media(max-width:645px)_and_(min-width:576px)]:!px-[15px]';
  // Exercice affiché (choisi, ou par défaut : année en cours et actif) ; null = tous les exercices
  $exerciceChoisi = ($exerciceId ?? null) ? ($exercices ?? collect())->firstWhere('id', $exerciceId) : null;
@endphp

@section('main-content')
    <div class="page-body">
      <div class="container w-full">
        <div class="page-title">
          <div class="grid grid-cols-12 mx-2 items-center">
            <div class="col-span-6 sm:col-span-12">
              <h3>
                  Liste des tâches</h3>
            </div>
            <div class="col-span-6 sm:col-span-12">
              <ol class="breadcrumb flex">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                    <svg class="stroke-icon">
                      <use href="{{ $ico('stroke-home') }}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Tâches</li>
                <li class="breadcrumb-item active">Liste</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- Container-fluid starts-->
      <div class="container">
        <div class="grid grid-cols-12 card-gap project-cards tabs">
          <div class="col-span-12 project-list">
            <div class="card">
              <div class="card-header">
                <div class="header-top">
                  <h5 class="m-0">Vue d'ensemble
                      &nbsp;&nbsp;<i class="fa-regular fa-calendar"></i>&nbsp;&nbsp;{{ $exerciceChoisi ? 'Exercice '.($exerciceChoisi->annee ?? $exerciceChoisi->libelle) : 'Tous les exercices' }}
                  
                  </h5>
                </div>
              </div>
              <div class="card-body">
                <div class="tl-ensemble">
                  {{-- Budget : colonne de gauche --}}
                  @include('feuille_de_route.bloc_budget')

                  {{-- Colonne de droite : agents en haut, avancement et total en dessous --}}
                  <div class="tl-ensemble__droite">
                    @php
                      $pctTotal = max(1, (int) $total);
                      $partsTotal = [
                          ['En attente', 'attente', $attente->count()],
                          ['En cours', 'encours', $encours->count()],
                          ['Terminées', 'terminee', $terminees->count()],
                      ];
                    @endphp

                    {{-- Agents mobilisés --}}
                    <div class="main-card-box tl-stat tl-agents tl-stat--large">
                      <div class="tl-stat__tete">
                        <div class="tl-bubble"><i class="fa-solid fa-user-group"></i></div>
                        <div class="tl-stat__titre"><strong class="tl-valeur">{{ $agents->count() }}</strong><span>Agents mobilisés</span></div>
                      </div>
                      @if ($agents->isEmpty())
                        <span class="tl-stat__vide">Aucun agent affecté.</span>
                      @else
                        <div class="tl-agentsbloc" data-agents>
                          <ul class="tl-agents-pile">
                            @foreach ($agents->take(6) as $p)
                              <li><a href="{{ route('dgepn.personnel.fiche', $p->id) }}" title="{{ $p->nomComplet() }}">
                                @if ($p->photo_url)<img src="{{ $p->photo_url }}" alt="{{ $p->nomComplet() }}">@else<span>{{ $p->initiales() }}</span>@endif
                              </a></li>
                            @endforeach
                          </ul>
                          @if ($agents->count() > 6)
                            <button class="tl-agents-plus" type="button" data-agents-ouvrir aria-expanded="false">+{{ $agents->count() - 6 }} · Voir tous</button>
                          @endif
                          <div class="tl-agents-panneau" data-agents-panneau hidden>
                            <input type="search" class="tl-agents-recherche" placeholder="Rechercher un agent…" data-agents-recherche aria-label="Rechercher un agent">
                            <ul class="tl-agents-tous">
                              @foreach ($agents as $p)
                                <li data-nom="{{ \Illuminate\Support\Str::lower($p->nomComplet()) }}"><a href="{{ route('dgepn.personnel.fiche', $p->id) }}">
                                  @if ($p->photo_url)<img src="{{ $p->photo_url }}" alt="">@else<span>{{ $p->initiales() }}</span>@endif
                                  <b>{{ $p->nomComplet() }}</b></a></li>
                              @endforeach
                            </ul>
                          </div>
                        </div>
                      @endif
                    </div>

                    <div class="tl-ensemble__duo">
                      {{-- Avancement --}}
                      <div class="main-card-box tl-stat tl-avancement">
                        <div class="tl-avancement__corps">
                          <div class="tl-jauge" style="--p: {{ min(100, max(0, (float) $avancementMoyen)) }}" role="img" aria-label="Avancement moyen {{ $avancementMoyen }} %">
                            <strong>{{ $avancementMoyen }}%</strong>
                          </div>
                          <div class="tl-avancement__texte">
                            <span class="tl-etiquette">Avancement moyen</span>
                            <div class="tl-ligne"><span>Taux de réalisation</span><b class="tl-ok"><i class="fa-solid fa-arrow-trend-up"></i> {{ $tauxRealisation }}%</b></div>
                            <div class="tl-ligne"><span>Tâches en retard</span><b class="{{ $nbRetard ? 'tl-ko' : 'tl-ok' }}"><i class="fa-solid {{ $nbRetard ? 'fa-arrow-trend-down' : 'fa-circle-check' }}"></i> {{ $nbRetard }}</b></div>
                          </div>
                        </div>
                      </div>

                      {{-- Total des tâches --}}
                      <div class="main-card-box tl-stat tl-total">
                        <div class="tl-stat__tete">
                          <div class="tl-bubble"><i class="fa-solid fa-file-pen"></i></div>
                          <div class="tl-stat__titre"><strong class="tl-valeur">{{ $total }}</strong><span>Total des tâches</span></div>
                        </div>
                        <div class="tl-repartition" role="img" aria-label="Répartition des tâches par statut">
                          @foreach ($partsTotal as [$libP, $tonP, $nbP])
                            @if ($nbP > 0)<i class="tl-{{ $tonP }}" style="width: {{ round($nbP / $pctTotal * 100, 2) }}%" title="{{ $libP }} : {{ $nbP }}"></i>@endif
                          @endforeach
                        </div>
                        <ul class="tl-repartition__legende">
                          @foreach ($partsTotal as [$libP, $tonP, $nbP])
                            <li class="tl-{{ $tonP }}"><span><i class="tl-dot"></i>{{ $libP }}</span><b>{{ $nbP }}</b></li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-12">
            @include('feuille_de_route._filtres', ['jusqua' => 'action_mesurable', 'direction' => true, 'mode' => true, 'retards' => true])
          </div>
          <div class="col-span-12">
            <div class="card p-3">
              <div class="common-space project-tabs">
                <ul class="nav tab-links border-tab">
                  <li class="nav-item fw-bold"><a class="tab-link tl-tab tl-neutre active {{ $mq }}" data-tabfilter="1"><i class="fa-solid fa-bullseye"></i>Tout <span class="tl-count tl-neutre">{{ $total }}</span></a></li>
                  @foreach ($onglets as $k => [$titre, $ton, $liste, $icone])
                    <li class="nav-item fw-bold"><a class="tab-link tl-tab tl-{{ $ton }} {{ $mq }}" data-tabfilter="{{ $k }}"><i class="{{ $icone }}"></i>{{ $titre }} <span class="tl-count tl-{{ $ton }}">{{ $liste->count() }}</span></a></li>
                  @endforeach
                  @php $tonDiligence = $retards->isNotEmpty() ? 'retard' : 'avenir'; @endphp
                  <li class="nav-item fw-bold"><a class="tab-link tl-tab tl-{{ $tonDiligence }} {{ $mq }}" data-tabfilter="5"><i class="fa-solid fa-triangle-exclamation"></i>Diligence <span class="tl-count tl-{{ $tonDiligence }}">{{ $nbDiligence }}</span></a></li>
                </ul>

                {{-- RECHERCHE --}}
                <div class="tl-recherche">
                  <i class="fa-solid fa-magnifying-glass"></i>
                  <input class="form-control" type="text" id="tl-recherche" placeholder="Rechercher une tâche (code, libellé, responsable…)" autocomplete="off" aria-label="Rechercher une tâche">
                  <button class="tl-recherche__effacer" type="button" id="tl-recherche-effacer" aria-label="Effacer la recherche" hidden><i class="fa-solid fa-xmark"></i></button>
                </div>
                <span class="tl-recherche__info" id="tl-recherche-info"></span>

                @include('feuille_de_route._export_taches')

                @hasanyrole('ADMIN|DG')
                <a class="btn btn-primary text-white" href="#!" onclick="add_form()"><i class="fa-solid fa-plus"></i> Ajouter une tâche  </a>
                @endhasanyrole
              </div>
            </div>
          </div>
          <div class="col-span-12">
            <div class="card">
              <div class="card-body projects-wrapper">
                <div class="tab-content">

                  {{-- ONGLET 1 : Tout (avec les 3 boutons d'action) --}}
                  <div class="tab-pan fade show active" data-tabcontent="1">
                    <div class="grid grid-cols-12 gap-4" data-pagination>
                      @forelse ($taches as $t)
                        @php
                          $tonT = $tons[$t->id] ?? 'attente';
                          $retardT = $tonT !== 'terminee' && $t->estEnRetard();
                          $resp = $t->affectations->where('role_affectation_id', $responsableRoleId)->filter(fn ($a) => $a->personnel);
                        @endphp
                        {{-- id="row-..." conservé pour que edit_tache / deletes_tache retrouvent l'élément --}}
                        @include('feuille_de_route._carte_tache', ['t' => $t, 'ton' => $tonT, 'retardTexte' => $retardT ? 'En retard' : null, 'avenirTexte' => null, 'actions' => true, 'rowId' => true])
                      @empty
                        <div class="col-span-12 tl-vide"><i class="fa-regular fa-folder-open"></i>Aucune tâche pour le moment.</div>
                      @endforelse
                    </div>
                  </div>

                  {{-- ONGLETS 2 à 4 : En cours / En attente / Terminées --}}
                  @foreach ($onglets as $k => [$titre, $ton, $liste, $icone])
                    <div class="tab-pan fade" data-tabcontent="{{ $k }}">
                      <div class="grid grid-cols-12 gap-4" data-pagination>
                        @forelse ($liste as $t)
                          @php
                            $retardT = $ton !== 'terminee' && $t->estEnRetard();
                            $resp = $t->affectations->where('role_affectation_id', $responsableRoleId)->filter(fn ($a) => $a->personnel);
                          @endphp
                          @include('feuille_de_route._carte_tache', ['t' => $t, 'ton' => $ton, 'retardTexte' => $retardT ? 'En retard' : null, 'avenirTexte' => null, 'actions' => true, 'rowId' => false])
                        @empty
                          <div class="col-span-12 tl-vide tl-{{ $ton }}"><i class="{{ $icone }}"></i>Aucune tâche dans cette catégorie.</div>
                        @endforelse
                      </div>
                    </div>
                  @endforeach

                  {{-- ONGLET 5 : Diligence (blocs dépliables : à venir, puis en retard) --}}
                  <div class="tab-pan fade" data-tabcontent="5">
                    @foreach ($diligence as [$titreD, $tonD, $listeD, $videD, $iconeD])
                      @php $idD = 'diligence-bloc-'.$loop->index; @endphp
                      <div class="diligence-bloc tl-{{ $tonD }}">
                        <a class="diligence-toggle" href="#!" role="button" aria-expanded="true" aria-controls="{{ $idD }}" data-diligence-toggle="{{ $idD }}">
                          <h6><i class="{{ $iconeD }} tl-ico"></i>{{ $titreD }} <span class="tl-count">{{ $listeD->count() }}</span></h6>
                          <i class="fa-solid fa-chevron-down diligence-chevron"></i>
                        </a>
                        <div id="{{ $idD }}">
                          <div style="padding: 16px;">
                            <div class="grid grid-cols-12 gap-4" data-pagination>
                              @forelse ($listeD as $t)
                                @php
                                  $resp = $t->affectations->where('role_affectation_id', $responsableRoleId)->filter(fn ($a) => $a->personnel);
                                  $ecart = (int) today()->diffInDays($t->date_fin->copy()->startOfDay(), false); // > 0 : à venir, < 0 : retard
                                @endphp
                                @include('feuille_de_route._carte_tache', ['t' => $t, 'ton' => $tonD, 'retardTexte' => $tonD === 'retard' ? max(1, abs($ecart)).' j de retard' : null, 'avenirTexte' => $tonD === 'retard' ? null : ($ecart <= 0 ? "Aujourd'hui" : ($ecart === 1 ? 'Demain' : 'Dans '.$ecart.' j')), 'actions' => false, 'rowId' => false])
                              @empty
                                <div class="col-span-12 tl-vide"><i class="{{ $iconeD }}"></i>{{ $videD }}</div>
                              @endforelse
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Container-fluid Ends-->

      @include('feuille_de_route.form_tache')
      @include('feuille_de_route.js_script')
    </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/support-ticket-custom.js') }}"></script>
  <script src="{{ asset('assets/js/tache-list.js') }}?v={{ filemtime(public_path('assets/js/tache-list.js')) }}"></script>

  <!-- select2 -->
  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
