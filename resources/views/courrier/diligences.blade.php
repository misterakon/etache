@extends('layout.master')
@section('title', 'Diligences du courrier')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--violet">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Tableau des diligences</h1>
          <p class="cr-hero__sous">Le travail demandé à chaque destinataire pour les courriers imputés : ce qui est en cours, en retard ou traité.</p>
        </div>
        <div class="cr-hero__actions">
          <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.diligences.export', request()->query()) }}"><i class="fa-solid fa-file-excel"></i> Exporter</a>
        </div>
      </div>

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-list-check"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['ouvertes'] }}</span><span class="cr-kpi__libelle">Diligences ouvertes</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-hand-holding"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['a_prendre'] }}</span><span class="cr-kpi__libelle">À prendre en charge</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['en_retard'] }}</span><span class="cr-kpi__libelle">En retard</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-circle-check"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['soldees_mois'] }}</span><span class="cr-kpi__libelle">Traitées ce mois</span></div></div>
      </div>

      <div class="cr-carte cr-filtres">
        <div class="cr-pilules">
          @foreach (['ouvertes' => 'Ouvertes', 'a_prendre_en_charge' => 'À prendre en charge', 'en_cours' => 'En cours', 'en_retard' => 'En retard', 'solde' => 'Traitées', 'annule' => 'Annulées', 'toutes' => 'Toutes'] as $cle => $libelle)
            <a class="cr-pilule {{ $statut === $cle ? 'cr-pilule--active' : '' }}" href="{{ route('courrier.diligences', array_merge(request()->except('statut', 'page'), ['statut' => $cle])) }}">{{ $libelle }}</a>
          @endforeach
        </div>
        <form method="GET" action="{{ route('courrier.diligences') }}" style="margin-top:14px;">
          <input type="hidden" name="statut" value="{{ $statut }}">
          <div class="cr-filtres__grille" style="grid-template-columns: 2fr 1.4fr 1fr 1fr auto;">
            <div class="cr-recherche"><label for="f_q">Recherche</label><i class="fa-solid fa-magnifying-glass" style="top:calc(50% + 11px)"></i><input class="form-control form-control-sm" id="f_q" type="search" name="q" value="{{ request('q') }}" placeholder="Code, courrier, expéditeur, objet, destinataire…"></div>
            <div>
              <label for="f_dir">Direction</label>
              <select class="form-select form-select-sm" id="f_dir" name="direction_id">
                <option value="">Toutes</option>
                @foreach ($directions as $dir) <option value="{{ $dir->id }}" @selected((string) request('direction_id') === (string) $dir->id)>{{ $dir->nom }}</option> @endforeach
              </select>
            </div>
            <div><label for="f_du">Imputées du</label><input class="form-control form-control-sm" id="f_du" type="date" name="du" value="{{ request('du') }}"></div>
            <div><label for="f_au">Au</label><input class="form-control form-control-sm" id="f_au" type="date" name="au" value="{{ request('au') }}"></div>
            <div style="display:flex;gap:8px;"><button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>@if (request()->hasAny(['q', 'direction_id', 'du', 'au']))<a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligences', ['statut' => $statut]) }}">Réinitialiser</a>@endif</div>
          </div>
        </form>
      </div>

      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-list-check"></i> {{ $diligences->total() }} diligence(s)</h2></div>
        @if ($diligences->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Aucune diligence à afficher</strong>Aucune diligence ne correspond à ce filtre.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead>
                <tr>
                  <th>Diligence</th>
                  <th>Courrier</th>
                  <th>Destinataire</th>
                  <th>Entre les mains de</th>
                  <th>Rôle</th>
                  <th>Échéance</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($diligences as $d)
                  @php
                    $suivi = $d->suivi();
                    $retard = ! in_array($d->statut, ['solde', 'annule'], true) && $suivi['echeance']->lt(today());
                  @endphp
                  <tr onclick="window.location='{{ route('courrier.diligence.show', $d->id) }}'">
                    <td><span class="cr-num">{{ $d->code }}</span></td>
                    <td><span class="cr-objet">{{ $d->courrier->numero }}</span><span class="cr-objet-sous">{{ $d->courrier->estConfidentiel() ? 'Courrier confidentiel' : \Illuminate\Support\Str::limit($d->courrier->objet, 70) }}</span></td>
                    <td>{{ $d->destinataire_libelle }}</td>
                    <td>
                      {{ $suivi['bout']->responsable?->nomComplet() ?? $suivi['bout']->destinataire_libelle }}
                      @if ($suivi['bout']->id !== $d->id)<span class="cr-objet-sous">via {{ $d->destinataire_libelle }}</span>@endif
                    </td>
                    <td>{{ $d->libelleRole() }}</td>
                    <td style="white-space:nowrap;{{ $retard ? 'color:#A73B34;font-weight:700;' : '' }}">{{ $suivi['echeance']->format('d/m/Y') }}</td>
                    <td><span class="cr-chip" style="--c: {{ $d->couleurStatut() }}">{{ $d->libelleStatut() }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="cr-pagination">{{ $diligences->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
