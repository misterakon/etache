{{-- Page Responsables : CONSULTATION SEULE. L'historique d'un poste s'ouvre au clic sur une unité ; les affectations, intérims et fins de fonction se gèrent dans la page Personnel (« Affecter à un poste »). --}}
<div class="modal fade panel-modal" id="modal_historique" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Historique des affectations — <span id="hist_unite"></span></h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body dark-modal">
        <div class="overflow-x-auto custom-scrollbar">
          <table class="table rs-hist">
            <thead>
              <tr>
                <th>Responsable</th>
                <th>Type</th>
                <th>Période</th>
                <th>État</th>
                <th>Acte / motif de fin</th>
                <th>Saisi par</th>
              </tr>
            </thead>
            <tbody id="hist_corps"></tbody>
          </table>
        </div>
        <p class="text-muted" style="font-size:12.5px; margin:8px 0 0;"><i class="fa-solid fa-circle-info"></i> Pour affecter un agent, mettre fin à un intérim ou à une fonction : page <b>Personnel</b>, bouton « Affecter à un poste ».</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
