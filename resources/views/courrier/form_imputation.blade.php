{{-- Formulaire (grande fenêtre) d'imputation d'un courrier : reprend la fiche de transmission. Variables : $courrier, $groupes, $instructions, $delais. Le JS est dans js_imputation. --}}
@php
  $oldDest = collect(old('dest', []))
      ->filter(fn ($cle) => is_string($cle) && preg_match('/^(direction|sous_direction|service|poste|agent):\d+$/', $cle))
      ->unique()
      ->values()
      ->all();
  $estReimputation = isset($diligenceParente);
  $actionImputation = $estReimputation
      ? route('courrier.diligence.reimputer', $diligenceParente->id)
      : route('courrier.imputer', $courrier->id);
  $valeursGrille = collect($groupes['postes'])->map(fn ($p) => "poste:{$p->id}")->all();
  $ouvrirImputation = old('_form') === 'imputation' && $errors->any();
@endphp

<div class="modal fade panel-modal cr-modal" id="modal_imputation" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $ouvrirImputation ? 1 : 0 }}">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-share-nodes"></i> {{ $estReimputation ? 'Réimputer le courrier' : 'Imputer le courrier' }} {{ $courrier->numero }}</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_imputation" action="{{ $actionImputation }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="imputation">
        <div class="modal-body dark-modal">
          @if ($errors->any() && old('_form') === 'imputation')
            <div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
          @endif

          @if ($courrier->estConfidentiel())
            <div class="cr-alerte-conf" style="margin-bottom:14px;">
              <i class="fa-solid fa-user-shield"></i>
              <div>
                <strong>Courrier confidentiel.</strong>
                {{ $estReimputation ? 'Réimputer étend la lecture du contenu et du scan à de nouvelles personnes.' : 'Tous les destinataires désignés, y compris en avis et en copie, pourront lire son contenu et son scan.' }}
                Ne désignez que les personnes qui doivent le connaître.
                <label style="display:flex;align-items:center;gap:8px;margin:10px 0 0;font-weight:600;cursor:pointer;">
                  <input type="checkbox" class="form-check-input" name="confirme_confidentiel" value="1" @checked(old('confirme_confidentiel'))> J'ai vérifié la liste des destinataires.
                </label>
              </div>
            </div>
          @endif

          <div class="cr-form-grille">

            {{-- ===== Destinataires ===== --}}
            <div>
              <p class="cr-form-section">Imputation du courrier</p>

              <div class="cr-dest-grille">
                @foreach ($groupes['postes'] as $p)
                  @php
                    $titulaire = $p->titulaireActuel();
                    $libellePoste = $p->libelleActuel().($titulaire ? ' — '.$titulaire->nomComplet() : ' — poste vacant');
                    if ($titulaire && $p->mandatActuel()?->type === \App\Models\MandatResponsable::INTERIM) { $libellePoste .= ' (intérim)'; }
                  @endphp
                  <label class="cr-dest"><input type="checkbox" class="imp-case" data-libelle="{{ $libellePoste }}" value="poste:{{ $p->id }}" name="dest[]" @checked(in_array("poste:{$p->id}", $oldDest, true))><span class="cr-dest__corps">{{ $libellePoste }}</span></label>
                @endforeach
              </div>

              <div class="cr-champ" style="margin-top:16px;">
                <label class="cr-etiquette" for="imp_autre">Autre destinataire (unité ou agent)</label>
                <div style="display:flex;gap:8px;">
                  <select class="form-select form-select-sm" id="imp_autre">
                    <option value="">Choisir…</option>
                    <optgroup label="Directions">@foreach ($groupes['directions'] as $u)<option value="direction:{{ $u->id }}" data-libelle="{{ $u->nom }}">{{ $u->nom }}</option>@endforeach</optgroup>
                    <optgroup label="Sous-directions">@foreach ($groupes['sous_directions'] as $u)<option value="sous_direction:{{ $u->id }}" data-libelle="{{ $u->nom }}">{{ $u->nom }}</option>@endforeach</optgroup>
                    <optgroup label="Services">@foreach ($groupes['services_dg']->concat($groupes['services']) as $u)<option value="service:{{ $u->id }}" data-libelle="{{ $u->nom }}">{{ $u->nom }}</option>@endforeach</optgroup>

                    <optgroup label="Agents">
                      @foreach ($groupes['agents'] as $a)
                        @php($details = collect([$a->fonction, $a->service?->nom])->filter()->implode(' · '))
                        <option value="agent:{{ $a->id }}" data-libelle="{{ $a->nomComplet() }}">{{ $a->nomComplet() }}@if ($details) — {{ $details }}@endif</option>
                      @endforeach
                    </optgroup>
                  </select>
                  <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" id="imp_ajouter"><i class="fa-solid fa-plus"></i> Ajouter</button>
                </div>
                <div id="imp_puces" class="cr-puces">
                  @foreach ($oldDest as $cle)
                    @continue(in_array($cle, $valeursGrille, true))
                    @php($partiesDestinataire = explode(':', $cle, 2))
                    @php($cible = \App\Support\DestinatairesImputation::resoudre($partiesDestinataire[0], (int) $partiesDestinataire[1]))
                    @if ($cible)
                      <span class="cr-puce" data-valeur="{{ $cle }}" data-libelle="{{ $cible['libelle'] }}">{{ $cible['libelle'] }}<button type="button" class="cr-puce__x" aria-label="Retirer">×</button><input type="hidden" name="dest[]" value="{{ $cle }}"></span>
                    @endif
                  @endforeach
                </div>
              </div>

              <div class="cr-champ">
                <span class="cr-etiquette" style="display:block;margin-bottom:6px;font-size:13px;font-weight:600;">Travaux et destinataires associés <span class="cr-requis">*</span></span>
                <table class="cr-roles" id="imp_roles"><tbody></tbody></table>
                <p class="c-o-light" id="imp_roles_vide" style="font-size:13px;margin:0;">Désignez un seul chef de file : il porte la diligence. Les autres destinataires lui sont rattachés (avis/aide ou copie).</p>
                <p class="c-o-light" id="imp_classement_note" style="display:none;font-size:13px;margin:0;">Instruction « Archiver » / « Pour information » seule : le courrier sera classé, sans diligence. Les destinataires choisis seront informés en copie.</p>
              </div>
            </div>

            {{-- ===== Instructions, délai ===== --}}
            <div>
              <p class="cr-form-section">Instructions de Monsieur le Directeur Général</p>
              <div class="cr-instr-grille">
                @foreach ($instructions as $ins)
                  <label class="cr-instr"><input type="checkbox" name="instructions[]" value="{{ $ins->id }}" data-classe="{{ in_array(\Illuminate\Support\Str::lower(trim($ins->libelle)), ['archiver', 'pour information'], true) ? 1 : 0 }}" @checked(in_array($ins->id, old('instructions', [])))><span>{{ $ins->libelle }}</span></label>
                @endforeach
              </div>

              <div class="cr-champs-2" style="margin-top:16px;">
                <div class="cr-champ">
                  <label class="cr-etiquette" for="imp_delai">Délai de traitement <span class="cr-requis">*</span></label>
                  <select class="form-select form-select-sm" id="imp_delai" name="delai_id" required>
                    <option value="">Choisir…</option>
                    @foreach ($delais as $dl) <option value="{{ $dl->id }}" data-jours="{{ $dl->delai_jours }}" data-libre="{{ $dl->saisie_libre ? 1 : 0 }}" @selected((string) old('delai_id') === (string) $dl->id)>{{ $dl->libelle }}</option> @endforeach
                  </select>
                </div>
                <div class="cr-champ">
                  <label class="cr-etiquette" for="imp_date">Date d'imputation <span class="cr-requis">*</span></label>
                  <input class="form-control form-control-sm" type="date" id="imp_date" name="date_imputation" value="{{ old('date_imputation', now()->toDateString()) }}" min="{{ $courrier->date_reception->toDateString() }}" max="{{ now()->toDateString() }}" required>
                </div>
              </div>
              <div class="cr-champ" id="imp_libre_bloc" style="display:none;">
                <label class="cr-etiquette" for="imp_libre">Échéance <span class="cr-requis">*</span></label>
                <input class="form-control form-control-sm" type="date" id="imp_libre" name="echeance_libre" value="{{ old('echeance_libre') }}" min="{{ now()->toDateString() }}" max="{{ $estReimputation ? $diligenceParente->echeance->toDateString() : '' }}">
              </div>
              <p class="cr-echeance-calc" id="imp_echeance_calc"></p>

              <div class="cr-champ">
                <label class="cr-etiquette" for="imp_particulieres">Instructions particulières</label>
                <textarea class="form-control form-control-sm" id="imp_particulieres" name="instructions_particulieres" rows="3" maxlength="2000">{{ old('instructions_particulieres') }}</textarea>
              </div>
              <div class="cr-champ">
                <label class="cr-etiquette" for="imp_observations">Observations</label>
                <textarea class="form-control form-control-sm" id="imp_observations" name="observations" rows="2" maxlength="2000">{{ old('observations') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="imp_btn" type="submit"><i class="fa-solid fa-paper-plane"></i> {{ $estReimputation ? 'Réimputer' : 'Imputer le courrier' }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
