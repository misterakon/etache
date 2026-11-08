{{-- Formulaire (modal) de dépôt / remplacement d'un fichier. Le JS est dans js_livrable. --}}
@php
  $extAutorisees = \App\Support\Reglages::get('fichiers.extensions');
  $tailleMaxMo = round(\App\Support\Reglages::get('fichiers.taille_max_ko') / 1024, 1);
@endphp
<div class="modal fade panel-modal" id="modal_livrable" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="lv_titre">Déposer un fichier</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_livrable" action="{{ route('feuille_de_route.piece_jointe.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="attachable_type" value="tache">
        <input type="hidden" name="attachable_id" value="">
        <input type="hidden" name="remplace_id" value="">

        <div class="modal-body dark-modal">
          <p id="lv_remplace_info" class="c-o-light" style="display:none;"></p>
          <div class="col-span-12">
            <label class="form-label" for="lv_fichier">Fichier</label>
            <input class="form-control" id="lv_fichier" name="fichier" type="file" required
                   accept="{{ '.'.implode(',.', $extAutorisees) }}">
            <small class="text-muted">Types autorisés : {{ strtoupper(implode(', ', $extAutorisees)) }} · {{ $tailleMaxMo }} Mo maximum.</small>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="lv_btn_enregistrer" type="submit">Envoyer</button>
        </div>
      </form>
    </div>
  </div>
</div>
