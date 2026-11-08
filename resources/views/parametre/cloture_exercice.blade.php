@extends('layout.master')
@section('title', "Clôture d'exercice")

@section('main-content')
  <div class="page-body">
    <div class="page-title">
      <div class="grid grid-cols-12 mx-2 items-center">
        <div class="col-span-6 sm:col-span-12"><h3>Clôture de l'exercice {{ $exercice->annee }}</h3></div>
        <div class="col-span-6 sm:col-span-12">
          <ol class="breadcrumb flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
            <li class="breadcrumb-item">Paramètres</li>
            <li class="breadcrumb-item"><a href="{{ route('parametre.exercice') }}">Exercice</a></li>
            <li class="breadcrumb-item active">Clôture</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="grid grid-cols-12 card-gap">

        @if ($exercice->estClos())
          {{-- ===== Exercice clos ===== --}}
          <div class="col-span-12">
            <div class="card">
              <div class="card-body">
                <h5><i class="fa-solid fa-lock"></i> Exercice clos</h5>
                <p>Clôturé le <strong>{{ $exercice->cloture_le->format('d/m/Y à H:i') }}</strong>@if ($exercice->cloturePar) par <strong>{{ $exercice->cloturePar->name }}</strong>@endif.</p>
                <p class="c-o-light">Plus aucune création, modification ou suppression n'est possible (axes, actions, tâches, sous-tâches, fichiers, justifications, import). La lecture, les exports et les analyses restent disponibles, et les alertes sont arrêtées.</p>
                <div class="flex gap-2" style="margin-top:12px;">
                  @if ($archiveDisponible)
                    <a class="btn btn-primary text-white" href="{{ route('parametre.exercice.archive', $exercice->id) }}"><i class="fa-solid fa-file-excel"></i> Télécharger l'archive Excel</a>
                  @else
                    <span class="c-o-light">Archive indisponible.</span>
                  @endif
                  @if ($peutRouvrir)
                    <button class="btn btn-danger text-white" type="button" onclick="reouverture_form({{ $exercice->id }})"><i class="fa-solid fa-lock-open"></i> Rouvrir l'exercice</button>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @else
          {{-- ===== Contrôles préalables ===== --}}
          <div class="col-span-12">
            <div class="card">
              <div class="card-header card-no-border"><h5>Contrôles préalables</h5></div>
              <div class="card-body">
                <div class="overflow-x-auto">
                  <table class="table table-striped" style="width:100%">
                    <thead><tr><th>Contrôle</th><th>Nombre</th><th>Niveau</th><th>Exemples</th></tr></thead>
                    <tbody>
                      @foreach ($controles as $c)
                        <tr>
                          <td>{{ $c['libelle'] }}@if ($c['precision']) <br><small class="c-o-light">{{ $c['precision'] }}</small>@endif</td>
                          <td><strong>{{ $c['nb'] }}</strong></td>
                          <td>
                            @if ($c['nb'] === 0) <span class="badge text-white rounded-full badge-success">OK</span>
                            @elseif ($c['bloquant']) <span class="badge text-white rounded-full badge-danger">Bloquant</span>
                            @else <span class="badge text-white rounded-full badge-warning">Avertissement</span>
                            @endif
                          </td>
                          <td>{{ implode(', ', $c['exemples']) }}@if ($c['nb'] > count($c['exemples'])) …@endif</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          {{-- ===== Confirmation ===== --}}
          <div class="col-span-12">
            <div class="card">
              <div class="card-body">
                @if (! $peutCloturer)
                  <p class="c-o-light"><i class="fa-solid fa-lock"></i> Votre profil n'est pas autorisé à clôturer un exercice (Réglages > Exercice).</p>
                @elseif ($bloquants)
                  <p style="color:#A73B34;"><i class="fa-solid fa-circle-exclamation"></i> Clôture impossible tant que les contrôles bloquants ne sont pas résolus.</p>
                @else
                  <p>La clôture génère l'<strong>archive Excel</strong> de l'exercice puis <strong>verrouille</strong> tout ce qui le concerne. Seuls les profils autorisés (Réglages > Exercice) pourront le rouvrir, avec un motif.</p>
                  <form method="post" action="{{ route('parametre.exercice.cloturer', $exercice->id) }}">
                    @csrf
                    <label class="form-label" for="confirmation">Pour confirmer, saisissez l'année de l'exercice (<strong>{{ $exercice->annee }}</strong>)</label>
                    <div class="flex gap-2">
                      <input class="form-control form-control-sm" style="max-width:160px;" id="confirmation" name="confirmation" type="text" required autocomplete="off">
                      <button class="btn btn-danger text-white" type="submit"><i class="fa-solid fa-lock"></i> Clôturer l'exercice</button>
                    </div>
                  </form>
                @endif
              </div>
            </div>
          </div>
        @endif

      </div>
    </div>

    @include('parametre.form_reouverture')
    @include('parametre.js_reouverture')
  </div>
@endsection
