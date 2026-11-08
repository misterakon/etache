@extends('layout.master')
@section('title', 'Charge de travail des agents')

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
          <h1 class="cr-hero__titre">Charge de travail</h1>
          <p class="cr-hero__sous">Tâches, diligences du courrier et actions de traitement ouvertes de chaque agent. À partir de {{ $seuil }} éléments, l'agent est en sur-affectation et son supérieur est alerté (seuil réglable dans Réglages).</p>
        </div>
      </div>
      @include('rh._nav')
      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-users"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['agents'] }}</span><span class="cr-kpi__libelle">Agents actifs</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-gauge-high"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['surcharges'] }}</span><span class="cr-kpi__libelle">En sur-affectation</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['en_retard'] }}</span><span class="cr-kpi__libelle">Avec des tâches en retard</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-mug-hot"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['sans_charge'] }}</span><span class="cr-kpi__libelle">Sans aucune charge</span></div></div>
      </div>
      <form class="cr-carte cr-filtres" method="GET" action="{{ route('rh.charge') }}">
        <div class="cr-filtres__grille" style="grid-template-columns: 2fr auto auto;">
          <div class="cr-recherche"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control form-control-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Nom de l'agent…"></div>
          <label style="display:flex;align-items:center;gap:8px;margin:0;font-weight:600;font-size:13px;"><input type="checkbox" class="rh-case" name="surcharge" value="1" @checked(request('surcharge') === '1') onchange="this.form.submit()"> Sur-affectation seulement</label>
          <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
        </div>
      </form>
      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-gauge-high"></i> {{ $lignes->count() }} agent(s)</h2></div>
        @if ($lignes->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Rien à afficher</strong>Aucun agent ne correspond à ce filtre.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Agent</th><th>Charge totale</th><th style="text-align:center;">Tâches</th><th style="text-align:center;">En retard</th><th style="text-align:center;">À 14 jours</th><th style="text-align:center;">Diligences</th><th style="text-align:center;">Actions</th></tr></thead>
              <tbody>
                @foreach ($lignes as $l)
                  @php $z = fn ($n) => $n === 0 ? 'rh-chiffre rh-chiffre--zero' : 'rh-chiffre'; $pct = min(100, round($l['charge'] / max(1, $l['seuil']) * 100)); @endphp
                  <tr onclick="window.location='{{ route('rh.agent', $l['personnel']->id) }}'">
                    <td><div class="rh-agent"><span class="rh-avatar">{{ $l['personnel']->initiales() }}</span><span><b>{{ $l['personnel']->nomComplet() }}</b><small>{{ $l['personnel']->service?->nom ?? '—' }}</small></span></div></td>
                    <td><div class="rh-jauge"><span class="rh-barre" style="--c: {{ $l['surcharge'] ? '#A73B34' : ($pct >= 70 ? '#C77D0A' : '#2E7D5B') }}"><span style="width: {{ $pct }}%"></span></span><strong>{{ $l['charge'] }}</strong>@if ($l['surcharge'])<span class="cr-chip" style="--c:#A73B34">Surcharge</span>@endif</div></td>
                    <td style="text-align:center;"><span class="{{ $z($l['taches']) }}">{{ $l['taches'] }}</span></td>
                    <td style="text-align:center;"><span class="{{ $l['retard'] ? 'rh-chiffre rh-chiffre--retard' : 'rh-chiffre rh-chiffre--zero' }}">{{ $l['retard'] }}</span></td>
                    <td style="text-align:center;"><span class="{{ $z($l['a_venir']) }}">{{ $l['a_venir'] }}</span></td>
                    <td style="text-align:center;"><span class="{{ $z($l['diligences']) }}">{{ $l['diligences'] }}</span></td>
                    <td style="text-align:center;"><span class="{{ $z($l['actions']) }}">{{ $l['actions'] }}</span></td>
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
