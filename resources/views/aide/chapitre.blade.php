@extends('layout.master')
@section('title', $chapitre['titre'].' · Aide')

@section('main-content')
  <div class="page-body">
    <div class="container"><x-titre-page :titre="$chapitre['titre']" :fil="['Aide', $chapitre['titre']]" /></div>
    <div class="container aide-page aide">
      <div class="aide-chap">
        <nav class="aide-nav" aria-label="Chapitres du manuel">
          <h4>Chapitres</h4>
          @foreach ($chapitres as $c)
            <a class="aide-c-{{ $c['couleur'] }} {{ $c['slug'] === $chapitre['slug'] ? 'actif' : '' }}" href="{{ route('aide.chapitre', $c['slug']) }}"><span class="aide-resultat__ico"><i class="{{ $c['icone'] }}"></i></span>{{ $c['titre'] }}</a>
          @endforeach
          <a href="{{ route('aide.index') }}" style="margin-top:6px"><i class="fa-solid fa-arrow-left"></i> Sommaire</a>
        </nav>

        <div class="aide-corps">
          <div class="aide-entete-chap aide-c-{{ $chapitre['couleur'] }}">
            <span class="aide-resultat__ico"><i class="{{ $chapitre['icone'] }}"></i></span>
            <div><h1>{{ $chapitre['titre'] }}</h1><p>{{ $chapitre['resume'] }} · {{ $chapitre['minutes'] }} min de lecture</p></div>
          </div>

          @if ($chapitre['intro_html'])<div class="aide-section"><div class="aide-texte">{!! $chapitre['intro_html'] !!}</div></div>@endif

          @if ($chapitre['type'] === 'faq')
            <div style="display:grid; gap:10px">
              @foreach ($chapitre['sections'] as $s)
                <details class="aide-faq" id="{{ $s['ancre'] }}"><summary><i class="q fa-solid fa-lightbulb"></i><span>{{ $s['titre'] }}</span><i class="fl fa-solid fa-chevron-down"></i></summary><div class="aide-texte">{!! $s['html'] !!}</div></details>
              @endforeach
            </div>
          @else
            @foreach ($chapitre['sections'] as $s)
              <section class="aide-section" id="{{ $s['ancre'] }}"><h2>{{ $s['titre'] }}<small>{{ $s['duree'] }} min</small></h2><div class="aide-texte">{!! $s['html'] !!}</div></section>
            @endforeach
          @endif

          <div class="aide-pagination">
            @if ($precedent)<a class="aide-btn" href="{{ route('aide.chapitre', $precedent['slug']) }}"><i class="fa-solid fa-arrow-left"></i> {{ $precedent['titre'] }}</a>@else<span></span>@endif
            @if ($suivant)<a class="aide-btn aide-btn--plein" href="{{ route('aide.chapitre', $suivant['slug']) }}">{{ $suivant['titre'] }} <i class="fa-solid fa-arrow-right"></i></a>@endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    (function () { var h = location.hash && document.getElementById(location.hash.slice(1)); if (h && h.tagName === 'DETAILS') { h.open = true; } })();
  </script>
@endsection
