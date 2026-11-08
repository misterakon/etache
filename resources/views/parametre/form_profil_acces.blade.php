<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content pa-page-wrap">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Profil d'accès</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('parametre.profil_acces.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_" value="">

          <div class="modal-body dark-modal">
            <div id="pa_alerte_systeme" class="alert alert-info" hidden><i class="fa-solid fa-circle-info"></i> Le profil <b>Admin</b> a tous les droits d'office et n'est pas modifiable : il garantit qu'il reste toujours au moins un compte capable de tout administrer.</div>

            <div class="pa-identite-form">
              {{-- <div class="pa-avatar" id="pa_avatar_apercu">?</div> --}}
              <div class="grid grid-cols-12 card-gap pa-identite-form__champs">
                <div class="col-span-6 sm:col-span-12">
                  <label class="form-label" for="libelle"><b>Libellé</b></label>
                  <input class="form-control" id="libelle" name="libelle" type="text" required>
                </div>
                <div class="col-span-3 sm:col-span-12">
                  <label class="form-label" for="slug"><b>Slug <span class="text-muted">(ex: DG, RD)</span></b></label>
                  <input class="form-control" id="slug" name="slug" type="text" maxlength="20" style="text-transform: uppercase;" required>
                </div>
                <div class="col-span-3 sm:col-span-12">
                  <label class="form-label" for="actif"><b>Statut</b></label>
                  <select class="form-select form-select-sm" id="actif" name="actif" required>
                    <option value="" selected disabled>Sélectionnez un statut</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="pa-legende">
              <span><b>Lecture</b> : voir</span>
              <span><b>Saisie</b> : voir, créer, modifier</span>
              <span><b>Gestion</b> : tout, y compris supprimer</span>
            </div>

            <div class="pa-entete">
              <h6 class="mb-0">Droits d'accès, sous-menu par sous-menu</h6>
              <div class="pa-entete__actions">
                <button type="button" class="pa-lien" id="pa_tout_accorder"><i class="fa-solid fa-bolt"></i> Tout accorder</button>
                <span class="pa-sep">·</span>
                <button type="button" class="pa-lien" id="pa_tout_ouvrir">Tout déplier</button>
                <span class="pa-sep">·</span>
                <button type="button" class="pa-lien" id="pa_tout_fermer">Tout replier</button>
              </div>
            </div>

            <div id="pa_catalogue">
              @foreach (\App\Support\Droits::catalogue() as $cleMenu => $menu)
                <details class="pa-menu" id="pa_menu_{{ $cleMenu }}">
                  <summary>
                    <span class="pa-menu__icone"><i class="{{ $menu['icone'] }}"></i></span>
                    <span class="pa-menu__libelle">{{ $menu['libelle'] }}</span>
                    <span class="pa-menu__compteur" data-menu="{{ $cleMenu }}">0/{{ count($menu['pages']) }}</span>
                    <label class="pa-switch pa-switch--sm pa-menu__interrupteur" title="Donner le plus haut niveau à tout le menu « {{ $menu['libelle'] }} »" onclick="event.stopPropagation()">
                      <input type="checkbox" class="pa-menu-tout" data-menu="{{ $cleMenu }}">
                      <span class="pa-switch__piste"></span>
                    </label>
                    <i class="pa-menu__chevron fa-solid fa-chevron-down"></i>
                  </summary>
                  <div class="pa-menu__pages">
                    @foreach ($menu['pages'] as $clePage => $page)
                      <div class="pa-page" data-page="{{ $clePage }}">
                        <div class="pa-page__haut">
                          <div class="pa-page__libelle">{{ $page['libelle'] }}@if ($page['a_venir'])<span class="badge badge-light-warning ms-1">à venir</span>@endif</div>
                          <div class="pa-niveaux" role="radiogroup" aria-label="Niveau d'accès — {{ $page['libelle'] }}">
                            @foreach (\App\Support\Droits::NIVEAUX as $cleNiveau => $libNiveau)
                              @if ($cleNiveau === 'aucun' || isset($page['niveaux'][$cleNiveau]))
                                <label class="pa-niveau pa-niveau--{{ $cleNiveau }}">
                                  <input type="radio" name="niveaux[{{ $clePage }}]" value="{{ $cleNiveau }}" @checked($cleNiveau === 'aucun')>
                                  <span>{{ $libNiveau }}</span>
                                </label>
                              @endif
                            @endforeach
                          </div>
                        </div>
                        @if (count($page['speciales']))
                          <div class="pa-speciales">
                            @foreach ($page['speciales'] as $cleSpeciale => $libSpeciale)
                              <label class="pa-switch pa-switch--sm pa-speciale" title="{{ $libSpeciale }}">
                                <input type="checkbox" name="speciales[]" value="{{ $cleSpeciale }}">
                                <span class="pa-switch__piste"></span>
                                <span class="pa-switch__texte">{{ $libSpeciale }}</span>
                              </label>
                            @endforeach
                          </div>
                        @endif
                      </div>
                    @endforeach
                  </div>
                </details>
              @endforeach
            </div>
          </div>
          <div class="modal-footer">
            <span id="pa_nb_pages" class="text-muted me-auto" style="font-size:13px"></span>
            <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-primary text-white submit" id="BtnEnregistrer" type="submit">Enregistrer</button>
          </div>
      </form>

    </div>
  </div>
</div>
