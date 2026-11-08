@extends('layout.master')
@section('title', 'Direction')

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Direction</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">DGEPN</li>
              <li class="breadcrumb-item active">Direction</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">

            @can('direction.creer')
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="add_form()"><i class="text-white fa-regular fa-plus"></i> AJOUTER</button>
            @endcan

            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Nom</th>
                      <th>Description</th>
                      @canany(['direction.modifier', 'direction.supprimer'])
                      <th>Action</th>
                      @endcanany
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($directions as $direction)
                      <tr id="row-{{ $direction->id }}">
                        <td><strong>{{ $direction->code }}</strong></td>
                        <td>{{ $direction->nom }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($direction->description, 80) }}</td>
                        @canany(['direction.modifier', 'direction.supprimer'])
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_direction({{ $direction->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_direction({{ $direction->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
                          </div>
                        </td>
                        @endcanany
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

    @include('dgepn.form_direction')
    @include('dgepn.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>
@endsection
