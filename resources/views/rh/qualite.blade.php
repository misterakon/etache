@extends('layout.master')
@section('title', 'Qualité des fiches du personnel')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Qualité des fiches</h1>
          <p class="cr-hero__sous">Les fiches des agents actifs auxquelles il manque des informations essentielles : identité, contact, emploi, date de prise de service, rattachement.</p>
        </div>
        <div class="cr-hero__actions"><a class="cr-btn cr-btn--blanc" href="{{ route('rh.qualite.export', request()->query()) }}"><i class="fa-solid fa-file-excel"></i> Exporter la liste</a></div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-users"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['agents'] }}</span><span class="cr-kpi__libelle">Agents actifs</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['incompletes'] }}</span><span class="cr-kpi__libelle">Fiches à compléter</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-circle-check"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['completes'] }}</span><span class="cr-kpi__libelle">Fiches complètes</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-percent"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['moyenne'] }} %</span><span class="cr-kpi__libelle">Complétude moyenne</span></div></div>
      </div>

      <form class="cr-carte cr-filtres" method="GET" action="{{ route('rh.qualite') }}">
        <div class="cr-pilules" style="margin-bottom:14px;">
          @foreach (['essentiels' => 'Essentiels manquants', 'toutes' => 'Toute information manquante', 'completes' => 'Fiches complètes'] as $cle => $libelle)
            <a class="cr-pilule {{ $niveau === $cle ? 'cr-pilule--active' : '' }}" href="{{ route('rh.qualite', array_merge(request()->except('niveau'), ['niveau' => $cle])) }}">{{ $libelle }}</a>
          @endforeach
        </div>
        <input type="hidden" name="niveau" value="{{ $niveau }}">
        <div class="cr-filtres__grille" style="grid-template-columns: 2fr 1.4fr auto;">
          <div class="cr-recherche"><label for="f_q">Recherche</label><i class="fa-solid fa-magnifying-glass" style="top:calc(50% + 11px)"></i><input class="form-control form-control-sm" id="f_q" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, prénom, matricule…"></div>
          <div><label for="f_dir">Direction</label><select class="form-select form-select-sm" id="f_dir" name="direction"><option value="">Toutes</option>@foreach ($directions as $d)<option value="{{ $d->id }}" @selected((string) request('direction') === (string) $d->id)>{{ $d->nom }}</option>@endforeach</select></div>
          <div style="display:flex;gap:8px;"><button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>@if (request()->hasAny(['q', 'direction']))<a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('rh.qualite', ['niveau' => $niveau]) }}">Réinitialiser</a>@endif</div>
        </div>
      </form>

      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clipboard-check"></i> {{ $lignes->count() }} fiche(s)</h2></div>
        @if ($lignes->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Rien à afficher</strong>Aucune fiche ne correspond à ce filtre.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Agent</th><th>Direction</th><th>Complétude</th><th>Informations manquantes</th><th></th></tr></thead>
              <tbody>
                @foreach ($lignes as $l)
                  <tr onclick="window.location='{{ route('dgepn.personnel.fiche', $l['agent']->id) }}'">
                    <td><div class="rh-agent"><span class="rh-avatar">{{ $l['agent']->initiales() }}</span><span><b>{{ $l['agent']->nomComplet() }}</b><small>{{ $l['agent']->matricule ?: 'Sans matricule' }}</small></span></div></td>
                    <td>{{ $l['direction'] }}</td>
                    <td><div class="rh-pourcent"><span class="rh-barre" style="--c: {{ $l['score'] >= 85 ? '#2E7D5B' : ($l['score'] >= 60 ? '#C77D0A' : '#A73B34') }}"><span style="width: {{ $l['score'] }}%"></span></span><strong>{{ $l['score'] }}%</strong></div></td>
                    <td>@forelse ($l['manquants'] as $m)<span class="rh-manque {{ in_array($m, $l['essentiels'], true) ? 'rh-manque--essentiel' : '' }}">{{ $m }}</span>@empty<span class="rh-ok"><i class="fa-solid fa-circle-check"></i> Complète</span>@endforelse</td>
                    <td><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('dgepn.personnel.fiche', $l['agent']->id) }}" onclick="event.stopPropagation()"><i class="fa-regular fa-pen-to-square"></i> Compléter</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
