@extends('layout.master')
@section('title', 'Validité des dossiers')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      @php $etats = ['expire' => ['Expiré', '#A73B34'], 'bientot' => ['Bientôt expiré', '#C77D0A'], 'valide' => ['Valide', '#2E7D5B'], 'sans_date' => ['Sans date de validité', '#8A94A6']]; @endphp
      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Validité des dossiers</h1>
          <p class="cr-hero__sous">Les documents des dossiers (CNI, diplômes, attestations…) avec leur fin de validité. L'agent et l'administrateur sont alertés {{ $jours }} jours avant l'échéance, puis à l'expiration.</p>
        </div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <div class="cr-carte cr-filtres">
        <div class="cr-pilules">
          <a class="cr-pilule {{ request('etat') ? '' : 'cr-pilule--active' }}" href="{{ route('rh.dossiers') }}">Tous ({{ $compteurs->sum() }})</a>
          @foreach ($etats as $cle => [$lib])<a class="cr-pilule {{ request('etat') === $cle ? 'cr-pilule--active' : '' }}" href="{{ route('rh.dossiers', ['etat' => $cle]) }}">{{ $lib }} ({{ $compteurs[$cle] ?? 0 }})</a>@endforeach
        </div>
      </div>
      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-folder-open"></i> {{ $lignes->count() }} document(s)</h2></div>
        @if ($lignes->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-folder-open"></i><strong>Aucun document</strong>Les documents déposés dans les dossiers des agents apparaissent ici.</div>
        @else
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Agent</th><th>Document</th><th>Date du document</th><th>Valide jusqu'au</th><th>État</th>@if ($peutModifier)<th></th>@endif</tr></thead>
              <tbody>
                @foreach ($lignes as $l)
                  <tr style="cursor:default;">
                    <td><div class="rh-agent"><span class="rh-avatar">{{ $l['agent']->initiales() }}</span><span><b><a href="{{ route('rh.agent', $l['agent']->id) }}" style="color:inherit;">{{ $l['agent']->nomComplet() }}</a></b><small>{{ $l['agent']->matricule }}</small></span></div></td>
                    <td><b>{{ $l['type'] }}</b><span class="cr-objet-sous"><a href="{{ route('dgepn.personnel.dossier.apercu', $l['piece']->id) }}" target="_blank" rel="noopener">{{ $l['piece']->nom_original }}</a></span></td>
                    <td>{{ $l['info']->date_document?->format('d/m/Y') ?? '—' }}</td>
                    <td style="white-space:nowrap;">{{ $l['info']->valide_jusqu_au?->format('d/m/Y') ?? '—' }}</td>
                    <td><span class="cr-chip" style="--c: {{ $etats[$l['etat']][1] }}">{{ $etats[$l['etat']][0] }}</span></td>
                    @if ($peutModifier)<td><button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick='rh_dates({{ $l['piece']->id }}, @js($l['info']->date_document?->toDateString()), @js($l['info']->valide_jusqu_au?->toDateString()), @js($l['info']->observation))'><i class="fa-regular fa-calendar"></i> Dates</button></td>@endif
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
      @if ($peutModifier)
        <div class="modal fade panel-modal cs-modal" id="modal_rh_dates" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
          <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h4 class="modal-title"><i class="fa-regular fa-calendar"></i> Validité du document</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form id="form_rh_dates" method="post" action="#">
              @csrf
              <div class="modal-body dark-modal">
                <div class="cr-champs-2">
                  <div class="cs-champ"><label for="rh_d_doc">Date du document</label><input class="form-control form-control-sm" id="rh_d_doc" type="date" name="date_document" max="{{ today()->toDateString() }}"></div>
                  <div class="cs-champ"><label for="rh_d_fin">Valide jusqu'au</label><input class="form-control form-control-sm" id="rh_d_fin" type="date" name="valide_jusqu_au"></div>
                </div>
                <div class="cs-champ"><label for="rh_d_obs">Observation <span class="cs-opt">(facultatif)</span></label><input class="form-control form-control-sm" id="rh_d_obs" type="text" name="observation" maxlength="255"></div>
                <div class="cs-info"><i class="fa-solid fa-circle-info"></i><div>Laissez « Valide jusqu'au » vide pour un document sans échéance (acte de naissance, diplôme…).</div></div>
              </div>
              <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Enregistrer</button></div>
            </form>
          </div></div>
        </div>
        <script type="text/javascript">
		function rh_dates(id, doc, fin, obs) {
			const m = document.getElementById('modal_rh_dates'); if (m.parentElement !== document.body) { document.body.appendChild(m); }
			document.getElementById('form_rh_dates').action = "{{ route('rh.dossiers.dates', ':id') }}".replace(':id', id);
			document.getElementById('rh_d_doc').value = doc || ''; document.getElementById('rh_d_fin').value = fin || ''; document.getElementById('rh_d_obs').value = obs || '';
			$('#modal_rh_dates').modal('show');
		}
        </script>
      @endif
    </div>
  </div>
@endsection
