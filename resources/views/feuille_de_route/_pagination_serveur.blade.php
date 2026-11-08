{{-- Pagination faite par le serveur : seule la page demandée est chargée. Variable : $paginateur (LengthAwarePaginator) --}}
@if (isset($paginateur) && $paginateur->total() > 0)
  @php
    $page = $paginateur->currentPage();
    $dernier = $paginateur->lastPage();
    $debut = max(1, min($page - 2, $dernier - 4));
    $fin = min($dernier, $debut + 4);
  @endphp
  <nav class="vt-pagination" aria-label="Pagination des tâches">
    <span class="vt-pagination__info">Tâches {{ $paginateur->firstItem() }} à {{ $paginateur->lastItem() }} sur {{ $paginateur->total() }}</span>
    @if ($dernier > 1)
      <div class="vt-pagination__boutons">
        <a class="{{ $paginateur->onFirstPage() ? 'inactif' : '' }}" href="{{ $paginateur->previousPageUrl() ?? '#' }}" aria-label="Page précédente"><i class="fa-solid fa-chevron-left"></i></a>
        @if ($debut > 1)<a href="{{ $paginateur->url(1) }}">1</a>@if ($debut > 2)<span class="vt-pagination__points">…</span>@endif @endif
        @for ($i = $debut; $i <= $fin; $i++)<a class="{{ $i === $page ? 'actif' : '' }}" href="{{ $paginateur->url($i) }}" @if ($i === $page) aria-current="page" @endif>{{ $i }}</a>@endfor
        @if ($fin < $dernier)@if ($fin < $dernier - 1)<span class="vt-pagination__points">…</span>@endif<a href="{{ $paginateur->url($dernier) }}">{{ $dernier }}</a>@endif
        <a class="{{ $paginateur->hasMorePages() ? '' : 'inactif' }}" href="{{ $paginateur->nextPageUrl() ?? '#' }}" aria-label="Page suivante"><i class="fa-solid fa-chevron-right"></i></a>
      </div>
    @endif
  </nav>
@endif
