{{-- Ligne de tâche de la liste. Variables : $t, $ton (retard|attente|encours|terminee…), $retardTexte (texte du badge rouge ou null), $avenirTexte (badge violet ou null), $actions (bool), $rowId (bool) --}}
@php
  $respT = $t->affectations->where('role_affectation_id', $responsableRoleId)->filter(fn ($a) => $a->personnel)->values();
  $dateT = $t->date_fin;
@endphp
<div class="pg-item col-span-6 xxl:col-span-12 md:col-span-12" @if (! empty($rowId)) id="row-{{ $t->id }}" @endif>
  <article class="tk tl-{{ $ton }}">
    <a class="tk__date" href="{{ route('feuille_de_route.tache.details', $t->id) }}" title="Échéance">
      @if ($dateT)
        <strong>{{ $dateT->format('d') }}</strong><span>{{ \Illuminate\Support\Str::upper(rtrim($dateT->translatedFormat('M'), '.')) }}</span><small>{{ $dateT->format('Y') }}</small>
      @else
        <i class="fa-regular fa-calendar-xmark"></i><small>Sans échéance</small>
      @endif
    </a>

    <div class="tk__corps">
      <div class="tk__titre">
        <a href="{{ route('feuille_de_route.tache.details', $t->id) }}">{{ $t->code }}</a>
        @include('feuille_de_route._badge_direction', ['tache' => $t])
      </div>
      <p class="tk__libelle" title="{{ $t->libelle }}">{{ $t->libelle }}</p>
      <div class="tk__meta">
        <span class="tl-badge tl-{{ ($ton === 'retard' || ! empty($retardTexte)) && $ton !== 'terminee' ? 'neutre' : $ton }}">{{ $t->statutExecution?->libelle ?? '—' }}</span>
        @if (! empty($retardTexte))<span class="tl-badge tl-retard tl-solid">{{ $retardTexte }}</span>@endif
        @if (! empty($avenirTexte))<span class="tl-badge tl-avenir">{{ $avenirTexte }}</span>@endif
        @include('feuille_de_route._badge_sous_taches', ['t' => $t])
        <span class="tk__prog" title="Avancement {{ $t->avancement_pourcentage }} %"><i><b style="width: {{ $t->avancement_pourcentage }}%"></b></i>{{ $t->avancement_pourcentage }}%</span>
        <span class="tk__budget" title="Budget estimé"><i class="fa-solid fa-coins"></i>{{ $t->budget_estime !== null ? $fcfa($t->budget_estime) : '—' }}</span>
      </div>
    </div>

    <div class="tk__droite">
      @if ($respT->isNotEmpty())
        <ul class="tk__agents">
          @foreach ($respT->take(3) as $affectation)
            @php $p = $affectation->personnel; @endphp
            <li title="{{ $p->nomComplet() }}">
              @if ($p->photo_url)<img src="{{ $p->photo_url }}" alt="{{ $p->nomComplet() }}">@else<span>{{ $p->initiales() }}</span>@endif
            </li>
          @endforeach
        </ul>
      @endif
      <div class="tk__actions">
        <a class="tk__ouvrir" href="{{ route('feuille_de_route.tache.details', $t->id) }}"><i class="fa-regular fa-folder-open"></i> Ouvrir</a>
        @if (! empty($actions))
          @hasanyrole('ADMIN|DG')
          <button class="tl-btn tl-btn--edit" type="button" title="Modifier" onclick="edit_tache({{ $t->id }})"><i class="fa-regular fa-pen-to-square"></i></button>
          <button class="tl-btn tl-btn--suppr" type="button" title="Supprimer" onclick="deletes_tache({{ $t->id }})"><i class="fa-solid fa-trash-can"></i></button>
          @endhasanyrole
        @endif
      </div>
    </div>
  </article>
</div>
