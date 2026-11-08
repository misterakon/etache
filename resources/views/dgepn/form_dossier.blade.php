{{-- Formulaire (fenêtre) d'ajout d'un document au dossier de l'agent. Le JS est dans js_dossier. Variable : $personnel --}}
@php
  $typesDossier = $typesDossierDisponibles ?? collect();
  $extDossier = \App\Support\Reglages::get('personnel.dossier_extensions');
  $maxDossierKo = \App\Support\Reglages::get('personnel.dossier_taille_max_ko');
  $reouvrirDossier = old('_form') === 'dossier' && $errors->any();
@endphp

<div class="modal fade panel-modal" id="modal_dossier" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $reouvrirDossier ? 1 : 0 }}">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-folder-plus"></i> Ajouter un document</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_dossier" action="{{ route('dgepn.personnel.dossier.store', $personnel->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_form" value="dossier">
        <div class="modal-body dark-modal">
          @if ($reouvrirDossier)
            <div class="alert alert-warning" style="color:black" role="alert">@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div>
          @endif
          <div class="col-span-12">
            <label class="form-label" for="dos_type"><b>Type de document</b></label>
            <select class="form-select form-select-sm" id="dos_type" name="type_document" required>
              <option value="">{{ $typesDossier->isEmpty() ? 'Tous les types ont déjà été déposés' : 'Choisir…' }}</option>
              @foreach ($typesDossier as $t) <option value="{{ $t->code }}" @selected(old('type_document') === $t->code)>{{ $t->libelle }}</option> @endforeach
            </select>
          </div>
          <div class="col-span-12 mt-2">
            <label class="form-label" for="dos_fichier"><b>Fichier</b></label>
            <input class="form-control" id="dos_fichier" name="fichier" type="file" accept="{{ '.'.implode(',.', $extDossier) }}" data-max-ko="{{ $maxDossierKo }}" required>
            <small class="text-muted">{{ strtoupper(implode(', ', $extDossier)) }} · {{ round($maxDossierKo / 1024, 1) }} Mo maximum</small>
            <p class="text-danger" id="dos_message" style="margin:4px 0 0;font-size:12.5px;"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="dos_btn" type="submit" @disabled($typesDossier->isEmpty())>Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>
