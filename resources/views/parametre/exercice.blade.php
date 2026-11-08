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
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Paramètres</li>
              <li class="breadcrumb-item active">Exercice</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">

            <div class="vt-barre-actions">
              <div class="vt-barre-actions__titre"><i class="fa-regular fa-calendar-days"></i><div><strong>Exercices</strong><small>Années de la feuille de route</small></div></div>
              @hasanyrole('ADMIN|DG')
              <div class="vt-barre-actions__boutons">
                <a class="vt-btn vt-btn--vert" href="{{ route('parametre.reconduction') }}"><i class="fa-solid fa-rotate"></i> Reconduction</a>
                <button class="vt-btn vt-btn--primaire" type="button" onclick="add_form()"><i class="fa-solid fa-plus"></i> Ajouter</button>
              </div>
              @endhasanyrole
            </div>

            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Année</th>
                      <th>Libellé</th>
                      <th>Date début</th>
                      <th>Date fin</th>
                      <th>Statut</th>
                      <th>Avancement</th>
                      @hasanyrole('ADMIN|DG')
                      <th>Action</th>
                      @endhasanyrole
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($exercices as $exercice)
                      <tr id="row-{{ $exercice->id }}">
                        <td>{{ $exercice->annee }}</td>
                        <td>{{ $exercice->libelle }}</td>
                        <td>{{ optional($exercice->date_debut)->format('d/m/Y') }}</td>
                        <td>{{ optional($exercice->date_fin)->format('d/m/Y') }}</td>
                        <td>
                          @if ($exercice->actif)
                            <span class="badge text-white rounded-full badge-success">Actif</span>
                          @else
                            <span class="badge text-white rounded-full badge-danger">Inactif</span>
                          @endif

                          @if ($exercice->estClos())
                            <span class="badge text-white rounded-full badge-warning"><i class="fa-solid fa-lock"></i> Clos le {{ $exercice->cloture_le->format('d/m/Y') }}</span>
                          @endif
                        </td>
                        <td>
                          @if ($exercice->avancementParSousTaches())
                            <span class="badge text-white rounded-full badge-primary">Par sous-tâches</span>
                          @else
                            <span class="badge text-white rounded-full badge-secondary">Manuel</span>
                          @endif
                        </td>
                        @hasanyrole('ADMIN|DG')
                        <td>
                          <div class="flex gap-2">
                            <a class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Clôture" style="width:30px; padding-left:0; padding-right:0;" href="{{ route('parametre.exercice.cloture', $exercice->id) }}"><i class="text-white fa-solid {{ $exercice->estClos() ? 'fa-lock' : 'fa-lock-open' }}"></i></a>
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_exercice({{ $exercice->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_exercice({{ $exercice->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
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

    @include('parametre.form_exercice')
    @include('parametre.js_script')
    @include('parametre.js_mode_avancement')

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
