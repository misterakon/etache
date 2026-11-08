@extends('layout.master')
@section('title', 'Actions clés')

@section('css')
    <!-- Select2 css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Actions clés</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Actions clés</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          @include('feuille_de_route._filtres', ['jusqua' => 'axe', 'direction' => true])
        </div>
        <div class="col-span-12">
          <div class="card">

            @hasanyrole('ADMIN|DG')
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="add_form()"><i class="text-white fa-regular fa-plus"></i> AJOUTER</button>
            @endhasanyrole

            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Axe</th>
                      <th>Code</th>
                      <th>Libellé</th>
                      <th>Direction</th>
                      <th>Responsable(s)</th>
                      @hasanyrole('ADMIN|DG')
                      <th>Action</th>
                      @endhasanyrole
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($actionsCles as $ac)
                      <tr id="row-{{ $ac->id }}">
                        <td>{{ $ac->axe->code }}</td>
                        <td>{{ $ac->code }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($ac->libelle, 70) }}</td>
                        <td>
                          @if ($ac->direction)<span class="tl-badge tl-encours" title="Direction porteuse">{{ $ac->direction->nom }}</span>@endif
                          @foreach ($ac->directionsAssociees as $d)<span class="tl-badge tl-neutre" title="Direction associée">{{ $d->nom }}</span>@endforeach
                        </td>
                        <td>
                          @foreach ($ac->affectations->where('role_affectation_id', $responsableRoleId) as $affectation)
                            <span class="badge badge-light-primary">{{ trim($affectation->personnel->prenom.' '.$affectation->personnel->nom) }}</span>
                          @endforeach
                        </td>
                        @hasanyrole('ADMIN|DG')
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_action_cle({{ $ac->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_action_cle({{ $ac->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
                          </div>
                        </td>
                        @endhasanyrole
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

    @include('feuille_de_route.form_action_cle')
    @include('feuille_de_route.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>

  <!-- select2 -->
  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
