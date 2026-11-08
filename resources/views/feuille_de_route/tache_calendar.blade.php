@extends('layout.master')
@section('title', 'Calendrier des tâches')

@php
  $libTons = ['retard' => 'En retard', 'encours' => 'En cours', 'attente' => 'En attente', 'terminee' => 'Terminées', 'neutre' => 'Annulées'];
@endphp

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/calendar.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/taches-vues.css') }}?v={{ filemtime(public_path('assets/css/taches-vues.css')) }}">
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Calendrier des tâches</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item"><a href="{{ route('feuille_de_route.tache') }}">Tâches</a></li>
              <li class="breadcrumb-item active">Calendrier</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container calendar-basic">
      @include('feuille_de_route._vues_taches')

      <div class="card">
        <div class="card-body">
          <div class="grid grid-cols-12 card-gap" id="wrap">

            {{-- Panneau latéral : filtres par statut, prochaines échéances, retards --}}
            <div class="col-span-3 xxl:col-span-12 box-col-12">
              <div class="cal-bloc">
                <h6 class="cal-bloc__titre"><i class="fa-solid fa-filter"></i>Filtrer par statut</h6>
                @foreach ($libTons as $tonF => $libF)
                  <label class="cal-filtre tl-{{ $tonF }}">
                    <input type="checkbox" checked data-cal-ton="{{ $tonF }}">
                    <span class="cal-filtre__case"></span>
                    <span class="cal-filtre__lib">{{ $libF }}</span>
                    <b>{{ $resume['tons'][$tonF] }}</b>
                  </label>
                @endforeach
              </div>

              <div class="cal-bloc">
                <h6 class="cal-bloc__titre"><i class="fa-regular fa-clock"></i>Prochaines échéances</h6>
                @forelse ($calendrier['prochaines'] as $x)
                  <a class="cal-item tl-{{ $x['ton'] }}" href="{{ $x['url'] }}">
                    <span class="cal-item__date"><b>{{ $x['jour'] }}</b><span>{{ $x['mois'] }}</span></span>
                    <span class="cal-item__txt"><b>{{ $x['code'] }}</b><span>{{ $x['libelle'] }}</span></span>
                    <em>{{ $x['jours'] <= 0 ? "Aujourd'hui" : ($x['jours'] === 1 ? 'Demain' : 'Dans '.$x['jours'].' j') }}</em>
                  </a>
                @empty
                  <p class="cal-vide">Aucune échéance dans les 14 prochains jours.</p>
                @endforelse
              </div>

              <div class="cal-bloc">
                <h6 class="cal-bloc__titre"><i class="fa-solid fa-triangle-exclamation" style="color:var(--tl-retard)"></i>En retard</h6>
                @forelse ($calendrier['retards'] as $x)
                  <a class="cal-item tl-retard" href="{{ $x['url'] }}">
                    <span class="cal-item__date"><b>{{ $x['jour'] }}</b><span>{{ $x['mois'] }}</span></span>
                    <span class="cal-item__txt"><b>{{ $x['code'] }}</b><span>{{ $x['libelle'] }}</span></span>
                    <em>{{ max(1, abs($x['jours'])) }} j</em>
                  </a>
                @empty
                  <p class="cal-vide">Aucune tâche en retard, bravo !</p>
                @endforelse
              </div>
            </div>

            {{-- Calendrier --}}
            <div class="col-span-9 xxl:col-span-12 box-col-12">
              <div class="calendar-default" id="calendar-container">
                <div class="cal-barre">
                  <div class="cal-nav">
                    <button type="button" class="cal-btn" data-cal="prev" aria-label="Précédent"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="cal-btn" data-cal="next" aria-label="Suivant"><i class="fa-solid fa-chevron-right"></i></button>
                    <button type="button" class="cal-btn cal-btn--texte" data-cal="today">Aujourd'hui</button>
                  </div>
                  <h4 class="cal-titre" id="cal-titre">&nbsp;</h4>
                  <div class="cal-vues">
                    <button type="button" class="cal-btn actif" data-cal-vue="dayGridMonth">Mois</button>
                    <button type="button" class="cal-btn" data-cal-vue="dayGridWeek">Semaine</button>
                  </div>
                </div>
                <div id="calendrier-taches" data-source="{{ route('feuille_de_route.tache.calendrier.evenements') }}"></div>
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
  {{-- fullcalendar-custom.js (démo du thème) n'est plus chargé : il aurait affiché de faux événements --}}
  <script src="{{ asset('assets/js/calendar/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/js/taches-vues.js') }}?v={{ filemtime(public_path('assets/js/taches-vues.js')) }}"></script>
  
  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
