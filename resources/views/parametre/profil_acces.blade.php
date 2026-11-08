@extends('layout.master')
@section('title', "Profil d'accès")

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/swiper/swiper-bundle.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/profil-acces.css') }}?v={{ filemtime(public_path('assets/css/profil-acces.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    {{-- <div class="container"><x-titre-page titre="Profil d'accès" :fil="['Paramètres', 'Profil d\'accès']" /></div> --}}

    @php $totalPages = count(\App\Support\Droits::pages()); @endphp

    <div class="container ep-page pa-page-wrap">
      <x-entete-page tag="Paramètres" titre="Profils d'accès" sous="Chaque profil choisit un niveau d'accès par sous-menu : Aucun accès, Lecture, Saisie ou Gestion, plus quelques droits spéciaux (signature, validation, export…).">
        <x-slot:badges>
          <span class="pa-tag"><i class="fa-solid fa-user-shield"></i> {{ $profilsAcces->count() }} profil(s)</span>
          <span class="pa-tag"><i class="fa-solid fa-list-check"></i> {{ $totalPages }} sous-menus à régler</span>
        </x-slot:badges>
        <x-slot:actions>
          @can('profils_acces.creer')
            <button class="pa-ajouter" type="button" onclick="add_form()"><i class="fa-solid fa-plus"></i> Nouveau profil</button>
          @endcan
        </x-slot:actions>
      </x-entete-page>

      <div class="pa-panneau data-source-wrapper">
        <div class="overflow-x-auto custom-scrollbar">
          <table class="pa-table" id="data-source-1" style="width:100%">
            <thead>
              <tr>
                <th><b>Profil</b></th>
                <th><b>Droits</b></th>
                <th><b>Comptes</b></th>
                <th><b>Statut</b></th>
                <th><b>Action</b></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($profilsAcces as $profil)
                <tr id="row-{{ $profil->id }}">
                  <td data-order="{{ $profil->libelle }}">
                    <div class="pa-identite">
                      {{-- <div class="pa-avatar">{{ mb_strtoupper(mb_substr($profil->libelle, 0, 1)) }}</div> --}}
                      <div>
                        <div class="pa-nom">{{ $profil->libelle }}@if ($profil->systeme)<span class="pa-badge-systeme"><i class="fa-solid fa-lock"></i> Système</span>@endif</div>
                        <div class="pa-slug">{{ $profil->slug }}</div>
                      </div>
                    </div>
                  </td>
                  <td data-order="{{ $profil->systeme ? 999 : $profil->nombrePages() }}">
                    @if ($profil->systeme)
                      <span class="pa-droits-chip pa-droits-chip--tous">Tous les droits</span>
                    @else
                      <span class="pa-droits-chip">{{ $profil->nombrePages() }} / {{ $totalPages }} sous-menus</span>
                    @endif
                  </td>
                  <td class="pa-comptes" data-order="{{ $profil->users_count }}">{{ $profil->users_count }}</td>
                  <td>
                    @if ($profil->actif)
                      <span class="pa-statut pa-statut--actif">Actif</span>
                    @else
                      <span class="pa-statut pa-statut--inactif">Inactif</span>
                    @endif
                  </td>
                  <td>
                    @if ($profil->systeme)
                      <span class="pa-badge-systeme" data-bs-toggle="tooltip" data-tooltip="Non modifiable"><i class="fa-solid fa-lock"></i> Verrouillé</span>
                    @else
                      <div class="pa-actions">
                        <button class="pa-btn pa-btn--modifier" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" onclick="edit_profil_acces({{ $profil->id }})"><i class="fa-regular fa-pen-to-square"></i></button>
                        <button class="pa-btn pa-btn--supprimer" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" onclick="deletes_profil_acces({{ $profil->id }})"><i class="fa-solid fa-trash-can"></i></button>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @include('parametre.form_profil_acces')
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
