<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Emploi</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('parametre.emploi.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label" for="famille_emploi_id">Famille d'emploi</label>
              <select class="form-select form-select-sm" id="famille_emploi_id" name="famille_emploi_id" required>
                <option value="" selected disabled>Sélectionnez une famille d'emploi</option>
                @foreach ($famillesEmploi as $famille)
                  <option value="{{ $famille->id }}">{{ $famille->libelle }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="libelle">Libellé</label>
              <input class="form-control" id="libelle" name="libelle" type="text" placeholder="Ex: Ingénieur informatique option gestion" required>
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
