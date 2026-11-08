@extends('layout.master')
@section('title', 'Alertes')

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Alertes envoyées</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Alertes</li>
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

              {{-- Filtres --}}
              <form method="get" action="{{ route('feuille_de_route.alerte') }}" class="grid grid-cols-12 gap-3 mb-4">
                <div class="col-span-3 sm:col-span-12">
                  <label class="form-label" for="tache_id">Tâche</label>
                  <select class="form-select form-select-sm" id="tache_id" name="tache_id">
                    <option value="">Toutes</option>
                    @foreach ($taches as $t)
                      <option value="{{ $t->id }}" @selected(request('tache_id') == $t->id)>{{ $t->code }} — {{ \Illuminate\Support\Str::limit($t->libelle, 40) }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-span-3 sm:col-span-12">
                  <label class="form-label" for="canal_notification_id">Canal</label>
                  <select class="form-select form-select-sm" id="canal_notification_id" name="canal_notification_id">
                    <option value="">Tous</option>
                    @foreach ($canaux as $canal)
                      <option value="{{ $canal->id }}" @selected(request('canal_notification_id') == $canal->id)>{{ $canal->libelle }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-span-2 sm:col-span-12">
                  <label class="form-label" for="date_debut">Du</label>
                  <input class="form-control" id="date_debut" name="date_debut" type="date" value="{{ request('date_debut') }}">
                </div>
                <div class="col-span-2 sm:col-span-12">
                  <label class="form-label" for="date_fin">Au</label>
                  <input class="form-control" id="date_fin" name="date_fin" type="date" value="{{ request('date_fin') }}">
                </div>
                <div class="col-span-2 sm:col-span-12 flex items-end">
                  <button class="btn btn-primary text-white w-full" type="submit">Filtrer</button>
                </div>
              </form>

              <div class="overflow-x-auto dom-source-data custom-scrollbar">
                <table class="display table-striped" style="width:100%">
                  <thead>
                    <tr>
                      <th>Date d'envoi</th>
                      <th>Tâche</th>
                      <th>Règle</th>
                      <th>Canal</th>
                      <th>Destinataire</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($alertes as $alerte)
                      <tr>
                        <td>{{ optional($alerte->envoye_le)->format('d/m/Y H:i') }}</td>
                        <td>{{ $alerte->tache?->code }} — {{ \Illuminate\Support\Str::limit($alerte->tache?->libelle, 50) }}</td>
                        <td>{{ $alerte->regleAlerte?->libelle }} <span class="text-muted">({{ $alerte->regleAlerte?->code }})</span></td>
                        <td>{{ $alerte->canalNotification?->libelle }}</td>
                        <td>{{ $alerte->personnel ? trim($alerte->personnel->prenom.' '.$alerte->personnel->nom) : '—' }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                          Aucune alerte pour l'instant — ce journal se remplira automatiquement une fois le moteur d'alertes en place.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <div class="mt-3">
                {{ $alertes->links() }}
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
