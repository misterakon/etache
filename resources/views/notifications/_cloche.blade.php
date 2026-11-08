{{-- Cloche de l'en-tete : compteur reel + 10 dernieres notifications non lues de l'utilisateur connecte (liste defilante) --}}
@php
  $utilisateur = auth()->user();
  $nbNonLues = $utilisateur->unreadNotifications()->count();
  // $recentes = $utilisateur->notifications()->latest()->limit(10)->get();
  $recentes = $utilisateur->unreadNotifications()->latest()->limit(10)->get();
  $bordures = ['retard' => 'border-l-danger', 'attente' => 'border-l-warning', 'avenir' => 'border-l-primary'];
@endphp
<li class="onhover-dropdown">
  @once
    <style>
      /* Liste défilante limitée à l'écran ; le pied est HORS de la liste : rien ne peut passer dessus */
      .onhover-show-div.notification-dropdown > ul { max-height: min(480px, calc(100vh - 260px)); overflow-y: auto !important; overscroll-behavior: contain; scrollbar-width: thin; margin-bottom: 0 !important; }
      /* Le thème centre le texte du DERNIER <li> de la liste (prévu pour l'ancien pied) : on force l'alignement à gauche */
      .onhover-show-div.notification-dropdown > ul > li > a, .onhover-show-div.notification-dropdown > ul > li > a .toast-body { text-align: left !important; }
      /* Même fond gris pour toutes les notifications (le thème le retirait au dernier <li>) */
      .onhover-show-div.notification-dropdown > ul > li:has(> a) { background: #F7F8FA !important; border-radius: 6px; }
      .onhover-show-div.notification-dropdown .cloche-pied { display: flex !important; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 18px; position: relative; z-index: 5; background: #fff !important; border-top: 1px solid #E3E8EF; box-shadow: 0 -10px 14px -10px rgba(20, 48, 79, .18); }
    </style>
  @endonce
  <div class="notification-box">
    <svg>
      <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
    </svg>
    @if ($nbNonLues > 0)
      <span class="badge rounded-full badge-danger text-white">{{ $nbNonLues > 9 ? '9+' : $nbNonLues }}</span>
    @endif
  </div>
  <div class="onhover-show-div notification-dropdown">
    <h6 class="mb-0 dropdown-title f-[18px]">Notifications @if ($nbNonLues > 0) <small class="c-o-light">({{ $nbNonLues }} non lue{{ $nbNonLues > 1 ? 's' : '' }})</small> @endif</h6>
    <ul>
      @forelse ($recentes as $notif)
        @php $d = $notif->data; @endphp
        <li class="{{ $bordures[$d['ton'] ?? ''] ?? 'border-l-secondary' }} default-show-toast items-center text-light fade show !border-l-4" style="{{ $notif->read_at ? 'opacity:.65;' : '' }}">
          <a href="{{ route('notifications.ouvrir', $notif->id) }}" style="display:block; color:inherit; text-decoration:none;">
            <div class="toast-body p-2" style="text-transform: none">
              <p class="mb-1" style="{{ $notif->read_at ? '' : 'font-weight:700;' }}">{{ $d['titre'] ?? 'Notification' }}@if (! empty($d['code'])) — {{ $d['code'] }}@endif</p>
              <small class="c-o-light" style="display:block;">{{ \Illuminate\Support\Str::limit($d['message'] ?? '', 110) }}</small>
              <small class="c-o-light">{{ $notif->created_at->locale('fr')->diffForHumans() }}</small>
            </div>
          </a>
        </li>
      @empty
        <li class="default-show-toast fade show"><div class="toast-body p-3 text-center"><p class="mb-0 c-o-light">Aucune notification</p></div></li>
      @endforelse
    </ul>
    <div class="cloche-pied">
          <a href="{{ route('notifications.index') }}">Voir tout @if ($nbNonLues > 10)<small class="c-o-light">(+ {{ $nbNonLues - 10 }} autre{{ $nbNonLues - 10 > 1 ? 's' : '' }})</small>@endif</a>
          @if ($nbNonLues > 0)
            <form method="POST" action="{{ route('notifications.tout_lire') }}" class="m-0">
              @csrf
              <button type="submit" class="btn btn-link p-0" style="font-size:13px;">Tout marquer comme lu</button>
            </form>
          @endif
    </div>
  </div>
</li>
