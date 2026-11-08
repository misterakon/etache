@extends('layout.master')
@section('title', 'Exercice')

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
            <h3>Exercice</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="index.html">                                       
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Parametres</li>
              <li class="breadcrumb-item active">Exercice</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <!-- HTML (DOM) sourced data  Starts-->
        <div class="col-span-12">
          <div class="card">
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button"  onclick="add_form()">AJOUTER</button>
            {{-- <div class="card-header pb-0 card-no-border">
              <h5 class="mb-3">Liste</h5>
              <p class="f-m-light mt-1">The foundation for DataTables is progressive enhancement, so it is very adept at reading table information directly from the DOM. This example shows how easy it is to add searching, ordering and paging to your HTML table by simply running DataTables on it.</p>
            </div> --}}
            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Exercice</th>
                      <th>Libelle</th>
                      <th>Date debut</th>
                      <th>Date fin</th>
                      <th>Statut</th>
                      <th>Action </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Tiger Nixon</td>
                      <td>System Architect</td>
                      <td class="action">                                  <a class="pdf" href="sample.pdf" target="_blank"><i class="icofont icofont-file-pdf"></i></a></td>
                      <td> <span class="badge text-white rounded-full badge-success">hired</span></td>
                      <td><span class="badge text-white rounded-full badge-danger">Pending</span></td>
                      <td> 
                        <ul class="action"> 
                          <li class="edit"> <a href="#"><i class="fa-regular fa-pen-to-square"></i></a></li>
                          <li class="delete"><a href="#"><i class="fa-solid fa-trash-can"></i></a></li>
                        </ul>
                      </td>
                    </tr>
                    <tr>
                      <td>Colleen Hurst</td>
                      <td>Javascript Developer</td>
                      <td class="action"> <a class="pdf" href="../assets/pdf/sample.pdf" target="_blank"><i class="icofont icofont-file-pdf"> </i></a></td>
                      <td> <span class="badge text-white rounded-full badge-danger">Pending</span></td>
                      <td><span class="badge text-white rounded-full badge-danger">Pending</span></td>
                      <td> 
                        <ul class="action"> 
                          <li class="edit"> <a href="#"><i class="fa-regular fa-pen-to-square"></i></a></li>
                          <li class="delete"><a href="#"><i class="fa-solid fa-trash-can"></i></a></li>
                        </ul>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @include('parametre.form_exercice')
    @include('parametre.js_script')

    <!-- Container-fluid Ends-->
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