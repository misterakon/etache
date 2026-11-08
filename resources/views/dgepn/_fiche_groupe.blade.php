{{-- Carte compacte d'informations. Variables : $titre, $icone, $ton (identite|contact|affectation|carriere|dossier), $lignes [[libellé, valeur|null, icône, lien|null], ...], $editer (appel JS du crayon, ou null) --}}
<section class="fa3-carte fa3-{{ $ton }}">
  <header class="fa3-carte__tete">
    <span class="fa3-carte__puce"><i class="{{ $icone }}"></i></span>
    <h3 class="fa3-carte__titre">{{ $titre }}</h3>
    @if (! empty($editer))
      <a class="fa3-crayon" href="#!" title="Modifier" onclick="{{ $editer }}; return false;"><i class="fa-solid fa-pen"></i></a>
    @endif
  </header>
  <div class="fa3-carte__corps">
    @foreach ($lignes as [$libI, $valI, $icoI, $lienI])
      <div class="fa3-ligne">
        <span class="fa3-ligne__ico"><i class="{{ $icoI }}"></i></span>
        <span class="fa3-ligne__lib">{{ $libI }}</span>
        @if ($valI)
          <span class="fa3-ligne__val">@if ($lienI)<a href="{{ $lienI }}">{{ $valI }}</a>@else{{ $valI }}@endif</span>
        @else
          <span class="fa3-ligne__val fa3-ligne__val--vide">Non renseigné</span>
        @endif
      </div>
    @endforeach
  </div>
</section>
