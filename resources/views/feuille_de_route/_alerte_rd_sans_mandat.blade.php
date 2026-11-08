{{-- À inclure en haut de dashboard_user et mes_taches. S'affiche seulement pour un RD sans mandat de direction en cours. --}}
@if (auth()->user()?->sansMandatDirection())
  <div class="alert alert-warning text-black" role="alert" style="margin-bottom:16px;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    Aucune direction ne vous est rattachée, <b>contactez l'administrateur</b>. En attendant, vous voyez et mettez à jour uniquement vos tâches.
  </div>
@endif
