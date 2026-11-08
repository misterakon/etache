<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Direction</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('dgepn.direction.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label" for="nom">Nom</label>
              <input class="form-control" id="nom" name="nom" type="text" placeholder="Ex: Direction de l'Informatique" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="code">Code <span class="text-muted">(optionnel)</span></label>
              <input class="form-control" id="code" name="code" type="text" maxlength="50" placeholder="Ex: DI">
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="description">Description <span class="text-muted">(optionnel)</span></label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
