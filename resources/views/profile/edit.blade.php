@extends('layout.master')
@section('title', 'Mon compte')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/mon-compte.css') }}?v={{ filemtime(public_path('assets/css/mon-compte.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container"><x-titre-page titre="Mon compte" :fil="['Mon compte']" /></div>
    <div class="container ep-page">

      <x-entete-page tag="Mon espace" titre="{{ $moi->name }}" sous="{{ $moi->email }} · Profil {{ $moi->profilAcces?->libelle ?? '—' }}" />

      @if (session('status') === 'password-updated')
        <div class="mc-flash"><i class="fa-solid fa-circle-check"></i> Mot de passe modifié. Vos autres sessions ont été fermées.</div>
      @endif

      <div class="mc-grille">

        {{-- Identité et rattachement --}}
        <section class="mc-carte">
          <header class="mc-carte__tete"><i class="fa-solid fa-id-badge"></i><h2>Mes informations</h2></header>
          <div class="mc-corps">
            <div class="mc-ligne"><span class="mc-ligne__ico"><i class="fa-regular fa-user"></i></span><div><div class="mc-ligne__lib">Nom</div><div class="mc-ligne__val">{{ $moi->name }}</div></div></div>
            <div class="mc-ligne"><span class="mc-ligne__ico"><i class="fa-regular fa-envelope"></i></span><div><div class="mc-ligne__lib">E-mail de connexion</div><div class="mc-ligne__val">{{ $moi->email }}</div></div></div>
            <div class="mc-ligne"><span class="mc-ligne__ico"><i class="fa-solid fa-user-lock"></i></span><div><div class="mc-ligne__lib">Profil d'accès</div><div class="mc-ligne__val">{{ $moi->profilAcces?->libelle ?? 'Non renseigné' }}</div></div></div>
            <div class="mc-ligne"><span class="mc-ligne__ico"><i class="fa-solid fa-key"></i></span><div><div class="mc-ligne__lib">Mot de passe modifié</div><div class="mc-ligne__val">{{ $moi->mot_de_passe_modifie_le?->format('d/m/Y à H:i') ?? 'Jamais depuis la création du compte' }}</div></div></div>

            @if ($agent)
              <div class="mc-section">Mon poste</div>
              <div class="mc-ligne"><span class="mc-ligne__ico"><i class="fa-solid fa-briefcase"></i></span><div><div class="mc-ligne__lib">{{ $agent->fonction ?: 'Agent' }}</div><div class="mc-ligne__val">{{ $agent->service?->nom ?? '—' }}</div></div></div>
              <a class="mc-lien" href="{{ route('dgepn.personnel.fiche', $agent->id) }}"><i class="fa-solid fa-arrow-up-right-from-square"></i> Voir ma fiche agent complète</a>
            @else
              <div class="mc-vide"><i class="fa-solid fa-circle-info"></i> Votre compte n'est relié à aucune fiche agent.</div>
            @endif
          </div>
        </section>

        {{-- Sécurité : changement de mot de passe --}}
        <section class="mc-carte">
          <header class="mc-carte__tete"><i class="fa-solid fa-shield-halved"></i><h2>Sécurité</h2></header>
          <div class="mc-corps">
            <p class="mc-texte">Changez votre mot de passe (8 caractères minimum). Vos autres sessions ouvertes seront fermées.</p>
            <form method="POST" action="{{ route('password.update') }}" id="mc_form_mdp">
              @csrf
              @method('PUT')
              <div class="mc-champ">
                <label class="form-label" for="update_password_current_password">Mot de passe actuel</label>
                <input type="password" id="update_password_current_password" name="current_password" class="form-control" autocomplete="current-password">
                @error('current_password', 'updatePassword')<div class="mc-erreur">{{ $message }}</div>@enderror
              </div>
              <div class="mc-champ">
                <label class="form-label" for="update_password_password">Nouveau mot de passe</label>
                <input type="password" id="update_password_password" name="password" class="form-control" autocomplete="new-password">
                @error('password', 'updatePassword')<div class="mc-erreur">{{ $message }}</div>@enderror
              </div>
              <div class="mc-champ">
                <label class="form-label" for="update_password_password_confirmation">Confirmer le nouveau mot de passe</label>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')<div class="mc-erreur">{{ $message }}</div>@enderror
              </div>
              <button type="submit" class="mc-btn"><i class="fa-solid fa-key"></i> Modifier mon mot de passe</button>
            </form>
          </div>
        </section>

      </div>
    </div>
  </div>
@endsection
