{{-- Ligne de tâche des onglets « Toutes mes tâches » et « Statuts » de Mes tâches.
     Variables : $t, $grp (attente|encours|terminees, facultatif) + les fonctions de mes_taches ($tonCarte, $tonStatut, $enRetard, $libellePriorite) et $justif. --}}
@php
  $tn = $tonCarte($t);
  $prioT = $libellePriorite($t);
  $retardT = $enRetard($t);
@endphp
<article class="mx-ligne tl-{{ $tn }}" data-ton="{{ $tn }}" data-grp="{{ $grp ?? '' }}" data-retard="{{ $retardT ? 1 : 0 }}" data-txt="{{ \Illuminate\Support\Str::lower($t->code.' '.$t->libelle) }}">
  <a class="mx-date" href="{{ route('feuille_de_route.tache.details', $t->id) }}" title="Échéance">
    @if ($t->date_fin)
      <strong>{{ $t->date_fin->format('d') }}</strong><b>{{ \Illuminate\Support\Str::upper(rtrim($t->date_fin->translatedFormat('M'), '.')) }}</b><small>{{ $t->date_fin->format('Y') }}</small>
    @else
      <i class="fa-regular fa-calendar-xmark"></i><small>Sans échéance</small>
    @endif
  </a>
  <div class="mx-corps">
    <div class="mx-titre">
      <a href="{{ route('feuille_de_route.tache.details', $t->id) }}" class="mx-code">{{ $t->code }}</a>
      @if ($t->statutExecution)<span class="tl-badge tl-{{ $tonStatut($t) }}">{{ $t->statutExecution->libelle }}</span>@endif
      @if ($retardT)<span class="tl-badge tl-retard tl-solid">{{ (int) $t->date_fin->diffInDays(today()) }} j de retard</span>@endif
      @include('feuille_de_route._badge_justification', ['t' => $t])
    </div>
    <p class="mx-libelle" title="{{ $t->libelle }}">{{ $t->libelle }}</p>
  </div>
  <div class="mx-suivi">
    <span class="mx-prio">{{ $prioT ? 'Priorité '.$prioT : 'Sans priorité' }}</span>
    <div class="mx-barre"><i><b style="width: {{ $t->avancement_pourcentage }}%"></b></i><span>{{ $t->avancement_pourcentage }}%</span></div>
  </div>
  <div class="mx-actions">
    <a class="mt-btn mt-btn--voir" href="{{ route('feuille_de_route.tache.details', $t->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Détails"><i class="fa-regular fa-eye"></i></a>
    @include('feuille_de_route._btn_justifier', ['t' => $t, 'classe' => 'mt-btn mt-btn--edit'])
  </div>
</article>
