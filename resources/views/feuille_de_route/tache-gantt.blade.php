@extends('layout.master')
@section('title', 'Diagramme de Gantt')

@php
  $legende = [['encours', 'En cours'], ['attente', 'En attente'], ['terminee', 'Terminée'], ['retard', 'En retard'], ['neutre', 'Annulée']];
@endphp

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/taches-vues.css') }}?v={{ filemtime(public_path('assets/css/taches-vues.css')) }}">
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Diagramme de Gantt</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item"><a href="{{ route('feuille_de_route.tache') }}">Tâches</a></li>
              <li class="breadcrumb-item active">Gantt</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      @include('feuille_de_route._vues_taches')

      <div class="card gt-zone-impression">
        <div class="card-body">
          <div class="gt-impr-entete">
            <h4>Diagramme de Gantt par axe</h4>
            <p>{{ ($exerciceId ?? null) ? 'Exercice '.optional($exercices->firstWhere('id', $exerciceId))->annee : 'Tous les exercices' }} · Filtres : {{ \App\Support\FiltresTaches::resume(\App\Support\FiltresTaches::depuis(request())) }}</p>
            <p>Édité le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name }}</p>
          </div>
          @if (empty($gantt['groupes']))
            <div class="vt-vide"><i class="fa-solid fa-bars-staggered"></i>Aucune tâche datée à afficher.<br>Renseignez une date de début ou d'échéance pour voir les tâches ici.</div>
          @else
            <div class="vt-outils">
              <div class="gt-zoom" role="group" aria-label="Échelle de temps">
                <button type="button" data-gt-zoom="jour">Jour</button>
                <button type="button" data-gt-zoom="semaine" class="actif">Semaine</button>
                <button type="button" data-gt-zoom="mois">Mois</button>
              </div>
              <button type="button" class="vt-btn-doux" data-gt-aujourdhui><i class="fa-solid fa-crosshairs" style="color:var(--tl-attente)"></i>Aujourd'hui</button>
              <div class="gt-zoom" role="group" aria-label="Niveau de détail">
                <button type="button" data-gt-niveau="1">Axes</button>
                <button type="button" data-gt-niveau="2">Actions clés</button>
                <button type="button" data-gt-niveau="3" class="actif">Tâches</button>
              </div>
              <button type="button" class="vt-btn-doux" data-gt-imprimer><i class="fa-solid fa-file-pdf" style="color:#A73B34"></i>Exporter en PDF</button>
              <div class="vt-legende">
                @php
                  $couleursLegende = ['encours' => '#2B6CB0', 'attente' => '#C77D0A', 'terminee' => '#2E7D5B', 'retard' => '#A73B34', 'neutre' => '#5B6B84'];
                  $carre = fn ($couleur) => '<i style="display:inline-block;flex:0 0 10px;width:10px;height:10px;border-radius:3px;background:'.$couleur.'"></i>';
                @endphp
                @foreach ($legende as [$tonL, $libL])
                  <span class="vt-leg">{!! $carre($couleursLegende[$tonL]) !!}&nbsp; {{ $libL }}</span>
                @endforeach
                <span class="vt-leg">{!! $carre('#5B6B84') !!}&nbsp;Flèche : dépendance</span>
                <span class="vt-leg">{!! $carre('#A73B34') !!}&nbsp;n : en retard</span>
                <span class="vt-leg">{!! $carre('#5A1914') !!}&nbsp;n : retard non justifié</span>
              </div>
            </div>

            <div class="gt" id="gantt-taches"></div>
            <script type="application/json" id="donnees-gantt">@json($gantt)</script>
            @include('feuille_de_route._pagination_serveur')
          @endif

          @if ($gantt['sansDates'] > 0)
            <p class="gt-note"><i class="fa-solid fa-circle-info"></i> {{ $gantt['sansDates'] }} tâche(s) sans date de début ni d'échéance n'apparaissent pas dans le diagramme.</p>
          @endif
        </div>
      </div>
    </div>

    @include('feuille_de_route.form_tache')
    @include('feuille_de_route.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/taches-vues.js') }}?v={{ filemtime(public_path('assets/js/taches-vues.js')) }}"></script>
  
  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
