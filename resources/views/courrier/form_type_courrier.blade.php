{{-- Formulaire (fenêtre) d'un type de courrier. Le JS est dans js_parametrage_courrier. --}}
<div class="modal fade panel-modal" id="modal_type_courrier" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ old('_form') === 'type' && $errors->any() ? 1 : 0 }}">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="pc_titre_type">Type de courrier</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_type_courrier" action="{{ route('courrier.parametrage.type.store') }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="type">
        <input type="hidden" name="id_" value="{{ old('id_') }}">
        <div class="modal-body dark-modal">
          <div class="cr-champ">
            <label class="cr-etiquette" for="pc_type_code">Code <span class="cr-requis">*</span></label>
            <input class="form-control form-control-sm" id="pc_type_code" name="code" type="text" maxlength="20" value="{{ old('code') }}" required>
            @error('code') <p class="cr-erreur">{{ $message }}</p> @enderror
          </div>
          <div class="cr-champ">
            <label class="cr-etiquette" for="pc_type_libelle">Libellé <span class="cr-requis">*</span></label>
            <input class="form-control form-control-sm" id="pc_type_libelle" name="libelle" type="text" maxlength="100" value="{{ old('libelle') }}" required>
            @error('libelle') <p class="cr-erreur">{{ $message }}</p> @enderror
          </div>
          <div class="cr-champs-2">
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_type_ordre">Ordre d'affichage</label>
              <input class="form-control form-control-sm" id="pc_type_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required>
            </div>
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_type_actif">Statut</label>
              <select class="form-select form-select-sm" id="pc_type_actif" name="actif"><option value="1" @selected(old('actif', 1) == 1)>Actif</option><option value="0" @selected(old('actif', 1) == 0)>Inactif</option></select>
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
