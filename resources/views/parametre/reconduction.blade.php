@extends('layout.master')
@section('title', "Reconduction d'exercice")

@section('main-content')
  <div class="page-body">
    <div class="page-title">
      <div class="grid grid-cols-12 mx-2 items-center">
        <div class="col-span-6 sm:col-span-12"><h3>Reconduction d'exercice</h3></div>
        <div class="col-span-6 sm:col-span-12">
          <ol class="breadcrumb flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
            <li class="breadcrumb-item">Paramètres</li>
            <li class="breadcrumb-item"><a href="{{ route('parametre.exercice') }}">Exercice</a></li>
            <li class="breadcrumb-item active">Reconduction</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="grid grid-cols-12 card-gap">

        {{-- 1. Choix des exercices --}}
        <div class="col-span-12">
          <div class="card">
            <div class="card-body">
              <form method="GET" action="{{ route('parametre.reconduction') }}">
                <div class="grid grid-cols-12 gap-3">
                  <div class="col-span-4 md:col-span-6 sm:col-span-12">
                    <label class="form-label" for="source_id">Exercice source</label>
                    <select class="form-select form-select-sm" id="source_id" name="source_id" required>
                      <option value="">Choisir…</option>
                      @foreach ($exercices as $e) <option value="{{ $e->id }}" @selected($source?->id === $e->id)>{{ $e->annee }}{{ $e->estClos() ? ' (clos)' : '' }}</option> @endforeach
                    </select>
                  </div>
                  <div class="col-span-4 md:col-span-6 sm:col-span-12">
                    <label class="form-label" for="destination_id">Exercice de destination</label>
                    <select class="form-select form-select-sm" id="destination_id" name="destination_id" required>
                      <option value="">Choisir…</option>
                      @foreach ($exercices as $e) <option value="{{ $e->id }}" @selected($destination?->id === $e->id) @disabled($e->estClos())>{{ $e->annee }}{{ $e->estClos() ? ' (clos)' : '' }}</option> @endforeach
                    </select>
                  </div>
                  <div class="col-span-4 sm:col-span-12" style="align-self:end;">
                    <button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-list-check"></i> Afficher</button>
                  </div>
                </div>
              </form>
              @if ($erreur) <p style="color:#A73B34; margin-top:12px;"><i class="fa-solid fa-circle-exclamation"></i> {{ $erreur }}</p> @endif
              @error('destination_id') <p style="color:#A73B34; margin-top:12px;">{{ $message }}</p> @enderror
              @error('echeances') <p style="color:#A73B34; margin-top:12px;">{{ $message }}</p> @enderror
              @error('taches') <p style="color:#A73B34; margin-top:12px;">{{ $message }}</p> @enderror
              @error('structure') <p style="color:#A73B34; margin-top:12px;">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>

        @if ($source && $destination && ! $erreur)

          {{-- 2. Tâches non terminées --}}
          <div class="col-span-12">
            <div class="card">
              <div class="card-header card-no-border">
                <h5>Tâches non terminées de {{ $source->annee }} ({{ $taches->count() }})</h5>
                <p class="m-0 c-o-light">Cochez les tâches à reprendre dans {{ $destination->annee }}, choisissez où les rattacher et indiquez une échéance pour chacune.</p>
              </div>
              <div class="card-body">
                <form method="post" action="{{ route('parametre.reconduction.executer') }}" id="form_reconduction">
                  @csrf
                  <input type="hidden" name="source_id" value="{{ $source->id }}">
                  <input type="hidden" name="destination_id" value="{{ $destination->id }}">

                  @php $groupesDestination = $destinationsAM->groupBy(fn ($am) => $am->actionCle->axe->id); @endphp

                  <div class="overflow-x-auto">
                    <table class="table table-striped" style="width:100%">
                      <thead>
                        <tr>
                          <th><input type="checkbox" class="form-check-input" id="rc-tout" title="Tout sélectionner"></th>
                          <th>Code</th><th>Tâche</th><th>Rattachement ({{ $source->annee }})</th><th>Statut</th><th>Avancement</th>
                          <th>Échéance actuelle</th><th style="min-width:260px;">Action mesurable de destination</th><th>Nouvelle échéance</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($taches as $t)
                          @php $dejaVersDestination = $t->reconduite_vers_exercice_id === $destination->id; @endphp
                          <tr>
                            <td><input type="checkbox" class="form-check-input rc-case" name="taches[]" value="{{ $t->id }}" @disabled($dejaVersDestination)></td>
                            <td style="white-space:nowrap;">{{ $t->code }}</td>
                            <td>{{ $t->libelle }}@if ($dejaVersDestination) <br><small class="c-o-light">Déjà reconduite vers {{ $destination->annee }}</small>@elseif ($t->reconduite_vers_exercice_id) <br><small class="c-o-light">Déjà reconduite vers un autre exercice</small>@endif</td>
                            <td><small>{{ $t->actionMesurable?->actionCle?->axe?->libelle }} › {{ $t->actionMesurable?->actionCle?->libelle }} › {{ $t->actionMesurable?->libelle }}</small></td>
                            <td>{{ $t->statutExecution?->libelle ?? '—' }}</td>
                            <td>{{ (int) $t->avancement_pourcentage }}%</td>
                            <td>{{ optional($t->date_fin)->format('d/m/Y') ?: '—' }}</td>
                            <td>
                              <select class="form-select form-select-sm rc-dest" name="destinations[{{ $t->id }}]" data-requis="{{ $creerStructure ? 0 : 1 }}" disabled>
                                <option value="">{{ $creerStructure ? "Même structure d'origine (créée si absente)" : 'Choisir…' }}</option>
                                @foreach ($groupesDestination as $groupe)
                                  @php $axeDest = $groupe->first()->actionCle->axe; @endphp
                                  <optgroup label="{{ $axeDest->code }} {{ $axeDest->libelle }}">
                                    @foreach ($groupe as $am)
                                      <option value="{{ $am->id }}" @selected((string) old('destinations.'.$t->id, $presel[$t->id] ?? '') === (string) $am->id)>{{ $am->actionCle->libelle }} › {{ $am->libelle }}</option>
                                    @endforeach
                                  </optgroup>
                                @endforeach
                              </select>
                            </td>
                            <td><input type="date" class="form-control form-control-sm rc-date" name="echeances[{{ $t->id }}]" value="{{ old('echeances.'.$t->id, $prefill[$t->id] ?? '') }}" disabled></td>
                          </tr>
                        @empty
                          <tr><td colspan="9" style="text-align:center;padding:24px;">Aucune tâche non terminée à reconduire dans l'exercice {{ $source->annee }}.</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>

                  @if ($taches->isNotEmpty())
                    <div style="margin-top:16px;">
                      <label class="flex items-center gap-2"><input type="checkbox" class="form-check-input" name="reprendre_affectations" value="1" @checked($optAffectations)> Reprendre les affectations (responsable, pilote, contributeurs)</label>
                      <label class="flex items-center gap-2" style="margin-top:6px;"><input type="checkbox" class="form-check-input" name="reprendre_sous_taches" value="1" @checked($optSousTaches)> Reprendre les sous-tâches non cochées</label>
                      <p class="c-o-light" style="margin-top:6px;">
                        Les fichiers, justifications et historiques ne sont jamais repris. Les tâches reconduites repartent à 0 % et « Non démarrée » ; la tâche d'origine garde son état et est marquée « reconduite vers {{ $destination->annee }} ».
                        @if ($creerStructure) Sans action mesurable choisie, la structure d'origine est créée dans {{ $destination->annee }} si elle n'y existe pas. @else Le choix d'une action mesurable de destination est obligatoire (Réglages > Exercice). @endif
                      </p>
                      <button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-rotate"></i> Reconduire les tâches sélectionnées</button>
                    </div>
                  @endif
                </form>
              </div>
            </div>
          </div>

          {{-- 3. Structure seule --}}
          <div class="col-span-12">
            <div class="card">
              <div class="card-header card-no-border">
                <h5>Structure de {{ $source->annee }} à reprendre dans {{ $destination->annee }}</h5>
                <p class="m-0 c-o-light">Pour les axes, actions clés et actions mesurables qui durent plusieurs années, avec ou sans tâches. Ceux qui existent déjà dans {{ $destination->annee }} (même libellé) ne sont pas dupliqués.</p>
              </div>
              <div class="card-body">
                <form method="post" action="{{ route('parametre.reconduction.structure') }}" id="form_structure">
                  @csrf
                  <input type="hidden" name="source_id" value="{{ $source->id }}">
                  <input type="hidden" name="destination_id" value="{{ $destination->id }}">

                  <ul class="rc-arbre">
                    @forelse ($axesSource as $axe)
                      <li>
                        <label><input type="checkbox" class="form-check-input rc-n" name="axes[]" value="{{ $axe->id }}"> <strong>{{ $axe->code }} {{ $axe->libelle }}</strong></label>
                        <ul>
                          @foreach ($axe->actionsCles as $ac)
                            <li>
                              <label><input type="checkbox" class="form-check-input rc-n" name="actions_cles[]" value="{{ $ac->id }}"> {{ $ac->code }} {{ $ac->libelle }}</label>
                              <ul>
                                @foreach ($ac->actionsMesurables as $am)
                                  <li><label><input type="checkbox" class="form-check-input rc-n" name="actions_mesurables[]" value="{{ $am->id }}"> {{ $am->code }} {{ $am->libelle }}</label></li>
                                @endforeach
                              </ul>
                            </li>
                          @endforeach
                        </ul>
                      </li>
                    @empty
                      <li class="c-o-light">Aucun axe dans l'exercice {{ $source->annee }}.</li>
                    @endforelse
                  </ul>

                  @if ($axesSource->isNotEmpty())
                    <label class="flex items-center gap-2" style="margin-top:12px;"><input type="checkbox" class="form-check-input" name="reprendre_affectations" value="1" @checked($optAffectations)> Reprendre les affectations des actions clés et mesurables créées</label>
                    <p class="c-o-light" style="margin-top:6px;">Cocher un axe reprend aussi toutes ses actions clés et actions mesurables ; cocher une action clé reprend ses actions mesurables. Vous pouvez ensuite décocher ce que vous ne voulez pas.</p>
                    <button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-sitemap"></i> Reconduire la structure sélectionnée</button>
                  @endif
                </form>
              </div>
            </div>
          </div>

        @endif
      </div>
    </div>

    @include('parametre.js_reconduction')

    <style>
      .rc-arbre, .rc-arbre ul { list-style: none; margin: 0; padding-left: 22px; }
      .rc-arbre { padding-left: 0; }
      .rc-arbre li { margin: 4px 0; }
      .rc-arbre label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    </style>
  </div>
@endsection
