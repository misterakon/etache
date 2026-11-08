<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="e-TACHE - DGEPN">
    <link rel="icon" href="{{ asset('assets/images/logo/dgepn_icon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/dgepn_icon.png') }}" type="image/x-icon">
    <title>Connexion | e-TACHE</title>
    <link rel="stylesheet" href="{{ asset('assets/fonts/plus-jakarta-sans/plus-jakarta-sans.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/login-etache.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>
  <body class="login-etache">
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
                {{-- Message de statut (ex: lien de reinitialisation envoye) --}}
                @if (session('status'))
                  <div class="alert alert-success text-center" role="alert">
                    {{ session('status') }}
                  </div>
                @endif

                <form class="theme-form" method="POST" action="{{ route('login') }}">
                  @csrf
                  <h4 class="text-center font-bold">e-TACHE</h4>
                  <p class="text-center">Veuillez entrer votre email et mot de passe</p>

                  <div class="form-group">
                    <label class="col-form-label" for="email">Email</label>
                    <input
                      class="form-control @error('email') is-invalid @enderror"
                      id="email"
                      type="email"
                      name="email"
                      value="{{ old('email') }}"
                      required
                      autofocus
                      autocomplete="username"
                      placeholder="nom@dgepn.ci">
                    @error('email')
                      <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="form-group">
                    <label class="col-form-label" for="password">Mot de passe</label>
                    <div class="form-input relative">
                      <input
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="*********">
                      <div class="show-hide"><span class="show"> </span></div>
                    </div>
                    @error('password')
                      <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="form-group mt-4">
                    <div class="form-check flex gap-1 items-center">
                      <input class="checkbox-primary form-check-input custom-checkbox" id="remember_me" type="checkbox" name="remember">
                      <label class="text-muted form-check-label !mb-0" for="remember_me">Se souvenir de moi</label>
                    </div>

                    {{-- @if (Route::has('password.request'))
                      <a class="link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    @endif --}}

                    <div class="text-center">
                      <button class="btn btn-primary btn-block w-auto px-6 py-2 text-white mt-[24px] text-center" type="submit">
                        SE CONNECTER
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
      <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
      <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
      <script src="{{ asset('assets/js/config.js') }}"></script>
      <script src="{{ asset('assets/js/modalpage/custom-modal.js') }}"></script>
      <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
      <script src="{{ asset('assets/js/script1.js') }}"></script>
      <script src="{{ asset('assets/js/script.js') }}"></script>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
          var zone = document.querySelector('.login-etache .show-hide');
          var champ = document.getElementById('password');
          if (!zone || !champ) return;
          zone.addEventListener('click', function () {
            var visible = champ.type === 'text';
            champ.type = visible ? 'password' : 'text';
            zone.classList.toggle('visible', !visible);
          });
        });
      </script>
    </div>
  </body>
</html>
