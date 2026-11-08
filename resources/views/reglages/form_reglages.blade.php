{{-- Formulaire des réglages d'UNE rubrique. Variables : $module, $m. Le JS est dans js_reglages ; le style dans public/assets/css/reglages.css. --}}
@php
  $retour = old('module') === $module; /* formulaire réaffiché après une erreur : on reprend la saisie */
  $nbPersonnalises = collect($m['reglages'])->keys()->filter(fn ($k) => $modifies["{$module}.{$k}"])->count();
@endphp

<form method="post" action="{{ route('reglages.store') }}" autocomplete="off">
  @csrf
  <input type="hidden" name="module" value="{{ $module }}">

  @foreach ($m['reglages'] as $reglage => $d)
    @php
      $cle = "{$module}.{$reglage}";
      $champ = "valeurs.{$reglage}";
      $id = "rg_{$module}_{$reglage}";
      $verrouille = \App\Support\Reglages::verrouille($cle);
      $valeur = $retour
          ? ($d['type'] === 'bool' ? (bool) old($champ) : old($champ, $d['type'] === 'liste' ? [] : null))
          : $valeurs[$cle];
    @endphp

    <div class="rg-ligne {{ $modifies[$cle] ? 'rg-ligne--modifie' : '' }} {{ $errors->has($champ) ? 'rg-ligne--erreur' : '' }} {{ $d['type'] === 'liste' ? 'rg-ligne--large' : '' }}" data-recherche="{{ mb_strtolower($d['libelle'].' '.($d['aide'] ?? '').' '.$m['libelle']) }}">
      <div class="rg-info">
        <label class="rg-libelle" for="{{ $id }}">{{ $d['libelle'] }}@if ($modifies[$cle]) <span class="rg-tag-modif">Modifié</span>@endif</label>
        @if (! empty($d['aide'])) <p class="rg-aide">{{ $d['aide'] }}</p> @endif
        @error($champ) <p class="rg-erreur"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
      </div>

      <div class="rg-ctrl">
        @switch($d['type'])
          @case('bool')
            <label class="rg-switch" for="{{ $id }}" title="{{ $d['libelle'] }}">
              <input type="checkbox" id="{{ $id }}" name="valeurs[{{ $reglage }}]" value="1" role="switch" aria-label="{{ $d['libelle'] }}" @checked($valeur)>
              <span class="rg-slider"></span>
              <span class="rg-etat"></span>
            </label>
            @break

          @case('int')
            <div class="rg-saisie">
              <input class="rg-input" type="number" id="{{ $id }}" name="valeurs[{{ $reglage }}]" value="{{ $valeur }}" min="{{ $d['min'] }}" max="{{ $d['max'] }}" required>
              @isset($d['unite']) <span class="rg-unite">{{ $d['unite'] }}</span> @endisset
            </div>
            @break

          @case('texte')
            <input class="rg-input" type="text" id="{{ $id }}" name="valeurs[{{ $reglage }}]" value="{{ $valeur }}" maxlength="{{ $d['max'] ?? 255 }}" required @readonly($verrouille)>
            @if ($verrouille) <span class="rg-defaut"><i class="fa-solid fa-lock"></i> {{ $d['verrou_message'] ?? 'Verrouillé.' }}</span> @endif
            @break

          @case('choix')
            <select class="rg-select" id="{{ $id }}" name="valeurs[{{ $reglage }}]">
              @foreach ($d['options'] as $option => $libelle) <option value="{{ $option }}" @selected((string) $valeur === (string) $option)>{{ $libelle }}</option> @endforeach
            </select>
            @break

          @case('liste')
            <div class="rg-chips" id="{{ $id }}">
              @foreach ($d['options'] as $option => $libelle)
                @php $obligatoire = in_array((string) $option, $d['obligatoires'] ?? [], true); @endphp
                <label class="rg-chip {{ $obligatoire ? 'rg-chip--fixe' : '' }}" @if ($obligatoire) title="Toujours autorisé" @endif>
                  <input type="checkbox" name="valeurs[{{ $reglage }}][]" value="{{ $option }}"
                         @checked($obligatoire || in_array((string) $option, array_map('strval', (array) $valeur), true)) @disabled($obligatoire)>
                  <span>{{ $libelle }}@if ($obligatoire) <small>(toujours)</small>@endif</span>
                </label>
                @if ($obligatoire) <input type="hidden" name="valeurs[{{ $reglage }}][]" value="{{ $option }}"> @endif
              @endforeach
            </div>
            @break
        @endswitch

        <span class="rg-defaut">
          @if ($d['type'] === 'int')
            Par défaut : <b>{{ \App\Support\Reglages::afficher($d, $d['defaut']) }}</b> · entre {{ number_format($d['min'], 0, ',', ' ') }} et {{ number_format($d['max'], 0, ',', ' ') }}
          @else
            Par défaut : <b>{{ \App\Support\Reglages::afficher($d, $d['defaut']) }}</b>
          @endif
        </span>

        @if ($modifies[$cle] && ! $verrouille)
          <button class="rg-retablir" type="button" data-retablir="{{ $cle }}" data-libelle="{{ $d['libelle'] }}"><i class="fa-solid fa-rotate-left"></i> Rétablir la valeur par défaut</button>
        @endif
      </div>
    </div>
  @endforeach

  <div class="rg-pied">
    @if ($nbPersonnalises)
      <button class="rg-btn rg-btn--neutre" type="button" data-retablir-module="{{ $module }}" data-libelle="{{ $m['libelle'] }}"><i class="fa-solid fa-rotate-left"></i> Valeurs par défaut</button>
    @endif
    <button class="rg-btn rg-btn--primaire" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
  </div>
</form>
