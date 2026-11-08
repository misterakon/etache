@extends('layout.master')
@section('title', "Journal d'activité")

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Journal d'activité</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item active">Journal d'activité</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      {{-- Filtres --}}
      <div class="card p-3" style="margin-bottom:16px;">
        <form method="GET" action="{{ route('journal.index') }}">
          <div class="grid grid-cols-12 gap-3">
            <div class="col-span-2 md:col-span-6 sm:col-span-12">
              <label class="form-label" for="f_du">Du</label>
              <input class="form-control form-control-sm" id="f_du" type="date" name="du" value="{{ request('du') }}">
            </div>
            <div class="col-span-2 md:col-span-6 sm:col-span-12">
              <label class="form-label" for="f_au">Au</label>
              <input class="form-control form-control-sm" id="f_au" type="date" name="au" value="{{ request('au') }}">
            </div>
            <div class="col-span-2 md:col-span-6 sm:col-span-12">
              <label class="form-label" for="f_module">Module</label>
              <select class="form-select form-select-sm" id="f_module" name="module">
                <option value="">Tous</option>
                @foreach ($modules as $cle => $lib) <option value="{{ $cle }}" @selected(request('module') === $cle)>{{ $lib }}</option> @endforeach
              </select>
            </div>
            <div class="col-span-3 md:col-span-6 sm:col-span-12">
              <label class="form-label" for="f_evenement">Événement</label>
              <select class="form-select form-select-sm" id="f_evenement" name="evenement">
                <option value="">Tous</option>
                @foreach ($evenements as $cle => $lib) <option value="{{ $cle }}" @selected(request('evenement') === $cle)>{{ $lib }}</option> @endforeach
              </select>
            </div>
            <div class="col-span-3 md:col-span-6 sm:col-span-12">
              <label class="form-label" for="f_user">Acteur</label>
              <select class="form-select form-select-sm" id="f_user" name="user_id">
                <option value="">Tous</option>
                @foreach ($acteurs as $a) <option value="{{ $a->id }}" @selected((string) request('user_id') === (string) $a->id)>{{ $a->name }}</option> @endforeach
              </select>
            </div>
            <div class="col-span-6 sm:col-span-12">
              <label class="form-label" for="f_q">Recherche dans le résumé</label>
              <input class="form-control form-control-sm" id="f_q" type="text" name="q" value="{{ request('q') }}" placeholder="Nom de fichier, sous-tâche...">
            </div>
            <div class="col-span-6 sm:col-span-12 flex gap-2" style="align-self:end;">
              <button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Appliquer</button>
              <a class="btn btn-secondary text-white" href="{{ route('journal.index') }}">Réinitialiser</a>
              <a class="btn btn-primary text-white" href="{{ route('journal.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}"><i class="fa-solid fa-download"></i> Excel</a>
            </div>
          </div>
        </form>
      </div>

      <div class="card">
        <div class="card-body">
          <p class="c-o-light">{{ $entrees->total() }} entrée(s)</p>
          <div class="overflow-x-auto">
            <table class="table table-striped" style="width:100%">
              <thead>
                <tr><th>Date</th><th>Acteur</th><th>Module</th><th>Événement</th><th>Résumé</th></tr>
              </thead>
              <tbody>
                @forelse ($entrees as $e)
                  <tr>
                    <td style="white-space:nowrap;">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $e->acteur_nom ?? '—' }}</td>
                    <td>{{ $e->libelleModule() }}</td>
                    <td>{{ $e->libelleEvenement() }}</td>
                    <td>{{ $e->resume }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" style="text-align:center;padding:24px;">Aucune entrée pour ces critères.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div style="margin-top:12px;">{{ $entrees->links() }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection
