@extends('layout.master')
@section('title', 'Registre du courrier départ')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--vert">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Registre des courriers - Départ</h1>
          <p class="cr-hero__sous">Chaque courrier expédié reçoit un numéro par destinataire, comme dans le registre papier, avec le lien vers le courrier d'origine et son archivage.</p>
        </div>
        <div class="cr-hero__actions">
          @if ($peutEnregistrer)<a class="cr-btn cr-btn--blanc" href="{{ route('courrier.depart.create') }}"><i class="fa-solid fa-plus"></i> Nouveau courrier départ</a>@endif
          <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.depart.export', request()->query()) }}"><i class="fa-solid fa-file-excel"></i> Exporter</a>
        </div>
      </div>

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-calendar-day"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['mois'] }}</span><span class="cr-kpi__libelle">Expédiés ce mois</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-paper-plane"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['annee'] }}</span><span class="cr-kpi__libelle">Expédiés en {{ now()->year }}</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-box-open"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['a_classer'] }}</span><span class="cr-kpi__libelle">À classer</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-lock"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['confidentiels'] }}</span><span class="cr-kpi__libelle">Confidentiels</span></div></div>
      </div>

      <form class="cr-carte cr-filtres" method="GET" action="{{ route('courrier.depart') }}">
        <div class="cr-filtres__grille" style="grid-template-columns: 2fr 1fr 1fr 1fr auto;">
          <div class="cr-recherche"><label for="f_q">Recherche</label><i class="fa-solid fa-magnifying-glass" style="top:calc(50% + 11px)"></i><input class="form-control form-control-sm" id="f_q" type="search" name="q" value="{{ request('q') }}" placeholder="N°, destinataire, objet, n° d'archives…"></div>
          <div><label for="f_du">Expédié du</label><input class="form-control form-control-sm" id="f_du" type="date" name="du" value="{{ request('du') }}"></div>
          <div><label for="f_au">Au</label><input class="form-control form-control-sm" id="f_au" type="date" name="au" value="{{ request('au') }}"></div>
          <div>
            <label for="f_st">Statut</label>
            <select class="form-select form-select-sm" id="f_st" name="statut"><option value="">Tous</option>@foreach (\App\Models\CourrierDepart::STATUTS as $cle => $s)<option value="{{ $cle }}" @selected(request('statut') === $cle)>{{ $s[0] }}</option>@endforeach</select>
          </div>
          <div style="display:flex;gap:8px;"><button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>@if (request()->hasAny(['q', 'du', 'au', 'statut']))<a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.depart') }}">Réinitialiser</a>@endif</div>
        </div>
      </form>

      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-paper-plane"></i> {{ $departs->total() }} courrier(s) départ</h2></div>
        @if ($departs->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-paper-plane"></i><strong>Aucun courrier départ</strong>Enregistrez le premier courrier expédié, ou signez une réponse.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>N°</th><th>Départ le</th><th>Destinataire et objet</th><th>Origine</th><th>Pièces</th><th>Statut</th></tr></thead>
              <tbody>
                @foreach ($departs as $d)
                  <tr onclick="window.location='{{ route('courrier.depart.show', $d->id) }}'">
                    <td><span class="cr-num">{{ $d->numero }}</span></td>
                    <td style="white-space:nowrap;">{{ $d->date_depart->format('d/m/Y') }}</td>
                    <td><span class="cr-objet">{{ $d->destinataire }}</span><span class="cr-objet-sous">{{ $d->estConfidentiel() ? 'Courrier confidentiel' : \Illuminate\Support\Str::limit($d->objet, 80) }}</span></td>
                    <td>@if ($d->courrier)<span class="cr-num" style="background:rgba(15,139,141,.1);color:#0b6466;">{{ $d->courrier->numero }}</span>@else<span class="c-o-light">—</span>@endif</td>
                    <td>{{ $d->nombre_pieces }} <span class="cr-icones">@if ($d->estConfidentiel())<i class="fa-solid fa-lock" title="Confidentiel"></i>@endif</span></td>
                    <td><span class="cr-chip" style="--c: {{ $d->couleurStatut() }}">{{ $d->libelleStatut() }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="cr-pagination">{{ $departs->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
