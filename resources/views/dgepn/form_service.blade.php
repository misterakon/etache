<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Service</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('dgepn.service.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label">Rattachement</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="rattachement" value="sous_direction" id="rattachement_sd" checked onclick="toggle_rattachement_service()">
                <label class="form-check-label" for="rattachement_sd">À une sous-direction</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="rattachement" value="direction" id="rattachement_d" onclick="toggle_rattachement_service()">
                <label class="form-check-label" for="rattachement_d">Directement à une direction</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="rattachement" value="dg" id="rattachement_dg" onclick="toggle_rattachement_service()">
                <label class="form-check-label" for="rattachement_dg">Directement à la Direction Générale <span class="text-muted">(ex: Service Financier)</span></label>
              </div>
            </div>

            <div class="col-span-12 mt-2" id="bloc_sous_direction">
              <label class="form-label" for="sous_direction_id">Sous-direction</label>
              <select class="form-select form-select-sm" id="sous_direction_id" name="sous_direction_id">
                <option value="" selected disabled>Sélectionnez une sous-direction</option>
                @foreach ($sousDirections as $sd)
                  <option value="{{ $sd->id }}">{{ $sd->direction->nom }} — {{ $sd->nom }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-span-12 mt-2 hidden" id="bloc_direction_directe">
              <label class="form-label" for="direction_id">Direction</label>
              <select class="form-select form-select-sm" id="direction_id" name="direction_id">
                <option value="" selected disabled>Sélectionnez une direction</option>
                @foreach ($directions as $direction)
                  <option value="{{ $direction->id }}">{{ $direction->nom }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-span-12 mt-2">
              <label class="form-label" for="nom">Nom</label>
              <input class="form-control" id="nom" name="nom" type="text" placeholder="Ex: Service Financier et des Moyens Généraux" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="code">Code <span class="text-muted">(optionnel)</span></label>
              <input class="form-control" id="code" name="code" type="text" maxlength="50">
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
