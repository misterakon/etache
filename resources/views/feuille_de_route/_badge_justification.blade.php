{{-- Badge "Report demandé" / "Justification en attente" / "Justifié". Variables : $t (tache), $justif --}}
@php $demandeAttente = $justif['enAttente']->get($t->id); @endphp
@if ($demandeAttente)
  <span class="tl-badge tl-attente" style="color: #242323">{{ $demandeAttente->estReport() ? 'Report demandé' : 'Justification en attente' }}</span>
@elseif (in_array($t->id, $justif['justifiees']))
  <span class="tl-badge tl-neutre">Justifié</span>
@endif
