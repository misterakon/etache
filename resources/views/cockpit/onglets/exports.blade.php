@php
  use App\Support\Cockpit;
  use App\Support\ExportTaches;
  $paramsExport = ['exercice_id' => $c['exercice']->id] + ($c['porteeType'] === 'axe' ? ['axe_id' => $c['porteeId']] : ($c['porteeType'] === 'direction' ? ['direction_id' => $c['porteeId']] : []));
  $types = [
      'en_retard' => ['fa-triangle-exclamation', 'rouge'], 'non_justifies' => ['fa-circle-question', 'ambre'], 'a_venir' => ['fa-calendar-day', 'orange'],
      'en_cours' => ['fa-bars-progress', 'bleu'], 'terminees' => ['fa-circle-check', 'vert'], 'sans_responsable' => ['fa-user-slash', 'sarcelle'], 'toutes' => ['fa-list', 'neutre'],
  ];
@endphp
<p class="ck-info ck-info--haut"><i class="fa-solid fa-circle-info"></i> Les exports tiennent compte du périmètre et de l'exercice choisis en haut de page.</p>

<div class="ck-exports">
  @foreach ($types as $type => [$ico, $ton])
    <article class="ck-export ck-export--{{ $ton }}">
      <span class="ck-export__ico"><i class="fa-solid {{ $ico }}"></i></span>
      <div class="ck-export__txt"><strong>{{ ExportTaches::TYPES[$type] }}</strong><small>Liste détaillée avec responsables et échéances</small></div>
      <div class="ck-export__btns">
        <a class="ck-btn ck-btn--vert" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'xlsx'] + $paramsExport) }}"><i class="fa-solid fa-file-excel"></i> Excel</a>
        <a class="ck-btn ck-btn--rouge" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'pdf'] + $paramsExport) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> PDF</a>
      </div>
    </article>
  @endforeach

  <article class="ck-export ck-export--violet">
    <span class="ck-export__ico"><i class="fa-solid fa-file-lines"></i></span>
    <div class="ck-export__txt"><strong>Note de synthèse du pilotage</strong><small>{{ $c['periode']['libelle'] }} : chiffres clés, axes, directions et texte rédigé</small></div>
    <div class="ck-export__btns">
      <a class="ck-btn ck-btn--vert" href="{{ route('pilotage.export.excel', $p) }}"><i class="fa-solid fa-file-excel"></i> Excel</a>
      <a class="ck-btn ck-btn--rouge" href="{{ route('pilotage.export.pdf', $p) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
  </article>

  @if ($c['dirs'] === null)
    <article class="ck-export ck-export--violet">
      <span class="ck-export__ico"><i class="fa-solid fa-envelope-open-text"></i></span>
      <div class="ck-export__txt"><strong>Diligences du courrier</strong><small>Toutes les diligences, avec leur statut et leur échéance</small></div>
      <div class="ck-export__btns"><a class="ck-btn" href="{{ route('courrier.diligences.export') }}"><i class="fa-solid fa-download"></i> Exporter</a></div>
    </article>
  @endif
</div>
