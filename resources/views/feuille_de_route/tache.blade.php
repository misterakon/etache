@extends('layout.master')
@section('title', 'Tâches')

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Tâches</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Tâches</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="add_form()">AJOUTER</button>

            <div class="card-body">
              <div class="support-ticket-table">
                <table class="display custom-border table-striped" id="basic-6">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Libellé</th>
                      <th>Échéance</th>
                      <th>Avancement</th>
                      <th>Statut</th>
                      <th>Responsable(s)</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($taches as $tache)
                      <tr id="row-{{ $tache->id }}">
                        <td>{{ $tache->code }}</td>
                        <td>
                          <div title="{{ $tache->libelle }}" style="max-width: 28vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $tache->libelle }}</div>
                        </td>
                        <td data-order="{{ optional($tache->date_fin)->format('Y-m-d') }}">{{ optional($tache->date_fin)->format('d/m/Y') }}</td>
                        <td>{{ $tache->avancement_pourcentage }}%</td>
                        <td>
                          @if ($tache->statutExecution)
                            <span class="badge text-white rounded-full" style="background-color: {{ $tache->statutExecution->couleur ?: '#5B6B84' }};">
                              {{ $tache->statutExecution->libelle }}
                            </span>
                          @endif
                        </td>
                        <td>
                          @php
                            $responsables = $tache->affectations->where('role_affectation_id', $responsableRoleId);
                          @endphp
                          @if ($responsables->isNotEmpty())
                            <ul class="common-f-start">
                              @foreach ($responsables as $affectation)
                                <li data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="{{ trim($affectation->personnel->prenom.' '.$affectation->personnel->nom) }}">
                                  @if ($affectation->personnel->photo_url)
                                    <img class="common-circle" src="{{ $affectation->personnel->photo_url }}" alt="{{ trim($affectation->personnel->prenom.' '.$affectation->personnel->nom) }}">
                                  @else
                                    <div class="common-circle bg-lighter-primary">{{ mb_strtoupper(mb_substr($affectation->personnel->prenom ?: $affectation->personnel->nom, 0, 1)) }}</div>
                                  @endif
                                </li>
                              @endforeach
                            </ul>
                          @endif
                        </td>
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_tache({{ $tache->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_tache({{ $tache->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
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

    @include('feuille_de_route.form_tache')
    @include('feuille_de_route.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/support-ticket-custom.js') }}"></script>

  {{-- <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script> --}}
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>
@endsection
