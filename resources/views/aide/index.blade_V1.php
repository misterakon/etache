@extends('layout.master')
@section('title', "Aide et manuel d'utilisation")

@section('main-content')
  @php
    // Parcours : [chapitre, ancre, icône, titre, sous-titre, couleur] ; une étape n'apparaît que si son chapitre est visible pour ce profil
    $parcours = [
      ['premiers-pas', 'se-connecter', 'fa-solid fa-right-to-bracket', 'Se connecter', 'Mot de passe, verrouillage', '#009E60'],
      ['mes-taches', 'mettre-a-jour-une-tache', 'fa-solid fa-pen-to-square', 'Mettre à jour', 'Avancement, sous-tâches, livrables', '#2B6CB0'],
      ['mes-taches', 'justifier-un-retard', 'fa-solid fa-hourglass-end', 'Justifier un retard', 'Demander un report', '#F26A0F'],
      ['pilotage', 'vue-densemble', 'fa-solid fa-chart-pie', 'Piloter', 'Lire la vue d’ensemble', '#6D5BD0'],
      ['pilotage', 'synthese-redigee', 'fa-solid fa-feather-pointed', 'Rédiger la synthèse', 'Générer et corriger', '#033074'],
    ];
    $visibles = collect($chapitres)->keyBy('slug');
  @endphp
  <div class="page-body">
    <div class="container"><x-titre-page titre="Aide" :fil="['Manuel d’utilisation']" /></div>
    <div class="container aide-page aide">

      <section class="aide-hero">
        <h1>Comment pouvons-nous vous aider ?</h1>
        <p>Pas à pas, règles à connaître et réponses aux blocages. Cherchez un mot ou parcourez les chapitres de votre profil.</p>
        <div class="aide-recherche" id="aide-recherche">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" id="aide-q" placeholder="Que cherchez-vous ? (ex. retard, justification, export, mot de passe…)" autocomplete="off" aria-label="Rechercher dans l'aide">
          <kbd>/</kbd>
          <div class="aide-resultats" id="aide-resultats" hidden></div>
        </div>
        <div class="aide-hero__actions">
          <button type="button" class="aide-btn aide-btn--orange" data-aide-visite><i class="fa-solid fa-route"></i> Faire la visite guidée</button>
          <a class="aide-btn" href="{{ route('aide.manuel') }}" target="_blank" rel="noopener"><i class="fa-solid fa-print"></i> Manuel complet (imprimer / PDF)</a>
        </div>
      </section>

      <section class="aide-sec">
        <h2><i class="fa-solid fa-route" style="color:#F26A0F"></i> Par où commencer ?</h2>
        <p>Cliquez sur une étape pour ouvrir l’explication.</p>
        <div class="aide-etapes">
          @foreach ($parcours as [$slug, $ancre, $icone, $titre, $sous, $couleur])
            @if ($visibles->has($slug))
              <a class="aide-etape" style="--ac: {{ $couleur }}" href="{{ route('aide.chapitre', $slug) }}#{{ $ancre }}"><i class="{{ $icone }}"></i><b>{{ $titre }}</b><small>{{ $sous }}</small></a>
            @endif
          @endforeach
        </div>
      </section>

      <section class="aide-sec">
        <h2><i class="fa-solid fa-book-open" style="color:#2B6CB0"></i> Les chapitres</h2>
        <p>{{ count($chapitres) }} chapitre{{ count($chapitres) > 1 ? 's' : '' }} pour votre profil.</p>
        <div class="aide-grille">
          @foreach ($chapitres as $c)
            <a class="aide-carte aide-c-{{ $c['couleur'] }}" href="{{ route('aide.chapitre', $c['slug']) }}">
              <span class="aide-resultat__ico"><i class="{{ $c['icone'] }}"></i></span>
              <h3>{{ $c['titre'] }}</h3>
              <p>{{ $c['resume'] }}</p>
              <footer><span>{{ count($c['sections']) }} rubrique{{ count($c['sections']) > 1 ? 's' : '' }}</span><span><i class="fa-regular fa-clock"></i> {{ $c['minutes'] }} min</span></footer>
            </a>
          @endforeach
        </div>
      </section>

      <div class="aide-bas"><i class="fa-solid fa-circle-info" style="color:#2B6CB0"></i> Raccourcis : <kbd class="aide-kbd">F1</kbd> ouvre l’aide de la page en cours, <kbd class="aide-kbd">/</kbd> place le curseur dans la recherche.</div>
    </div>
  </div>
@endsection
