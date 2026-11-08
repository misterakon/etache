<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Situation temporelle</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('parametre.situation_temporelle.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label" for="libelle">Libellé</label>
              <input class="form-control" id="libelle" name="libelle" type="text" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="couleur">Couleur <span class="text-muted">(badges dashboard)</span></label>
              <input class="form-control form-control-color" id="couleur" name="couleur" type="color" value="#5B6B84" title="Choisir une couleur">
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="actif">Statut</label>
              <select class="form-select form-select-sm" id="actif" name="actif" required>
                <option value="" selected disabled>Sélectionnez un statut</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
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
