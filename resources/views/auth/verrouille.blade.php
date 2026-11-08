<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="icon" href="{{ asset('assets/images/logo/dgepn_icon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/dgepn_icon.png') }}" type="image/x-icon">
    <title>Session verrouillée | e-TACHE</title>
    <link rel="stylesheet" href="{{ asset('assets/fonts/plus-jakarta-sans/plus-jakarta-sans.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/login-etache.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/verrouillage.css') }}?v={{ filemtime(public_path('assets/css/verrouillage.css')) }}">
  </head>
  <body class="login-etache vr-page">
    <div class="container">
      <div class="grid grid-cols-12 m-0">
        <div class="col-span-12 p-0">
          <div class="login-card login-dark">
            <div>
              <div>
                <a class="logo" href="{{ url('/') }}">
                  <img class="max-w-full h-auto for-light" src="{{ asset('assets/images/logo/dgepn.png') }}" style="width: 50%" alt="DGEPN">
                  <img class="max-w-full h-auto for-dark" src="{{ asset('assets/images/logo/dgepn.png') }}" style="width: 50%" alt="DGEPN">
                </a>
              </div>

              <div class="login-main">
                <form class="theme-form" method="POST" action="{{ route('verrouillage.unlock') }}" autocomplete="off">
                  @csrf
                  <h4 class="text-center font-bold">e-TACHE</h4>

                  <div class="vr-etat"><i class="fa-solid fa-lock" aria-hidden="true"></i> Session verrouillée</div>
                  <p class="vr-info">Aucune activité depuis {{ $minutesInactivite }} minutes : par sécurité, votre session a été verrouillée. </p>

                  {{-- Identité : affichée, jamais modifiable --}}
                  <div class="vr-identite">
                    <span class="vr-avatar">
                      @if ($photo)
                        <img src="{{ $photo }}" alt="{{ $nom }}">
                      @else
                        <span class="vr-avatar__initiales">{{ $initiales }}</span>
                      @endif
                      <i class="vr-avatar__cadenas fa-solid fa-lock" aria-hidden="true"></i>
                    </span>
                    <div class="vr-identite__texte">
                      <div class="vr-identite__nom">{{ $nom }}</div>
                      <div class="vr-identite__email" title="Compte connecté">{{ $email }}</div>
                    </div>
                  </div>
                  {{-- Aide aux gestionnaires de mots de passe du navigateur (champ caché, jamais envoyé) --}}
                  <input type="text" value="{{ $email }}" autocomplete="username" readonly hidden tabindex="-1" aria-hidden="true">

                  <div class="form-group">
                    <label class="col-form-label" for="password">Mot de passe</label>
                    <div class="form-input relative">
                      <input
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        type="password"
                        name="password"
                        required
                        autofocus
                        autocomplete="current-password"
                        placeholder="*********">
                      <div class="show-hide" role="button" tabindex="0" aria-label="Afficher ou masquer le mot de passe"><span class="show"> </span></div>
                    </div>
                    @error('password')
                      <div class="vr-erreur" role="alert"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                  </div>

                  <div class="text-center">
                    <button class="btn btn-primary btn-block w-auto px-6 py-2 text-white mt-[18px] text-center" type="submit">
                      <i class="fa-solid fa-lock-open"></i>&nbsp; DÉVERROUILLER
                    </button>
                  </div>
                  {{-- <p class="vr-aide">Après {{ $minutesMax }} minutes de verrouillage ou {{ $essaisRestants }} essai(s) manqué(s), une connexion complète sera demandée.</p> --}}
                </form>

                <form method="POST" action="{{ route('logout') }}" class="vr-sortie">
                  @csrf
                  <button type="submit">Ce n'est pas vous ? Se déconnecter</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="{{ asset('assets/js/verrouillage-page.js') }}"></script>
  </body>
</html>
