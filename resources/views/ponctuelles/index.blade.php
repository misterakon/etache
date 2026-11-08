@extends('layout.master')
@section('title', 'Tâches ponctuelles')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/select/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/ponctuelles.css') }}?v={{ filemtime(public_path('assets/css/ponctuelles.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container"><x-titre-page titre="Tâches ponctuelles" :fil="['Feuille de route', 'Tâches ponctuelles']" /></div>
    <div class="container ep-page">
      <x-entete-page tag="Hors feuille de route" titre="Votre travail du quotidien, au même endroit" sous="Une demande imprévue, un dossier à sortir, une réunion à préparer : notez-le ici. Votre supérieur le voit, il compte dans vos retards et votre charge, jamais dans l'avancement de la feuille de route." />
      @include('ponctuelles._dispositif')
    </div>
  </div>

  @include('ponctuelles._modal_et_config')
@endsection

@section('script')
  <script src="{{ asset('assets/select/select2.min.js') }}"></script>
  <script src="{{ asset('assets/js/ponctuelles.js') }}?v={{ filemtime(public_path('assets/js/ponctuelles.js')) }}"></script>
@endsection
