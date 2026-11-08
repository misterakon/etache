@extends('layout.master')
@section('title', 'Agents archivés')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--ardoise">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Agents archivés</h1>
          <p class="cr-hero__sous">Aucune fiche n'est jamais supprimée : un agent qui quitte la structure est archivé, avec son motif et tout son historique.</p>
        </div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <form class="cr-carte cr-filtres" method="GET" action="{{ route('rh.archives') }}">
        <div class="cr-filtres__grille" style="grid-template-columns: 1fr auto;">
          <div class="cr-recherche"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control form-control-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, prénom, matricule…"></div>
          <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-filter"></i> Rechercher</button>
        </div>
      </form>
      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-box-archive"></i> {{ $agents->total() }} agent(s) archivé(s)</h2></div>
        @if ($agents->isEmpty())
          <div class="cr-vide"><i class="fa-solid fa-box-archive"></i><strong>Aucun agent archivé</strong>Les agents archivés apparaissent ici.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Agent</th><th>Service</th><th>Archivé le</th><th>Motif</th><th>Compte</th>@if ($peutRestaurer)<th></th>@endif</tr></thead>
              <tbody>
                @foreach ($agents as $a)
                  <tr onclick="window.location='{{ route('dgepn.personnel.fiche', $a->id) }}'">
                    <td><div class="rh-agent"><span class="rh-avatar" style="background:#8A94A6;">{{ $a->initiales() }}</span><span><b>{{ $a->nomComplet() }}</b><small>{{ $a->matricule ?: 'Sans matricule' }}</small></span></div></td>
                    <td>{{ $a->service?->nom ?? '—' }}</td>
                    <td style="white-space:nowrap;">{{ $a->archive_le?->format('d/m/Y') }}</td>
                    <td>{{ $a->archive_motif ?: '—' }}</td>
                    <td>@if ($a->user)<span class="cr-chip" style="--c: {{ $a->user->actif ? '#C77D0A' : '#8A94A6' }}">{{ $a->user->actif ? 'Encore actif' : 'Désactivé' }}</span>@else<span class="c-o-light">—</span>@endif</td>
                    @if ($peutRestaurer)<td onclick="event.stopPropagation()"><form method="post" action="{{ route('rh.restaurer', $a->id) }}" onsubmit="return cs_confirmer(this, 'Restaurer cet agent ?')">@csrf<button class="cr-btn cr-btn--clair cr-btn--petit" type="submit"><i class="fa-solid fa-rotate-left"></i> Restaurer</button></form></td>@endif
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="cr-pagination">{{ $agents->links() }}</div>
        @endif
      </div>
      <script type="text/javascript">
		function cs_confirmer(form, texte) {
			Swal.fire({ title: 'e-Tâche', text: texte, icon: 'question', showCancelButton: true, confirmButtonText: 'Oui', cancelButtonText: 'Annuler' }).then(function (r) { if (r.isConfirmed) { form.submit(); } });
			return false;
		}
      </script>
    </div>
  </div>
@endsection
