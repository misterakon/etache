{{-- Contenu du tiroir « ? » : le chapitre de la page où l'on est (sinon le sommaire). Variables : $chapitre (ou null), $chapitres --}}
@if ($chapitre)
  @php
    $ancreCible = $chapitre['section_ancre'] ?? null;
    $rubriques = collect($chapitre['sections'])->sortBy(fn ($s) => $s['ancre'] === $ancreCible ? 0 : 1)->values();
  @endphp
  <div class="aide-c-{{ $chapitre['couleur'] }}" data-titre="{{ $chapitre['titre'] }}" data-lien="{{ route('aide.chapitre', $chapitre['slug']) }}{{ $ancreCible ? '#'.$ancreCible : '' }}">
    <div class="aide-note aide-note--savoir" style="margin-top:0"><p><strong>À savoir</strong> Voici l’aide de cette page. Dépliez une rubrique, ou cherchez un mot ci-dessus.</p></div>
    <div style="display:grid; gap:10px">
      @foreach ($rubriques as $s)
        <details class="aide-faq" @if ($loop->first) open @endif>
          <summary><i class="q fa-solid fa-lightbulb"></i><span>{{ $s['titre'] }}</span><i class="fl fa-solid fa-chevron-down"></i></summary>
          <div class="aide-texte">{!! $s['html'] !!}</div>
        </details>
      @endforeach
    </div>
  </div>
@else
  <div data-titre="Aide" data-lien="{{ route('aide.index') }}">
    <div class="aide-note aide-note--savoir" style="margin-top:0"><p><strong>À savoir</strong> Pas d’aide propre à cette page : voici les chapitres du manuel.</p></div>
    <div class="aide-liste-chap">
      @foreach ($chapitres as $c)
        <a class="aide-c-{{ $c['couleur'] }}" href="{{ route('aide.chapitre', $c['slug']) }}"><span class="aide-resultat__ico"><i class="{{ $c['icone'] }}"></i></span><div><b>{{ $c['titre'] }}</b><small>{{ $c['resume'] }}</small></div></a>
      @endforeach
    </div>
  </div>
@endif
