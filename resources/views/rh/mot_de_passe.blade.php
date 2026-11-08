@extends('layout.master')
@section('title', 'Changer votre mot de passe')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--bleu">
        <div>
          <p class="cr-hero__sur">Première connexion</p>
          <h1 class="cr-hero__titre">Choisissez votre mot de passe</h1>
          <p class="cr-hero__sous">Votre compte a été créé avec un mot de passe provisoire. Pour la sécurité de vos données, choisissez maintenant un mot de passe personnel.</p>
        </div>
      </div>
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <div class="cr-carte" style="max-width:560px;">
        <div class="cr-carte__corps">
          <form method="post" action="{{ route('rh.mot_de_passe.store') }}">
            @csrf
            <div class="cs-champ"><label for="mp_actuel">Mot de passe provisoire</label><input class="form-control" id="mp_actuel" type="password" name="current_password" autocomplete="current-password" required autofocus></div>
            <div class="cs-champ"><label for="mp_nouveau">Nouveau mot de passe <span class="cs-opt">(8 caractères minimum)</span></label><input class="form-control" id="mp_nouveau" type="password" name="password" autocomplete="new-password" minlength="8" required></div>
            <div class="cs-champ"><label for="mp_conf">Confirmation</label><input class="form-control" id="mp_conf" type="password" name="password_confirmation" autocomplete="new-password" required></div>
            <button class="cr-btn cr-btn--primaire" type="submit"><i class="fa-solid fa-lock"></i> Enregistrer mon mot de passe</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
