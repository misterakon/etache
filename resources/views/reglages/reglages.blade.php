@extends('layout.master')
@section('title', 'Réglages')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/reglages.css') }}?v={{ filemtime(public_path('assets/css/reglages.css')) }}">
@endsection

@section('main-content')
  @php
    // Une icône par rubrique ; une rubrique du catalogue sans icône ici reçoit l'engrenage
    $icones = [
        'exercice' => 'fa-calendar-check', 'sous_taches' => 'fa-list-check', 'cloture_taches' => 'fa-circle-check', 'taches_ponctuelles' => 'fa-bolt',
        'fichiers' => 'fa-paperclip', 'courrier' => 'fa-envelope-open-text', 'personnel' => 'fa-users',
        'exports' => 'fa-file-export', 'synthese' => 'fa-wand-magic-sparkles', 'journal' => 'fa-clock-rotate-left',
    ];

    $nbModifies = [];
    foreach ($catalogue as $module => $m) {
        $nbModifies[$module] = collect($m['reglages'])->keys()->filter(fn ($k) => $modifies["{$module}.{$k}"])->count();
    }
    $totalReglages = collect($catalogue)->sum(fn ($m) => count($m['reglages']));
    $totalModifies = array_sum($nbModifies);

    // Rubrique affichée : celle de l'adresse (?module=), de la saisie à corriger, sinon la première
    $actif = request('module', old('module'));
    if ($actif !== 'historique' && ! isset($catalogue[$actif])) {
        $actif = array_key_first($catalogue);
    }
  @endphp

  <div class="page-body">
    <div class="container rg-page">

      {{-- Bannière : titre, résumé, recherche --}}
      <section class="rg-hero">
        <span class="rg-hero__dots" aria-hidden="true"></span>
        <i class="rg-hero__mark fa-solid fa-sliders" aria-hidden="true"></i>

        <div class="rg-hero__text">
          <span class="rg-tag"><span class="rg-tag__dot"></span>Paramètres</span>
          <h1 class="rg-hero__title">Réglages</h1>
          <p class="rg-hero__sub">Tout ce qui était fixé dans l'application se règle ici. Chaque réglage affiche sa valeur par défaut, qui reproduit le comportement d'origine ; toute modification est tracée dans le journal d'activité.</p>
          <div class="rg-hero__extra">
            <span><i class="fa-solid fa-layer-group"></i> {{ count($catalogue) }} rubriques</span>
            <span><i class="fa-solid fa-toggle-on"></i> {{ $totalReglages }} réglages</span>
            <span><i class="fa-solid fa-pen-ruler"></i> {{ $totalModifies ? $totalModifies.' personnalisé(s)' : 'Valeurs par défaut' }}</span>
          </div>
        </div>

        <div class="rg-hero__outils">
          <label class="rg-recherche">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" id="rg-recherche" placeholder="Rechercher un réglage…" autocomplete="off" aria-label="Rechercher un réglage">
          </label>
        </div>
      </section>

      <div class="rg-grille">

        {{-- Colonne de gauche : rubriques --}}
        <div class="rg-colonne">
          <div class="rg-carte">
            <div class="rg-carte__tete">
              <div>
                <h2 class="rg-carte__titre">Rubriques</h2>
                <div class="rg-carte__sous">Choisissez ce que vous voulez régler.</div>
              </div>
            </div>
            <div class="rg-carte__corps">
              <nav class="rg-rubs" aria-label="Rubriques de réglages">
                @foreach ($catalogue as $module => $m)
                  <button type="button" class="rg-rub {{ $module === $actif ? 'is-actif' : '' }}" data-rub="{{ $module }}" aria-controls="rub-{{ $module }}" aria-selected="{{ $module === $actif ? 'true' : 'false' }}">
                    <span class="rg-rub__icone"><i class="fa-solid {{ $icones[$module] ?? 'fa-gear' }}"></i></span>
                    <span class="rg-rub__texte">{{ $m['libelle'] }}</span>
                    @if ($nbModifies[$module])
                      <span class="rg-rub__nb" title="{{ $nbModifies[$module] }} réglage(s) personnalisé(s)">{{ $nbModifies[$module] }}</span>
                    @endif
                  </button>
                @endforeach
                <button type="button" class="rg-rub {{ $actif === 'historique' ? 'is-actif' : '' }}" data-rub="historique" aria-controls="rub-historique" aria-selected="{{ $actif === 'historique' ? 'true' : 'false' }}">
                  <span class="rg-rub__icone"><i class="fa-solid fa-clock-rotate-left"></i></span>
                  <span class="rg-rub__texte">Historique des modifications</span>
                </button>
              </nav>
            </div>
          </div>

          <div class="rg-note">
            <span class="rg-note__icone"><i class="fa-solid fa-lock"></i></span>
            <div>
              <div class="rg-note__titre">Réglages protégés</div>
              <div class="rg-note__texte">La séparation des rôles et le journal d'audit ne se paramètrent jamais. L'accès à la base de données reste dans le fichier de configuration du serveur.</div>
            </div>
          </div>
        </div>

        {{-- Colonne de droite : réglages de la rubrique choisie --}}
        <div class="rg-colonne">
          <p id="rg-aucun" class="rg-aucun rg-carte" hidden>Aucun réglage ne correspond à votre recherche.</p>

          @foreach ($catalogue as $module => $m)
            <section class="rg-carte rg-panneau" id="rub-{{ $module }}" data-rub="{{ $module }}" @if ($module !== $actif) hidden @endif>
              <div class="rg-panneau__tete">
                <span class="rg-panneau__icone"><i class="fa-solid {{ $icones[$module] ?? 'fa-gear' }}"></i></span>
                <div class="rg-panneau__texte">
                  <h2 class="rg-panneau__titre">{{ $m['libelle'] }}</h2>
                  <div class="rg-panneau__sous">{{ $m['description'] }}</div>
                </div>
                <span class="rg-panneau__nb {{ $nbModifies[$module] ? 'rg-panneau__nb--modif' : '' }}">
                  {{ count($m['reglages']) }} réglage(s)@if ($nbModifies[$module]) · {{ $nbModifies[$module] }} modifié(s)@endif
                </span>
              </div>
              @include('reglages.form_reglages', ['module' => $module, 'm' => $m])
            </section>
          @endforeach

          {{-- Historique des modifications --}}
          <section class="rg-carte rg-panneau" id="rub-historique" data-rub="historique" @if ($actif !== 'historique') hidden @endif>
            <div class="rg-panneau__tete">
              <span class="rg-panneau__icone"><i class="fa-solid fa-clock-rotate-left"></i></span>
              <div class="rg-panneau__texte">
                <h2 class="rg-panneau__titre">Historique des modifications</h2>
                <div class="rg-panneau__sous">Les 30 dernières modifications de réglages : qui, quand, et la valeur avant et après.</div>
              </div>
            </div>
            <div class="rg-table-zone">
              <table class="rg-table">
                <thead><tr><th>Date</th><th>Acteur</th><th>Modification</th></tr></thead>
                <tbody>
                  @forelse ($historique as $e)
                    <tr>
                      <td class="rg-date">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                      <td>{{ $e->acteur_nom ?? '—' }}</td>
                      <td>{{ $e->resume }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="rg-vide">Aucune modification : tous les réglages ont leur valeur par défaut.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </div>

    {{-- Formulaire caché utilisé par les boutons « Rétablir » (un réglage ou toute une rubrique) --}}
    <form id="form_retablir" method="post" action="{{ route('reglages.retablir') }}" hidden>
      @csrf
      <input type="hidden" name="cle" value="">
      <input type="hidden" name="module" value="">
    </form>

    @include('reglages.js_reglages')

  </div>
@endsection
