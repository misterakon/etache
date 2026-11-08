@php
  use App\Support\Cockpit;
  $filtre = $d['filtre'];
  $type = Cockpit::TYPE_EXPORT[$filtre] ?? null;
  $paramsExport = ['exercice_id' => $c['exercice']->id] + ($c['porteeType'] === 'axe' ? ['axe_id' => $c['porteeId']] : ($c['porteeType'] === 'direction' ? ['direction_id' => $c['porteeId']] : []));
  $tonsLigne = ['retard' => 'retard', 'terminee' => 'terminee', 'encours' => 'encours', 'attente' => 'attente', 'neutre' => 'neutre'];
  $justifs = ['justifie' => ['Justifié', 'vert'], 'attente' => ['Décision en attente', 'ambre'], 'non_justifie' => ['Non justifié', 'rouge']];
  $icones = ['retards' => 'fa-triangle-exclamation', 'non_justifies' => 'fa-circle-question', 'proches' => 'fa-calendar-day', 'en_cours' => 'fa-bars-progress', 'attente' => 'fa-hourglass-half', 'sans_responsable' => 'fa-user-slash', 'terminees' => 'fa-circle-check', 'toutes' => 'fa-list'];
@endphp
<section class="ck-carte ck-carte--liste">
  <div class="ck-segments" role="group" aria-label="Filtrer les tâches">
    @foreach (Cockpit::FILTRES_TACHES as $cle => [$lib, $ton])
      <button type="button" class="ck-segment ck-segment--{{ $ton }} {{ $cle === $filtre ? 'actif' : '' }}" data-ck-filtre="{{ $cle }}" aria-pressed="{{ $cle === $filtre ? 'true' : 'false' }}">
        <i class="fa-solid {{ $icones[$cle] }}"></i>
        <b>{{ $d['compteurs'][$cle] }}</b>
        <span>{{ $lib }}</span>
      </button>
    @endforeach
  </div>

  <div class="ck-barre-outils">
    <label class="ck-recherche"><i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" data-liste-recherche placeholder="Rechercher une tâche, une direction, un agent…" autocomplete="off">
    </label>
    <span class="ck-compte" data-liste-compte></span>
    @if ($type)
      <a class="ck-btn ck-btn--vert" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'xlsx'] + $paramsExport) }}"><i class="fa-solid fa-file-excel"></i> Excel</a>
      <a class="ck-btn ck-btn--rouge" href="{{ route('feuille_de_route.export.taches', ['type' => $type, 'format' => 'pdf'] + $paramsExport) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    @endif
  </div>

  @if (empty($d['lignes']))
    <div class="ck-vide"><i class="fa-regular fa-circle-check"></i><strong>Rien à afficher</strong><span>Aucune tâche ne correspond à ce filtre dans ce périmètre.</span></div>
  @else
    @if ($d['tronque'])<p class="ck-info"><i class="fa-solid fa-circle-info"></i> Les 300 premières échéances sont affichées sur {{ $d['total'] }} : exportez pour tout obtenir.</p>@endif
    <div class="ck-taches" data-liste data-taille="10">
      @foreach ($d['lignes'] as $l)
        @php
          $ton = $tonsLigne[$l['ton']] ?? 'neutre';
          $date = $l['echeance'] ? \Carbon\Carbon::createFromFormat('d/m/Y', $l['echeance']) : null;
        @endphp
        <article class="tk tk--{{ $ton }}" data-ligne data-recherche="{{ \Illuminate\Support\Str::lower($l['code'].' '.$l['libelle'].' '.$l['direction'].' '.collect($l['agents'])->pluck('nom')->implode(' ')) }}">
          <a class="tk__date" href="{{ $l['url'] }}" title="Échéance">
            @if ($date)
              <strong>{{ $date->format('d') }}</strong><em>{{ \Illuminate\Support\Str::upper(rtrim($date->translatedFormat('M'), '.')) }}</em><small>{{ $date->format('Y') }}</small>
            @else
              <i class="fa-regular fa-calendar-xmark"></i><small>Sans échéance</small>
            @endif
          </a>
          <div class="tk__corps">
            <div class="tk__titre">
              <a href="{{ $l['url'] }}">{{ $l['code'] }}</a>
              @if ($l['direction'])<span class="tk__dir"><i class="fa-solid fa-sitemap"></i>{{ $l['direction'] }}</span>@endif
              @if ($l['axe'])<span class="tk__dir tk__dir--axe">{{ $l['axe'] }}</span>@endif
            </div>
            <p class="tk__libelle" title="{{ $l['libelle'] }}">{{ $l['libelle'] }}</p>
            <div class="tk__meta">
              <span class="tk__badge">{{ $l['ton'] === 'retard' ? ($l['jours'] !== null ? abs($l['jours']).' j de retard' : 'En retard') : ($l['statut'] ?? '—') }}</span>
              @if ($l['justif'])<span class="tk__badge tk__badge--{{ $justifs[$l['justif']][1] }}">{{ $justifs[$l['justif']][0] }}</span>@endif
              @if ($l['jours'] !== null && $l['ton'] !== 'terminee' && $l['ton'] !== 'retard' && $l['jours'] <= 7)<span class="tk__badge tk__badge--ambre">{{ $l['jours'] === 0 ? 'Aujourd’hui' : 'Dans '.$l['jours'].' j' }}</span>@endif
              <span class="tk__prog" title="Avancement {{ $l['avancement'] }} %"><i><b style="width: {{ $l['avancement'] }}%"></b></i>{{ $l['avancement'] }}%</span>
            </div>
          </div>
          <div class="tk__droite">
            @if (! empty($l['agents']))
              <ul class="tk__agents">
                @foreach ($l['agents'] as $ag)
                  <li title="{{ $ag['nom'] }}">@if ($ag['photo'])<img src="{{ $ag['photo'] }}" alt="{{ $ag['nom'] }}">@else<span>{{ $ag['initiales'] }}</span>@endif</li>
                @endforeach
              </ul>
            @else
              <span class="tk__aucun"><i class="fa-solid fa-user-slash"></i> Sans responsable</span>
            @endif
            <a class="tk__ouvrir" href="{{ $l['url'] }}"><i class="fa-regular fa-folder-open"></i> Ouvrir</a>
          </div>
        </article>
      @endforeach
    </div>
    <div class="ck-pagination" data-liste-pagination></div>
  @endif
</section>
