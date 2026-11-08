<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Action mesurable</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('feuille_de_route.action_mesurable.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="grid grid-cols-12 gap-3">

              @include('feuille_de_route._cascade', ['jusqua' => 'action_cle'])

              <div class="col-span-4 sm:col-span-12">
                <label class="form-label" for="ordre">Ordre</label>
                <input class="form-control" id="ordre" name="ordre" type="number" min="1" required>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="libelle">Libellé (action mesurable à exécuter)</label>
                <textarea class="form-control" id="libelle" name="libelle" rows="2" required></textarea>
              </div>
              <div class="col-span-12">
                <label class="form-label" for="description">Description <span class="text-muted">(optionnel)</span></label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
              </div>

              <div class="col-span-12">
                <hr>
                <label class="form-label font-semibold"><b>AFFECTATIONS</b></label>
                @foreach ($roles as $role)
                  <div class="mt-2">
                    <label class="form-label" for="role_{{ $role->id }}"><b>{{ $role->libelle }}</b></label>
                    <select class="form-select form-select-sm select-basic-multiple-four" id="role_{{ $role->id }}" name="affectations[{{ $role->id }}][]" multiple size="4">
                      @foreach ($personnels as $personnel)
                        <option value="{{ $personnel->id }}">{{ trim($personnel->prenom.' '.$personnel->nom) }}</option>
                      @endforeach
                    </select>
                  </div>
                @endforeach
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
