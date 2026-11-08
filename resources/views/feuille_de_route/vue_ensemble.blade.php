@extends('layout.master')
@section('title', "Vue d'ensemble")

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Vue d'ensemble</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Vue d'ensemble</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">
        <div class="col-span-12">
          <div class="card">
            <div class="card-body">

              <div class="mb-3 flex gap-2">
                <button class="btn btn-light-primary btn-sm" type="button" onclick="toutDeplier()">Tout déplier</button>
                <button class="btn btn-light-secondary btn-sm" type="button" onclick="toutReplier()">Tout replier</button>
              </div>

              @forelse ($axes as $axe)
                <div class="ve-noeud ve-axe">
                  <div class="ve-entete" onclick="toggleBloc(this)">
                    <span class="ve-icone">+</span>
                    <strong><font color="#a8070f">{{ $axe->code }}</font> — {{ $axe->libelle }}</strong>
                    <span class="badge badge-light-primary ms-2">{{ $axe->actionsCles->count() }} action(s) clé(s)</span>
                  </div>
                  <div class="ve-corps hidden">
                    @if ($axe->resultat_attendu)
                      <p class="text-muted ve-resultat fw-bold" style="color: #20ac04">Résultat attendu : {{ $axe->resultat_attendu }}</p>
                    @endif

                    @forelse ($axe->actionsCles as $ac)
                      <div class="ve-noeud ve-action-cle">
                        <div class="ve-entete" onclick="toggleBloc(this)">
                          <span class="ve-icone">+</span>
                          <strong><font color="#020072">{{ $ac->code }} — {{ \Illuminate\Support\Str::limit($ac->libelle, 90) }}</font></strong>
                          <span class="badge badge-light-secondary ms-2">{{ $ac->actionsMesurables->count() }} action(s) mesurable(s)</span>
                        </div>
                        <div class="ve-corps hidden">
                          @forelse ($ac->actionsMesurables as $am)
                            <div class="ve-noeud ve-action-mesurable">
                              <div class="ve-entete" onclick="toggleBloc(this)">
                                <span class="ve-icone">+</span>
                                <strong><font color="#386a6e">{{ $am->code }} — {{ \Illuminate\Support\Str::limit($am->libelle, 90) }}</font></strong>
                                <span class="badge badge-light-warning ms-2">{{ $am->taches->count() }} tâche(s)</span>
                              </div>
                              <div class="ve-corps hidden">
                                @if ($am->taches->isNotEmpty())
                                  <div class="overflow-x-auto custom-scrollbar">
                                    <table class="table-sm w-full ve-table">
                                      <thead>
                                        <tr>
                                          <th>Code</th>
                                          <th>Tâche</th>
                                          <th>Échéance</th>
                                          <th>Avancement</th>
                                          <th>Statut</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach ($am->taches as $tache)
                                          <tr>
                                            <td>{{ $tache->code }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($tache->libelle, 70) }}</td>
                                            <td>{{ optional($tache->date_fin)->format('d/m/Y') }}</td>
                                            <td>{{ $tache->avancement_pourcentage }}%</td>
                                            <td>
                                              @if ($tache->statutExecution)
                                                <span class="badge text-white rounded-full" style="background-color: {{ $tache->statutExecution->couleur ?: '#5B6B84' }};">
                                                  {{ $tache->statutExecution->libelle }}
                                                </span>
                                              @endif
                                            </td>
                                          </tr>
                                        @endforeach
                                      </tbody>
                                    </table>
                                  </div>
                                @else
                                  <p class="text-muted">Aucune tâche pour cette action mesurable.</p>
                                @endif
                              </div>
                            </div>
                          @empty
                            <p class="text-muted">Aucune action mesurable pour cette action clé.</p>
                          @endforelse
                        </div>
                      </div>
                    @empty
                      <p class="text-muted">Aucune action clé pour cet axe.</p>
                    @endforelse
                  </div>
                </div>
              @empty
                <p class="text-muted text-center py-4">Aucun axe pour l'instant.</p>
              @endforelse

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('css')
  <style>
    .ve-noeud { border: 1px solid var(--recent-border-color, #eee); border-radius: 6px; margin-bottom: 8px; }
    .ve-entete { padding: 10px 14px; cursor: pointer; font-size: 14px; }
    .ve-entete:hover { background-color: rgba(0,0,0,0.03); }
    .ve-icone { display: inline-block; width: 16px; font-weight: bold; }
    .ve-corps { padding: 4px 14px 12px 30px; }
    .ve-action-cle { margin-top: 6px; }
    .ve-action-mesurable { margin-top: 6px; }
    .ve-resultat { font-size: 13px; margin-bottom: 10px; }
    .ve-table th, .ve-table td { padding: 6px 8px; font-size: 13px; }
  </style>
@endsection

@section('script')
  <script>
    function toggleBloc(entete) {
      const corps = entete.nextElementSibling;
      const icone = entete.querySelector('.ve-icone');
      corps.classList.toggle('hidden');
      icone.textContent = corps.classList.contains('hidden') ? '+' : '−';
    }

    function toutDeplier() {
      document.querySelectorAll('.ve-corps').forEach(el => el.classList.remove('hidden'));
      document.querySelectorAll('.ve-icone').forEach(el => el.textContent = '−');
    }

    function toutReplier() {
      document.querySelectorAll('.ve-corps').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.ve-icone').forEach(el => el.textContent = '+');
    }
  </script>
@endsection
