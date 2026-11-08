@php
  // Sélecteur de vues commun à Liste / Matrice / Calendrier / Gantt (variables : $vue, $resume ; option : $sansAjout)
  $vuesDispo = [
      'liste' => ['Liste', 'fa-solid fa-list'],
      'tableau' => ['Matrice', 'fa-solid fa-table-cells'],
      'calendrier' => ['Calendrier', 'fa-regular fa-calendar-days'],
      'gantt' => ['Gantt', 'fa-solid fa-bars-staggered'],
  ];
  // Liens : mêmes noms de routes que le menu latéral. Si l'aide $lien du menu est disponible dans cette
  // vue, on l'utilise (mêmes règles d'accès) ; sinon route(). Repli sur ?vue= si la route n'existe pas encore.
  $nomsRoutes = ['liste' => 'feuille_de_route.tache', 'tableau' => 'feuille_de_route.tache_matrice', 'calendrier' => 'feuille_de_route.tache_calendrier', 'gantt' => 'feuille_de_route.tache_gantt'];
  $aideLien = $lien ?? null;
  $urlVue = function ($cle) use ($nomsRoutes, $aideLien) {
      // null -> '' : sinon http_build_query()/route() suppriment exercice_id= et le choix "Tous" serait perdu
      $filtres = array_map(fn ($valeur) => $valeur ?? '', request()->except('vue'));
      $nom = $nomsRoutes[$cle];

      if (! \Illuminate\Support\Facades\Route::has($nom)) {
          return route('feuille_de_route.tache', array_merge($filtres, $cle === 'liste' ? [] : ['vue' => $cle]));
      }

      $url = is_callable($aideLien) ? $aideLien($nom) : route($nom);
      if (! is_string($url) || $url === '' || $url[0] === '#' || ! $filtres) {
          return $url;
      }

      return $url.(str_contains($url, '?') ? '&' : '?').http_build_query($filtres);
  };
  $r = $resume ?? null;
  $chipsResume = [
      ['retard', 'En retard', 'fa-solid fa-triangle-exclamation'],
      ['encours', 'En cours', 'fa-solid fa-bars-progress'],
      ['attente', 'En attente', 'fa-regular fa-hourglass-half'],
      ['terminee', 'Terminées', 'fa-solid fa-circle-check'],
  ];
@endphp

<div class="grid grid-cols-12 card-gap vt-bloc-entete">
  <div class="col-span-12">
    <div class="card p-3">
      <div class="common-space vt-entete">
        <div class="vt-vues">
          @foreach ($vuesDispo as $cleV => [$libV, $icoV])
            <a class="vt-vue vt-vue--{{ $cleV }} {{ ($vue ?? 'liste') === $cleV ? 'actif' : '' }}"
               href="{{ $urlVue($cleV) }}"><i class="{{ $icoV }}"></i>{{ $libV }}</a>
          @endforeach
        </div>
        <div class="vt-droite">
          @include('feuille_de_route._export_taches')
          @unless ($sansAjout ?? false)
            {{-- <a class="btn btn-primary text-white" href="#!" onclick="add_form()"><i class="fa-solid fa-plus"></i> Ajouter une tâche</a> --}}
          @endunless
        </div>
      </div>

      @if ($r)
        <div class="vt-resume">
          <span class="vt-chip tl-neutre"><i class="fa-solid fa-bullseye"></i>&nbsp;&nbsp;Total&nbsp;&nbsp;<strong>{{ $r['total'] }}</strong></span>
          @foreach ($chipsResume as [$tonR, $libR, $icoR])
            <span class="vt-chip tl-{{ $tonR }}"><i class="{{ $icoR }}"></i>&nbsp;&nbsp;{{ $libR }}&nbsp;&nbsp;<strong>{{ $r['tons'][$tonR] }}</strong></span>
          @endforeach
          <div class="vt-avanc tl-terminee">
            <span>Avancement moyen</span>
            <div class="vt-meter {{ $r['avancement'] > 0 ? '' : 'vt-meter--vide' }}" style="--p: {{ $r['avancement'] }}%"></div>
            <strong>{{ $r['avancement'] }}%</strong>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<div class="grid grid-cols-12 card-gap">
  <div class="col-span-12">
    @include('feuille_de_route._filtres', ['jusqua' => 'action_mesurable', 'direction' => true, 'mode' => true, 'retards' => true])
  </div>
</div>
