<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Tâche</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('feuille_de_route.tache.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="grid grid-cols-12 gap-3">

              @include('feuille_de_route._cascade', ['jusqua' => 'action_mesurable'])

              <div class="col-span-12">
                <label class="form-label" for="libelle">Libellé de la tâche</label>
                <textarea class="form-control" id="libelle" name="libelle" rows="2" required></textarea>
              </div>

              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="indicateur">Indicateur</label>
                <textarea class="form-control" id="indicateur" name="indicateur" rows="2"></textarea>
              </div>
              <div class="col-span-6 sm:col-span-12">
                <label class="form-label" for="livrable">Livrable</label>
                <textarea class="form-control" id="livrable" name="livrable" rows="2"></textarea>
              </div>

              <div class="col-span-3 sm:col-span-12">
                <label class="form-label" for="date_debut">Date début</label>
                <input class="form-control" id="date_debut" name="date_debut" type="date">
              </div>
              <div class="col-span-3 sm:col-span-12">
                <label class="form-label" for="date_fin">Date fin (échéance)</label>
                <input class="form-control" id="date_fin" name="date_fin" type="date">
              </div>
              <div class="col-span-3 sm:col-span-12">
                <label class="form-label" for="duree_jours">Durée (jours)</label>
                <input class="form-control" id="duree_jours" name="duree_jours" type="number" min="0">
              </div>
              <div class="col-span-3 sm:col-span-12">
                <label class="form-label" for="ordre">Ordre</label>
                <input class="form-control" id="ordre" name="ordre" type="number" min="1" required>
              </div>

              <div class="col-span-4 sm:col-span-12">
                <label class="form-label" for="priorite_id">Priorité</label>
                <select class="form-select form-select-sm" id="priorite_id" name="priorite_id">
                  <option value="" selected>Non définie</option>
                  @foreach ($priorites as $priorite)
                    <option value="{{ $priorite->id }}">{{ $priorite->libelle }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-span-4 sm:col-span-12">
                <label class="form-label" for="statut_execution_id">Statut d'exécution</label>
                <select class="form-select form-select-sm" id="statut_execution_id" name="statut_execution_id" required>
                  <option value="" selected disabled>Sélectionnez un statut</option>
                  @foreach ($statutsExecution as $statut)
                    <option value="{{ $statut->id }}">{{ $statut->libelle }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-span-4 sm:col-span-12">
                <label class="form-label" for="avancement_pourcentage">Avancement (%)</label>
                <input class="form-control" id="avancement_pourcentage" name="avancement_pourcentage" type="number" min="0" max="100" required>
                <small id="avancement_aide" class="text-muted hidden">Calculé à partir des sous-tâches</small>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="budget_estime">Budget estimé <span class="text-muted">(optionnel)</span></label>
                <input class="form-control" id="budget_estime" name="budget_estime" type="number" step="0.01" min="0">
              </div>

              <div class="col-span-12">
                <label class="form-label" for="observations">Observations <span class="text-muted">(optionnel)</span></label>
                <textarea class="form-control" id="observations" name="observations" rows="2"></textarea>
              </div>

              <div class="col-span-12">
                <label class="form-label" for="dependances">Dépend de <span class="text-muted">(tâches devant être terminées avant celle-ci)</span></label>
                <select class="form-select form-select-sm select-basic-multiple-four" id="dependances" name="dependances[]" multiple size="5">
                  @foreach ($toutesLesTaches as $t)
                    <option value="{{ $t->id }}" data-tache-courante="non">{{ $t->code }} — {{ \Illuminate\Support\Str::limit($t->libelle, 60) }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-span-12">
                <hr>
                <label class="form-label font-semibold"><b>AFFECTATIONS</b></label>
                @foreach ($roles as $role)
                  <div class="mt-2">
                    <label class="form-label" for="role_{{ $role->id }}"><b>{{ $role->libelle }}</b></label>
                    <select class="form-select form-select-sm select-basic-multiple-four" id="role_{{ $role->id }}" name="affectations[{{ $role->id }}][]" multiple size="4">
                      @foreach ($personnels as $personnel)
                        <option value="{{ $personnel->id }}">{{ trim($personnel->prenom.' '.$personnel->nom) }}</option>
                      @endforeach
                    </select>
                  </div>
                @endforeach
              </div>

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

@include('feuille_de_route.js_avancement_mode')
