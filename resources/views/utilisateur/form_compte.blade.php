{{-- ===== Style du champ « Agent lié » avec recherche (Select2) : limité à ce modal ===== --}}
<style>
  #tooltipmodal .select2-container { width: 100% !important; }
  #tooltipmodal .select2-container--default .select2-selection--single {
    height: calc(1.5em + .5rem + 2px); display: flex; align-items: center;
    border: 1px solid var(--bs-border-color, #8a8a8a); border-radius: .375rem; background-color: var(--bs-body-bg, #fff);
  }
  #tooltipmodal .select2-container--default .select2-selection--single .select2-selection__rendered {
    width: 100%; padding: 0 2rem 0 .5rem; font-size: .875rem; line-height: 1.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  #tooltipmodal .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; top: 0; right: .35rem; }
  #tooltipmodal .select2-container--default .select2-search--dropdown { padding: .4rem; }
  #tooltipmodal .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid var(--bs-border-color, #8a8a8a); border-radius: .25rem; padding: .25rem .5rem; outline: 0; box-shadow: none;
  }
  #tooltipmodal .select2-container--default .select2-search--dropdown .select2-search__field:focus { border-color: #86b7fe; box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .2); }
  #tooltipmodal .select2-container--default .select2-results__option { font-size: .875rem; white-space: normal; word-break: break-word; }
</style>

<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Compte utilisateur</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('utilisateur.compte.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div class="col-span-12">
              <label class="form-label fw-bold" for="personnel_id">Agent lié <span class="text-muted">(optionnel)</span></label>
              <select class="form-select form-select-sm cpt-select-agent" id="personnel_id" name="personnel_id">
                <option value="" selected>Aucun</option>
              </select>
              <small class="text-muted" id="cpt_info_agent"></small>
              <small class="text-muted d-block">Seuls les agents qui n'ont pas encore de compte sont proposés. Le nom et l'email se remplissent à la sélection : ils restent modifiables.</small>
            </div>
            <div class="col-span-12">
              <label class="form-label" for="name"><b>Nom</b></label>
              <input class="form-control" id="name" name="name" type="text" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="email"><b>Email</b></label>
              <input class="form-control" id="email" name="email" type="email" required>
            </div>
            <div class="col-span-12 mt-2">
              <label class="form-label" for="password"><b>Mot de passe</b> <span class="text-muted" id="password_hint">(min. 8 caractères)</span></label>
              <div class="input-group flex !flex-nowrap">
                <input class="form-control rounded-r-none" id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                <button class="btn btn-primary text-white rounded-l-none" id="btn_voir_mdp" type="button" onclick="mdp_basculer()" title="Afficher / masquer le mot de passe"><i class="fa-regular fa-eye"></i></button>
              </div>

              <div class="card-wrapper custom-border rounded-3 checkbox-checked mt-2">
                <div class="form-check-size rtl-input">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input me-2" id="mdp_saisir" type="radio" name="mdp_mode" value="saisir" checked onchange="mdp_changer_mode('saisir')">
                    <label class="form-check-label" for="mdp_saisir">Saisir</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input me-2" id="mdp_generer" type="radio" name="mdp_mode" value="generer" onchange="mdp_changer_mode('generer')">
                    <label class="form-check-label" for="mdp_generer" title="Générer automatiquement">Générer</label>
                  </div>
                </div>
              </div>

              <div class="mt-2 hidden" id="mdp_actions">
                <button class="btn btn-primary text-white btn-sm" type="button" onclick="mdp_regenerer()"><i class="fa-solid fa-rotate"></i> Régénérer</button>
                <button class="btn btn-primary text-white btn-sm" id="btn_copier_mdp" type="button" onclick="mdp_copier()"><i class="fa-regular fa-copy"></i> Copier</button>
              </div>
            </div>
            
            
            <div class="col-span-12 mt-2">
              <label class="form-label" for="profil_acces_id">Profil d'accès</label>
              <select class="form-select form-select-sm" id="profil_acces_id" name="profil_acces_id" required>
                <option value="" selected disabled>Sélectionnez un profil</option>
                @foreach ($profilsAcces as $profil)
                  <option value="{{ $profil->id }}">{{ $profil->libelle }}</option>
                @endforeach
              </select>
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

{{-- Agents proposés : la liste est reconstruite à chaque ouverture du formulaire (agents sans compte, plus l'agent du compte modifié) --}}
@php
  // Calculé ici : la directive json de Blade découpe son argument sur les virgules, donc elle ne supporte pas une expression complexe
  $cptAgents = $personnels->map(fn ($p) => [
      'id' => $p->id,
      'texte' => ($p->matricule ? $p->matricule.' -- ' : '').trim($p->prenom.' '.$p->nom),
      'nom_complet' => trim($p->prenom.' '.$p->nom),
      'email' => $p->email,
      'compte' => $p->user !== null,
      'info' => collect([$p->matricule, $p->fonction, $p->service?->nom])->filter()->implode(' · '),
  ])->values();
@endphp
<script type="text/javascript">
  const CPT_AGENTS = @json($cptAgents);
</script>
