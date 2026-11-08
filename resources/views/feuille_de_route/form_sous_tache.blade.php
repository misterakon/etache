{{-- Formulaire (modal) d'une sous-tâche. Variable : $tache. Le JS est dans js_sous_tache. --}}
<div class="modal fade panel-modal" id="modal_sous_tache" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Sous-tâche</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_sous_tache" action="{{ route('feuille_de_route.sous_tache.store') }}" method="post">
        @csrf
        <input type="hidden" name="id_" value="">
        <input type="hidden" name="tache_id" value="{{ $tache->id }}">

        <div class="modal-body dark-modal">
          <div class="col-span-12">
            <label class="form-label" for="st_libelle">Libellé</label>
            <input class="form-control" id="st_libelle" name="libelle" type="text" maxlength="255" required>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="st_description">Description <span class="text-muted">(optionnel)</span></label>
            <textarea class="form-control" id="st_description" name="description" rows="2"></textarea>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="st_echeance">Échéance <span class="text-muted">(optionnel)</span></label>
            <input class="form-control" id="st_echeance" name="echeance" type="date">
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="st_modalites">Modalités d'exécution <span class="text-muted">(optionnel)</span></label>
            <textarea class="form-control" id="st_modalites" name="modalites" rows="2" maxlength="2000"></textarea>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="st_conditions">Conditions d'exécution <span class="text-muted">(optionnel)</span></label>
            <textarea class="form-control" id="st_conditions" name="conditions" rows="2" maxlength="2000"></textarea>
          </div>
          <p class="text-muted mt-2" style="font-size:12px;">Soumise à la validation du supérieur hiérarchique : elle ne compte dans l'avancement qu'une fois validée.</p>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="st_btn_enregistrer" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
