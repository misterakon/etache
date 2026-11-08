{{-- Onglet "Sous-tâches" du détail d'une tâche. Variable : $tache. Style : public/assets/css/tache-details.css (td-panel, td-st-*) --}}
@php $droits = \App\Support\SousTacheDroits::pour($tache, auth()->user()); @endphp

<div class="td-panel">
  <div class="td-panel__tete">
    <div class="td-card__tete">
      <span class="td-card__ico td-card__ico--bleu"><i class="fa-solid fa-list-check"></i></span>
      <div><h5>Sous-tâches</h5><p>Décomposer la tâche : chaque sous-tâche faite fait avancer la tâche</p></div>
    </div>
    @if ($droits['gerer'])
      <button class="td-btn td-btn--primary" type="button" onclick="st_add_form({{ $tache->id }})"><i class="fa-solid fa-plus"></i> Ajouter une sous-tâche</button>
    @endif
  </div>

  <div id="zone-sous-taches">
    @include('feuille_de_route._sous_taches_liste', ['tache' => $tache, 'droits' => $droits])
  </div>
</div>

@include('feuille_de_route.form_sous_tache', ['tache' => $tache])
@include('feuille_de_route.js_sous_tache')
