@if ($affectations->isNotEmpty())
  <ul class="common-f-start">
    @foreach ($affectations as $affectation)
      <li data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="{{ $affectation->personnel->nomComplet() }}">
        @if ($affectation->personnel->photo_url)
          <img class="common-circle" src="{{ $affectation->personnel->photo_url }}" alt="{{ $affectation->personnel->nomComplet() }}">
        @else
          <div class="common-circle bg-lighter-{{ $couleur ?? 'primary' }}">{{ $affectation->personnel->initiales() }}</div>
        @endif
      </li>
    @endforeach
  </ul>
@endif
