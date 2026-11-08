@extends('layout.master')
@section('title', 'Pilotage')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/cockpit.css') }}?v={{ filemtime(public_path('assets/css/cockpit.css')) }}">
@endsection

@section('main-content')
@php
  use App\Support\Cockpit;
  use App\Support\AnalysesPilotage;
  $periode = $c['periode'];
  $portee = $c['porteeBrut'];
  $pctEnRetard = $kpis['taches'] ? (int) round($kpis['retards'] / $kpis['taches'] * 100) : 0;
  $kpiCartes = [
      ['cle' => 'taches', 'libelle' => 'Tâches suivies', 'icone' => 'fa-list-check', 'ton' => 'bleu', 'sous' => $kpis['non_demarrees'].' pas encore démarrée(s)', 'onglet' => 'taches', 'filtre' => 'toutes'],
      ['cle' => 'avancement', 'libelle' => 'Avancement moyen', 'icone' => 'fa-gauge-high', 'ton' => 'orange', 'sous' => 'Toutes tâches confondues', 'suffixe' => ' %', 'barre' => true],
      ['cle' => 'taux', 'libelle' => 'Taux de réalisation', 'icone' => 'fa-circle-check', 'ton' => 'vert', 'sous' => 'Tâches terminées', 'suffixe' => ' %', 'onglet' => 'taches', 'filtre' => 'terminees'],
      ['cle' => 'retards', 'libelle' => 'En retard', 'icone' => 'fa-triangle-exclamation', 'ton' => 'rouge', 'sous' => $kpis['non_justifies'].' sans justification', 'onglet' => 'taches', 'filtre' => 'retards'],
      $kpis['diligences_retard'] !== null
        ? ['cle' => 'diligences_retard', 'libelle' => 'Diligences en retard', 'icone' => 'fa-envelope-open-text', 'ton' => 'violet', 'sous' => $kpis['diligences_ouvertes'].' diligence(s) ouverte(s)', 'onglet' => 'courrier']
        : ['cle' => 'echeances', 'libelle' => 'Échéances à 4 semaines', 'icone' => 'fa-calendar-days', 'ton' => 'violet', 'sous' => 'Tâches à surveiller', 'onglet' => 'taches', 'filtre' => 'proches'],
  ];
@endphp
<div class="page-body">
  <div class="container"><x-titre-page titre="Pilotage" :fil="['Feuille de route', 'Pilotage']" /></div>
  <div class="container ck-page" id="ck">

    {{-- ===== En-tête : titre + filtres ===== --}}
    <header class="ck-entete">
      <div class="ck-entete__titre">
        <h1>Où en sommes-nous ?</h1>
        <p id="ck_contexte">{{ $restreint ? 'Vos directions' : 'Toute la structure' }} · {{ $periode['libelle'] }}</p>
      </div>
      <form class="ck-filtres" id="ck_filtres" onsubmit="return false">
        <label class="ck-champ"><span>Exercice</span>
          <select name="exercice_id" id="ck_ex">
            @foreach ($c['exercices'] as $e)<option value="{{ $e->id }}" @selected($e->id === $c['exercice']->id)>{{ $e->annee }}</option>@endforeach
          </select>
        </label>
        <label class="ck-champ"><span>Période</span>
          <select name="type" id="ck_type">
            @foreach (AnalysesPilotage::TYPES as $k => $l)<option value="{{ $k }}" @selected($periode['type'] === $k)>{{ $l }}</option>@endforeach
          </select>
        </label>
        <label class="ck-champ" id="ck_valeur_bloc"><span id="ck_valeur_lib">Mois</span>
          <select name="valeur" id="ck_valeur" data-valeur="{{ $periode['valeur'] }}"></select>
        </label>
        <label class="ck-champ ck-champ--large"><span>Périmètre</span>
          <select name="portee" id="ck_portee">
            <option value="global" @selected($portee === 'global')>{{ $restreint ? 'Toutes mes directions' : 'Toute la structure' }}</option>
            <optgroup label="Directions">
              @foreach ($directions as $dr)<option value="direction:{{ $dr->id }}" @selected($portee === 'direction:'.$dr->id)>{{ $dr->nom }}</option>@endforeach
            </optgroup>
            <optgroup label="Axes stratégiques">
              @foreach ($axes as $ax)<option value="axe:{{ $ax->id }}" @selected($portee === 'axe:'.$ax->id)>{{ $ax->code }} · {{ $ax->libelle }}</option>@endforeach
            </optgroup>
          </select>
        </label>
        <button type="button" class="ck-btn" id="ck_actualiser" title="Actualiser les chiffres"><i class="fa-solid fa-rotate"></i><span>Actualiser</span></button>
      </form>
    </header>

    {{-- ===== Indicateurs (fond dégradé du bloc Budget) ===== --}}
    <section class="ck-kpis" id="ck_kpis" aria-label="Indicateurs clés">
      @foreach ($kpiCartes as $k)
        <button type="button" class="ck-kpi ck-kpi--{{ $k['ton'] }}" data-kpi="{{ $k['cle'] }}" @isset($k['onglet']) data-ck-onglet="{{ $k['onglet'] }}" @endisset @isset($k['filtre']) data-ck-filtre="{{ $k['filtre'] }}" @endisset>
          <span class="ck-kpi__ico"><i class="fa-solid {{ $k['icone'] }}"></i></span>
          <span class="ck-kpi__corps">
            <span class="ck-kpi__lib">{{ $k['libelle'] }}</span>
            <strong class="ck-kpi__val"><span data-compteur="{{ $kpis[$k['cle']] ?? 0 }}" data-decimales="{{ in_array($k['cle'], ['avancement', 'taux'], true) ? 1 : 0 }}">{{ $kpis[$k['cle']] ?? 0 }}</span>{{ $k['suffixe'] ?? '' }}</strong>
            <span class="ck-kpi__sous" data-kpi-sous>{{ $k['sous'] }}</span>
            @if (! empty($k['barre']))<span class="ck-kpi__barre"><i style="width: {{ min(100, $kpis['avancement']) }}%"></i></span>@endif
          </span>
        </button>
      @endforeach
    </section>

    {{-- ===== Onglets ===== --}}
    <nav class="ck-onglets" role="tablist" aria-label="Sections du pilotage">
      @foreach (Cockpit::ONGLETS as $cle => [$lib, $ico])
        <button type="button" role="tab" class="ck-onglet {{ $cle === $onglet ? 'actif' : '' }}" data-onglet="{{ $cle }}" aria-selected="{{ $cle === $onglet ? 'true' : 'false' }}" id="ck_tab_{{ $cle }}">
          <i class="fa-solid {{ $ico }}"></i><span>{{ $lib }}</span>
          @if ($cle === 'taches')<em class="ck-onglet__nb" data-nb-retards>{{ $kpis['retards'] }}</em>@endif
        </button>
      @endforeach
    </nav>

    <div class="ck-panneau" id="ck_panneau" role="tabpanel" aria-live="polite" aria-busy="true">
      <div class="ck-squelette"><i></i><i></i><i></i></div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script type="application/json" id="ck_config">{!! json_encode([
      'onglet' => $onglet, 'syntheseId' => $syntheseId ?? null, 'parametres' => $parametres, 'url' => route('cockpit.index'), 'urlOnglet' => route('cockpit.onglet', ['onglet' => '__O__']),
      'urlSynthese' => route('pilotage.synthese'), 'jeton' => csrf_token(), 'moisNoms' => array_values(AnalysesPilotage::MOIS),
      'onglets' => array_map(fn ($o) => $o[0], Cockpit::ONGLETS),
  ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
  <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('assets/js/cockpit.js') }}?v={{ filemtime(public_path('assets/js/cockpit.js')) }}"></script>
@endsection
