@extends('layout.master')
@section('title', 'Mes notifications')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}"> {{-- classes tl-* (badges) --}}
@endsection

@php
  $ico = fn ($nom) => asset('assets/svg/icon-sprite.svg#'.$nom);
  $tons = [
      'retard' => ['Retard', 'tl-retard tl-solid', 'color:#fff'],
      'attente' => ["Aujourd'hui", 'tl-attente', 'color:#242323'],
      'avenir' => ['À venir', 'tl-avenir', 'color:#000'],
  ];
@endphp

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Mes notifications</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ $ico('stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item active">Notifications</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            <div class="card-header card-no-border flex justify-between items-center flex-wrap" style="gap:10px;">
              <div>
                <a class="btn {{ $filtre === 'toutes' ? 'btn-primary text-white' : 'btn-outline-primary' }}" href="{{ route('notifications.index') }}">Toutes</a>
                <a class="btn {{ $filtre === 'non_lues' ? 'btn-primary text-white' : 'btn-outline-primary' }}" href="{{ route('notifications.index', ['filtre' => 'non_lues']) }}">
                  Non lues <span class="badge badge-danger ms-1">{{ $nbNonLues }}</span>
                </a>
              </div>
              @if ($nbNonLues > 0)
                <form method="POST" action="{{ route('notifications.tout_lire') }}" class="m-0">
                  @csrf
                  <button class="btn btn-info text-black" type="submit"><i class="fa-solid fa-check-double"></i> Tout marquer comme lu</button>
                </form>
              @endif
            </div>
            <div class="card-body">
              <div class="table-responsive custom-scrollbar">
                <table class="display" id="data-source-1">
                  <thead>
                    <tr>
                      <th>Reçue le</th>
                      <th>Niveau</th>
                      <th>Alerte</th>
                      <th>Message</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($notifications as $notif)
                      @php
                        $d = $notif->data;
                        [$libTon, $classeTon, $styleTon] = $tons[$d['ton'] ?? ''] ?? ['Info', 'tl-encours', ''];
                        $nonLue = is_null($notif->read_at);
                      @endphp
                      <tr style="{{ $nonLue ? 'font-weight:600;' : 'opacity:.75;' }}">
                        {{-- ordre inverse : le tri croissant par defaut du tableau affiche ainsi les plus recentes en premier --}}
                        <td data-order="{{ -1 * $notif->created_at->timestamp }}" style="white-space:nowrap;">
                          @if ($nonLue)<span title="Non lue" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#2B6CB0; margin-right:6px;"></span>@endif
                          {{ $notif->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td><span class="tl-badge {{ $classeTon }}" style="{{ $styleTon }}">{{ $libTon }}</span></td>
                        <td>{{ $d['titre'] ?? 'Notification' }}@if (! empty($d['code'])) <div class="c-o-light"><b>{{ $d['code'] }}</b></div>@endif</td>
                        <td>{{ $d['message'] ?? '—' }}</td>
                        <td>
                          <a class="btn btn-primary" href="{{ route('notifications.ouvrir', $notif->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Ouvrir" style="width:30px; padding-left:5px; padding-right:5px;"><i class="text-white fa-regular fa-eye"></i></a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <p class="text-muted text-center mt-2 mb-0" style="font-size:12px;">Les 300 notifications les plus récentes sont affichées.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
@endsection
