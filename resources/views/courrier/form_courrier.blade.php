{{-- Formulaire (grande fenêtre) d'enregistrement / modification d'un courrier arrivée. Le JS est dans js_courrier. --}}
@php
  $typesActifs = \App\Models\TypeCourrier::where('actif', true)->orderBy('ordre')->get();
  $urgencesActives = \App\Models\UrgenceCourrier::where('actif', true)->orderBy('ordre')->get();
  $extScan = \App\Support\Reglages::get('courrier.scan_extensions');
  $maxScanKo = \App\Support\Reglages::get('courrier.scan_taille_max_ko');
  $scanObligatoire = \App\Support\Reglages::get('courrier.scan_obligatoire');
  $retourErreur = old('_form') === 'courrier' && ($errors->any() || session('courrier_doublon'));
  $doublon = session('courrier_doublon');
@endphp

<div class="modal fade panel-modal cr-modal" id="modal_courrier" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $retourErreur ? 1 : 0 }}">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="cr_titre_form"><i class="fa-solid fa-envelope-open-text"></i> Enregistrer un courrier arrivée</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_courrier" action="{{ route('courrier.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_form" value="courrier">
        <input type="hidden" name="id_" value="{{ old('id_') }}">

        <div class="modal-body dark-modal">
          <div class="cr-form-grille">

            {{-- ===== Colonne gauche : informations ===== --}}
            <div>
              <p class="cr-form-section">Informations du courrier</p>

              <div class="cr-doublon {{ $doublon ? 'cr-doublon--visible' : '' }}" id="cr_doublon">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                  <strong>Un courrier identique semble déjà enregistré.</strong><br>
                  Même expéditeur, même référence et même date : <a id="cr_doublon_lien" href="{{ $doublon['url'] ?? '#' }}" target="_blank" rel="noopener"><span id="cr_doublon_num">{{ $doublon['numero'] ?? '' }}</span></a> (reçu le <span id="cr_doublon_date">{{ $doublon['recu_le'] ?? '' }}</span>).
                  <label><input type="checkbox" class="form-check-input" name="confirmer_doublon" value="1" id="cr_confirmer_doublon"> Je confirme : enregistrer quand même</label>
                </div>
              </div>

              <div class="cr-champs-2">
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_origine">Enregistré par <span class="cr-requis">*</span></label>
                  <select class="form-select form-select-sm" id="cr_origine" name="origine" required>
                    @foreach (\App\Models\Courrier::ORIGINES as $cle => $libelle) <option value="{{ $cle }}" @selected(old('origine', 'service_courrier') === $cle)>{{ $libelle }}</option> @endforeach
                  </select>
                  @error('origine') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_date_reception">Date de réception <span class="cr-requis">*</span></label>
                  <input class="form-control form-control-sm" type="date" id="cr_date_reception" name="date_reception" value="{{ old('date_reception', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                  @error('date_reception') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
              </div>

              <div class="cr-champs-2">
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_date_courrier">Date du courrier</label>
                  <input class="form-control form-control-sm" type="date" id="cr_date_courrier" name="date_courrier" value="{{ old('date_courrier') }}">
                  @error('date_courrier') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_reference">Référence du courrier</label>
                  <input class="form-control form-control-sm" type="text" id="cr_reference" name="reference_courrier" maxlength="100" value="{{ old('reference_courrier') }}" placeholder="N° ou référence de l'expéditeur">
                  @error('reference_courrier') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
              </div>

              <div class="cr-champ">
                <label class="cr-etiquette" for="cr_expediteur">Expéditeur <span class="cr-requis">*</span></label>
                <input class="form-control form-control-sm" type="text" id="cr_expediteur" name="expediteur" maxlength="255" value="{{ old('expediteur') }}" required placeholder="Ministère, direction, entreprise, personne…">
                @error('expediteur') <p class="cr-erreur">{{ $message }}</p> @enderror
              </div>

              <div class="cr-champ">
                <label class="cr-etiquette" for="cr_objet">Objet <span class="cr-requis">*</span></label>
                <textarea class="form-control form-control-sm" id="cr_objet" name="objet" rows="3" maxlength="500" required>{{ old('objet') }}</textarea>
                @error('objet') <p class="cr-erreur">{{ $message }}</p> @enderror
              </div>

              <div class="cr-champs-2">
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_type">Type de courrier <span class="cr-requis">*</span></label>
                  <select class="form-select form-select-sm" id="cr_type" name="type_courrier_id" required>
                    <option value="">Choisir…</option>
                    @foreach ($typesActifs as $t) <option value="{{ $t->id }}" @selected((string) old('type_courrier_id') === (string) $t->id)>{{ $t->libelle }}</option> @endforeach
                  </select>
                  @error('type_courrier_id') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_urgence">Urgence <span class="cr-requis">*</span></label>
                  <select class="form-select form-select-sm" id="cr_urgence" name="urgence_id" required data-defaut="{{ $urgencesActives->first()?->id }}">
                    @foreach ($urgencesActives as $i => $u) <option value="{{ $u->id }}" @selected((string) old('urgence_id', $urgencesActives->first()?->id) === (string) $u->id)>{{ $u->libelle }}</option> @endforeach
                  </select>
                  @error('urgence_id') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
              </div>

              <div class="cr-champs-2">
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_mode">Mode de transmission <span class="cr-requis">*</span></label>
                  <select class="form-select form-select-sm" id="cr_mode" name="mode_transmission" required>
                    <option value="">Choisir…</option>
                    @foreach (\App\Models\Courrier::MODES_TRANSMISSION as $cle => $libelle) <option value="{{ $cle }}" @selected(old('mode_transmission') === $cle)>{{ $libelle }}</option> @endforeach
                  </select>
                  @error('mode_transmission') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
                <div class="cr-champ">
                  <label class="cr-etiquette" for="cr_pieces">Pièces jointes (nombre) <span class="cr-requis">*</span></label>
                  <input class="form-control form-control-sm" type="number" id="cr_pieces" name="nombre_pieces" min="0" max="999" value="{{ old('nombre_pieces', 0) }}" required>
                  @error('nombre_pieces') <p class="cr-erreur">{{ $message }}</p> @enderror
                </div>
              </div>

              <div class="cr-champ" id="cr_mode_precision_bloc" style="display:none;">
                <label class="cr-etiquette" for="cr_mode_precision">Précisez le mode de transmission <span class="cr-requis">*</span></label>
                <input class="form-control form-control-sm" type="text" id="cr_mode_precision" name="mode_transmission_precision" maxlength="150" value="{{ old('mode_transmission_precision') }}">
                @error('mode_transmission_precision') <p class="cr-erreur">{{ $message }}</p> @enderror
              </div>

              <div class="cr-champ">
                <label class="cr-interrupteur" for="cr_confidentiel">
                  <input type="checkbox" id="cr_confidentiel" name="confidentialite" value="confidentiel" @checked(old('confidentialite') === 'confidentiel')>
                  <span class="cr-interrupteur__piste"></span>
                  <span class="cr-interrupteur__texte"><strong>Courrier confidentiel</strong><small>Visible seulement du DG, de l'administrateur et de vous-même ; chaque consultation est tracée.</small></span>
                </label>
              </div>

              <div class="cr-champ">
                <label class="cr-etiquette" for="cr_observations">Observations</label>
                <textarea class="form-control form-control-sm" id="cr_observations" name="observations" rows="2" maxlength="2000">{{ old('observations') }}</textarea>
              </div>
            </div>

            {{-- ===== Colonne droite : scan ===== --}}
            <div class="cr-form-colonne-scan">
              <p class="cr-form-section">Scan du courrier @if ($scanObligatoire)<span class="cr-requis" id="cr_scan_requis">*</span>@endif</p>

              <div class="cr-drop" id="cr_drop" tabindex="0" data-ext="{{ implode(',', $extScan) }}" data-max-ko="{{ $maxScanKo }}" data-requis="{{ $scanObligatoire ? 1 : 0 }}">
                <input type="file" id="cr_scan" name="scan" accept="{{ '.'.implode(',.', $extScan) }}" tabindex="-1">
                <div id="cr_drop_invite">
                  <span class="cr-drop__icone"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                  <span class="cr-drop__titre">Glissez le scan du courrier ici</span>
                  <span class="cr-drop__aide">ou <u>cliquez pour parcourir</u><br>{{ strtoupper(implode(', ', $extScan)) }} · {{ round($maxScanKo / 1024, 1) }} Mo maximum</span>
                </div>
                <div class="cr-drop__fichier" id="cr_drop_fichier" hidden>
                  <i class="fa-regular fa-file-pdf" id="cr_drop_icone"></i>
                  <div><span class="cr-drop__nom" id="cr_drop_nom"></span><span class="cr-drop__taille" id="cr_drop_taille"></span></div>
                  <button type="button" class="cr-drop__retirer" id="cr_drop_retirer"><i class="fa-solid fa-xmark"></i> Retirer</button>
                </div>
              </div>
              <p class="cr-drop__message" id="cr_drop_message">@error('scan') {{ $message }} @enderror</p>
              <p class="c-o-light" id="cr_scan_modif" style="display:none;font-size:13px;">Un scan est déjà joint : en déposer un nouveau le remplace (l'ancienne version est conservée).</p>

              <div class="cr-apercu" id="cr_apercu" hidden>
                <div class="cr-apercu__titre"><i class="fa-regular fa-eye"></i> Aperçu avant enregistrement</div>
                <div id="cr_apercu_corps"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="cr_btn_enregistrer" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer le courrier</button>
        </div>
      </form>
    </div>
  </div>
</div>
