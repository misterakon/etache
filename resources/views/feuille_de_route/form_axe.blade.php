@php $defautId = $exerciceDefautId ?? \App\Models\Exercice::parDefaut()?->id; @endphp

<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Axe</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('feuille_de_route.axe.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="grid grid-cols-12 gap-3">

              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="exercice_id">Exercice</label>
                <select class="form-select form-select-sm" id="exercice_id" name="exercice_id" required>
                  <option value="" disabled>Sélectionnez un exercice</option>
                  @foreach ($exercices as $exercice)
                    <option value="{{ $exercice->id }}" @selected($exercice->id == $defautId)>{{ $exercice->libelle ?: $exercice->annee }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="ordre">Ordre d'affichage</label>
                <input class="form-control" id="ordre" name="ordre" type="number" min="1" required>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="libelle">Libellé de l'axe</label>
                <input class="form-control" id="libelle" name="libelle" type="text" placeholder="Ex: Harmonisation du cadre juridique et de la gouvernance" required>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="resultat_attendu">Résultat attendu</label>
                <textarea class="form-control" id="resultat_attendu" name="resultat_attendu" rows="2"></textarea>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="description">Description <span class="text-muted">(optionnel)</span></label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-primary text-white submit" id="BtnEnregistrer" type="submit">Enregistrer</button>
          </div>
      </form>

    </div>
  </div>
</div>
