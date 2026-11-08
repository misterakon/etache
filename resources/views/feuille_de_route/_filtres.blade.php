{{--
  Barre de filtres des listes (même composant partout).
  Paramètres : jusqua ('exercice'|'axe'|'action_cle'|'action_mesurable'), direction (bool), mode (bool), retards (bool)
  Variables lues : $exerciceId (exercice affiché), $directions (facultatif)
  Exemples :  @include('feuille_de_route._filtres', ['jusqua' => 'action_mesurable', 'direction' => true, 'mode' => true, 'retards' => true])   (tâches)
              @include('feuille_de_route._filtres', ['jusqua' => 'axe', 'direction' => true])   (actions clés)
--}}
@php
    $jusqua = $jusqua ?? 'action_mesurable';
    $avecDirection = $direction ?? false;
    $avecMode = $mode ?? false;
    $avecRetards = $retards ?? false;
    $valeursFiltre = [
        'exercice_id' => $exerciceId ?? '',
        'axe_id' => request('axe_id', ''),
        'action_cle_id' => request('action_cle_id', ''),
        'action_mesurable_id' => request('action_mesurable_id', ''),
    ];
    $directionsFiltre = $directions ?? \App\Models\Direction::orderBy('nom')->get();
    $nbFiltres = collect(['axe_id', 'action_cle_id', 'action_mesurable_id', 'direction_id', 'en_retard', 'non_justifie'])
        ->filter(fn ($k) => filled(request($k)))->count();
    $urlReinit = url()->current().(request()->filled('vue') ? '?vue='.request('vue') : '');
@endphp

<details class="card p-3 vt-filtres" @if ($nbFiltres) open @endif>
  <summary class="font-medium" style="cursor:pointer;">
    <i class="fa-solid fa-filter"></i>&nbsp;Filtres
    @if ($nbFiltres) <span class="tl-badge tl-encours">{{ $nbFiltres }} actif{{ $nbFiltres > 1 ? 's' : '' }}</span> @endif
  </summary>

  <form method="GET" action="{{ url()->current() }}" class="mt-3" id="form_filtres">
    @if (request()->filled('vue')) <input type="hidden" name="vue" value="{{ request('vue') }}"> @endif

    <div class="grid grid-cols-12 gap-3">
      @include('feuille_de_route._cascade', ['jusqua' => $jusqua, 'filtre' => true, 'valeurs' => $valeursFiltre])

      @if ($avecDirection)
        <div class="col-span-3 md:col-span-6 sm:col-span-12">
          <label class="form-label" for="f_direction_id">Direction</label>
          <select class="form-select form-select-sm" id="f_direction_id" name="direction_id">
            <option value="">Toutes les directions</option>
            @foreach ($directionsFiltre as $d)
              <option value="{{ $d->id }}" @selected((string) request('direction_id') === (string) $d->id)>{{ $d->nom }}</option>
            @endforeach
          </select>
        </div>
      @endif

      @if ($avecMode)
        <div class="col-span-3 md:col-span-6 sm:col-span-12">
          <label class="form-label" for="f_direction_role">La direction est…</label>
          <select class="form-select form-select-sm" id="f_direction_role" name="direction_role">
            <option value="toutes" @selected(request('direction_role', 'toutes') === 'toutes')>Porteuse ou impliquée</option>
            <option value="porteuse" @selected(request('direction_role') === 'porteuse')>Porteuse (principale ou associée)</option>
            <option value="impliquee" @selected(request('direction_role') === 'impliquee')>Impliquée (responsable ou pilote)</option>
          </select>
        </div>
      @endif

      @if ($avecRetards)
        <div class="col-span-6 md:col-span-12 sm:col-span-12 flex flex-wrap gap-2" style="align-self:end;">
          <label class="form-check"><input class="form-check-input" type="checkbox" name="en_retard" value="1" @checked(request()->boolean('en_retard'))> En retard</label>
          <label class="form-check"><input class="form-check-input" type="checkbox" name="non_justifie" value="1" @checked(request()->boolean('non_justifie'))> En retard non justifié</label>
        </div>
      @endif

      <div class="col-span-12 flex gap-2">
        <button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Appliquer</button>
        <a class="btn btn-secondary text-white" href="{{ $urlReinit }}">Réinitialiser</a>
      </div>
    </div>
  </form>
</details>

@once
  @include('feuille_de_route.js_filtres')
@endonce
