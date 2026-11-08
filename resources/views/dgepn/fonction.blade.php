@extends('layout.master')
@section('title', 'Fonctions')

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Fonctions</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg></a></li>
              <li class="breadcrumb-item">DGEPN</li>
              <li class="breadcrumb-item active">Fonctions</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="fn_nouvelle()"><i class="text-white fa-regular fa-plus"></i> AJOUTER</button>
            <div class="card-body">
              <p class="text-muted">Les fonctions servent à affecter les agents aux postes de l'organigramme. Les postes à titulaire unique (directeur général, DGA, directeur, sous-directeur, chef de service) sont fixes ; vous ajoutez librement les autres fonctions (agent, chargé d'études, conseiller, technicien…).</p>
              <div class="support-ticket-table">
                <table class="display custom-border table-striped" id="basic-6">
                  <thead><tr><th>Ordre</th><th>Code</th><th>Fonction</th><th>Rattachement</th><th>Agents</th><th>Statut</th><th>Action</th></tr></thead>
                  <tbody>
                    @foreach ($fonctions as $f)
                      <tr>
                        <td>{{ $f->ordre }}</td>
                        <td><b>{{ $f->code }}</b></td>
                        <td>{{ $f->libelle }}@if ($f->systeme) <span class="badge badge-secondary text-white rounded-full">Origine</span>@endif</td>
                        <td>{{ \App\Models\Fonction::CATEGORIES[$f->categorie] ?? $f->categorie }}</td>
                        <td>{{ $f->personnel_count }}</td>
                        <td>@if ($f->actif) <span class="badge text-white rounded-full badge-success">Active</span> @else <span class="badge text-white rounded-full badge-danger">Inactive</span> @endif</td>
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" style="width:30px; padding-left:0; padding-right:0;" title="Modifier"
                                    data-id="{{ $f->id }}" data-code="{{ $f->code }}" data-libelle="{{ $f->libelle }}" data-categorie="{{ $f->categorie }}" data-ordre="{{ $f->ordre }}" data-actif="{{ $f->actif ? 1 : 0 }}" data-systeme="{{ $f->systeme ? 1 : 0 }}" onclick="fn_modifier(this)"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" style="width:30px; padding-left:0; padding-right:0;" title="Supprimer" onclick="fn_supprimer({{ $f->id }}, @js($f->libelle))"><i class="text-white fa-solid fa-trash-can"></i></button>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @include('dgepn.form_fonction')
    @include('dgepn.js_fonction')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/support-ticket-custom.js') }}"></script>
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>
@endsection
