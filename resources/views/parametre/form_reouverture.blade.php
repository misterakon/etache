{{-- Formulaire (modal) de réouverture d'un exercice clos. Le JS est dans js_reouverture. --}}
<div class="modal fade panel-modal" id="modal_reouverture" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Rouvrir l'exercice</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_reouverture" action="#" method="post">
        @csrf
        <div class="modal-body dark-modal">
          <p class="c-o-light">La réouverture rétablit les droits d'écriture et est tracée dans le journal d'activité.</p>
          <div class="col-span-12">
            <label class="form-label" for="reouverture_motif">Motif de la réouverture</label>
            <textarea class="form-control" id="reouverture_motif" name="motif" rows="3" minlength="10" maxlength="1000" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-danger text-white" type="submit">Rouvrir</button>
        </div>
      </form>
    </div>
  </div>
</div>
