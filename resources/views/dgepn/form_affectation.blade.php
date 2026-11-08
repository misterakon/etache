{{-- Formulaire (fenêtre) d'affectation d'un agent à un poste de l'organigramme, avec intérim. Le JS est dans js_affectation. --}}
@php
  $aff = \App\Support\AffectationPoste::donneesFormulaire();
  $conflit = session('affectation_conflit');
  $reouvrir = old('_form') === 'affectation' && ($errors->any() || $conflit);
@endphp

<div class="modal fade panel-modal" id="modal_affectation" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $reouvrir ? 1 : 0 }}">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-user-tie"></i> Affecter à un poste</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Postes à titulaire unique que l'agent occupe aujourd'hui : on peut y mettre fin (intérim ou fonction) --}}
      <div id="aff_postes_actuels" style="display:none; padding:14px 20px 0;">
        <p class="form-label" style="margin-bottom:6px;"><b>Postes occupés actuellement</b></p>
        <div id="aff_postes_liste"></div>
        <hr style="margin:14px 0 0;">
      </div>

      <form id="form_affectation" action="{{ route('dgepn.personnel.affectation') }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="affectation">
        <input type="hidden" name="personnel_id" value="{{ old('personnel_id') }}">

        <div class="modal-body dark-modal">
          @if ($errors->has('affectation') || $conflit)
            <div class="alert alert-warning" role="alert" style="margin-bottom:12px;">
              @if ($conflit) <i class="fa-solid fa-triangle-exclamation"></i> {{ $conflit }} @else {{ $errors->first('affectation') }} @endif
            </div>
          @endif

          <div class="col-span-12">
            <label class="form-label"><b>Agent</b></label>
            <input class="form-control" id="aff_agent" type="text" value="" readonly>
          </div>

          <div class="col-span-12 mt-2">
            <label class="form-label" for="aff_fonction"><b>Fonction</b></label>
            <select class="form-select form-select-sm" id="aff_fonction" name="fonction_id" required>
              <option value="">Choisir…</option>
              @foreach ($aff['fonctions'] as $f) <option value="{{ $f['id'] }}" @selected((string) old('fonction_id') === (string) $f['id'])>{{ $f['libelle'] }}</option> @endforeach
            </select>
            @error('fonction_id') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-span-12 mt-2" id="aff_bloc_unite" style="display:none;">
            <label class="form-label" for="aff_unite" id="aff_etiquette_unite"><b>Poste</b></label>
            <select class="form-select form-select-sm" id="aff_unite" name="unite_id"><option value="">Choisir…</option></select>
          </div>

          <div class="mt-2" id="aff_occupation" style="display:none;"></div>

          <div class="col-span-12 mt-2" id="aff_bloc_interim" style="display:none;">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="aff_interim" name="interim" value="1" @checked(old('interim'))>
              <label class="form-check-label" for="aff_interim"><b>Intérim</b> <span class="text-muted">(l'agent assure l'intérim, il n'est pas titulaire du poste)</span></label>
            </div>
          </div>

          <div class="col-span-12 mt-2">
            <label class="form-label" for="aff_date"><b>Date de prise de fonction</b></label>
            <input class="form-control" id="aff_date" name="date_debut" type="date" value="{{ old('date_debut', now()->toDateString()) }}" required>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="aff_acte"><b>Référence de l'acte</b> <span class="text-muted">(facultatif)</span></label>
            <input class="form-control" id="aff_acte" name="reference_acte" type="text" maxlength="100" value="{{ old('reference_acte') }}">
          </div>

          <div class="col-span-12 mt-3" id="aff_bloc_confirmer" style="display:{{ $conflit ? '' : 'none' }};">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="aff_confirmer" name="confirmer" value="1" @checked($conflit)>
              <label class="form-check-label" for="aff_confirmer"><b>Je confirme le remplacement</b></label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="aff_btn" type="submit">Affecter</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Mettre fin à une affectation (intérim ou titulaire) --}}
<div class="modal fade panel-modal" id="modal_fin_affectation" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ old('_form') === 'fin_affectation' && $errors->any() ? 1 : 0 }}">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="fin_titre">Mettre fin à l'affectation</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_fin_affectation" action="{{ route('dgepn.personnel.affectation.fin') }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="fin_affectation">
        <input type="hidden" name="mandat_id" value="{{ old('mandat_id') }}">
        <input type="hidden" name="libelle_poste" value="{{ old('libelle_poste') }}">
        <input type="hidden" name="est_interim" value="{{ old('est_interim') }}">
        <div class="modal-body dark-modal">
          @if ($errors->has('fin_affectation') || (old('_form') === 'fin_affectation' && $errors->any()))
            <div class="alert alert-warning" role="alert" style="margin-bottom:12px;">{{ $errors->first('fin_affectation') ?: $errors->first() }}</div>
          @endif
          <p id="fin_poste" style="font-weight:600;"></p>
          <p class="text-muted" id="fin_consequence" style="font-size:12.5px;"></p>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="fin_date"><b>Dernier jour au poste</b></label>
            <input class="form-control" id="fin_date" name="date_fin" type="date" value="{{ old('date_fin', now()->toDateString()) }}" required>
            <small class="text-muted"><i class="fa-solid fa-clock"></i> L'affectation prend fin <b>à la fin de cette journée</b> : l'agent reste en poste jusqu'à ce soir (minuit), et le poste est libéré le lendemain.</small>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="fin_motif"><b>Motif</b></label>
            <input class="form-control" id="fin_motif" name="motif_fin" type="text" maxlength="255" value="{{ old('motif_fin') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-danger text-white" type="submit">Mettre fin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  const AFF = @json($aff);
</script>
