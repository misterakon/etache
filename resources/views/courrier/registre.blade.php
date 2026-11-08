@extends('layout.master')
@section('title', 'Registre du courrier arrivée')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      {{-- Bandeau --}}
      <div class="cr-hero">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Registre des courriers - Arrivée</h1>
          <p class="cr-hero__sous">Enregistrez chaque courrier reçu, joignez son scan et retrouvez-le à tout moment, avec son numéro unique.</p>
        </div>
        <div class="cr-hero__actions">
          @if ($peutEnregistrer)
            <button type="button" class="cr-btn cr-btn--blanc" onclick="cr_nouveau()"><i class="fa-solid fa-plus"></i> Nouveau courrier</button>
          @endif
          <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.export', request()->query()) }}"><i class="fa-solid fa-file-excel"></i> Exporter</a>
        </div>
      </div>

      {{-- Indicateurs --}}
      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-calendar-day"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['mois'] }}</span><span class="cr-kpi__libelle">Reçus ce mois</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-inbox"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['annee'] }}</span><span class="cr-kpi__libelle">Reçus en {{ now()->year }}</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-hourglass-half"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['a_imputer'] }}</span><span class="cr-kpi__libelle">À imputer</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-lock"></i></span><div><span class="cr-kpi__valeur">{{ $kpis['confidentiels'] }}</span><span class="cr-kpi__libelle">Confidentiels</span></div></div>
      </div>

      {{-- Filtres --}}
      <form class="cr-carte cr-filtres" method="GET" action="{{ route('courrier.arrivee') }}">
        <div class="cr-filtres__grille">
          <div class="cr-recherche">
            <label for="f_q">Recherche</label>
            <i class="fa-solid fa-magnifying-glass" style="top:calc(50% + 11px)"></i>
            <input class="form-control form-control-sm" id="f_q" type="search" name="q" value="{{ request('q') }}" placeholder="N°, expéditeur, objet, référence…">
          </div>
          <div><label for="f_du">Reçu du</label><input class="form-control form-control-sm" id="f_du" type="date" name="du" value="{{ request('du') }}"></div>
          <div><label for="f_au">Au</label><input class="form-control form-control-sm" id="f_au" type="date" name="au" value="{{ request('au') }}"></div>
          <div>
            <label for="f_type">Type</label>
            <select class="form-select form-select-sm" id="f_type" name="type_id">
              <option value="">Tous</option>
              @foreach ($types as $t) <option value="{{ $t->id }}" @selected((string) request('type_id') === (string) $t->id)>{{ $t->libelle }}</option> @endforeach
            </select>
          </div>
          <div>
            <label for="f_urg">Urgence</label>
            <select class="form-select form-select-sm" id="f_urg" name="urgence_id">
              <option value="">Toutes</option>
              @foreach ($urgences as $u) <option value="{{ $u->id }}" @selected((string) request('urgence_id') === (string) $u->id)>{{ $u->libelle }}</option> @endforeach
            </select>
          </div>
          <div>
            <label for="f_statut">Statut</label>
            <select class="form-select form-select-sm" id="f_statut" name="statut">
              <option value="">Tous</option>
              @foreach (\App\Models\Courrier::STATUTS as $cle => $s) <option value="{{ $cle }}" @selected(request('statut') === $cle)>{{ $s[0] }}</option> @endforeach
            </select>
          </div>
        </div>
        <div class="cr-filtres__pied">
          <label style="display:flex;align-items:center;gap:8px;margin:0;text-transform:none;letter-spacing:0;font-size:13.5px;cursor:pointer;">
            <input type="checkbox" class="form-check-input" name="confidentiel" value="1" @checked(request("confidentiel") === "1")> Confidentiels seulement
          </label>
          <div style="display:flex;gap:8px;">
            <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.arrivee') }}">Réinitialiser</a>
            <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
          </div>
        </div>
      </form>

      {{-- Registre --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-book-open"></i> {{ $courriers->total() }} courrier(s)</h2>
        </div>
        @if ($courriers->isEmpty())
          <div class="cr-vide">
            <i class="fa-regular fa-envelope-open"></i>
            <strong>Aucun courrier à afficher</strong>
            {{ request()->query() ? 'Aucun courrier ne correspond à ces filtres.' : 'Le registre est vide : enregistrez le premier courrier arrivée.' }}
          </div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead>
                <tr><th>N°</th><th>Reçu le</th><th>Expéditeur</th><th>Objet</th><th>Type</th><th>Urgence</th><th></th><th>Statut</th></tr>
              </thead>
              <tbody>
                @foreach ($courriers as $c)
                  <tr onclick="window.location='{{ route('courrier.show', $c->id) }}'">
                    <td><span class="cr-num">{{ $c->numero }}</span></td>
                    <td style="white-space:nowrap;">{{ $c->date_reception->format('d/m/Y') }}</td>
                    <td>{{ $c->expediteur }}</td>
                    <td>
                      <span class="cr-objet">{{ \Illuminate\Support\Str::limit($c->objet, 120) }}</span>
                      @if ($c->reference_courrier) <span class="cr-objet-sous">Réf. {{ $c->reference_courrier }}@if ($c->date_courrier) · du {{ $c->date_courrier->format('d/m/Y') }}@endif</span> @endif
                    </td>
                    <td>{{ $c->type?->libelle }}</td>
                    <td>@if ($c->urgence) <span class="cr-chip" style="--c: {{ $c->urgence->couleur }}">{{ $c->urgence->libelle }}</span> @endif</td>
                    <td>
                      <span class="cr-icones">
                        @if ($c->estConfidentiel()) <i class="fa-solid fa-lock" title="Confidentiel"></i> @endif
                        @if ($c->scans->isNotEmpty()) <i class="fa-solid fa-paperclip" title="Scan joint"></i> @endif
                      </span>
                    </td>
                    <td><span class="cr-chip" style="--c: {{ $c->couleurStatut() }}">{{ $c->libelleStatut() }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="cr-pagination">{{ $courriers->links() }}</div>
        @endif
      </div>
    </div>

    @if ($peutEnregistrer)
      @include('courrier.form_courrier')
    @endif
    @include('courrier.js_courrier')
  </div>
@endsection
