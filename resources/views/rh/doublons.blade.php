@extends('layout.master')
@section('title', 'Doublons de fiches')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Doublons de fiches</h1>
          <p class="cr-hero__sous">Des fiches qui se ressemblent : même adresse e-mail, même téléphone, même nom et prénom. À la création, un doublon probable est déjà refusé ; cette page repère ceux qui existent depuis avant.</p>
        </div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      @forelse ($groupes as $g)
        <div class="cr-carte rh-groupe">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clone"></i> {{ $g['agents']->count() }} fiches</h2><span class="rh-groupe__raison"><i class="fa-solid fa-link"></i> {{ ucfirst($g['raison']) }}</span></div>
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Agent</th><th>E-mail</th><th>Téléphone</th><th>Naissance</th><th>Service</th><th></th></tr></thead>
              <tbody>
                @foreach ($g['agents'] as $a)
                  <tr>
                    <td><div class="rh-agent"><span class="rh-avatar">{{ $a->initiales() }}</span><span><b>{{ $a->nomComplet() }}</b><small>{{ $a->matricule ?: 'Sans matricule' }}</small></span></div></td>
                    <td>{{ $a->email ?: '—' }}</td><td>{{ $a->telephone ?: '—' }}</td><td>{{ $a->date_naissance?->format('d/m/Y') ?? '—' }}</td><td>{{ $a->service?->nom ?? '—' }}</td>
                    <td style="white-space:nowrap;">
                      <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('dgepn.personnel.fiche', $a->id) }}"><i class="fa-solid fa-eye"></i> Fiche</a>
                      @role('ADMIN')<button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="rh_archiver({{ $a->id }}, @js($a->nomComplet()))"><i class="fa-solid fa-box-archive"></i> Archiver</button>@endrole
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @empty
        <div class="cr-carte"><div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Aucun doublon repéré</strong>Aucune fiche ne partage la même adresse e-mail, le même téléphone ou le même nom et prénom.</div></div>
      @endforelse
      @role('ADMIN') @include('rh._archiver') @endrole
    </div>
  </div>
@endsection
