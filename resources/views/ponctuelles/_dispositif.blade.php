{{-- Dispositif des tâches ponctuelles (barre d'outils, onglets d'état, liste). Utilisé par la page « Tâches ponctuelles » et par l'onglet de « Mes tâches ».
     Variables facultatives : $supervise, $agentFiltre. Le formulaire modal et la configuration sont dans ponctuelles._modal_et_config. --}}
@php
  $supervise = $supervise ?? \App\Support\PonctuellesDroits::supervise(auth()->user());
  $agentFiltre = $agentFiltre ?? null;
@endphp
<div class="ep-page pt-page" id="pt">
  @if ($agentFiltre)
    <div class="pt-agent"><i class="fa-solid fa-user"></i><span>Tâches ponctuelles de <b>{{ $agentFiltre['moi'] ? 'vous-même' : $agentFiltre['nom'] }}</b></span>@unless ($agentFiltre['moi'])<a href="{{ route('dgepn.personnel.fiche', $agentFiltre['id']) }}">Voir sa fiche</a>@endunless<a href="{{ route('ponctuelle.index') }}"><i class="fa-solid fa-xmark"></i> Revenir à mes tâches</a></div>
  @endif

  <div class="pt-barre">
    <div class="pt-onglets" role="tablist" id="pt_portees" @if ($agentFiltre) hidden @endif>
      <button type="button" role="tab" class="actif" data-portee="mes"><i class="fa-solid fa-user-check"></i> Mes tâches <em data-nb="mes"></em></button>
      <button type="button" role="tab" data-portee="confiees"><i class="fa-solid fa-hand-holding-hand"></i> Confiées par moi <em data-nb="confiees"></em></button>
      @if ($supervise)<button type="button" role="tab" data-portee="equipe"><i class="fa-solid fa-people-group"></i> Mon équipe <em data-nb="equipe"></em></button>@endif
    </div>
    <label class="pt-recherche"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="pt_q" placeholder="Rechercher une tâche, un agent…" autocomplete="off"></label>
    <button type="button" class="pt-chip pt-chip--valider" id="pt_a_valider" hidden><i class="fa-solid fa-scale-balanced"></i> À valider <b>0</b></button>
    <button type="button" class="pt-nouvelle-btn" id="pt_nouvelle"><i class="fa-solid fa-plus"></i> Nouvelle tâche <kbd class="pt-kbd">N</kbd></button>
  </div>

  <div class="pt-panneau">
    <div class="pt-etats" role="tablist" id="pt_etats" aria-label="Filtrer par état"></div>
    <div class="pt-zone" id="pt_zone" aria-live="polite"><div class="pt-chargement"><i class="fa-solid fa-spinner fa-spin"></i> Chargement…</div></div>
  </div>
</div>
