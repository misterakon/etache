{{--
  Tableau de taches de la page Charge de travail.
  Variables : $idTable, $lignes (chaque ligne : ['tache' => ..., 'jours' => ...]),
              $colonne ('retard' | 'reste' | 'avancement'), $vide (message), $roleResponsableId
--}}
@if ($lignes->isEmpty())
  <p class="text-muted text-center" style="padding:24px 0;">{{ $vide }}</p>
@else
  <div class="flex items-center justify-between mb-3">
    <span class="c-o-light">{{ $lignes->count() }} tâche(s)</span>
    <input class="form-control form-control-sm" style="max-width:240px;" type="search" placeholder="Rechercher…" data-recherche="[data-nom='{{ $idTable }}']">
  </div>

  <div class="recent-table overflow-x-auto custom-scrollbar">
    <table class="table table-charge" id="basic-6" data-nom="{{ $idTable }}" style="width:100%; table-layout:fixed;">
      <thead>
        <tr>
          <th style="width:9%">Code</th>
          <th style="width:29%">Tâche</th>
          <th style="width:14%">Responsable(s)</th>
          <th style="width:11%">Échéance</th>
          <th style="width:14%">{{ ['retard' => 'Retard', 'reste' => 'Délai', 'avancement' => 'Avancement'][$colonne] }}</th>
          <th style="width:16%">Statut</th>
          <th style="width:7%">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($lignes as $ligne)
          @php $t = $ligne['tache']; @endphp
          <tr>
            <td>{{ $t->code }}</td>
            <td title="{{ $t->libelle }}" style="max-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><a href="{{ route('feuille_de_route.tache.details', $t->id) }}">{{ $t->libelle }}</a></td>
            <td>
              @include('feuille_de_route._avatars', [
                'affectations' => $t->affectations->where('role_affectation_id', $roleResponsableId),
                'couleur' => 'primary',
              ])
            </td>
            <td data-order="{{ optional($t->date_fin)->format('Y-m-d') }}">{{ optional($t->date_fin)->format('d/m/Y') ?: '—' }}</td>

            @if ($colonne === 'retard')
              <td data-order="{{ $ligne['jours'] }}"><span class="tl-badge tl-retard tl-solid">{{ $ligne['jours'] }} j</span></td>
            @elseif ($colonne === 'reste')
              @php $tonDelai = $ligne['jours'] <= 7 ? 'retard' : ($ligne['jours'] <= 15 ? 'attente' : 'encours'); @endphp
              <td data-order="{{ $ligne['jours'] }}"><span class="tl-badge tl-{{ $tonDelai }}">J-{{ $ligne['jours'] }}</span></td>
            @else
              <td data-order="{{ $t->avancement_pourcentage }}">
                <div class="ct-cellule-prog">
                  <span>{{ $t->avancement_pourcentage }}%</span>
                  <div class="ct-meter {{ $t->avancement_pourcentage > 0 ? '' : 'ct-meter--vide' }}" style="--p: {{ $t->avancement_pourcentage }}%"></div>
                </div>
              </td>
            @endif

            <td>
              @if ($t->statutExecution)
                <span class="badge text-white rounded-full" style="background-color: {{ $t->statutExecution->couleur ?: '#5B6B84' }}; white-space:nowrap;">{{ $t->statutExecution->libelle }}</span>
              @endif
            </td>
            <td>
              <div class="flex gap-2">
                <a class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Voir la tâche" style="width:30px; padding-left:0; padding-right:0;" href="{{ route('feuille_de_route.tache.details', $t->id) }}"><i class="text-white fa-regular fa-eye"></i></a>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
