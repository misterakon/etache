@extends('layout.master')
@section('title', 'Import et export du personnel')

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
          <h1 class="cr-hero__titre">Import et export Excel</h1>
          <p class="cr-hero__sous">Créez ou mettez à jour de nombreux agents d'un coup. Le fichier est d'abord analysé sans rien écrire ; s'il contient une seule erreur, rien n'est importé.</p>
        </div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      @if ($resultat)
        <div class="rh-secret">
          <i class="fa-solid fa-circle-check"></i>
          <div style="flex:1;"><strong>Import terminé.</strong>
            <div class="cs-aide">{{ $resultat['crees'] }} fiche(s) créée(s), {{ $resultat['maj'] }} mise(s) à jour, {{ $resultat['comptes'] }} compte(s) créé(s).@if ($resultat['comptes']) Les mots de passe provisoires ne sont téléchargeables qu'une fois.@endif</div></div>
          @if ($avecMotDePasse)<a class="cr-btn cr-btn--primaire" href="{{ route('rh.comptes.telecharger') }}"><i class="fa-solid fa-file-excel"></i> Télécharger les mots de passe</a>@endif
        </div>
      @endif

      <div class="rh-etapes">
        <div class="rh-etape">
          <span class="rh-etape__num">1</span><h3>Récupérer le fichier</h3>
          <p>Téléchargez le <strong>modèle vide</strong>, ou <strong>exportez les agents déjà saisis</strong> au même format pour les compléter dans Excel.</p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;"><a class="cr-btn cr-btn--primaire cr-btn--petit" href="{{ route('rh.import.modele') }}"><i class="fa-solid fa-file-arrow-down"></i> Modèle vide</a><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('rh.import.export') }}"><i class="fa-solid fa-file-excel"></i> Exporter les agents</a></div>
        </div>
        <div class="rh-etape">
          <span class="rh-etape__num">2</span><h3>Remplir dans Excel</h3>
          <p>Matricule, nom et prénom sont obligatoires. Un matricule déjà connu met la fiche à jour ; une cellule vide n'efface jamais une valeur existante.</p>
          <span class="cs-aide"><i class="fa-solid fa-circle-info"></i> Jusqu'à 500 agents par fichier.</span>
        </div>
        <div class="rh-etape">
          <span class="rh-etape__num">3</span><h3>Envoyer et vérifier</h3>
          @if ($peutImporter)
            <form method="post" action="{{ route('rh.import.apercu') }}" enctype="multipart/form-data">
              @csrf
              <input class="form-control form-control-sm" type="file" name="fichier" accept=".xlsx" required style="margin-bottom:10px;">
              <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Analyser le fichier</button>
            </form>
          @else
            <p>Seul l'administrateur peut importer. Vous pouvez exporter et consulter.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
