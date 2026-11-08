{{-- Formulaire (fenêtre) des informations administratives de l'agent : état civil, famille, carrière. Le JS est dans js_carriere. Variable : $personnel --}}
@php
  $gradesOptions = \App\Models\Grade::orderBy('ordre')->get(['id', 'code']);
  $classesOptions = \App\Models\ClasseAgent::orderBy('ordre')->get(['id', 'libelle']);
  $echelonsOptions = \App\Models\Echelon::orderBy('numero')->get(['id', 'numero']);
  $reouvrirCarriere = old('_form') === 'carriere' && $errors->any();
@endphp

<div class="modal fade panel-modal" id="modal_carriere" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $reouvrirCarriere ? 1 : 0 }}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-briefcase"></i> Carrière — {{ $personnel->nomComplet() }}</h4>
        <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_carriere" action="{{ route('dgepn.personnel.carriere', $personnel->id) }}" method="post">
        @csrf
        <input type="hidden" name="_form" value="carriere">
        <div class="modal-body dark-modal">
          @if ($reouvrirCarriere)
            <div class="alert alert-warning" style="color:black" role="alert">@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div>
          @endif

          <p class="fa2-form-titre">Carrière</p>
          <div class="grid grid-cols-12 gap-3">
            <div class="col-span-3 sm:col-span-12">
              <label class="form-label" for="car_grade">Grade</label>
              <select class="form-select form-select-sm" id="car_grade" name="grade_id"><option value="">—</option>@foreach ($gradesOptions as $o)<option value="{{ $o->id }}">{{ $o->code }}</option>@endforeach</select>
            </div>
            <div class="col-span-4 sm:col-span-12">
              <label class="form-label" for="car_classe">Classe</label>
              <select class="form-select form-select-sm" id="car_classe" name="classe_id"><option value="">—</option>@foreach ($classesOptions as $o)<option value="{{ $o->id }}">{{ $o->libelle }}</option>@endforeach</select>
            </div>
            <div class="col-span-2 sm:col-span-12">
              <label class="form-label" for="car_echelon">Échelon</label>
              <select class="form-select form-select-sm" id="car_echelon" name="echelon_id"><option value="">—</option>@foreach ($echelonsOptions as $o)<option value="{{ $o->id }}">{{ $o->numero }}</option>@endforeach</select>
            </div>
            <div class="col-span-3 sm:col-span-12">
              <label class="form-label">Indice</label>
              <div class="fa2-indice" id="car_indice">—</div>
            </div>
            <div class="col-span-12"><small class="text-muted" id="car_aide_indice">L'indice se calcule d'après l'échelle de traitement : le grade, la classe et l'échelon vont ensemble.</small></div>
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="car_premiere">Date de première prise de service</label>
              <input class="form-control" id="car_premiere" name="date_premiere_prise_service" type="date">
            </div>
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="car_prise">Date de prise de service</label>
              <input class="form-control" id="car_prise" name="date_prise_service" type="date">
            </div>
          </div>

          <p class="fa2-form-titre">Fin de carrière</p>
          <div class="grid grid-cols-12 gap-3">
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="car_retraite">Date de départ à la retraite</label>
              <input class="form-control" id="car_retraite" name="date_depart_retraite" type="date">
            </div>
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="car_deces">Date de décès</label>
              <input class="form-control" id="car_deces" name="date_deces" type="date">
            </div>
            <div class="col-span-12"><small class="text-muted"><i class="fa-solid fa-circle-info"></i> Ces deux dates sont informatives : elles ne rendent pas l'agent inactif et ne libèrent pas son poste.</small></div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
          <button class="btn btn-primary text-white" id="car_btn" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
