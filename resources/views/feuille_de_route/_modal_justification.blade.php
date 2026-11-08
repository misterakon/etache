{{-- Modal de demande (justification / report). Inclure une fois par page, avec feuille_de_route.js_script (fonctions justifier_tache, envoyer_justification...). --}}
<div class="modal fade panel-modal" id="modalJustification" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Justifier un retard / demander un report</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formJustification" method="post" action="#" onsubmit="envoyer_justification(); return false;">
        @csrf
        <div class="modal-body dark-modal">
          <p class="c-o-light mb-3" id="justifTitre" style="color: #000e44; font-weight:bold"></p>
          <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
              <label class="form-label d-block"><b>Type de demande</b></label>
              <label class="me-3"><input type="radio" name="type" value="justification" onchange="justification_majType()" checked> Justification seule <small class="text-danger">(l'échéance ne change pas)</small></label>
              <label><input type="radio" name="type" value="report" id="justifRadioReport" onchange="justification_majType()"> Demande de report</label>
            </div>
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="justif_motif"><b>Motif</b></label>
              <select class="form-select form-select-sm" id="justif_motif" name="motif" required>
                @foreach (\App\Support\JustificationRetard::MOTIFS as $cle => $lib)
                  <option value="{{ $cle }}">{{ $lib }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-span-6 sm:col-span-12" id="justifBlocReport" style="display:none;">
              <label class="form-label" for="justif_date"><b>Nouvelle échéance proposée</b></label>
              <input class="form-control" id="justif_date" name="echeance_proposee" type="date">
            </div>
            <div class="col-span-12">
              <label class="form-label" for="justif_commentaire"><b>Commentaire </b><span class="text-danger">*</span></label>
              <textarea class="form-control" id="justif_commentaire" name="commentaire" rows="4" minlength="10" maxlength="2000" required></textarea>
            </div>
            <div class="col-span-12" id="justifReportInfo" style="display:none;">
              <div class="alert alert-success text-black mb-0">Report de 15 jours ou moins (et premier report) : décidé par le responsable de direction. Au-delà, ou à partir du 2<sup>e</sup> report : décidé par le Directeur Général.</div>
            </div>
            <div class="col-span-12" id="justifErreur" style="display:none;">
              <div class="alert alert-danger mb-0" id="justifErreurTexte"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="btnEnvoyerJustif" type="submit">Envoyer la demande</button>
        </div>
      </form>
    </div>
  </div>
</div>
