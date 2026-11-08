@extends('layout.master')
@section('title', 'Historique des synthèses')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pilotage.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--rose">
        <div><p class="cr-hero__sur"><a href="{{ route('pilotage.index', ['exercice_id' => $exercice->id]) }}"><i class="fa-solid fa-arrow-left"></i> Pilotage et analyses</a></p><h1 class="cr-hero__titre">Historique des synthèses</h1><p class="cr-hero__sous">Toutes les synthèses conservées pour l'exercice {{ $exercice->annee }} : rédigées par l'IA, par les modèles de phrases ou corrigées à la main.</p></div>
        <form method="GET" action="{{ route('pilotage.historique') }}" class="cr-hero__actions"><select class="form-select form-select-sm" name="exercice_id" onchange="this.form.submit()" style="width:auto;">@foreach ($exercices as $e)<option value="{{ $e->id }}" @selected($e->id === $exercice->id)>Exercice {{ $e->annee }}</option>@endforeach</select></form>
      </div>
      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clock-rotate-left"></i> {{ $lignes->total() }} synthèse(s)</h2></div>
        @if ($lignes->isEmpty())<div class="cr-vide"><i class="fa-regular fa-file-lines"></i><strong>Aucune synthèse</strong>Générez la première depuis « Pilotage et analyses ».</div>@else
          <div class="overflow-x-auto"><table class="cr-table">
            <thead><tr><th>Date</th><th>Périmètre</th><th>Période</th><th>Source</th><th>Auteur</th><th>Extrait</th><th></th></tr></thead>
            <tbody>@foreach ($lignes as $h)
              @php $url = route('pilotage.index', ['exercice_id' => $h->exercice_id, 'type' => $h->periode_type, 'valeur' => $h->periode_valeur, 'portee' => $h->portee_type === 'global' ? 'global' : $h->portee_type.':'.$h->portee_id, 'synthese_id' => $h->id]); @endphp
              <tr onclick="window.location='{{ $url }}'">
                <td style="white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                <td>@if ($h->portee_type === 'axe'){{ $axes[$h->portee_id] ?? 'Axe' }}@elseif ($h->portee_type === 'direction'){{ $directions[$h->portee_id] ?? 'Direction' }}@else Toute la structure @endif</td>
                <td>{{ \App\Support\AnalysesPilotage::TYPES[$h->periode_type] ?? $h->periode_type }}{{ $h->periode_valeur ? ' '.$h->periode_valeur : '' }}</td>
                <td><span class="cr-chip" style="--c: {{ $h->couleurSource() }}">{{ $h->libelleSource() }}</span></td>
                <td>{{ $h->auteur?->name ?? 'Automatique' }}</td>
                <td>{{ \Illuminate\Support\Str::limit(trim(preg_replace('/^#+.*$/m', '', $h->contenu)), 110) }}</td>
                <td><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ $url }}">Ouvrir</a></td>
              </tr>
            @endforeach</tbody>
          </table></div>
          <div class="cr-pagination">{{ $lignes->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
