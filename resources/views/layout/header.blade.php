<div class="page-header">
    <div class="header-wrapper grid grid-cols-12 m-0">

        <div class="header-logo-wrapper hidden col-auto p-0 lg:block">
            <div class="logo-wrapper">
                <a href="index.html">
                    <img class="max-w-full h-auto for-light" src="{{ asset('assets/images/logo/dgepn_logo.png') }}" alt="">
                    <img class="max-w-full h-auto for-dark" src="{{ asset('assets/images/logo/dgepn_logo.png') }}" alt="">
                </a>
            </div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
        </div>
        {{-- <div class="left-header col-span-5 xxl:col-span-6 xl:col-span-5 lg:col-span-4 md:col-span-3">
            <div class="notification-slider">
                <div class="h-full !flex items-center"> <img src="{{ asset('assets/images/giftools.gif') }}" alt="gif">
                <h6 class="mb-0 font-normal"><span class="font-primary">Don't Miss Out!</span><span class="f-light"> Our new update has been released.</span></h6><i class="icon-arrow-top-right f-light"></i>
                </div>
                <div class="h-full !flex items-center"> <img src="{{ asset('assets/images/giftools.gif') }}" alt="gif">
                <h6 class="mb-0 font-normal"><span class="f-light">Something you love is now on sale!</span></h6><a class="ms-1" href="https://1.envato.market/3GVzd" target="_blank">Buy now !</a>
                </div>
            </div>
        </div> --}}
        <div class="nav-right col-span-7 xxl:col-span-6 xl:col-span-7 md:col-span-11 float-right right-header p-0 ms-auto">
        <ul class="nav-menus">
            {{-- <li class="language-nav">
                <div class="translate_wrapper relative">
                    <input class="peer hidden" id="lang-toggle" type="checkbox">
                    <label class="current_lang flex items-center cursor-pointer !mb-0" for="lang-toggle"><span class="lang flex items-center"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">EN</span></span></label>
                    <div class="more_lang custom-scrollbar absolute top-full mt-2 bg-white rounded-md shadow-md w-44 z-50 hidden peer-checked:block">
                    
                    <div class="lang" data-value="en">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-us"></i><span class="lang-txt text-sm flex flex-col">English<span class="text-xs text-gray-500">(US)</span></span></div>
                    </div>
                    <div class="lang" data-value="de">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-de"></i><span class="lang-txt text-sm">Deutsch</span></div>
                    </div>
                    <div class="lang" data-value="es">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-es"></i><span class="lang-txt text-sm">Español</span></div>
                    </div>
                    <div class="lang selected" data-value="fr">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-fr"></i><span class="lang-txt text-sm">Français</span></div>
                    </div>
                    <div class="lang" data-value="pt">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-pt"></i><span class="lang-txt text-sm flex flex-col">Português<span class="text-xs text-gray-500">(BR)</span></span></div>
                    </div>
                    <div class="lang" data-value="cn">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-cn"></i><span class="lang-txt text-sm">简体中文</span></div>
                    </div>
                    <div class="lang" data-value="ae">
                        <div class="flex items-center cursor-pointer"><i class="flag-icon flag-icon-ae"></i><span class="lang-txt text-sm flex flex-col">لعربية<span class="text-xs text-gray-500">(ae)</span></span></div>
                    </div>
                    </div>
                </div>
            </li> --}}
            <li class="recherche-globale" id="rg">
                <form method="get" action="{{ route('recherche') }}" role="search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" id="rg-q" placeholder="Rechercher…  ( / )" maxlength="120" autocomplete="off" aria-label="Rechercher dans toute l'application" aria-controls="rg-resultats"></form>
                <div class="rg-resultats" id="rg-resultats" role="listbox" hidden></div>
            </li>
            <li class="aide-bulle-aide"><button type="button" class="aide-btn-entete" id="aide-ouvrir" title="Aide de cette page (F1)" aria-label="Aide de cette page">?</button></li>
            <li class="fullscreen-body">                       <span>
                <svg id="maximize-screen">
                <use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
                </svg></span></li>
            {{-- <li>                         <span class="header-search">
                <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>
                </svg></span></li> --}}

            <li>
            <div class="mode">
                <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
                </svg>
            </div>
            </li>
            @include('notifications._cloche')
            <li class="profile-nav onhover-dropdown !py-0 !pe-0">

            @php
            $moi = auth()->user()?->personnel;

            // Initiales de l'agent ; si le compte n'est lié à aucun agent, celles de son nom (2 premiers mots)
            $initiales = $moi?->initiales()
                ?: collect(preg_split('/\s+/', trim(auth()->user()->name)))->filter()->take(2)
                        ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))->implode('');

            $avatarInitiales = 'data:image/svg+xml;charset=UTF-8,'.rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><rect width="120" height="120" fill="#dbe7f7"/><text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Arial, sans-serif" font-size="44" fill="#0c447c">'.e($initiales ?: '?').'</text></svg>'
            );
            @endphp

            <div class="flex profile-media items-center">
                <img src="{{ $moi?->photo_url ?: $avatarInitiales }}" alt="{{ $moi?->nomComplet() ?: auth()->user()->name }}" width="35" height="35" style="object-fit: cover; border-radius: 50%;">
                <div class="profile-content"><span><b>{{ auth()->user()->name }}</b></span>
                    <p class="mb-0" style="color: #111213; font-weight:bold">{{ auth()->user()->profilAcces?->libelle ?? 'Utilisateur' }} <i class="align-middle fa-solid fa-angle-down"></i></p>
                </div>
            </div>
            <ul class="profile-dropdown onhover-show-div">
                <li> <a class="flex items-center" href="{{ route('profile.edit') }}"><i data-feather="user"></i><span>Mon compte</span></a></li>
                @if (auth()->user()->personnel_id)
                <li> <a class="flex items-center" href="{{ route('dgepn.personnel.fiche', auth()->user()->personnel_id) }}"><i data-feather="briefcase"></i><span>Ma fiche agent</span></a></li>
                @endif
                <li><a class="flex items-center" href="{{ route('feuille_de_route.tache_mes_taches') }}"><i data-feather="file-text"></i><span>Mes tâches</span></a></li>
                <li>
                    <form method="POST" action="{{ route('verrouillage.lock') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="retour" value="{{ url()->current() }}">
                        <a class="flex items-center" href="{{ route('verrouillage.lock') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i data-feather="lock"></i><span>Verrouiller</span>
                        </a>
                    </form>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <a class="flex items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i data-feather="log-in"></i><span>Déconnexion</span>
                        </a>
                    </form>
                </li>
            </ul>
            </li>
        </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
        <div class="ProfileCard u-cf">                        
        <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
        <div class="ProfileCard-details">
        <div class="ProfileCard-realName"></div>
        </div>
        </div>
        </script>
        <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>