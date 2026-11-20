@extends('layout.master')
@section('title', "Journal d'activité")

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/journal-alertes.css') }}?v={{ filemtime(public_path('assets/css/journal-alertes.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container ep-page flux-page">
      <x-entete-page tag="Système" titre="Journal d'activité" sous="Chaque action sensible est enregistrée : non modifiable, non supprimable.">
        <x-slot:badges>
          <span class="ep-hero__code"><i class="fa-solid fa-list-check"></i> {{ $entrees->total() }} entrée(s)</span>
        </x-slot:badges>
        <x-slot:actions>
          <a class="flux-btn flux-btn--principal" href="{{ route('journal.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}"><i class="fa-solid fa-download"></i> Excel</a>
        </x-slot:actions>
      </x-entete-page>

      {{-- Filtres --}}
      <form method="GET" action="{{ route('journal.index') }}" class="flux-filtres">
        <div class="flux-filtres__grille">
          <div class="flux-champ">
            <label for="f_du">Du</label>
            <input id="f_du" type="date" name="du" value="{{ request('du') }}">
          </div>
          <div class="flux-champ">
            <label for="f_au">Au</label>
            <input id="f_au" type="date" name="au" value="{{ request('au') }}">
          </div>
          <div class="flux-champ">
            <label for="f_module">Module</label>
            <select id="f_module" name="module">
              <option value="">Tous</option>
              @foreach ($modules as $cle => $lib) <option value="{{ $cle }}" @selected(request('module') === $cle)>{{ $lib }}</option> @endforeach
            </select>
          </div>
          <div class="flux-champ">
            <label for="f_evenement">Événement</label>
            <select id="f_evenement" name="evenement">
              <option value="">Tous</option>
              @foreach ($evenements as $cle => $lib) <option value="{{ $cle }}" @selected(request('evenement') === $cle)>{{ $lib }}</option> @endforeach
            </select>
          </div>
          <div class="flux-champ">
            <label for="f_user">Acteur</label>
            <select id="f_user" name="user_id">
              <option value="">Tous</option>
              @foreach ($acteurs as $a) <option value="{{ $a->id }}" @selected((string) request('user_id') === (string) $a->id)>{{ $a->name }}</option> @endforeach
            </select>
          </div>
          <div class="flux-champ flux-champ--large">
            <label for="f_q">Recherche dans le résumé</label>
            <input id="f_q" type="text" name="q" value="{{ request('q') }}" placeholder="Nom de fichier, sous-tâche...">
          </div>
          <div class="flux-filtres__actions">
            <button class="flux-btn flux-btn--principal" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            <a class="flux-btn flux-btn--doux" href="{{ route('journal.index') }}">Réinitialiser</a>
          </div>
        </div>
      </form>

      <div class="flux-panneau" style="margin-top:18px;">
        <div class="overflow-x-auto custom-scrollbar">
          <table class="flux-table" style="width:100%">
            <thead>
              <tr>
                <th>Date</th>
                <th>Acteur</th>
                <th>Module</th>
                <th>Événement</th>
                <th>Résumé</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($entrees as $e)
                @php $initiales = $e->acteur_nom ? collect(explode(' ', $e->acteur_nom))->map(fn ($m) => mb_substr($m, 0, 1))->take(2)->implode('') : '?'; @endphp
                <tr>
                  <td class="flux-date"><b>{{ $e->created_at->format('d/m/Y') }}</b>{{ $e->created_at->format('H:i') }}</td>
                  <td><span class="flux-acteur"><span class="flux-avatar">{{ mb_strtoupper($initiales) }}</span>{{ $e->acteur_nom ?? '—' }}</span></td>
                  <td><span class="flux-chip flux-chip--{{ crc32($e->module) % 8 }}">{{ $e->libelleModule() }}</span></td>
                  <td><span class="flux-badge flux-ton-{{ $e->ton() }}"><i class="{{ $e->icone() }}"></i> {{ $e->libelleEvenement() }}</span></td>
                  <td class="flux-lib">{{ $e->resume }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5">
                    <div class="flux-vide"><i class="fa-regular fa-folder-open"></i>Aucune entrée pour ces critères.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="flux-pagination">
          {{ $entrees->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
