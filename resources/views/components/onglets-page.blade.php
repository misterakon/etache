@props(['onglets'])

{{-- Onglets horizontaux avec icône et compteur. $onglets : [numéro => [icône Font Awesome, titre, compteur ou null]].
     Le contenu s'affiche dans des blocs .tab-pan[data-tabcontent="numéro"] (script du thème). --}}
<div class="tabs">
  <ul class="tab-links ep-tabs" role="tablist">
    @foreach ($onglets as $n => [$icone, $titre, $nb])
      <li>
        <a class="tab-link {{ $loop->first ? 'active' : '' }}" data-tabfilter="{{ $n }}" role="tab" href="javascript:void(0)">
          <i class="fa-solid {{ $icone }}"></i><span>{{ $titre }}</span>
          @if ($nb !== null)<em class="ep-tabs__nb">{{ $nb }}</em>@endif
        </a>
      </li>
    @endforeach
  </ul>
</div>
