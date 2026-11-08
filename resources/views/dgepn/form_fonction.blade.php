{{-- Formulaire (fenêtre) d'une fonction. Le JS est dans js_fonction. --}}
<div class="modal fade panel-modal" id="modal_fonction" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ old('_form') === 'fonction' && $errors->any() ? 1 : 0 }}">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="fn_titre">Fonction</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_fonction" action="{{ route('dgepn.fonction.store') }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="fonction">
        <input type="hidden" name="id_" value="{{ old('id_') }}">
        <div class="modal-body dark-modal">
          <div class="col-span-12">
            <label class="form-label" for="fn_code"><b>Code</b></label>
            <input class="form-control" id="fn_code" name="code" type="text" maxlength="30" value="{{ old('code') }}" required>
            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="fn_libelle"><b>Libellé</b></label>
            <input class="form-control" id="fn_libelle" name="libelle" type="text" maxlength="100" value="{{ old('libelle') }}" required>
            @error('libelle') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="fn_categorie"><b>Rattachement</b></label>
            <select class="form-select form-select-sm" id="fn_categorie" name="categorie" required>
              <option value="service">Rattaché à un service</option>
              <option value="direction_generale">Rattaché à la Direction Générale</option>
              <option value="mandat" disabled>Poste à titulaire unique (fonctions d'origine)</option>
            </select>
            @error('categorie') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          <div class="col-span-12 mt-2">
            <div class="grid grid-cols-12 gap-3">
              <div class="col-span-6"><label class="form-label" for="fn_ordre"><b>Ordre</b></label><input class="form-control" id="fn_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required></div>
              <div class="col-span-6"><label class="form-label" for="fn_actif"><b>Statut</b></label><select class="form-select form-select-sm" id="fn_actif" name="actif"><option value="1">Active</option><option value="0">Inactive</option></select></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
