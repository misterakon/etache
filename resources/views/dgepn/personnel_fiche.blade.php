@extends('layout.master')
@section('title', 'Fiche agent')

@php
  // Avatar a initiales (image SVG integree) quand l'agent n'a pas de photo
  $avatarDefaut = 'data:image/svg+xml;charset=UTF-8,'.rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><rect width="120" height="120" fill="#dbe7f7"/><text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Arial, sans-serif" font-size="44" fill="#0c447c">'.e($personnel->initiales()).'</text></svg>'
  );

  // Couleur (classes du template) associee a l'etat d'une tache
  $classeStatut = fn ($tache) => $tache->estEnRetard()
    ? 'danger'
    : match ($tache->statutExecution?->code) {
        \App\Models\StatutExecution::TERMINEE => 'success',
        \App\Models\StatutExecution::EN_COURS => 'primary',
        \App\Models\StatutExecution::BLOQUEE => 'danger',
        \App\Models\StatutExecution::SUSPENDUE => 'warning',
        default => 'secondary',
      };

  $direction = $personnel->service?->direction();
  $sousDirection = $personnel->service?->sousDirection;
  $libelleDirection = $personnel->service ? ($direction?->nom ?? 'Direction Générale') : null;
  $compte = $personnel->user;

  // Ton (couleur de la palette, voir personnel-fiche.css) d'une tache
  $tonTache = function ($t) {
      $code = $t->statutExecution?->code;

      return match (true) {
          $code === \App\Models\StatutExecution::TERMINEE => 'terminee',
          $code === \App\Models\StatutExecution::ANNULEE => 'neutre',
          $t->estEnRetard(), $code === \App\Models\StatutExecution::BLOQUEE => 'retard',
          $code === \App\Models\StatutExecution::EN_COURS => 'encours',
          default => 'attente',
      };
  };

  // Indicateurs : [libelle, valeur, icone, ton]
  $tuiles = [
      ['Tâches', $compteurs['total'], 'fa-solid fa-list-check', 'teal'],
      ['En cours', $compteurs['en_cours'], 'fa-solid fa-bars-progress', 'encours'],
      ['En retard', $compteurs['en_retard'], 'fa-solid fa-triangle-exclamation', 'retard'],
      ['Terminées', $compteurs['terminees'], 'fa-solid fa-circle-check', 'terminee'],
  ];

  // Informations : lignes [libelle, valeur|null, icone, lien|null]
  $emploiTexte = $personnel->emploi
      ? trim(($personnel->emploi->familleEmploi?->libelle ? $personnel->emploi->familleEmploi->libelle.' — ' : '').$personnel->emploi->libelle)
      : null;
  $naissance = trim((optional($personnel->date_naissance)->format('d/m/Y') ?: '').($personnel->lieu_naissance ? ', '.$personnel->lieu_naissance : ''), ', ');
  $nbEnfants = $personnel->nombre_enfants;
  $indice = \App\Models\EchelleTraitement::indiceDe($personnel->grade_id, $personnel->classe_id, $personnel->echelon_id);

  $groupeIdentite = [
      ['Civilité', $personnel->civilite?->libelle, 'fa-solid fa-user', null],
      ['Genre', $personnel->genre?->libelle, 'fa-solid fa-venus-mars', null],
      ['Naissance', $naissance ?: null, 'fa-solid fa-cake-candles', null],
      ['Statut matrimonial', $personnel->statutMatrimonial?->libelle, 'fa-solid fa-ring', null],
      ['Enfants', $nbEnfants !== null ? $nbEnfants.' enfant'.($nbEnfants > 1 ? 's' : '') : null, 'fa-solid fa-children', null],
      ['Père', $personnel->nom_pere ?: null, 'fa-solid fa-person', null],
      ['Mère', $personnel->nom_mere ?: null, 'fa-solid fa-person-dress', null],
  ];
  $groupeContact = [
      ['Email', $personnel->email ?: null, 'fa-solid fa-envelope', $personnel->email ? 'mailto:'.$personnel->email : null],
      ['Téléphone 1', $personnel->telephone ?: null, 'fa-solid fa-phone', $personnel->telephone ? 'tel:'.$personnel->telephone : null],
      ['Téléphone 2', $personnel->telephone_2 ?: null, 'fa-solid fa-mobile-screen', $personnel->telephone_2 ? 'tel:'.$personnel->telephone_2 : null],
      ['Habitation', $personnel->lieu_habitation ?: null, 'fa-solid fa-location-dot', null],
  ];
  if (! $voitPrive) {
      $prive = ['Naissance', 'Statut matrimonial', 'Enfants', 'Père', 'Mère', 'Email', 'Téléphone 1', 'Téléphone 2', 'Habitation'];
      $masque = fn ($l) => in_array($l[0], $prive, true) ? [$l[0], null, $l[2], null] : $l;
      $groupeIdentite = array_map($masque, $groupeIdentite);
      $groupeContact = array_map($masque, $groupeContact);
  }
  $groupeAffectation = [
      ['Poste', $personnel->fonction ?: null, 'fa-solid fa-user-tie', null],
      ['Direction', $libelleDirection ?: null, 'fa-solid fa-sitemap', null],
      ...($sousDirection ? [['Sous-direction', $sousDirection->nom, 'fa-solid fa-diagram-project', null]] : []),
      ['Service', $personnel->service?->nom ?: null, 'fa-solid fa-building', null],
      ['Supérieur', ($superieur ?? null)?->nomComplet(), 'fa-solid fa-user-shield', ($superieur ?? null) ? route('dgepn.personnel.fiche', $superieur->id) : null],
      ...((($unitesDirigees ?? '') !== '') ? [['Responsable de', $unitesDirigees, 'fa-solid fa-star', null]] : []),
  ];
  $editerFiche = ($peutModifier ?? false) ? 'edit_info('.$personnel->id.')' : null;
  $voirDossier = auth()->user()?->hasRole('ADMIN|DG') || (auth()->user()?->personnel_id !== null && (int) auth()->user()->personnel_id === (int) $personnel->id);
@endphp

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/personnel-fiche.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/personnel-fiche-v2.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/entete-page.css') }}?v={{ filemtime(public_path('assets/css/entete-page.css')) }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Fiche agent</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item">DGEPN</li>
              <li class="breadcrumb-item"><a href="{{ route('dgepn.personnel') }}">Personnel</a></li>
              <li class="breadcrumb-item active">Fiche</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="user-profile pf-page">
        <div class="grid grid-cols-12 card-gap">

          {{-- Formulaires caches : changement et retrait de la photo --}}
          <form id="form-photo" class="hidden" method="post" action="{{ route('dgepn.personnel.photo', $personnel->id) }}" enctype="multipart/form-data">
            @csrf
          </form>
          <form id="form-retirer-photo" class="hidden" method="post" action="{{ route('dgepn.personnel.photo', $personnel->id) }}">
            @csrf
            <input type="hidden" name="supprimer_photo" value="1">
          </form>

          {{-- En-tete : bandeau, photo, identite, indicateurs --}}
          <div class="col-span-12">
            <div class="card pf-hero">
              <div class="pf-hero__corps">
                <div class="pf-avatar">
                  <img id="output" src="{{ $personnel->photo_url ?: $avatarDefaut }}" alt="{{ $personnel->nomComplet() }}">
                  @if ($peutModifier ?? false)
                    <label class="pf-avatar__editer" title="Changer la photo">
                      <i class="fa-solid fa-camera"></i>
                      <input type="file" name="photo" form="form-photo" accept="image/png,image/jpeg,image/webp" onchange="document.getElementById('form-photo').submit()">
                    </label>
                    @if ($personnel->photo)
                      <button type="button" class="pf-avatar__retirer" title="Retirer la photo" onclick="document.getElementById('form-retirer-photo').submit()"><i class="fa-solid fa-xmark"></i></button>
                    @endif
                  @endif
                </div>

                <div class="pf-identite">
                  <h2 class="pf-nom">{{ $personnel->nomComplet() }}</h2>
                  <p class="pf-fonction">{{ $personnel->fonction ?: 'Fonction non renseignée' }}</p>
                  <div class="pf-chips">
                    @if ($libelleDirection) <span class="pf-chip tl-encours"><i class="fa-solid fa-sitemap"></i>&nbsp;&nbsp;{{ $libelleDirection }}</span> @endif
                    @if ($personnel->service) <span class="pf-chip tl-teal"><i class="fa-solid fa-building"></i>&nbsp;&nbsp;{{ $personnel->service->nom }}</span> @endif
                    @foreach ($personnel->postesActuels() as $poste)
                      <span class="pf-chip tl-{{ $poste['interim'] ? 'attente' : 'terminee' }}"><i class="fa-solid fa-user-tie"></i>&nbsp;&nbsp;{{ $poste['interim'] ? 'Intérim : ' : '' }}{{ $poste['libelle'] }}
                        @if (($estAdmin ?? false) && ! empty($poste['id']))
                          <a href="#!" class="pf-chip-fin" onclick="aff_fin_ouvrir({{ $poste['id'] }}, @js($poste['libelle']), {{ $poste['interim'] ? 'true' : 'false' }}, @js($poste['debut_iso'] ?? '')); return false;">mettre fin</a>
                        @endif
                      </span>
                    @endforeach
                    @if ($personnel->date_deces) <span class="pf-chip tl-retard"><i class="fa-solid fa-ribbon"></i>&nbsp;&nbsp;Décédé(e)</span> @endif
                    @if ($personnel->typeAgent) <span class="pf-chip tl-avenir"><i class="fa-solid fa-id-badge"></i>&nbsp;&nbsp;{{ $personnel->typeAgent->libelle }}</span> @endif
                    @if ($personnel->matricule && $voitPrive) <span class="pf-chip tl-attente"><i class="fa-solid fa-hashtag"></i>&nbsp;&nbsp;{{ $personnel->matricule }}</span> @endif
                    @if ($compte) <span class="pf-chip tl-{{ $compte->actif ? 'terminee' : 'retard' }}"><i class="fa-solid fa-user-shield"></i>&nbsp;&nbsp;Compte {{ $compte->actif ? 'actif' : 'inactif' }}</span> @endif
                  </div>
                </div>
                @php
                  $nbPonctuellesAgent = \App\Models\TachePonctuelle::visiblePar(auth()->user())->actives()->where('responsable_personnel_id', $personnel->id)->count();
                  $maFiche = (int) auth()->user()->personnel_id === (int) $personnel->id;
                @endphp
                @if ($nbPonctuellesAgent > 0 || $maFiche)
                  <div class="pf-actions"><a class="pf-btn" href="{{ route('ponctuelle.index', ['agent' => $personnel->id]) }}"><i class="fa-solid fa-bolt"></i> Tâches ponctuelles ({{ $nbPonctuellesAgent }})</a></div>
                @endif

                @if ($peutModifier ?? false)
                  <div class="pf-actions">
                    @if ($estAdmin ?? false)
                      <a class="pf-btn" href="#!" onclick="aff_ouvrir({{ $personnel->id }}, @js($personnel->nomComplet()), {{ $personnel->fonction_id ?? 'null' }}, {{ $personnel->service_id ?? 'null' }}); return false;"><i class="fa-solid fa-user-tie"></i> Affecter à un poste</a>
                    @endif
                    <a class="pf-btn" href="#!" onclick="edit_info({{ $personnel->id }}); return false;"><i class="fa-solid fa-pen"></i> {{ ($estAdmin ?? false) ? 'Modifier la fiche' : 'Modifier mes informations' }}</a>
                  </div>
                @endif
              </div>

              @php
                $totalT = max(1, (int) $compteurs['total']);
                $pastilles = [
                    'Tâches' => ['vert', 'affectées à l’agent'],
                    'En cours' => ['bleu', 'en cours d’exécution'],
                    'En retard' => ['rouge', $compteurs['en_retard'] ? 'à traiter en priorité' : 'aucun retard'],
                    'Terminées' => ['orange', 'tâches closes'],
                ];
              @endphp
              <div class="pf-stats pf-stats--v2">
                @foreach ($tuiles as [$libT, $valT, $icoT, $tonT])
                  @php [$couleurT, $sousT] = $pastilles[$libT] ?? ['bleu', '']; $pctT = $libT === 'Tâches' ? 100 : (int) round($valT / $totalT * 100); @endphp
                  <div class="pf-k pf-k--{{ $couleurT }}">
                    <span class="pf-k__pct">{{ $pctT }} %</span>
                    <div class="pf-k__haut">
                      <div class="pf-k__anneau" style="--p: {{ $pctT }}"><div class="pf-k__ico"><i class="{{ $icoT }}"></i></div></div>
                      <div class="pf-k__texte">
                        <span class="pf-k__lib">{{ $libT }}</span>
                        <div class="pf-k__val counter" data-target="{{ $valT }}">{{ $valT }}</div>
                      </div>
                    </div>
                    <span class="pf-k__pastille">{{ $sousT }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Fiche : trois colonnes de cartes compactes (identité et contact | affectation et dossier | carrière) --}}
          <div class="col-span-12">
            <div class="fa3-grille">
              <div class="fa3-col">
                @include('dgepn._fiche_groupe', ['titre' => 'Identité et état civil', 'icone' => 'fa-solid fa-id-card', 'ton' => 'identite', 'lignes' => $groupeIdentite, 'editer' => $editerFiche])
                @include('dgepn._fiche_groupe', ['titre' => 'Contact', 'icone' => 'fa-solid fa-address-book', 'ton' => 'contact', 'lignes' => $groupeContact, 'editer' => $editerFiche])
              </div>
              <div class="fa3-col">
                @include('dgepn._fiche_groupe', ['titre' => 'Affectation', 'icone' => 'fa-solid fa-sitemap', 'ton' => 'affectation', 'lignes' => $groupeAffectation, 'editer' => null])
                @if ($voirDossier)
                  @include('dgepn._fiche_dossier')
                  @include('rh._fiche', ['personnel' => $personnel])
                @endif
              </div>
              <div class="fa3-col">
                @if ($voitPrive)
                  @include('dgepn._fiche_carriere')
                @endif
              </div>
            </div>
          </div>

          {{-- Mouvements de poste : ADMIN, DG et l'agent lui-même --}}
          @if ($peutModifier ?? false)
            <div class="col-span-12 pf-section-mouvements">
              @include('dgepn._fiche_mouvements')
            </div>
          @endif

          @hasanyrole('ADMIN|DG')
          {{-- Onglets --}}
          <div class="col-span-12 pf-section-onglets">
            @php
              $ongletsFiche = [
                  1 => ['fa-list-check', 'Tâches', $compteurs['total']],
                  2 => ['fa-timeline', 'Activité récente', null],
                  3 => ['fa-bell', 'Alertes reçues', null],
                  4 => ['fa-user-shield', 'Compte', null],
              ];
            @endphp
            <x-onglets-page :onglets="$ongletsFiche" />

            <div class="ep-contenu" style="margin-top: 20px;">
                    <div class="tab-content">

                      {{-- 1. Taches --}}
                      <div class="tab-pan fade show active" data-tabcontent="1">
                        <div class="card">
                          <div class="card-header">
                            <h5>Tâches</h5>
                            <span class="c-o-light">Responsable de {{ $nbActionsCles }} action(s) clé(s) et de {{ $nbActionsMesurables }} action(s) mesurable(s)</span>
                          </div>
                          <div class="card-body projects-wrapper">
                            <div class="grid grid-cols-12 gap-[24px]">
                              @forelse ($taches as $tache)
                                @php
                                  $ton = $tonTache($tache);
                                  $roles = $tache->affectations->where('personnel_id', $personnel->id)
                                    ->pluck('role_affectation_id')->map(fn ($r) => $rolesParId[$r] ?? null)->filter()->unique()->implode(', ');
                                @endphp
                                <div class="col-span-4 xxl:col-span-6 md:col-span-12 box-col-6 xl-50">
                                  <div class="pf-tache tl-{{ $ton }}">
                                    <div class="pf-tache__haut">
                                      <span class="tl-badge tl-{{ $ton }}">{{ $tache->estEnRetard() && $ton === 'retard' && $tache->statutExecution?->code !== \App\Models\StatutExecution::BLOQUEE ? 'En retard' : $tache->statutExecution?->libelle }}</span>
                                      <span class="pf-date"><i class="fa-regular fa-calendar"></i> {{ optional($tache->date_fin)->format('d/m/Y') ?: 'Sans échéance' }}</span>
                                    </div>
                                    <h6 class="pf-tache__titre" title="{{ $tache->libelle }}"><a href="{{ route('feuille_de_route.tache.details', $tache->id) }}">{{ \Illuminate\Support\Str::limit($tache->libelle, 60) }}</a></h6>
                                    <div class="pf-meta"><span class="pf-code">{{ $tache->code }}</span>@if ($roles)<span><i class="fa-solid fa-user-tag"></i> {{ $roles }}</span>@endif</div>
                                    <div class="pf-prog">
                                      <div class="pf-meter {{ $tache->avancement_pourcentage > 0 ? '' : 'pf-meter--vide' }}" style="--p: {{ $tache->avancement_pourcentage }}%"></div>
                                      <b>{{ $tache->avancement_pourcentage }}%</b>
                                    </div>
                                  </div>
                                </div>
                              @empty
                                <div class="col-span-12">
                                  <p class="text-muted text-center" style="padding:24px 0;">Aucune tâche n'est affectée à cet agent pour l'instant.</p>
                                </div>
                              @endforelse
                            </div>
                          </div>
                        </div>
                      </div>

                      {{-- 2. Activite recente --}}
                      <div class="tab-pan fade" data-tabcontent="2">
                        <div class="notification">
                          <div class="card">
                            <div class="card-header"><h5>Activité récente</h5></div>
                            <div class="card-body dark-timeline">
                              @if ($activite->isEmpty())
                                <p class="text-muted text-center" style="padding:24px 0;">Aucune mise à jour récente sur ses tâches.</p>
                              @else
                                <ul>
                                  @foreach ($activite as $tache)
                                    <li class="flex">
                                      <div class="activity-dot-{{ $classeStatut($tache) === 'success' ? 'primary' : 'warning' }}"></div>
                                      <div class="w-full ms-3">
                                        <p class="flex justify-between mb-2">
                                          <span class="date-content light-background">{{ $tache->derniere_mise_a_jour_le->format('d/m/Y') }}</span>
                                          <span>{{ $tache->derniere_mise_a_jour_le->format('H:i') }}</span>
                                        </p>
                                        <h6>{{ \Illuminate\Support\Str::limit($tache->libelle, 80) }}<span class="dot-notification"></span></h6>
                                        <div class="pf-meta" style="margin:6px 0 0;"><span class="pf-code tl-{{ $tonTache($tache) }}">{{ $tache->code }}</span><span class="tl-badge tl-{{ $tonTache($tache) }}">{{ $tache->statutExecution?->libelle }}</span><span class="pf-pct tl-{{ $tonTache($tache) }}">{{ $tache->avancement_pourcentage }}%</span></div>
                                      </div>
                                    </li>
                                  @endforeach
                                </ul>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>

                      {{-- 3. Alertes recues --}}
                      <div class="tab-pan fade" data-tabcontent="3">
                        <div class="card">
                          <div class="card-header"><h5>Alertes reçues</h5></div>
                          <div class="card-body">
                            <div class="user-notifications">
                              @forelse ($alertes->groupBy(fn ($a) => $a->envoye_le->format('d/m/Y')) as $jour => $alertesDuJour)
                                <h6>{{ $jour === now()->format('d/m/Y') ? "Aujourd'hui" : ($jour === now()->subDay()->format('d/m/Y') ? 'Hier' : $jour) }}</h6>
                                <ul>
                                  @foreach ($alertesDuJour as $alerte)
                                    <li>
                                      <div class="[@media(max-width:500px)]:flex-wrap">
                                        <div class="common-circle pf-bulle tl-attente"><i class="fa-regular fa-bell"></i></div>
                                        <div>
                                          <p>{{ $alerte->regleAlerte?->libelle }} · {{ $alerte->tache?->code }}<span class="ps-2">{{ $alerte->envoye_le->format('H:i') }}</span></p>
                                          <span>{{ \Illuminate\Support\Str::limit($alerte->tache?->libelle, 90) }} — via {{ $alerte->canalNotification?->libelle }}</span>
                                        </div>
                                      </div>
                                    </li>
                                  @endforeach
                                </ul>
                              @empty
                                <p class="text-muted text-center" style="padding:24px 0;">Aucune alerte reçue pour l'instant. Elles apparaîtront ici dès que le moteur d'alertes sera en service.</p>
                              @endforelse
                            </div>
                          </div>
                        </div>
                      </div>

                      {{-- 4. Compte --}}
                      <div class="tab-pan fade" data-tabcontent="4">
                        <div class="card">
                          <div class="card-header"><h5>Compte utilisateur</h5></div>
                          <div class="card-body setting-wrapper">
                            @if ($compte)
                              <div class="pf-groupes pf-groupes--compte">
                                <div class="pf-groupe tl-encours">
                                  <div class="pf-groupe__tete"><i class="fa-solid fa-user-shield"></i>Compte de connexion</div>
                                  <div class="pf-ligne">
                                    <div class="pf-ligne__ico"><i class="fa-solid fa-user"></i></div>
                                    <div class="pf-ligne__corps"><div class="pf-ligne__lib">Nom affiché</div><div class="pf-ligne__val">{{ $compte->name }}</div></div>
                                  </div>
                                  <div class="pf-ligne">
                                    <div class="pf-ligne__ico"><i class="fa-solid fa-at"></i></div>
                                    <div class="pf-ligne__corps"><div class="pf-ligne__lib">Email de connexion</div><div class="pf-ligne__val">{{ $voitPrive ? $compte->email : '—' }}</div></div>
                                  </div>
                                  <div class="pf-ligne">
                                    <div class="pf-ligne__ico"><i class="fa-solid fa-user-lock"></i></div>
                                    <div class="pf-ligne__corps"><div class="pf-ligne__lib">Profil d'accès</div><div class="pf-ligne__val">{{ $compte->profilAcces?->libelle ?: 'Non renseigné' }}</div></div>
                                  </div>
                                  <div class="pf-ligne">
                                    <div class="pf-ligne__ico"><i class="fa-solid fa-{{ $compte->actif ? 'circle-check' : 'circle-xmark' }}"></i></div>
                                    <div class="pf-ligne__corps"><div class="pf-ligne__lib">Statut du compte</div>
                                      <div class="pf-ligne__val"><span class="tl-badge tl-{{ $compte->actif ? 'terminee' : 'retard' }} tl-solid">{{ $compte->actif ? 'Actif' : 'Inactif' }}</span></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            @else
                              <p class="text-muted">Cet agent n'a pas de compte de connexion. Il peut être affecté à des tâches et recevoir des alertes sans en avoir.</p>
                            @endif

                            <div class="mt-3">
                              <a class="btn btn-primary text-white" href="{{ route('utilisateur.compte') }}">{{ $compte ? 'Gérer les comptes' : 'Créer un compte' }}</a>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
            </div>
          </div>
          @endhasanyrole

        </div>
      </div>
    </div>

    @if ($peutModifier ?? false)
      @include('dgepn.form_info')
      @include('dgepn.js_script')
      @include('dgepn.js_champs_agent')
      @if ($estAdmin ?? false)
        @include('dgepn.form_affectation')
        @include('dgepn.js_affectation')
        @include('dgepn.form_carriere')
        @include('dgepn.js_carriere')
      @endif
      @if ($peutAjouterDossier ?? false)
        @include('dgepn.form_dossier')
        @include('dgepn.js_dossier')
      @endif
    @endif
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/counter/custom-counter1.js') }}"></script>
@endsection
