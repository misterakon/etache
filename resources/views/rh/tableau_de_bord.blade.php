@extends('layout.master')
@section('title', 'Tableau de bord du personnel')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      @php
        $e = $s['effectifs'];
        $maxDir = max(1, (int) ($s['par_direction']->max() ?? 1));
        $maxType = max(1, (int) ($s['par_type']->max() ?? 1));
        $maxGenre = max(1, (int) ($s['par_genre']->max() ?? 1));
        $maxPyr = max(1, collect($s['pyramide'])->map(fn ($t) => max($t['h'], $t['f']))->max());
      @endphp
      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Tableau de bord du personnel</h1>
          <p class="cr-hero__sous">Effectifs, répartition, âges, retraites à venir, qualité des fiches et charge de travail, en un coup d'œil.</p>
        </div>
      </div>
      @include('rh._nav')

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-users"></i></span><div><span class="cr-kpi__valeur">{{ $e['actifs'] }}</span><span class="cr-kpi__libelle">Agents actifs</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-user-lock"></i></span><div><span class="cr-kpi__valeur">{{ $e['avec_compte'] }}<small style="font-size:14px;color:#374151;"> / {{ $e['actifs'] }}</small></span><span class="cr-kpi__libelle">Avec un compte</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-user-clock"></i></span><div><span class="cr-kpi__valeur">{{ $s['retraites_12_mois'] }}</span><span class="cr-kpi__libelle">Retraites sous 12 mois</span></div></div>
        <div class="cr-kpi" style="--c:#8A94A6"><span class="cr-kpi__icone"><i class="fa-solid fa-box-archive"></i></span><div><span class="cr-kpi__valeur">{{ $e['archives'] }}</span><span class="cr-kpi__libelle">Agents archivés</span></div></div>
      </div>
      <div class="cr-kpis">
        <a href="{{ route('rh.organigramme') }}" style="text-decoration:none;color:inherit;"><div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-user-slash"></i></span><div><span class="cr-kpi__valeur">{{ $vacants }}</span><span class="cr-kpi__libelle">Postes vacants (sur {{ $unites }})</span></div></div></a>
        <a href="{{ route('rh.organigramme') }}" style="text-decoration:none;color:inherit;"><div class="cr-kpi" style="--c:#7B6BE0"><span class="cr-kpi__icone"><i class="fa-solid fa-user-tie"></i></span><div><span class="cr-kpi__valeur">{{ $interims }}</span><span class="cr-kpi__libelle">Intérims en cours</span></div></div></a>
        <a href="{{ route('rh.charge', ['surcharge' => 1]) }}" style="text-decoration:none;color:inherit;"><div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-gauge-high"></i></span><div><span class="cr-kpi__valeur">{{ $surcharges }}</span><span class="cr-kpi__libelle">Agents en sur-affectation (≥ {{ $seuil }})</span></div></div></a>
        <a href="{{ route('rh.dossiers') }}" style="text-decoration:none;color:inherit;"><div class="cr-kpi" style="--c:{{ $docs_expires ? '#A73B34' : '#2E7D5B' }}"><span class="cr-kpi__icone"><i class="fa-solid fa-folder-open"></i></span><div><span class="cr-kpi__valeur">{{ $docs_expires + $docs_bientot }}</span><span class="cr-kpi__libelle">Documents à renouveler ({{ $docs_expires }} expiré(s))</span></div></div></a>
      </div>

      <div class="rh-grille" style="margin-bottom:20px;">
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-building"></i> Effectifs par direction</h2></div>
          <div class="rh-barres">@foreach ($s['par_direction'] as $nom => $n)<div class="rh-barres__ligne"><span title="{{ $nom }}">{{ $nom }}</span><span class="rh-barre" style="--c:#2B6CB0"><span style="width: {{ round($n / $maxDir * 100) }}%"></span></span><b>{{ $n }}</b></div>@endforeach</div>
        </div>
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-chart-column"></i> Pyramide des âges</h2>@if ($s['age_moyen'])<span class="cs-aide">Âge moyen : <b>{{ round($s['age_moyen']) }} ans</b></span>@endif</div>
          <div class="rh-pyramide">
            @foreach ($s['pyramide'] as $tranche => $t)
              <div class="rh-pyramide__ligne">
                <div class="rh-pyramide__h"><em>{{ $t['h'] ?: '' }}</em><i style="width: {{ $t['h'] ? max(4, round($t['h'] / $maxPyr * 100)) : 0 }}%"></i></div>
                <span>{{ $tranche }}</span>
                <div class="rh-pyramide__f"><i style="width: {{ $t['f'] ? max(4, round($t['f'] / $maxPyr * 100)) : 0 }}%"></i><em>{{ $t['f'] ?: '' }}</em></div>
              </div>
            @endforeach
            <div class="rh-pyramide__legende"><span><i style="background:#2B6CB0"></i>Hommes</span><span><i style="background:#D9577A"></i>Femmes</span></div>
            @if ($s['sans_date_naissance']) <p class="cs-aide" style="text-align:center;margin:10px 0 0;"><i class="fa-solid fa-circle-info"></i> {{ $s['sans_date_naissance'] }} agent(s) sans date de naissance ne figurent pas dans la pyramide.</p> @endif
          </div>
        </div>
      </div>

      <div class="rh-grille rh-grille--3" style="margin-bottom:20px;">
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-id-badge"></i> Type d'agent</h2></div>
          <div class="rh-barres">@foreach ($s['par_type'] as $nom => $n)<div class="rh-barres__ligne"><span>{{ $nom }}</span><span class="rh-barre" style="--c:#0F8B8D"><span style="width: {{ round($n / $maxType * 100) }}%"></span></span><b>{{ $n }}</b></div>@endforeach</div>
        </div>
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-venus-mars"></i> Genre</h2></div>
          <div class="rh-barres">@foreach ($s['par_genre'] as $nom => $n)<div class="rh-barres__ligne"><span>{{ $nom }}</span><span class="rh-barre" style="--c:#7B6BE0"><span style="width: {{ round($n / $maxGenre * 100) }}%"></span></span><b>{{ $n }}</b></div>@endforeach</div>
        </div>
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clipboard-check"></i> Qualité des fiches</h2></div>
          <div class="rh-donut-bloc">
            <div class="rh-donut" style="--p: {{ $s['completude_moyenne'] }};--c: {{ $s['completude_moyenne'] >= 85 ? '#2E7D5B' : ($s['completude_moyenne'] >= 60 ? '#C77D0A' : '#A73B34') }}"><span>{{ $s['completude_moyenne'] }}%</span></div>
            <div><p>Complétude moyenne</p><strong>{{ $s['fiches_incompletes'] }} fiche(s) à compléter</strong><br><a class="cr-btn cr-btn--clair cr-btn--petit" style="margin-top:10px;" href="{{ route('rh.qualite') }}">Voir la liste</a></div>
          </div>
        </div>
      </div>

      <div class="rh-grille">
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-umbrella-beach"></i> Retraites prévues (36 mois)</h2>@if ($s['anciennete_moyenne'])<span class="cs-aide">Ancienneté moyenne : <b>{{ round($s['anciennete_moyenne'], 1) }} ans</b></span>@endif</div>
          @forelse ($s['retraites']->take(12) as $r)
            <div class="rh-retraite"><span class="rh-agent"><span class="rh-avatar" style="width:32px;height:32px;font-size:11px;">{{ $r['agent']->initiales() }}</span><span><b><a href="{{ route('rh.agent', $r['agent']->id) }}" style="color:inherit;">{{ $r['agent']->nomComplet() }}</a></b></span></span><span class="cr-chip" style="--c: {{ $r['estimee'] ? '#8A94A6' : '#C77D0A' }}" title="{{ $r['estimee'] ? 'Date estimée à partir de la naissance' : 'Date renseignée sur la fiche' }}">{{ $r['date']->format('d/m/Y') }}{{ $r['estimee'] ? ' (est.)' : '' }}</span></div>
          @empty
            <div class="cs-vide"><i class="fa-regular fa-circle-check"></i>Aucun départ à la retraite prévu dans les 36 mois.</div>
          @endforelse
        </div>
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-gauge-high"></i> Agents les plus chargés</h2><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('rh.charge') }}">Tout voir</a></div>
          @forelse ($plusCharges as $l)
            <div class="rh-retraite"><span class="rh-agent"><span class="rh-avatar" style="width:32px;height:32px;font-size:11px;">{{ $l['personnel']->initiales() }}</span><span><b><a href="{{ route('rh.agent', $l['personnel']->id) }}" style="color:inherit;">{{ $l['personnel']->nomComplet() }}</a></b></span></span><span class="cr-chip" style="--c: {{ $l['surcharge'] ? '#A73B34' : '#2B6CB0' }}">{{ $l['charge'] }} élément(s)</span></div>
          @empty
            <div class="cs-vide"><i class="fa-regular fa-circle-check"></i>Aucune charge à signaler.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
