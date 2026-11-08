@extends('layout.master')
@section('title', 'Actions mesurables')

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
            <h3>Actions mesurables</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Actions mesurables</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          @include('feuille_de_route._filtres', ['jusqua' => 'action_cle'])
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
                      <th>Code</th>
                      <th>Action clé</th>
                      <th>Libellé</th>
                      @hasanyrole('ADMIN|DG')
                      <th>Action</th>
                      @endhasanyrole
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($actionsMesurables as $am)
                      <tr id="row-{{ $am->id }}">
                        <td>{{ $am->code }}</td>
                        <td>{{ $am->actionCle->axe->code }} / {{ $am->actionCle->code }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($am->libelle, 80) }}</td>
                        @hasanyrole('ADMIN|DG')
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_action_mesurable({{ $am->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_action_mesurable({{ $am->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
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

    @include('feuille_de_route.form_action_mesurable')
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
