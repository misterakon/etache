{{--
  Cascade Exercice > Axe > Action clé > Action mesurable.
  FORMULAIRE : @include('feuille_de_route._cascade', ['jusqua' => 'axe'|'action_cle'|'action_mesurable'])
               seul le dernier niveau est envoyé (name="{niveau}_id").
  FILTRES    : @include('feuille_de_route._cascade', ['jusqua' => ..., 'filtre' => true, 'valeurs' => [...]])
               tous les niveaux sont envoyés ("Tous les ..."), préremplis depuis $valeurs.
  Le wrapper "cascade-groupe" isole chaque cascade : formulaire et filtres peuvent coexister sur la même page.
--}}
@php
    $jusqua = $jusqua ?? 'action_cle';
    $filtre = $filtre ?? false;
    $valeurs = $valeurs ?? [];
    $niveauxCascade = [
        'exercice' => ['Exercice', 'col-span-4'],
        'axe' => ['Axe', 'col-span-8'],
        'action_cle' => ['Action clé', 'col-span-12'],
        'action_mesurable' => ['Action mesurable', 'col-span-12'],
    ];
    $exercicesCascade = $exercices ?? \App\Models\Exercice::orderByDesc('annee')->get();
    $exerciceDefautCascade = $exerciceDefautId ?? \App\Models\Exercice::parDefaut()?->id;
@endphp

<div class="cascade-groupe" style="display:contents" data-filtre="{{ $filtre ? 1 : 0 }}" @if ($filtre) data-valeurs="{{ json_encode($valeurs) }}" @endif>
@foreach ($niveauxCascade as $niveau => [$libelleNiveau, $largeur])
  @php
    $final = ! $filtre && $niveau === $jusqua;
    $idSelect = $filtre ? 'f_'.$niveau.'_id' : ($final ? $niveau.'_id' : 'cascade_'.$niveau);
  @endphp
  <div class="{{ $filtre ? 'col-span-3 md:col-span-6' : $largeur }} sm:col-span-12">
    <label class="form-label" for="{{ $idSelect }}">{{ $libelleNiveau }}</label>
    <select class="form-select form-select-sm cascade-select" id="{{ $idSelect }}"
            @if ($filtre) name="{{ $niveau }}_id" @elseif ($final) name="{{ $niveau }}_id" required @endif
            data-niveau="{{ $niveau }}" data-final="{{ $final ? 1 : 0 }}"
            @if ($niveau === 'exercice') data-defaut="{{ $exerciceDefautCascade }}" @else disabled @endif>
      @if ($niveau === 'exercice')
        <option value="">{{ $filtre ? 'Tous les exercices' : 'Sélectionnez un exercice' }}</option>
        @foreach ($exercicesCascade as $exercice)
          <option value="{{ $exercice->id }}" @if ($filtre) @selected((string) $exercice->id === (string) ($valeurs['exercice_id'] ?? '')) @endif>{{ $exercice->libelle ?: $exercice->annee }}</option>
        @endforeach
      @else
        <option value="">—</option>
      @endif
    </select>
  </div>
  @break($niveau === $jusqua)
@endforeach
</div>

@once
<style>
  /* Select2 de la cascade : même gabarit que .form-select-sm (la boîte et le texte avaient des hauteurs différentes) */
  .cascade-groupe .select2-container { width: 100% !important; }
  .cascade-groupe .select2-container--default .select2-selection--single {
    height: calc(1.8em + .5rem + 2px);
    display: flex;
    align-items: center;
    border: 1px solid var(--bs-border-color, #9b9c9c);
    border-radius: .375rem;
    background-color: var(--bs-body-bg, #fff);
  }
  .cascade-groupe .select2-container--default .select2-selection--single .select2-selection__rendered {
    width: 100%;
    padding: 0 2rem 0 .5rem;
    font-size: .875rem;
    line-height: 1.5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #2b2c2c;
    font-size: 16px;
  }
  .cascade-groupe .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    top: 0;
    right: .35rem;
  }
  .cascade-groupe .select2-container--default.select2-container--disabled .select2-selection--single {
    background-color: var(--bs-secondary-bg, #e9ecef);
  }

  /* Liste déroulante (ajoutée dans le modal) : champ de recherche discret, libellés longs sur plusieurs lignes */
  #tooltipmodal .select2-container--default .select2-search--dropdown { padding: .4rem; }
  #tooltipmodal .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid var(--bs-border-color, #ced4da);
    border-radius: .25rem;
    padding: .25rem .5rem;
    outline: 0;
    box-shadow: none;
    color:#2b2c2c;
  }
  #tooltipmodal .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .2);
  }
  #tooltipmodal .select2-container--default .select2-results__option {
    font-size: .875rem;
    white-space: normal;
    word-break: break-word;
  }
</style>
@include('feuille_de_route.js_cascade')
@endonce
