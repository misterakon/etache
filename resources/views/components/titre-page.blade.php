@props(['titre', 'fil' => []])

{{-- Titre de page et fil d'Ariane (même bandeau que les autres pages).
     $fil : liste de libellés ; le dernier est la page courante. Ex. <x-titre-page titre="Pilotage" :fil="['Feuille de route', 'Pilotage']" /> --}}
<div class="page-title">
  <div class="grid grid-cols-12 mx-2 items-center">
    <div class="col-span-6 sm:col-span-12"><h3>{{ $titre }}</h3></div>
    <div class="col-span-6 sm:col-span-12">
      <ol class="breadcrumb flex">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
        @foreach ($fil as $libelle)
          <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">{{ $libelle }}</li>
        @endforeach
      </ol>
    </div>
  </div>
</div>
