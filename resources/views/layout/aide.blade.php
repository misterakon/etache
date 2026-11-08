{{-- Panneau d'aide « ? » (tiroir à droite), disponible sur toutes les pages : F1 l'ouvre, Échap le ferme.
     Contient aussi le point d'entrée de la visite guidée (lancée seule à la première connexion). Script : public/assets/js/aide.js --}}
<div class="aide-fond" id="aide-fond"></div>
<aside class="aide-tiroir aide" id="aide-tiroir" aria-hidden="true" aria-label="Aide"
  data-page="{{ request()->route()?->getName() }}" data-contexte="{{ route('aide.contexte') }}" data-recherche="{{ route('aide.recherche') }}"
  data-suggestions="{{ route('recherche.suggestions') }}" data-page-recherche="{{ route('recherche') }}"
  data-visite="{{ auth()->user()?->visite_guidee_le ? 0 : 1 }}" data-visite-fin="{{ route('aide.visite.terminee') }}" data-jeton="{{ csrf_token() }}"
  data-profil="{{ auth()->user()?->profilSlug() }}" data-nom="{{ \Illuminate\Support\Str::before((string) auth()->user()?->name, ' ') }}">
  <div class="aide-tiroir__tete"><i class="fa-solid fa-life-ring"></i>
    <h3 id="aide-tiroir-titre">Aide<small>Aide de la page en cours</small></h3>
    <button type="button" class="aide-tiroir__x" id="aide-fermer" aria-label="Fermer l'aide"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="aide-tiroir__recherche"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="aide-tiroir-q" placeholder="Chercher dans l'aide…" autocomplete="off" aria-label="Chercher dans l'aide"></div>
  <div class="aide-tiroir__corps" id="aide-tiroir-corps"><div class="aide-vide"><i class="fa-solid fa-spinner fa-spin"></i></div></div>
  <div class="aide-tiroir__pied">
    <a class="aide-btn aide-btn--plein" id="aide-tiroir-lien" href="{{ route('aide.index') }}"><i class="fa-solid fa-book-open"></i> Ouvrir le chapitre</a>
    <a class="aide-btn" href="{{ route('aide.index') }}"><i class="fa-solid fa-list"></i> Sommaire</a>
    <button type="button" class="aide-btn" data-aide-visite><i class="fa-solid fa-route"></i> Visite guidée</button>
  </div>
</aside>
