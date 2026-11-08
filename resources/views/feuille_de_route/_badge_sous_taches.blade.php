{{-- Indicateurs "3/5" (sous-tâches faites) et trombone (livrables déposés). Variable : $t (tâche chargée avec ->avecCompteSousTaches()). N'affiche rien sans donnée. --}}
@if (($t->sous_taches_count ?? 0) > 0)
  <span class="tl-badge tl-neutre" title="Sous-tâches faites" style="color:#242323"><i class="fa-solid fa-list-check"></i> {{ $t->sous_taches_cochees_count ?? 0 }}/{{ $t->sous_taches_count }}</span>
@endif
@if (($t->pieces_jointes_count ?? 0) > 0)
  <span class="tl-badge tl-neutre" title="Livrable(s) déposé(s)" style="color:#242323"><i class="fa-solid fa-paperclip"></i> {{ $t->pieces_jointes_count }}</span>
@endif
