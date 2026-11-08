<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cuba admin is super flexible, powerful, clean &amp; modern responsive Tailwind admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Cuba admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <title>Login | e-Tache</title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>
  <body>
    <!-- login page start-->
    <div class="container">
      <div class="grid grid-cols-12 m-0">
        <div class="col-span-12 p-0">    
          <div class="login-card login-dark">
            <div>
              <div>
                <a class="logo" href="index.html">
                <img class="max-w-full h-auto for-light" src="{{ asset('assets/images/logo/dgepn.png') }}" style="width: 50%" alt="looginpage">
                <img class="max-w-full h-auto for-dark" src="{{ asset('assets/images/logo/dgepn.png') }}" style="width: 50%" alt="looginpage">
                </a>
              </div>
              <div class="login-main"> 
                <form class="theme-form" action="{{ route('dashboard') }}" method="get">
                  @csrf
                  <h4 class="text-center font-bold">e-TACHE</h4>
                  <p class="text-center">Veuillez entrer votre email et mot de passe</p>
                  <div class="form-group">
                    <label class="col-form-label">Email</label>
                    <input class="form-control" type="email" name="login[email]" required placeholder="test@gmail.com">
                  </div>
                  <div class="form-group">
                    <label class="col-form-label">Mot de passe</label>
                    <div class="form-input relative">
                      <input class="form-control" type="password" name="login[password]" required placeholder="*********">
                      <div class="show-hide"><span class="show">                         </span></div>
                    </div>
                  </div>
                  <div class="form-group mt-4">
                    <div class="form-check flex gap-1 items-center">
                      <input class="checkbox-primary form-check-input custom-checkbox" id="checkbox1" type="checkbox">
                      <label class="text-muted form-check-label !mb-0" for="checkbox1">Remember password</label>
                    </div><a class="link" href="{{ route('dashboard') }}">Dashboard</a>
                    <div class="text-center">
                      <button class="btn btn-primary btn-block w-auto px-6 py-2 text-white mt-[24px] text-center" type="submit">SE CONNECTER</button>
                    </div>
                  </div>
                  {{-- <h6 class="text-muted or mt-[24px]">Or Sign in with</h6>
                  <div class="social mt-[24px]">
                    <div class="btn-showcase"><a class="btn btn-light inline-block" href="https://www.linkedin.com/login" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a><a class="btn btn-light inline-block" href="https://twitter.com/login?lang=en" target="_blank"><i class="fa-brands fa-x-twitter"></i></a><a class="btn btn-light inline-block" href="https://www.facebook.com/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a><a class="btn btn-light inline-block" href="https://www.google.com/" target="_blank"><i class="fa-brands fa-google"></i></a></div>
                  </div>
                  <p class="text-center mt-[24px] !mb-0">Don't have account?<a class="ms-2" href="sign-up.html">Create Account</a></p> --}}
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- latest jquery-->
      <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
      <!-- feather icon js-->
      <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
      <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
      <!-- scrollbar js-->
      <!-- Sidebar jquery-->
      <script src="{{ asset('assets/js/config.js') }}"></script>
      <script src="{{ asset('assets/js/modalpage/custom-modal.js') }}"> </script>
      <!-- Plugins JS start-->
      <!-- tooltip JS Ends-->
      <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
      <!-- Plugins JS Ends-->
      <!-- Theme js-->
      <script src="{{ asset('assets/js/script1.js') }}"></script>
      <script src="{{ asset('assets/js/script.js') }}"></script>
    </div>
  </body>
</html>