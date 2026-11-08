@extends('layout.master')
@section('title', 'Comptes utilisateurs')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection


@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Comptes utilisateurs</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Utilisateurs</li>
              <li class="breadcrumb-item active">Comptes utilisateurs</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            @can('comptes_utilisateurs.creer')
            <button class="btn btn-primary btn-flottant-centre ripple-button text-white" type="button" onclick="add_form()"><i class="text-white fa-regular fa-plus"></i> AJOUTER</button>
            @endcan

            <div class="card-body">
              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Nom</th>
                      <th>Email</th>
                      <th>Agent lié</th>
                      <th>Profil</th>
                      <th>Statut</th>
                      @canany(['comptes_utilisateurs.modifier', 'comptes_utilisateurs.supprimer'])
                      <th>Action</th>
                      @endcanany
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($comptes as $compte)
                      <tr id="row-{{ $compte->id }}">
                        <td>{{ $compte->name }}</td>
                        <td><b>{{ $compte->email }}</b></td>
                        <td>{{ $compte->personnel ? trim($compte->personnel->prenom.' '.$compte->personnel->nom) : '—' }}</td>
                        <td><b>{{ $compte->profilAcces?->libelle }}</b>
                          @if ($compte->sansMandatDirection())
                            <span class="badge badge-warning text-white rounded-full" title="Aucune direction rattachée : ce compte agit comme un RA">RD sans mandat</span>
                          @endif
                        </td>
                        <td>
                          @if ($compte->actif)
                            <span class="badge text-white rounded-full badge-success">Actif</span>
                          @else
                            <span class="badge text-white rounded-full badge-danger">Inactif</span>
                          @endif
                        </td>
                        @canany(['comptes_utilisateurs.modifier', 'comptes_utilisateurs.supprimer'])
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" style="width:30px; padding-left:0; padding-right:0;" onclick="edit_compte({{ $compte->id }})"><i class="text-white fa-regular fa-pen-to-square"></i></button>
                            @role('ADMIN')
                            <button class="btn btn-danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" style="width:30px; padding-left:0; padding-right:0;" onclick="deletes_compte({{ $compte->id }})"><i class="text-white fa-solid fa-trash-can"></i></button>
                            @endrole
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

    @include('utilisateur.form_compte')
    @include('utilisateur.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
  <script src="{{ asset('assets/js/custom-btn-ripple.js') }}"></script>
  <script src="{{ asset('assets/js/bs-indeterminate.js') }}"></script>

  <!-- select2 -->
  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
