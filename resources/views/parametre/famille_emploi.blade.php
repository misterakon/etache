@extends('layout.master')
@section('title', "Famille d'emploi")

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/swiper/swiper-bundle.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Famille d'emploi</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Paramètres</li>
              <li class="breadcrumb-item active">Famille d'emploi</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            @can('famille_emploi.creer')
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="add_form()"><i class="text-white fa-regular fa-plus"></i> AJOUTER</button>
            @endcan

            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Libellé</th>
                      <th>Statut</th>
                      @canany(['famille_emploi.modifier', 'famille_emploi.supprimer'])
                      <th>Action</th>
                      @endcanany
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($famillesEmploi as $famille)
                      <tr id="row-{{ $famille->id }}">
                        <td>{{ $famille->code }}</td>
                        <td>{{ $famille->libelle }}</td>
                        <td>
                          @if ($famille->actif)
                            <span class="badge text-white rounded-full badge-success">Actif</span>
                          @else
                            <span class="badge text-white rounded-full badge-danger">Inactif</span>
                          @endif
                        </td>
                        @canany(['famille_emploi.modifier', 'famille_emploi.supprimer'])
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_famille_emploi({{ $famille->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_famille_emploi({{ $famille->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
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

    @include('parametre.form_famille_emploi')
    @include('parametre.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>

  <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
  <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
  <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
  <script src="{{ asset('assets/js/height-equal.js') }}"></script>
  <script src="{{ asset('assets/js/popover-custom.js') }}"></script>
  <script src="{{ asset('assets/js/modalpage/validation-modal.js') }}"></script>
@endsection
