<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Règle d'alerte</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('parametre.regle_alerte.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label" for="code">Code <span class="text-muted">(ex: J-30, J-1, J+7)</span></label>
              <input class="form-control" id="code" name="code" type="text" maxlength="20" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="libelle">Libellé</label>
              <input class="form-control" id="libelle" name="libelle" type="text" placeholder="Ex: Échéance à venir" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="decalage_jours">Décalage (jours) <span class="text-muted">négatif = avant l'échéance</span></label>
              <input class="form-control" id="decalage_jours" name="decalage_jours" type="number" placeholder="Ex: -30 ou 7" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label fw-bold"><b>Canaux déclenchés</b></label>
              <div class="canaux-checkboxes">
                @foreach ($canaux as $canal)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="canaux[]" value="{{ $canal->id }}" id="canal_{{ $canal->id }}">
                    <label class="form-check-label" for="canal_{{ $canal->id }}">{{ $canal->libelle }}</label>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label"><b>Destinataires </b><span class="text-muted">(qui reçoit l'alerte)</span></label>
              <div class="destinataires-checkboxes">
                @foreach (\App\Models\RegleAlerte::DESTINATAIRES as $cle => $libelle)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="destinataires[]" value="{{ $cle }}" id="dest_{{ $cle }}">
                    <label class="form-check-label" for="dest_{{ $cle }}">{{ $libelle }}</label>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="actif">Statut</label>
              <select class="form-select form-select-sm" id="actif" name="actif" required>
                <option value="" selected disabled>Sélectionnez un statut</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
              </select>
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
