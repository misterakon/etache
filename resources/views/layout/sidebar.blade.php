@php
    $moi = auth()->user();

    /*
     | Menu du même principe que celui du projet TPI : une entrée n'apparaît que si le profil a le droit
     | (clé « droit », absente = ouvert à tout utilisateur connecté) et si sa route existe. « role » est
     | l'ancien mécanisme (hasRole), conservé uniquement pour les écrans du Courrier qui gèrent encore leurs
     | propres accès par hiérarchie (voir App\Support\CourrierDroits) et n'ont pas rejoint le catalogue.
     | Un droit à « Aucun accès » retire donc l'entrée du menu ET l'accès à la page elle-même : les deux
     | sont vérifiés avec le même droit, côté contrôleur (Gate::authorize) et ici (can()).
     */
    $nbPonct = $moi->personnel_id
        ? \App\Models\TachePonctuelle::enRetard()->where('responsable_personnel_id', $moi->personnel_id)->count()
            + \App\Models\TachePonctuelle::query()->where('validation', 'en_attente')->when(! $moi->hasRole('ADMIN|DG'), fn ($q) => $q->whereJsonContains('superieurs', (int) $moi->personnel_id))->count()
        : 0;
    $nbAValider = \App\Support\JustificationRetard::nbAValider($moi);

    $menu = [
        ['titre' => 'Tableau de bord', 'icone' => 'home', 'route' => 'dashboard'],

        ['titre' => 'DGEPN', 'icone' => 'administration', 'enfants' => [
            ['titre' => 'Directions centrales', 'route' => 'dgepn.direction'],
            ['titre' => 'Sous-Directions', 'route' => 'dgepn.sous_direction'],
            ['titre' => 'Services', 'route' => 'dgepn.service'],
            ['titre' => 'Gestion du personnel', 'enfants' => [
                ['titre' => 'Personnels', 'route' => 'dgepn.personnel', 'droit' => 'personnel.voir'],
                ['titre' => 'Organigramme interactif', 'route' => 'rh.organigramme', 'droit' => 'rh_organigramme.voir'],
                ['titre' => 'Tableau de bord RH', 'route' => 'rh.tableau_de_bord', 'droit' => 'rh_tableau_de_bord.voir'],
            ]],
        ]],

        ['titre' => 'Courrier', 'icone' => 'email', 'enfants' => [
            ['titre' => 'Mes courriers à traiter', 'route' => 'courrier.mes'],
            ['titre' => 'Registre - Arrivée', 'route' => 'courrier.arrivee', 'role' => 'ADMIN|DG|COURRIER'],
            ['titre' => 'Tableau des diligences', 'route' => 'courrier.diligences', 'role' => 'ADMIN|DG|COURRIER'],
            ['titre' => 'Registre - Départ', 'route' => 'courrier.depart', 'role' => 'ADMIN|DG|COURRIER'],
            ['titre' => 'Archives', 'route' => 'courrier.archives', 'role' => 'ADMIN|DG|COURRIER'],
            ['titre' => 'Paramétrage', 'route' => 'courrier.parametrage', 'droit' => 'parametrage_courrier.voir'],
        ]],

        ['titre' => 'Feuille de route', 'icone' => 'file', 'enfants' => [
            ['titre' => 'Importation matrice', 'route' => 'feuille_de_route.import_matrice.index', 'droit' => 'import_matrice.importer', 'icnTexte' => 'fa-solid fa-warning'],
            ['titre' => 'Axes', 'route' => 'feuille_de_route.axe', 'droit' => 'axe.voir'],
            ['titre' => 'Actions clés', 'route' => 'feuille_de_route.action_cle', 'droit' => 'action_cle.voir'],
            ['titre' => 'Actions mésurables', 'route' => 'feuille_de_route.action_mesurable', 'droit' => 'action_mesurable.voir'],
            ['titre' => "Vue d'ensemble", 'route' => 'feuille_de_route.vue_ensemble', 'droit' => 'vue_ensemble.voir'],
            ['titre' => 'Tâches', 'enfants' => [
                ['titre' => 'Liste', 'route' => 'feuille_de_route.tache', 'droit' => 'tache.voir'],
                ['titre' => 'Tableau matrice', 'route' => 'feuille_de_route.tache', 'query' => ['vue' => 'tableau'], 'droit' => 'vues_taches.voir'],
                ['titre' => 'Calendrier', 'route' => 'feuille_de_route.tache', 'query' => ['vue' => 'calendrier'], 'droit' => 'vues_taches.voir'],
                ['titre' => 'Diagramme de Gantt', 'route' => 'feuille_de_route.tache', 'query' => ['vue' => 'gantt'], 'droit' => 'vues_taches.voir'],
                ['titre' => 'Mes tâches', 'route' => 'feuille_de_route.tache_mes_taches'],
                ['titre' => 'Tâches ponctuelles', 'route' => 'ponctuelle.index', 'badge' => $nbPonct, 'badgeTitre' => 'Tâches en retard ou à valider'],
            ]],
            ['titre' => 'Alertes', 'route' => 'feuille_de_route.alerte', 'droit' => 'alerte.voir'],
            ["titre" => "Journal d'activité", 'route' => 'journal.index', 'droit' => 'journal.voir'],
            ['titre' => 'Demandes', 'route' => 'feuille_de_route.justification.index', 'droit' => 'justification.voir', 'badge' => $nbAValider],
        ]],

        ['titre' => 'Pilotage', 'icone' => 'statistique', 'route' => 'cockpit.index', 'droit' => 'cockpit.voir'],

        ['titre' => 'Utilisateurs', 'icone' => 'user', 'enfants' => [
            ["titre" => "Profil d'accès", 'route' => 'parametre.profil_acces', 'droit' => 'profils_acces.voir'],
            ['titre' => 'Comptes utilisateurs', 'route' => 'utilisateur.compte', 'droit' => 'comptes_utilisateurs.voir'],
        ]],

        ['titre' => 'Paramètres', 'icone' => 'icons', 'enfants' => [
            ['titre' => 'Réglages', 'route' => 'reglages.index', 'droit' => 'reglages.voir'],
            ['titre' => 'Exercice', 'route' => 'parametre.exercice', 'droit' => 'exercice.voir'],
            ['titre' => 'Type agent', 'route' => 'parametre.type_agent', 'droit' => 'type_agent.voir'],
            ['titre' => 'Fonctions', 'route' => 'dgepn.fonction', 'droit' => 'fonction.voir'],
            ["titre" => "Famille d'emploi", 'route' => 'parametre.famille_emploi', 'droit' => 'famille_emploi.voir'],
            ['titre' => 'Emploi', 'route' => 'parametre.emploi', 'droit' => 'emploi.voir'],
            ["titre" => "Rôle d'affectation", 'route' => 'parametre.role_affectation', 'droit' => 'role_affectation.voir'],
            ['titre' => 'Priorité', 'route' => 'parametre.priorite', 'droit' => 'priorite.voir'],
            ["titre" => "Statut d'exécution", 'route' => 'parametre.statut_execution', 'droit' => 'statut_execution.voir'],
            ['titre' => 'Situation temporelle', 'route' => 'parametre.situation_temporelle', 'droit' => 'situation_temporelle.voir'],
            ['titre' => 'Canal de notification', 'route' => 'parametre.canal_notification', 'droit' => 'canal_notification.voir'],
            ["titre" => "Règle d'alerte", 'route' => 'parametre.regle_alerte', 'droit' => 'regle_alerte.voir'],
        ]],
    ];

    // « droit » : un droit, ou une liste de droits dont un seul suffit. « role » : ancien mécanisme (hasRole).
    $permis = function (array $e) use ($moi) {
        if (! empty($e['droit']) && ! collect((array) $e['droit'])->contains(fn ($d) => $moi->can($d))) {
            return false;
        }

        return empty($e['role']) || $moi->hasRole($e['role']);
    };
    $existe = fn (array $e) => ! empty($e['href']) || (! empty($e['route']) && \Illuminate\Support\Facades\Route::has($e['route']));
    $lien = function (array $e) {
        if (! empty($e['href'])) {
            return $e['href'];
        }
        if (empty($e['route']) || ! \Illuminate\Support\Facades\Route::has($e['route'])) {
            return '#';
        }

        return route($e['route']).(isset($e['query']) ? '?'.http_build_query($e['query']) : '');
    };
    // Filtre une liste d'entrées (et ses sous-menus) selon les droits ; un groupe sans entrée visible disparaît.
    $filtrer = function (array $liste) use (&$filtrer, $permis, $existe) {
        return collect($liste)->map(function ($e) use (&$filtrer, $permis, $existe) {
            if (isset($e['enfants'])) {
                $e['enfants'] = $filtrer($e['enfants'])->all();

                return $e['enfants'] === [] ? null : $e;
            }

            return $permis($e) && $existe($e) ? $e : null;
        })->filter()->values();
    };
@endphp

<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div>
    <div class="logo-wrapper"><a href="{{ route('dashboard') }}"><img class="max-w-full h-auto for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img class="max-w-full h-auto for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt=""></a>
        <div class="back-btn hidden lg:block"><i class="fa-solid fa-angle-left"></i></div>
        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
    </div>
    <div class="logo-icon-wrapper"><a href="{{ route('dashboard') }}"><img class="max-w-full h-auto" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
    <nav class="sidebar-main">
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"> <a href="{{ route('dashboard') }}"><img class="max-w-full h-auto" src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                    <div class="mobile-back text-end"><span>Back</span><i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i></div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>

                @foreach ($menu as $entree)
                    @continue(! $permis($entree))

                    @if (isset($entree['enfants']))
                        @php $enfants = $filtrer($entree['enfants']); @endphp
                        @continue($enfants->isEmpty())

                        <li class="sidebar-list"><i class="fa-solid fa-thumbtack"></i>
                            <a class="sidebar-link sidebar-title" href="#">
                                <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-'.$entree['icone']) }}"></use></svg>
                                <svg class="fill-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#fill-'.$entree['icone']) }}"></use></svg>
                                <span>{{ $entree['titre'] }}</span>
                            </a>
                            @include('layout.sidebar_sous_menu', ['enfants' => $enfants])
                        </li>
                    @elseif ($existe($entree))
                        <li class="sidebar-list"><i class="fa-solid fa-thumbtack"></i>
                            <a class="sidebar-link sidebar-title link-nav" href="{{ $lien($entree) }}">
                                <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-'.$entree['icone']) }}"></use></svg>
                                <svg class="fill-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#fill-'.$entree['icone']) }}"></use></svg>
                                <span>{{ $entree['titre'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach

            </ul>
        </div>
    </nav>
    </div>
</div>
<!-- Page Sidebar Ends-->
