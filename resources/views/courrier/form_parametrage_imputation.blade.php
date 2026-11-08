{{-- Trois petites fenêtres : instruction du DG, délai de traitement, poste destinataire. Le JS est dans js_parametrage_courrier. Variable : $agents --}}
@php $ouvrir = fn (string $nom) => old('_form') === $nom && $errors->any() ? 1 : 0; @endphp

{{-- Instruction --}}
<div class="modal fade panel-modal" id="modal_instruction" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $ouvrir('instruction') }}">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title" id="pc_titre_instruction">Instruction</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <form id="form_instruction" action="{{ route('courrier.parametrage.instruction.store') }}" method="post">
      @csrf
      <input type="hidden" name="_form" value="instruction"><input type="hidden" name="id_" value="{{ old('id_') }}">
      <div class="modal-body dark-modal">
        <div class="cr-champ"><label class="cr-etiquette" for="pc_ins_libelle">Libellé <span class="cr-requis">*</span></label>
          <input class="form-control form-control-sm" id="pc_ins_libelle" name="libelle" type="text" maxlength="100" value="{{ old('libelle') }}" required>
          @error('libelle') <p class="cr-erreur">{{ $message }}</p> @enderror</div>
        <div class="cr-champs-2">
          <div class="cr-champ"><label class="cr-etiquette" for="pc_ins_ordre">Ordre</label><input class="form-control form-control-sm" id="pc_ins_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required></div>
          <div class="cr-champ"><label class="cr-etiquette" for="pc_ins_actif">Statut</label><select class="form-select form-select-sm" id="pc_ins_actif" name="actif"><option value="1">Actif</option><option value="0">Inactif</option></select></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Enregistrer</button></div>
    </form>
  </div></div>
</div>

{{-- Délai --}}
<div class="modal fade panel-modal" id="modal_delai" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $ouvrir('delai') }}">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title" id="pc_titre_delai">Délai de traitement</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <form id="form_delai" action="{{ route('courrier.parametrage.delai.store') }}" method="post">
      @csrf
      <input type="hidden" name="_form" value="delai"><input type="hidden" name="id_" value="{{ old('id_') }}">
      <div class="modal-body dark-modal">
        <div class="cr-champ"><label class="cr-etiquette" for="pc_del_code">Code <span class="cr-requis">*</span></label>
          <input class="form-control form-control-sm" id="pc_del_code" name="code" type="text" maxlength="20" value="{{ old('code') }}" required>
          @error('code') <p class="cr-erreur">{{ $message }}</p> @enderror</div>
        <div class="cr-champ"><label class="cr-etiquette" for="pc_del_libelle">Libellé <span class="cr-requis">*</span></label>
          <input class="form-control form-control-sm" id="pc_del_libelle" name="libelle" type="text" maxlength="60" value="{{ old('libelle') }}" required>
          @error('libelle') <p class="cr-erreur">{{ $message }}</p> @enderror</div>
        <div class="cr-champs-2">
          <div class="cr-champ"><label class="cr-etiquette" for="pc_del_libre">Échéance</label><select class="form-select form-select-sm" id="pc_del_libre" name="saisie_libre"><option value="0">Calculée (en jours)</option><option value="1">Date saisie à l'imputation</option></select></div>
          <div class="cr-champ"><label class="cr-etiquette" for="pc_del_jours">Nombre de jours</label><input class="form-control form-control-sm" id="pc_del_jours" name="delai_jours" type="number" min="0" max="365" value="{{ old('delai_jours', 1) }}">
            @error('delai_jours') <p class="cr-erreur">{{ $message }}</p> @enderror</div>
        </div>
        <div class="cr-champs-2">
          <div class="cr-champ"><label class="cr-etiquette" for="pc_del_ordre">Ordre</label><input class="form-control form-control-sm" id="pc_del_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required></div>
          <div class="cr-champ"><label class="cr-etiquette" for="pc_del_actif">Statut</label><select class="form-select form-select-sm" id="pc_del_actif" name="actif"><option value="1">Actif</option><option value="0">Inactif</option></select></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Enregistrer</button></div>
    </form>
  </div></div>
</div>

{{-- Poste destinataire --}}
<div class="modal fade panel-modal" id="modal_poste" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $ouvrir('poste') }}">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title" id="pc_titre_poste">Poste destinataire</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <form id="form_poste" action="{{ route('courrier.parametrage.poste.store') }}" method="post">
      @csrf
      <input type="hidden" name="_form" value="poste"><input type="hidden" name="id_" value="{{ old('id_') }}">
      <div class="modal-body dark-modal">
        <div class="cr-champ"><label class="cr-etiquette" for="pc_pos_position">Poste de l'organigramme <span class="cr-requis">*</span></label>
          <select class="form-select form-select-sm" id="pc_pos_position" name="position" required onchange="pc_afficher_titulaire_poste()">
            <option value="">Choisir un poste</option>
            @foreach ($positionsOrganigramme as $position)
              <option value="{{ $position['cle'] }}" data-titulaire="{{ $position['titulaire']?->nomComplet() ?? 'Poste vacant' }}" data-interim="{{ $position['interimaire'] ? 1 : 0 }}" @disabled($position['configuration'] && (string) $position['configuration']->id !== (string) old('id_')) @selected(old('position') === $position['cle'])>{{ $position['libelle'] }}</option>
            @endforeach
          </select>
          @error('position') <p class="cr-erreur">{{ $message }}</p> @enderror</div>
        <div class="cr-champ"><label class="cr-etiquette" for="pc_pos_titulaire">Titulaire actuel</label>
          <input class="form-control form-control-sm" id="pc_pos_titulaire" type="text" value="" readonly>
          <small class="c-o-light" id="pc_pos_titulaire_aide">Le titulaire ou l'intérimaire est déterminé automatiquement.</small></div>
        <div class="cr-champs-2">
          <div class="cr-champ"><label class="cr-etiquette" for="pc_pos_reimpute">Peut réimputer</label><select class="form-select form-select-sm" id="pc_pos_reimpute" name="peut_reimputer"><option value="0">Non</option><option value="1">Oui</option></select></div>
          <div class="cr-champ"><label class="cr-etiquette" for="pc_pos_ordre">Ordre</label><input class="form-control form-control-sm" id="pc_pos_ordre" name="ordre" type="number" min="1" max="999" value="{{ old('ordre', 1) }}" required></div>
        </div>
        <div class="cr-champ"><label class="cr-etiquette" for="pc_pos_actif">Statut</label><select class="form-select form-select-sm" id="pc_pos_actif" name="actif"><option value="1">Actif</option><option value="0">Inactif</option></select></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Enregistrer</button></div>
    </form>
  </div></div>
</div>
