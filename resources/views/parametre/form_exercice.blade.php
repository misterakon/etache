<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Exercice</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('parametre.exercice.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">

            <div class="col-span-12">
              <label class="form-label" for="annee">Exercice / Année</label>
              <input class="form-control" id="annee" name="annee" type="number" min="2000" max="2100" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="libelle">Libellé <span class="text-muted">(optionnel)</span></label>
              <input class="form-control" id="libelle" name="libelle" type="text" placeholder="Feuille de route 2026">
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="date_debut">Date début</label>
              <input class="form-control" id="date_debut" type="date" name="date_debut" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="date_fin">Date fin</label>
              <input class="form-control" id="date_fin" type="date" name="date_fin" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="actif">Statut</label>
              <select class="form-select form-select-sm" id="actif" name="actif" required>
                <option value="" selected disabled>Sélectionnez un statut</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
              </select>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="mode_avancement">Mode d'avancement</label>
              <select class="form-select form-select-sm" id="mode_avancement" name="mode_avancement" required>
                <option value="manuel" selected>Manuel (saisi par le responsable)</option>
                <option value="sous_taches">Par sous-tâches (calculé)</option>
              </select>
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
