{{--
  Formulaire Agent (fiche).
  Variable : $estAdmin  -> true : tous les champs modifiables ;
                           false : l'agent concerne modifie son identite, son etat civil et ses contacts.
  Variable : $peutChangerMdp -> true uniquement sur SA PROPRE fiche : affiche la section "mot de passe".
  (Le serveur applique la meme regle : voir DgepnController::storePersonnel.)
--}}
@php $restreint = ! ($estAdmin ?? false); @endphp
@php
  $listesAgent = [
      'civilite' => \App\Models\ListeValeur::deListe('civilite')->get(['id', 'libelle']),
      'genre' => \App\Models\ListeValeur::deListe('genre')->get(['id', 'libelle']),
      'statut_matrimonial' => \App\Models\ListeValeur::deListe('statut_matrimonial')->get(['id', 'libelle']),
  ];
@endphp

<div class="modal fade panel-modal" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mySmallModalLabel">Formulaire Agent</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="modal_form" action="{{ route('dgepn.personnel.store') }}" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id_" value="">
          <input type="hidden" name="supprimer_photo" id="supprimer_photo" value="0">

          <div class="modal-body dark-modal">
            @if ($restreint)
              <div class="info-lecture"><i class="fa-solid fa-circle-info"></i>
                Vous pouvez modifier votre <strong>identité</strong>, votre <strong>état civil</strong>, vos <strong>contacts</strong> et votre <strong>photo</strong>.
                Le matricule, l'emploi, le statut et l'affectation restent gérés par l'administrateur.</div>
            @endif
            <div class="grid grid-cols-12 gap-4">

              {{-- Photo --}}
              <div class="col-span-3 md:col-span-12 text-center">
                <div style="width:130px; height:130px; margin:0 auto 12px; border-radius:50%; overflow:hidden; background:#eef0f2; display:flex; align-items:center; justify-content:center;">
                  <img id="photo_apercu" class="hidden" src="" alt="Photo de l'agent" style="width:100%; height:100%; object-fit:cover;">
                  <i id="photo_placeholder" class="fa-solid fa-user" style="font-size:52px; color:#adb5bd;"></i>
                </div>

                <label class="btn btn-primary text-white btn-sm" for="photo">Choisir une photo</label>
                <input class="hidden" type="file" id="photo" name="photo" accept="image/png,image/jpeg,image/webp" onchange="apercu_photo(this)">

                <div>
                  <button class="btn btn-secondary text-white btn-sm mt-2 hidden" id="btn_retirer_photo" type="button" onclick="retirer_photo()">Retirer la photo</button>
                </div>
                <p class="text-muted mt-2" style="font-size:12px;">JPG, PNG ou WebP, 2 Mo maximum.</p>
              </div>

              {{-- Champs --}}
              <div class="col-span-9 md:col-span-12">
                <div class="grid grid-cols-12 gap-3">

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="nom">Nom</label>
                    <input class="form-control" id="nom" name="nom" type="text" required>
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="prenom">Prénom</label>
                    <input class="form-control" id="prenom" name="prenom" type="text">
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="civilite_id">Civilité</label>
                    <select class="form-select form-select-sm" id="civilite_id" name="civilite_id">
                      <option value="" selected>—</option>
                      @foreach ($listesAgent['civilite'] as $o) <option value="{{ $o->id }}">{{ $o->libelle }}</option> @endforeach
                    </select>
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="genre_id">Genre</label>
                    <select class="form-select form-select-sm" id="genre_id" name="genre_id">
                      <option value="" selected>—</option>
                      @foreach ($listesAgent['genre'] as $o) <option value="{{ $o->id }}">{{ $o->libelle }}</option> @endforeach
                    </select>
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="matricule">Matricule <span class="text-muted">(optionnel)</span></label>
                    <input class="form-control{{ $restreint ? ' champ-lecture' : '' }}" id="matricule" name="matricule" type="text" maxlength="50" @readonly($restreint)>
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="type_agent_id">Type d'agent</label>
                    <select class="form-select form-select-sm{{ $restreint ? ' champ-lecture' : '' }}" id="type_agent_id" name="type_agent_id" @disabled($restreint)>
                      <option value="" selected>Aucun / à définir plus tard</option>
                      @foreach ($typesAgent as $type) <option value="{{ $type->id }}">{{ $type->libelle }}</option> @endforeach
                    </select>
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="date_naissance">Date de naissance</label>
                    <input class="form-control" id="date_naissance" name="date_naissance" type="date">
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="lieu_naissance">Lieu de naissance</label>
                    <input class="form-control" id="lieu_naissance" name="lieu_naissance" type="text">
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="nom_pere">Nom du père</label>
                    <input class="form-control" id="nom_pere" name="nom_pere" type="text" maxlength="255">
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="nom_mere">Nom de la mère</label>
                    <input class="form-control" id="nom_mere" name="nom_mere" type="text" maxlength="255">
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="statut_matrimonial_id">Statut matrimonial</label>
                    <select class="form-select form-select-sm" id="statut_matrimonial_id" name="statut_matrimonial_id">
                      <option value="" selected>—</option>
                      @foreach ($listesAgent['statut_matrimonial'] as $o) <option value="{{ $o->id }}">{{ $o->libelle }}</option> @endforeach
                    </select>
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="nombre_enfants">Nombre d'enfants</label>
                    <input class="form-control" id="nombre_enfants" name="nombre_enfants" type="number" min="0" max="30">
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email">
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="telephone">Téléphone 1 <span class="text-muted">(alertes SMS)</span></label>
                    <input class="form-control" id="telephone" name="telephone" type="text">
                  </div>

                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="telephone_2">Téléphone 2</label>
                    <input class="form-control" id="telephone_2" name="telephone_2" type="text" maxlength="30">
                  </div>
                  <div class="col-span-6 sm:col-span-12">
                    <label class="form-label" for="lieu_habitation">Lieu d'habitation</label>
                    <input class="form-control" id="lieu_habitation" name="lieu_habitation" type="text">
                  </div>

                  <div class="col-span-8 sm:col-span-12">
                    <label class="form-label" for="emploi_id">Emploi</label>
                    <select class="form-select form-select-sm{{ $restreint ? ' champ-lecture' : '' }}" id="emploi_id" name="emploi_id" @disabled($restreint)>
                      <option value="" selected>Aucun / à définir plus tard</option>
                      @foreach ($emplois as $emploi) <option value="{{ $emploi->id }}">{{ $emploi->familleEmploi->libelle }} — {{ $emploi->libelle }}</option> @endforeach
                    </select>
                  </div>
                  <div class="col-span-4 sm:col-span-12">
                    <label class="form-label" for="actif">Statut</label>
                    <select class="form-select form-select-sm{{ $restreint ? ' champ-lecture' : '' }}" id="actif" name="actif" required @disabled($restreint)>
                      <option value="" selected disabled>Sélectionnez un statut</option>
                      <option value="1">Actif</option>
                      <option value="0">Inactif</option>
                    </select>
                  </div>

                  <div class="col-span-12">
                    <p class="text-muted" style="font-size:12.5px; margin:0;"><i class="fa-solid fa-circle-info"></i> La <b>fonction</b> et le <b>service</b> ne se saisissent plus ici : ils se définissent par <b>« Affecter à un poste »</b>.</p>
                  </div>

                  {{-- Mot de passe : uniquement pour l'agent connecte, sur sa propre fiche (variable $peutChangerMdp) --}}
                  @if ($peutChangerMdp ?? false)
                    <div class="col-span-12" style="border-top:1px dashed rgba(91,107,132,.35); margin-top:6px; padding-top:16px;">
                      <h6 class="mb-1"><i class="fa-solid fa-key me-2"></i>Changer mon mot de passe</h6>
                      <p class="text-muted mb-3" style="font-size:12.5px;">Laissez ces champs vides pour conserver votre mot de passe actuel. 8 caractères minimum.</p>
                      <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-4 sm:col-span-12">
                          <label class="form-label" for="current_password">Mot de passe actuel</label>
                          <input class="form-control" id="current_password" name="current_password" type="password" autocomplete="current-password">
                        </div>
                        <div class="col-span-4 sm:col-span-12">
                          <label class="form-label" for="password">Nouveau mot de passe</label>
                          <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                        </div>
                        <div class="col-span-4 sm:col-span-12">
                          <label class="form-label" for="password_confirmation">Confirmer le nouveau</label>
                          <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
                        </div>
                      </div>
                    </div>
                  @endif

                </div>
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
