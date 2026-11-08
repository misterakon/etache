{{-- Badge "Porteuse" / "Transversale" d'une tâche, par rapport à la direction filtrée. Usage : @include('feuille_de_route._badge_direction', ['tache' => $t]) --}}
@php $roleDirection = ($rolesDirection ?? [])[$tache->id] ?? null; @endphp
@if ($roleDirection)
  <span class="tl-badge {{ $roleDirection === 'porteuse' ? 'tl-encours' : 'tl-avenir' }}" title="{{ $roleDirection === 'porteuse' ? 'La direction porte l\'action clé' : 'La direction est impliquée (responsable ou pilote), sans porter l\'action clé' }}">{{ $roleDirection === 'porteuse' ? 'Porteuse' : 'Transversale' }}</span>
@endif
