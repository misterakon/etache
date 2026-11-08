@extends('layout.master')
@section('title', 'Aperçu avant import')

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
          <p class="cr-hero__sur"><a href="{{ route('rh.import') }}" style="color:#fff;opacity:.85;text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Import et export</a></p>
          <h1 class="cr-hero__titre">Prévisualisation de l'import</h1>
          <p class="cr-hero__sous">Fichier : {{ $nomFichier }}. Rien n'a encore été écrit : vérifiez le résultat, puis confirmez.</p>
        </div>
      </div>
      @include('rh._nav')

      @if ($analyse['erreurs_fichier'])
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($analyse['erreurs_fichier'] as $e) {{ $e }}<br> @endforeach</div></div>
        <a class="cr-btn cr-btn--clair" href="{{ route('rh.import') }}"><i class="fa-solid fa-arrow-left"></i> Renvoyer un fichier</a>
      @else
        @php $r = $analyse['resume']; $bloque = $r['erreurs'] > 0; @endphp
        <div class="cr-kpis">
          <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-table-list"></i></span><div><span class="cr-kpi__valeur">{{ $r['lignes'] }}</span><span class="cr-kpi__libelle">Lignes lues</span></div></div>
          <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-user-plus"></i></span><div><span class="cr-kpi__valeur">{{ $r['creations'] }}</span><span class="cr-kpi__libelle">Fiches à créer</span></div></div>
          <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-user-pen"></i></span><div><span class="cr-kpi__valeur">{{ $r['mises_a_jour'] }}</span><span class="cr-kpi__libelle">Fiches à mettre à jour</span></div></div>
          <div class="cr-kpi" style="--c:{{ $bloque ? '#A73B34' : '#8A94A6' }}"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $r['erreurs'] }}</span><span class="cr-kpi__libelle">Lignes en erreur</span></div></div>
        </div>

        @if ($bloque)
          <div class="cr-alerte-conf"><i class="fa-solid fa-ban"></i><div><strong>Import impossible.</strong> {{ $r['erreurs'] }} ligne(s) contiennent des erreurs : corrigez le fichier et renvoyez-le. « Tout ou rien » : rien n'est importé tant qu'une erreur subsiste.</div></div>
        @else
          <form method="post" action="{{ route('rh.import.appliquer') }}" class="cr-carte" style="padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            @csrf <input type="hidden" name="token" value="{{ $token }}">
            <div><strong>Prêt à importer.</strong> <span class="cs-aide">{{ $r['creations'] }} création(s), {{ $r['mises_a_jour'] }} mise(s) à jour{{ $r['comptes'] ? ', '.$r['comptes'].' compte(s) à créer' : '' }}{{ $r['avertissements'] ? ' · '.$r['avertissements'].' avertissement(s)' : '' }}</span></div>
            <button class="cr-btn cr-btn--primaire" type="submit"><i class="fa-solid fa-check"></i> Confirmer l'import</button>
          </form>
        @endif

        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-table-list"></i> Détail ligne par ligne</h2></div>
          <div class="overflow-x-auto" style="max-height:620px;overflow-y:auto;">
            <table class="cr-table">
              <thead><tr><th>Ligne</th><th>Matricule</th><th>Agent</th><th>Action</th><th>Détail</th></tr></thead>
              <tbody>
                @foreach ($analyse['lignes'] as $l)
                  <tr style="cursor:default;" class="{{ $l['erreurs'] ? 'rh-ligne-erreur' : '' }}">
                    <td>{{ $l['num'] }}</td><td><span class="cr-num">{{ $l['matricule'] }}</span></td><td>{{ $l['prenom'] }} {{ $l['nom'] }}</td>
                    <td>@if ($l['erreurs'])<span class="cr-chip" style="--c:#A73B34">Erreur</span>@elseif ($l['action'] === 'creer')<span class="cr-chip" style="--c:#2E7D5B">Création</span>@else<span class="cr-chip" style="--c:#0F8B8D">Mise à jour</span>@endif @if ($l['creer_compte'])<span class="cr-chip" style="--c:#7B6BE0">+ compte</span>@endif</td>
                    <td>
                      @if ($l['erreurs'])<ul class="rh-liste-msg rh-liste-msg--erreur">@foreach ($l['erreurs'] as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
                      @if ($l['avertissements'])<ul class="rh-liste-msg rh-liste-msg--avert">@foreach ($l['avertissements'] as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
                      @if (! $l['erreurs'] && $l['action'] === 'maj' && $l['changements'])<span class="cs-aide">Modifie : {{ implode(', ', $l['changements']) }}</span>@endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
