@extends('layout.master')
@section('title', 'Archives du courrier')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--ardoise">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Archives</h1>
          <p class="cr-hero__sous">Les courriers classés, leur n° d'archives et leur durée de conservation. Rien n'est jamais supprimé automatiquement : l'échéance de conservation est une information.</p>
        </div>
      </div>

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-inbox"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['arrivee'] }}</span><span class="cr-kpi__libelle">Arrivée classés</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-paper-plane"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['depart'] }}</span><span class="cr-kpi__libelle">Départ classés</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-tag"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['a_renseigner'] }}</span><span class="cr-kpi__libelle">N° d'archives à renseigner</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-hourglass-end"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['depassees'] }}</span><span class="cr-kpi__libelle">Conservation échue</span></div></div>
      </div>

      <div class="cs-onglets">
        <a class="{{ $sens === 'arrivee' ? 'cs-onglet--actif' : '' }}" href="{{ route('courrier.archives', ['sens' => 'arrivee']) }}"><i class="fa-solid fa-inbox"></i> Courriers arrivée</a>
        <a class="{{ $sens === 'depart' ? 'cs-onglet--actif' : '' }}" href="{{ route('courrier.archives', ['sens' => 'depart']) }}"><i class="fa-solid fa-paper-plane"></i> Courriers départ</a>
      </div>

      @if ($sens === 'arrivee' && $peutClasser && $prets->isNotEmpty())
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-box-open"></i> Traités, prêts à classer</h2><span class="cs-compte">{{ $prets->count() }}</span></div>
          @foreach ($prets as $c)
            <div class="cs-pret">
              <div><span class="cr-num">{{ $c->numero }}</span> <span class="cr-objet-sous" style="display:inline !important;margin-left:8px;">{{ $c->estConfidentiel() ? 'Courrier confidentiel' : \Illuminate\Support\Str::limit($c->objet, 80) }}</span></div>
              <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_classer_ouvrir('{{ route('courrier.archives.courrier', $c->id) }}', 'Classer le courrier {{ $c->numero }}')"><i class="fa-solid fa-box-archive"></i> Classer</button>
            </div>
          @endforeach
        </div>
      @endif

      <form class="cr-carte cr-filtres" method="GET" action="{{ route('courrier.archives') }}">
        <input type="hidden" name="sens" value="{{ $sens }}">
        <div class="cr-filtres__grille" style="grid-template-columns: 2fr 1.2fr auto;">
          <div class="cr-recherche"><label for="f_q">Recherche</label><i class="fa-solid fa-magnifying-glass" style="top:calc(50% + 11px)"></i><input class="form-control form-control-sm" id="f_q" type="search" name="q" value="{{ request('q') }}" placeholder="N°, {{ $sens === 'depart' ? 'destinataire' : 'expéditeur' }}, objet, n° d'archives…"></div>
          <div>
            <label for="f_cons">Conservation</label>
            <select class="form-select form-select-sm" id="f_cons" name="conservation">
              <option value="">Toutes</option>
              <option value="a_renseigner" @selected(request('conservation') === 'a_renseigner')>N° d'archives à renseigner</option>
              <option value="depassee" @selected(request('conservation') === 'depassee')>Échéance dépassée</option>
              <option value="proche" @selected(request('conservation') === 'proche')>Échéance sous 12 mois</option>
            </select>
          </div>
          <div style="display:flex;gap:8px;"><button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>@if (request()->hasAny(['q', 'conservation']))<a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.archives', ['sens' => $sens]) }}">Réinitialiser</a>@endif</div>
        </div>
      </form>

      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-box-archive"></i> {{ $lignes->total() }} courrier(s) classé(s)</h2></div>
        @if ($lignes->isEmpty())
          <div class="cr-vide"><i class="fa-solid fa-box-archive"></i><strong>Aucun courrier classé</strong>Les courriers classés apparaissent ici avec leur n° d'archives.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>N°</th><th>{{ $sens === 'depart' ? 'Destinataire et objet' : 'Expéditeur et objet' }}</th><th>Classé le</th><th>N° d'archives</th><th>Conservation</th>@if ($peutClasser)<th></th>@endif</tr></thead>
              <tbody>
                @foreach ($lignes as $l)
                  @php
                    $url = $sens === 'depart' ? route('courrier.depart.show', $l->id) : route('courrier.show', $l->id);
                    $action = $sens === 'depart' ? route('courrier.archives.depart', $l->id) : route('courrier.archives.courrier', $l->id);
                    $cache = $l->estConfidentiel();
                    $conservation = $l->a_conserver_jusqu_au;
                    $classe = $conservation ? ($conservation->lt(today()) ? 'cs-conservation--depassee' : ($conservation->lt(today()->addYear()) ? 'cs-conservation--proche' : '')) : '';
                  @endphp
                  <tr onclick="window.location='{{ $url }}'">
                    <td><span class="cr-num">{{ $l->numero }}</span></td>
                    <td><span class="cr-objet">{{ $sens === 'depart' ? $l->destinataire : $l->expediteur }}</span><span class="cr-objet-sous">{{ $cache ? 'Courrier confidentiel' : \Illuminate\Support\Str::limit($l->objet, 80) }}</span></td>
                    <td>{{ optional($l->archive_le)->format('d/m/Y') ?? '—' }}</td>
                    <td>@if ($l->numero_archives)<span class="cs-boite"><i class="fa-solid fa-box-archive"></i>{{ $l->numero_archives }}</span>@else<span class="cs-boite cs-boite--vide">À renseigner</span>@endif</td>
                    <td>@if ($conservation)<span class="cs-conservation {{ $classe }}"><i class="fa-regular fa-calendar"></i> {{ $conservation->format('d/m/Y') }}@if ($classe === 'cs-conservation--depassee') · échue @endif</span>@else — @endif</td>
                    @if ($peutClasser)<td onclick="event.stopPropagation()"><button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="cs_classer_ouvrir('{{ $action }}', 'Archivage de {{ $l->numero }}', @js($l->numero_archives), @js(optional($conservation)->toDateString()))"><i class="fa-regular fa-pen-to-square"></i></button></td>@endif
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="cr-pagination">{{ $lignes->links() }}</div>
        @endif
      </div>
    </div>
    @if ($peutClasser) @include('courrier._classer') @endif
  </div>
@endsection
