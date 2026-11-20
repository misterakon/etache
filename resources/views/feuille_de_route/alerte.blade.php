@extends('layout.master')
@section('title', 'Alertes')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
  <link rel="stylesheet" href="{{ asset('assets/css/journal-alertes.css') }}?v={{ filemtime(public_path('assets/css/journal-alertes.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container ep-page flux-page">
      <x-entete-page tag="Feuille de route" titre="Alertes envoyées" sous="Historique des notifications automatiques envoyées pour les retards et les échéances proches ou dépassées.">
        <x-slot:badges>
          <span class="ep-hero__code"><i class="fa-solid fa-bell"></i> {{ $alertes->total() }} alerte(s)</span>
        </x-slot:badges>
      </x-entete-page>

      {{-- Filtres --}}
      <form method="get" action="{{ route('feuille_de_route.alerte') }}" class="flux-filtres">
        <div class="flux-filtres__grille">
          <div class="flux-champ">
            <label for="tache_id">Tâche</label>
            <select id="tache_id" name="tache_id">
              <option value="">Toutes</option>
              @foreach ($taches as $t)
                <option value="{{ $t->id }}" @selected(request('tache_id') == $t->id)>{{ $t->code }} — {{ \Illuminate\Support\Str::limit($t->libelle, 40) }}</option>
              @endforeach
            </select>
          </div>
          <div class="flux-champ">
            <label for="canal_notification_id">Canal</label>
            <select id="canal_notification_id" name="canal_notification_id">
              <option value="">Tous</option>
              @foreach ($canaux as $canal)
                <option value="{{ $canal->id }}" @selected(request('canal_notification_id') == $canal->id)>{{ $canal->libelle }}</option>
              @endforeach
            </select>
          </div>
          <div class="flux-champ">
            <label for="date_debut">Du</label>
            <input id="date_debut" name="date_debut" type="date" value="{{ request('date_debut') }}">
          </div>
          <div class="flux-champ">
            <label for="date_fin">Au</label>
            <input id="date_fin" name="date_fin" type="date" value="{{ request('date_fin') }}">
          </div>
          <div class="flux-filtres__actions">
            <button class="flux-btn flux-btn--principal" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
            @if (request()->anyFilled(['tache_id', 'canal_notification_id', 'date_debut', 'date_fin']))
              <a class="flux-btn flux-btn--doux" href="{{ route('feuille_de_route.alerte') }}">Réinitialiser</a>
            @endif
          </div>
        </div>
      </form>

      <div class="flux-panneau" style="margin-top:18px;">
        <div class="overflow-x-auto custom-scrollbar">
          <table class="flux-table" style="width:100%">
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
                @php
                  $canalLib = $alerte->canalNotification?->libelle;
                  $canalIcone = match (true) {
                      str_contains((string) $canalLib, 'Email') => 'fa-solid fa-envelope',
                      str_contains((string) $canalLib, 'SMS') => 'fa-solid fa-comment-sms',
                      default => 'fa-solid fa-bell',
                  };
                  $destinataire = $alerte->personnel ? trim($alerte->personnel->prenom.' '.$alerte->personnel->nom) : null;
                @endphp
                <tr>
                  <td class="flux-date">
                    <b>{{ optional($alerte->envoye_le)->format('d/m/Y') }}</b>{{ optional($alerte->envoye_le)->format('H:i') }}
                  </td>
                  <td>
                    <span class="flux-code">{{ $alerte->tache?->code }}</span>
                    <span class="flux-lib">{{ \Illuminate\Support\Str::limit($alerte->tache?->libelle, 50) }}</span>
                  </td>
                  <td>
                    @if ($alerte->regleAlerte)
                      <span class="flux-badge flux-ton-{{ $alerte->regleAlerte->ton() }}"><i class="fa-solid fa-circle"></i> {{ $alerte->regleAlerte->libelle }} ({{ $alerte->regleAlerte->code }})</span>
                    @else
                      <span class="flux-lib">—</span>
                    @endif
                  </td>
                  <td><span class="flux-canal"><i class="{{ $canalIcone }}"></i> {{ $canalLib ?? '—' }}</span></td>
                  <td>
                    @if ($destinataire)
                      <span class="flux-acteur"><span class="flux-avatar">{{ mb_strtoupper(mb_substr($alerte->personnel->prenom, 0, 1).mb_substr($alerte->personnel->nom, 0, 1)) }}</span>{{ $destinataire }}</span>
                    @else
                      <span class="flux-lib">—</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5">
                    <div class="flux-vide"><i class="fa-regular fa-bell-slash"></i>Aucune alerte pour l'instant — ce journal se remplira automatiquement une fois le moteur d'alertes en place.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="flux-pagination">
          {{ $alertes->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
