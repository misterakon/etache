@extends('layout.master')
@section('title', 'Responsables')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/responsables.css') }}">
  <!-- Select2 css -->
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection


@section('main-content')
  @php
    $libellesNiveau = [
        'direction_generale' => 'Direction Générale',
        'direction' => 'Direction',
        'sous_direction' => 'Sous-direction',
        'service' => 'Service',
    ];
    $etats = [
        'titulaire' => ['Titulaire', 'terminee'],
        'interim' => ['Intérim', 'avenir'],
        'sans' => ['Sans responsable', 'attente'],
        'inactif' => ['Agent inactif', 'retard'],
    ];
    $tuiles = [
        ['Unités', $synthese['unites'], 'fa-solid fa-sitemap', 'encours'],
        ['Avec un responsable', $synthese['titulaires'], 'fa-solid fa-user-check', 'terminee'],
        ['Intérims en cours', $synthese['interims'], 'fa-solid fa-user-clock', 'avenir'],
        ['À traiter', $synthese['sans'] + $synthese['inactifs'], 'fa-solid fa-triangle-exclamation', 'attente'],
    ];
  @endphp

  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Organigramme / Affectation</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">DGEPN</li>
              <li class="breadcrumb-item active">Responsables</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper rs-page">

      {{-- Synthèse --}}
      <div class="rs-tuiles">
        @foreach ($tuiles as [$libelle, $valeur, $icone, $ton])
          <div class="rs-stat tl-{{ $ton }}">
            <div>
              <span class="rs-stat__lib">{{ $libelle }}</span>
              <div class="rs-stat__val">{{ $valeur }}</div>
            </div>
            <div class="rs-stat__ico"><i class="{{ $icone }}"></i></div>
          </div>
        @endforeach
      </div>

      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            <div class="card-body">

              <div class="rs-legende">
                @foreach ($etats as [$libelleEtat, $tonEtat])
                  <span class="tl-badge tl-{{ $tonEtat }}">{{ $libelleEtat }}</span>
                @endforeach
                <span class="text-muted">Le responsable en poste aujourd'hui est affiché ; l'historique complet est accessible à chaque ligne.</span>
              </div>

              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" id="data-source-1" style="width:100%">
                  <thead>
                    <tr>
                      <th>Unité</th>
                      <th>Niveau</th>
                      <th>Responsable</th>
                      <th>En poste depuis</th>
                      <th>État</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($lignes as $l)
                      @php
                        $mandat = $l['mandat'];
                        $r = $l['responsable'];
                        [$libelleEtat, $tonEtat] = $etats[$l['etat']];
                      @endphp
                      <tr>
                        {{-- data-order : ordre hiérarchique (DG, directions, sous-directions, services) --}}
                        <td data-order="{{ $l['ordre'] }}" style="width:50%">
                          <div class="rs-unite" style="padding-left: {{ $l['profondeur'] * 22 }}px;">
                            @if ($l['profondeur'] > 0)<span class="rs-branche">└</span>@endif
                            <strong>{{ $l['nom'] }}</strong>
                            @if ($l['code']) <small class="text-muted">{{ $l['code'] }}</small> @endif
                            <span class="hidden">{{ $l['parent'] }}</span>
                          </div>
                        </td>
                        <td><span class="rs-niveau rs-niveau--{{ $l['niveau'] }}">{{ $libellesNiveau[$l['niveau']] }}</span></td>
                        <td>
                          @if ($r)
                            <div class="flex items-center gap-2">
                              @if ($r->photo_url)
                                <img class="common-circle" src="{{ $r->photo_url }}" alt="{{ $r->nomComplet() }}">
                              @else
                                <div class="common-circle bg-lighter-primary">{{ $r->initiales() }}</div>
                              @endif
                              <div>
                                <a class="font-medium" href="{{ route('dgepn.personnel.fiche', $r->id) }}">{{ $r->nomComplet() }}</a>
                                @if ($r->fonction)<br><small class="text-muted">{{ $r->fonction }}</small>@endif
                              </div>
                            </div>
                          @else
                            <span class="text-muted" style="font-style:italic;">Aucun responsable</span>
                          @endif
                        </td>
                        <td data-order="{{ $mandat?->date_debut?->format('Y-m-d') }}">
                          @if ($mandat)
                            {{ $mandat->date_debut->format('d/m/Y') }}
                            @if ($mandat->date_fin)<br><small class="text-muted">jusqu'au {{ $mandat->date_fin->format('d/m/Y') }}</small>@endif
                          @else
                            —
                          @endif
                        </td>
                        <td><span class="tl-badge tl-{{ $tonEtat }}">{{ $libelleEtat }}</span></td>
                        <td>
                          <div class="flex gap-2">
                            <button class="btn btn-warning" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Historique" style="width:30px; padding-left:0; padding-right:0;"
                                    onclick="voir_historique('{{ $l['niveau'] }}', {{ $l['unite_id'] ?? 'null' }}, @js($l['nom']))"><i class="text-white fa-solid fa-clock-rotate-left"></i></button>
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

    @include('dgepn.form_responsable')
    @include('dgepn.js_responsable')
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
