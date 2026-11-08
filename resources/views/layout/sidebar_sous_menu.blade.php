{{-- Sous-menu d'une entrée du menu : liens et groupes imbriqués. Variables : $enfants, $lien --}}
<ul class="sidebar-submenu">
    @foreach ($enfants as $enfant)
        @if (isset($enfant['enfants']))
            <li>
                <a class="submenu-title" href="#">{{ $enfant['titre'] }}<span class="sub-arrow"><i class="fa-solid fa-angle-right"></i></span></a>
                @include('layout.sidebar_sous_menu', ['enfants' => collect($enfant['enfants'])])
            </li>
        @else
            <li>
                @isset($enfant['badge'])
                    <label class="badge badge-danger text-white" title="{{ $enfant['badgeTitre'] ?? '' }}">@if ($enfant['badge']) {{ $enfant['badge'] }} @endif</label>
                @endisset
                <a href="{{ $lien($enfant) }}">@if (! empty($enfant['icnTexte']))<i class="{{ $enfant['icnTexte'] }}"></i> @endif{{ $enfant['titre'] }}</a>
            </li>
        @endif
    @endforeach
</ul>
