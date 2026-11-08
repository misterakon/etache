@php
  use App\Support\Cockpit;
  $filtre = $d['filtre'];
  $type = Cockpit::TYPE_EXPORT[$filtre] ?? null;
  $paramsExport = ['exercice_id' => $c['exercice']->id] + ($c['porteeType'] === 'axe' ? ['axe_id' => $c['porteeId']] : ($c['porteeType'] === 'direction' ? ['direction_id' => $c['porteeId']] : []));
  $tons = ['retard' => 'rouge', 'terminee' => 'vert', 'encours' => 'bleu', 'attente' => 'ambre', 'neutre' => 'gris'];
  $justifs = ['justifie' => ['Justifié', 'vert'], 'attente' => ['En attente de décision', 'ambre'], 'non_justifie' => ['Non justifié', 'rouge']];
@endphp
<section class="ck-carte ck-carte--liste">
  <div class="ck-puces" role="group" aria-label="Filtrer les tâches">
    @foreach (Cockpit::FILTRES_TACHES as $cle => [$lib, $ton])
      <button type="button" class="ck-puce-filtre ck-puce-filtre--{{ $ton }} {{ $cle === $filtre ? 'actif' : '' }}" data-ck-filtre="{{ $cle }}" aria-pressed="{{ $cle === $filtre ? 'true' : 'false' }}">
        {{ $lib }} <b>{{ $d['compteurs'][$cle] }}</b>
      </button>
    @endforeach
  </div>

  <div class="ck-barre-outils">
    <label class="ck-recherche"><i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" id="ck_recherche" placeholder="Rechercher une tâche, une direction, un agent…" autocomplete="off">
    </label>
    <span class="ck-compte" id="ck_compte"></span>
    @if ($type)
      <a class="ck-btn ck-btn--vert" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'xlsx'] + $paramsExport) }}"><i class="fa-solid fa-file-excel"></i> Excel</a>
      <a class="ck-btn ck-btn--rouge" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'pdf'] + $paramsExport) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    @endif
  </div>

  @if (empty($d['lignes']))
    <div class="ck-vide"><i class="fa-regular fa-circle-check"></i><strong>Rien à afficher</strong><span>Aucune tâche ne correspond à ce filtre dans ce périmètre.</span></div>
  @else
    @if ($d['tronque'])<p class="ck-info"><i class="fa-solid fa-circle-info"></i> Les 300 premières échéances sont affichées sur {{ $d['total'] }} : exportez pour tout obtenir.</p>@endif
    <div class="ck-table-enveloppe">
      <table class="ck-table" id="ck_table">
        <thead><tr><th>Tâche</th><th>Direction</th><th>Responsables</th><th>Échéance</th><th>Avancement</th><th>État</th></tr></thead>
        <tbody>
          @foreach ($d['lignes'] as $l)
            <tr data-recherche="{{ \Illuminate\Support\Str::lower($l['code'].' '.$l['libelle'].' '.$l['direction'].' '.collect($l['agents'])->pluck('nom')->implode(' ')) }}">
              <td class="ck-table__tache">
                <a href="{{ $l['url'] }}"><span class="ck-code">{{ $l['code'] }}</span>{{ $l['libelle'] }}</a>
              </td>
              <td><span class="ck-dir">{{ $l['direction'] ?? '—' }}</span>@if ($l['axe'])<small class="ck-axe">{{ $l['axe'] }}</small>@endif</td>
              <td>
                @forelse ($l['agents'] as $ag)
                  <span class="ck-avatar ck-avatar--petit" title="{{ $ag['nom'] }}">{{ $ag['initiales'] }}</span>
                @empty
                  <span class="ck-puce ck-puce--sarcelle">Aucun</span>
                @endforelse
              </td>
              <td class="ck-table__date">
                {{ $l['echeance'] ?? '—' }}
                @if ($l['jours'] !== null && $l['ton'] !== 'terminee')
                  <small class="{{ $l['jours'] < 0 ? 'neg' : ($l['jours'] <= 7 ? 'proche' : '') }}">{{ $l['jours'] < 0 ? abs($l['jours']).' j de retard' : ($l['jours'] === 0 ? 'aujourd’hui' : 'dans '.$l['jours'].' j') }}</small>
                @endif
              </td>
              <td><span class="ck-prog" title="{{ $l['avancement'] }} %"><i class="ck-prog--{{ $tons[$l['ton']] ?? 'bleu' }}" style="width: {{ $l['avancement'] }}%"></i></span><small class="ck-prog__nb">{{ $l['avancement'] }} %</small></td>
              <td>
                <span class="ck-puce ck-puce--{{ $tons[$l['ton']] ?? 'gris' }}">{{ $l['ton'] === 'retard' ? 'En retard' : ($l['statut'] ?? '—') }}</span>
                @if ($l['justif'])<small class="ck-justif ck-justif--{{ $justifs[$l['justif']][1] }}">{{ $justifs[$l['justif']][0] }}</small>@endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="ck-pagination" id="ck_pagination"></div>
  @endif
</section>
