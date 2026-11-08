{{-- Formulaire modal des tâches ponctuelles : création, et consultation / modification d'une tâche existante.
     Variables : $priorites, $agents, $controle, $aPersonnel. Script : public/assets/js/ponctuelles.js --}}
<div class="modal fade panel-modal" id="modal_ponctuelle" tabindex="-1" role="dialog" aria-labelledby="titre_modal_ponctuelle" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="titre_modal_ponctuelle">Nouvelle tâche ponctuelle</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" id="form_ponctuelle" action="{{ route('ponctuelle.store') }}" method="post" novalidate>
        @csrf
        <input type="hidden" name="id_" id="pt_id" value="">

        <div class="modal-body dark-modal">
          <div class="grid grid-cols-12 gap-3">

            {{-- Alerte d'état (en attente de validation, refusée, annulée) : modification seulement --}}
            <div class="col-span-12 pt-edit" id="pt_etat" hidden></div>

            {{-- Création : à qui confier la tâche --}}
            @if (count($agents) > 1)
              <div class="col-span-12 pt-creation">
                <label class="form-label" for="pt_pour">Pour</label>
                <select class="form-select" id="pt_pour" name="responsable_personnel_id">
                  @foreach ($agents as $a)<option value="{{ $a['id'] }}" @selected($a['moi'])>{{ $a['moi'] ? 'Moi-même' : $a['nom'] }}</option>@endforeach
                </select>
                <small class="form-text">Vous pouvez confier la tâche à un agent placé sous votre responsabilité : il en sera prévenu.</small>
              </div>
            @elseif (! $aPersonnel)
              <div class="col-span-12 pt-creation"><div class="alert alert-warning mb-0">Votre compte n'est lié à aucun agent : vous ne pouvez pas créer de tâche ponctuelle pour vous-même.</div></div>
            @endif

            {{-- Modification : responsable et origine --}}
            <div class="col-span-12 pt-edit" id="pt_qui" hidden></div>

            <div class="col-span-12">
              <label class="form-label" for="pt_libelle">Que faut-il faire ? <span class="text-danger">*</span></label>
              <textarea class="form-control" id="pt_libelle" name="libelle" rows="2" maxlength="255" required placeholder="Ex. : préparer la note pour la réunion de jeudi"></textarea>
              <div class="invalid-feedback">Décrivez la tâche en quelques mots.</div>
            </div>

            <div class="col-span-12">
              <label class="form-label" for="pt_description">Description</label>
              <textarea class="form-control" id="pt_description" name="description" rows="3" maxlength="5000" placeholder="Contexte, personnes concernées, consignes… (facultatif)"></textarea>
            </div>

            <div class="col-span-4 sm:col-span-12">
              <label class="form-label" for="pt_date_debut">Date de début</label>
              <input class="form-control" id="pt_date_debut" name="date_debut" type="date">
            </div>
            <div class="col-span-4 sm:col-span-12">
              <label class="form-label" for="pt_date_fin">Échéance</label>
              <input class="form-control" id="pt_date_fin" name="date_fin" type="date">
            </div>
            <div class="col-span-4 sm:col-span-12">
              <label class="form-label" for="pt_priorite">Priorité</label>
              <select class="form-select" id="pt_priorite" name="priorite_id">
                <option value="">—</option>
                @foreach ($priorites as $p)<option value="{{ $p->id }}">{{ $p->libelle }}</option>@endforeach
              </select>
            </div>

            {{-- Modification : avancement, statut, livrables, décision --}}
            <div class="col-span-12 pt-edit" hidden>
              <label class="form-label d-flex justify-content-between" for="pt_range"><span>Avancement</span> &nbsp;&nbsp;<b id="pt_pct">0 %</b></label>
              <input type="range" min="0" max="100" step="5" value="0" id="pt_range" style="--p:0%">
            </div>
            <div class="col-span-12 pt-edit" hidden>
              <span class="form-label d-block">Statut</span>
              <div class="pt-statuts" id="pt_statuts">
                <button type="button" data-statut="a_faire"><i class="fa-solid fa-hourglass-half"></i><b>À faire</b><small>Pas encore démarrée</small></button>
                <button type="button" data-statut="en_cours"><i class="fa-solid fa-bars-progress"></i><b>En cours</b><small>Je travaille dessus</small></button>
                <button type="button" data-statut="terminee"><i class="fa-solid fa-circle-check"></i><b>Terminée</b><small>C’est fait</small></button>
              </div>
            </div>
            <div class="col-span-12 pt-edit" hidden>
              <span class="form-label d-block">Livrables <b id="pt_nb_livrables"></b></span>
              <ul class="pt-livrables" id="pt_livrables"></ul>
              <label class="pt-depot" id="pt_depot"><i class="fa-solid fa-cloud-arrow-up"></i><span>Glissez un fichier ici ou <b>parcourez</b></span><input type="file" id="pt_fichier" hidden></label>
            </div>
            <div class="col-span-12 pt-edit" id="pt_decision" hidden>
              <div class="pt-decision">
                <span>Décision de la hiérarchie</span>
                <div class="pt-decision__boutons">
                  <button type="button" class="btn btn-success text-white" data-decision="valider"><i class="fa-solid fa-check"></i> Valider</button>
                  <button type="button" class="btn btn-danger text-white" data-decision="refuser"><i class="fa-solid fa-xmark"></i> Refuser</button>
                  <button type="button" class="btn btn-outline-danger" data-decision="annuler"><i class="fa-solid fa-ban"></i> Annuler la tâche</button>
                </div>
              </div>
            </div>

            <div class="col-span-12 pt-creation">
              @if ($controle === 'validation')
                <div class="alert alert-info mb-0"><i class="fa-solid fa-circle-info"></i> Votre tâche attend l'accord de votre supérieur avant de démarrer.</div>
              @elseif ($controle === 'notification')
                <div class="alert alert-info mb-0"><i class="fa-solid fa-circle-info"></i> Votre supérieur est prévenu de chaque nouvelle tâche et peut la refuser ou l'annuler.</div>
              @endif
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-danger me-auto" type="button" id="pt_supprimer" hidden><i class="fa-solid fa-trash-can"></i> Supprimer</button>
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white submit" type="submit" id="BtnEnregistrerPonctuelle">ENREGISTRER</button>
        </div>
      </form>
    </div>
  </div>
</div>
