{{-- Formulaire (fenêtre) d'une urgence de courrier. Le JS est dans js_parametrage_courrier. --}}
<div class="modal fade panel-modal" id="modal_urgence_courrier" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ old('_form') === 'urgence' && $errors->any() ? 1 : 0 }}">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="pc_titre_urgence">Urgence</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_urgence_courrier" action="{{ route('courrier.parametrage.urgence.store') }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="urgence">
        <input type="hidden" name="id_" value="{{ old('id_') }}">
        <div class="modal-body dark-modal">
          <div class="cr-champ">
            <label class="cr-etiquette" for="pc_urg_code">Code <span class="cr-requis">*</span></label>
            <input class="form-control form-control-sm" id="pc_urg_code" name="code" type="text" maxlength="20" value="{{ old('code') }}" required>
            @error('code') <p class="cr-erreur">{{ $message }}</p> @enderror
          </div>
          <div class="cr-champ">
            <label class="cr-etiquette" for="pc_urg_libelle">Libellé <span class="cr-requis">*</span></label>
            <input class="form-control form-control-sm" id="pc_urg_libelle" name="libelle" type="text" maxlength="100" value="{{ old('libelle') }}" required>
            @error('libelle') <p class="cr-erreur">{{ $message }}</p> @enderror
          </div>
          <div class="cr-champs-2">
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_urg_delai">Délai proposé (jours) <span class="cr-requis">*</span></label>
              <input class="form-control form-control-sm" id="pc_urg_delai" name="delai_jours" type="number" min="1" max="365" value="{{ old('delai_jours', 15) }}" required>
              @error('delai_jours') <p class="cr-erreur">{{ $message }}</p> @enderror
            </div>
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_urg_couleur">Couleur</label>
              <input class="form-control form-control-sm" id="pc_urg_couleur" name="couleur" type="color" value="{{ old('couleur', '#2B6CB0') }}" style="height:31px;padding:2px;" required>
              @error('couleur') <p class="cr-erreur">{{ $message }}</p> @enderror
            </div>
          </div>
          <div class="cr-champs-2">
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_urg_ordre">Ordre d'affichage</label>
              <input class="form-control form-control-sm" id="pc_urg_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required>
            </div>
            <div class="cr-champ">
              <label class="cr-etiquette" for="pc_urg_actif">Statut</label>
              <select class="form-select form-select-sm" id="pc_urg_actif" name="actif"><option value="1" @selected(old('actif', 1) == 1)>Actif</option><option value="0" @selected(old('actif', 1) == 0)>Inactif</option></select>
            </div>
          </div>
          <p class="c-o-light" style="font-size:12.5px;">Le délai est une valeur proposée : il pourra être modifié à chaque imputation.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
