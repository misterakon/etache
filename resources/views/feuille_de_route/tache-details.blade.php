@extends('layout.master')
@section('title', 'details-tâche')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tache-details.css') }}">
@endsection

@use('App\Models\StatutExecution')
@php
  $am = $tache->actionMesurable;
  $ac = $am?->actionCle;
  $axe = $ac?->axe;

  $code = $tache->statutExecution?->code;
  $terminee = $code === StatutExecution::TERMINEE;
  $enRetard = ! $terminee && $tache->estEnRetard();
  $joursRetard = $enRetard ? (int) $tache->date_fin->diffInDays(today()) : 0;
  $joursRestants = ($tache->date_fin && ! $enRetard && ! $terminee && $tache->date_fin->gte(today())) ? (int) today()->diffInDays($tache->date_fin) : null;

  // Tons de la palette (voir public/assets/css/tache-details.css)
  $tonTache = match ($code) {
      StatutExecution::TERMINEE => 'terminee',
      StatutExecution::EN_COURS => 'encours',
      StatutExecution::ANNULEE => 'neutre',
      default => 'attente',
  };
  $hex = ['encours' => '#2B6CB0', 'attente' => '#C77D0A', 'terminee' => '#2E7D5B', 'retard' => '#A73B34', 'avenir' => '#6D5BD0', 'neutre' => '#5B6B84'];

  $avatar = fn ($p) => $p->photo_url
      ? '<img class="common-circle" src="'.e($p->photo_url).'" alt="'.e($p->nomComplet()).'">'
      : '<div class="common-circle bg-lighter-primary">'.e($p->initiales()).'</div>';
  $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ').' FCFA';
  $ico = fn ($nom) => asset('assets/svg/icon-sprite.svg#'.$nom);

  $avancement = (int) $tache->avancement_pourcentage;

  // Échéance : texte et ton
  $echeanceTexte = $terminee ? 'Tâche terminée'
      : ($enRetard ? $joursRetard.' j de retard'
      : ($joursRestants === 0 ? "Échéance aujourd'hui"
      : ($joursRestants !== null ? 'Dans '.$joursRestants.' j' : "Pas d'échéance à venir")));
  $tonEcheance = $terminee ? 'terminee' : ($enRetard ? 'retard' : ($joursRestants !== null ? 'avenir' : 'neutre'));

  // Équipe : une entrée par agent (avec tous ses rôles sur la tâche), responsables d'abord
  $affectations = $tache->affectations->filter(fn ($a) => $a->personnel);
  $agents = $affectations->pluck('personnel')->unique('id')->values();
  $vide = ['total' => 0, 'terminees' => 0, 'en_cours' => 0, 'retard' => 0];
  $rangRole = fn ($lib) => match (true) {
      str_contains(mb_strtolower($lib), 'respons') => 0,
      str_contains(mb_strtolower($lib), 'pilot') => 1,
      default => 2,
  };
  $tonRole = fn ($lib) => [0 => 'encours', 1 => 'avenir', 2 => 'neutre'][$rangRole($lib)];
  $equipe = $affectations->groupBy('personnel_id')->map(fn ($g) => [
      'p' => $g->first()->personnel,
      'roles' => $g->map(fn ($a) => $rolesParId[$a->role_affectation_id] ?? 'Rôle')->unique()->sortBy($rangRole)->values(),
  ])->sortBy(fn ($e) => $rangRole($e['roles']->first()))->values();

  // Bandeau d'indicateurs : [libellé, valeur, sous-texte, icône, ton, barre %]
  $kpis = [
      ['Avancement', $avancement.'%', $tache->statutExecution?->libelle ?? '—', 'fa-solid fa-gauge-high', $tonTache, $avancement],
      ['Échéance', optional($tache->date_fin)->format('d/m/Y') ?: '—', $echeanceTexte, 'fa-regular fa-calendar-check', $tonEcheance, null],
      ['Budget estimé', $tache->budget_estime !== null ? $fcfa($tache->budget_estime) : '—', $tache->budget_estime !== null ? 'Enveloppe prévue' : 'Non renseigné', 'fa-solid fa-coins', 'teal', null],
      ['Équipe', $equipe->count(), 'agent(s) affecté(s)', 'fa-solid fa-users', 'avenir', null],
  ];

  // Informations : hiérarchie [niveau, code, libellé, extra, icône, ton] (texte COMPLET, jamais tronqué)
  $hierarchie = [
      ['Axe', $axe?->code, $axe?->libelle, null, 'fa-solid fa-layer-group', 'avenir'],
      ['Action clé', $ac?->code, $ac?->libelle, $nbTachesAc.' tâche(s)', 'fa-solid fa-key', 'encours'],
      ['Action mesurable', $am?->code, $am?->libelle, $nbTachesAm.' tâche(s)', 'fa-solid fa-ruler-combined', 'teal'],
      ['Tâche', $tache->code, $tache->libelle, null, 'fa-solid fa-list-check', $tonTache],
  ];
  $notes = [
      ['Indicateur', $tache->indicateur, 'fa-solid fa-chart-line', 'encours'],
      ['Livrable', $tache->livrable, 'fa-solid fa-box-open', 'terminee'],
      ['Observations', $tache->observations, 'fa-regular fa-comment-dots', 'attente'],
  ];
  
  if ($tache->reconduite_de_id) {
      $notes[] = ['Reconduite de', ($tache->reconduiteDe?->code ?? '—').' ('.($tache->reconduiteDe?->exerciceTache()?->annee ?? '—').')', 'fa-solid fa-rotate', 'avenir'];
  }
  if ($tache->reconduite_vers_exercice_id) {
      $notes[] = ['Reconduite vers', 'Exercice '.($tache->reconduiteVersExercice?->annee ?? '—'), 'fa-solid fa-rotate-right', 'avenir'];
  }

  // Graphique circulaire : temps écoulé (début -> échéance) et avancement
  $tempsEcoule = null;
  if ($tache->date_debut && $tache->date_fin && $tache->date_fin->gt($tache->date_debut)) {
      $tempsEcoule = (int) max(0, min(100, round((today()->timestamp - $tache->date_debut->timestamp) / ($tache->date_fin->timestamp - $tache->date_debut->timestamp) * 100)));
  }
  $radial = ['series' => [], 'labels' => [], 'colors' => []];
  if ($tempsEcoule !== null) {
      $radial['series'][] = $tempsEcoule; $radial['labels'][] = 'Temps écoulé'; $radial['colors'][] = $hex['avenir'];
  }
  $radial['series'][] = $avancement; $radial['labels'][] = 'Avancement'; $radial['colors'][] = $hex['terminee'];
  $radial += ['centre' => $avancement.'%', 'libelleCentre' => $terminee ? 'Terminée' : 'Réalisé'];

  // Activité : historique des modifications + alertes envoyées (inchangé)
  // $champs = ['priorite_id' => 'Priorité', 'statut_execution_id' => 'Statut', 'avancement_pourcentage' => 'Avancement'];
    $champs = [
        'priorite_id' => 'Priorité', 'statut_execution_id' => 'Statut', 'avancement_pourcentage' => 'Avancement',
        'date_fin' => 'Échéance', 'date_debut' => 'Date de début',
        'responsable' => 'Responsable', 'pilote' => 'Pilote', 'contributeur' => 'Contributeurs',
        'sous_tache' => 'Sous-tâche',
    ];
    // Les dates sont journalisées au format AAAA-MM-JJ : on les affiche en JJ/MM/AAAA
    $valeurJournal = fn ($champ, $valeur) => ($valeur === null || $valeur === '')
        ? '—'
        : (in_array($champ, ['date_fin', 'date_debut'], true) ? \Carbon\Carbon::parse($valeur)->format('d/m/Y') : $valeur);
    // Report d'échéance : écart en jours, par exemple (+15 j) ou (-3 j)
    $ecartJours = function ($h) {
        if ($h->champ !== 'date_fin' || ! $h->ancienne_valeur || ! $h->nouvelle_valeur) {
            return '';
        }
        $jours = (int) \Carbon\Carbon::parse($h->ancienne_valeur)->diffInDays(\Carbon\Carbon::parse($h->nouvelle_valeur), false);

        return $jours === 0 ? '' : ' ('.($jours > 0 ? '+' : '').$jours.' j)';
    };

  $activites = collect();
  foreach ($tache->historiques as $h) {
      $activites->push([
          'date' => $h->created_at, 'ton' => 'primary',
          'titre' => ($champs[$h->champ] ?? $h->champ).' : '.$valeurJournal($h->champ, $h->ancienne_valeur).' → '.$valeurJournal($h->champ, $h->nouvelle_valeur).$ecartJours($h),
          // 'titre' => ($champs[$h->champ] ?? $h->champ).' : '.($h->ancienne_valeur ?? '—').' → '.($h->nouvelle_valeur ?? '—'),
          'detail' => $h->user?->name ? 'Par '.$h->user->name : '',
      ]);
  }
  foreach ($tache->alertesEnvoyees as $a) {
      $activites->push([
          'date' => $a->envoye_le, 'ton' => 'warning',
          'titre' => 'Alerte envoyée'.($a->regleAlerte?->libelle ? ' — '.$a->regleAlerte->libelle : ''),
          'detail' => trim(($a->canalNotification?->libelle ?? '').' · '.($a->personnel?->nomComplet() ?? ''), ' ·'),
      ]);
  }
  foreach (\App\Models\JournalActivite::pourTache($tache) as $e) {
      $activites->push([
          'date' => $e->created_at, 'ton' => $e->module === 'fichiers' ? 'success' : 'primary',
          'titre' => $e->resume,
          'detail' => $e->acteur_nom ? 'Par '.$e->acteur_nom : '',
      ]);
  }
  // Demandes de justification / report et décisions
  $nomJ = fn ($u) => $u?->personnel?->nomComplet() ?? $u?->name ?? '—';
  foreach ($tache->justifications as $j) {
      $quoi = $j->estReport() ? 'report' : 'justification';
      $activites->push([
          'date' => $j->created_at, 'ton' => 'warning',
          'titre' => 'Demande de '.$quoi.($j->estReport() ? ' : '.$j->echeance_actuelle->format('d/m/Y').' → '.$j->echeance_proposee->format('d/m/Y') : ''),
          'detail' => 'Par '.$nomJ($j->demandeur).' — '.(\App\Support\JustificationRetard::MOTIFS[$j->motif] ?? $j->motif).' : '.$j->commentaire,
      ]);
      if ($j->decide_le) {
          $activites->push([
              'date' => $j->decide_le,
              'ton' => ['approuvee' => 'success', 'refusee' => 'danger'][$j->statut] ?? 'secondary',
              'titre' => 'Demande de '.$quoi.' '.(['approuvee' => 'approuvée', 'refusee' => 'refusée'][$j->statut] ?? 'annulée'),
              'detail' => $j->decideur ? 'Par '.$nomJ($j->decideur).($j->decision_commentaire ? ' — '.$j->decision_commentaire : '') : '',
          ]);
      }
  }
  $activites = $activites->filter(fn ($x) => $x['date'])->sortByDesc('date')->values();

  $onglets = [1 => ['fa-gauge-high', 'Résumé'], 2 => ['fa-users', 'Équipe'], 3 => ['fa-list-check', 'Sous-tâches'], 4 => ['fa-folder-open', 'Livrables'], 5 => ['fa-clock-rotate-left', 'Activité']];

  $nbSousTaches = $tache->sousTaches()->count();
  $nbSousTachesFaites = $tache->sousTaches()->where('cochee', true)->count();
  $nbLivrables = $tache->piecesJointes()->where('actuelle', true)->count();
  $comptes = [1 => null, 2 => (string) $equipe->count(), 3 => $nbSousTachesFaites.'/'.$nbSousTaches, 4 => (string) $nbLivrables, 5 => (string) $activites->count()];
  $prioriteLib = $tache->priorite?->libelle;

@endphp

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>
                Détails de la tâche</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                  <svg class="stroke-icon">
                    <use href="{{ $ico('stroke-home') }}"></use>
                  </svg></a></li>
              <li class="breadcrumb-item"><a href="{{ route('feuille_de_route.tache') }}">Tâches</a></li>
              <li class="breadcrumb-item active">{{ $tache->code }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    @include('feuille_de_route._validation_cloture', ['tache' => $tache])

    <div class="container td-page">

      {{-- ===== En-tête (héros) ===== --}}
      <section class="td-hero">
        <div class="td-hero__ligne">
          <div class="td-hero__badges">
            <span class="td-hero__code">{{ $tache->code }}</span>
            <span class="td-pill td-pill--{{ $tonTache }}">{{ $tache->statutExecution?->libelle ?? '—' }}</span>
            @if ($enRetard) <span class="td-pill td-pill--retard">{{ $joursRetard }} j de retard</span> @endif
            @if ($prioriteLib) <span class="td-pill td-pill--prio">Priorité {{ mb_strtolower($prioriteLib) }}</span> @endif
            <span class="td-hero__just">@include('feuille_de_route._badge_justification', ['t' => $tache])</span>
          </div>
          <div class="td-hero__actions">
            @if ($agents->isNotEmpty())
              <div class="td-hero__avs">
                @foreach ($agents->take(4) as $p)
                  <span data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="{{ $p->nomComplet() }}">{!! $avatar($p) !!}</span>
                @endforeach
                @if ($agents->count() > 4) <span class="td-hero__plus">+{{ $agents->count() - 4 }}</span> @endif
              </div>
            @endif
            @include('feuille_de_route._btn_justifier', ['t' => $tache, 'classe' => 'td-btn td-btn--amber', 'libelle' => ' Justifier / Reporter'])
            @php $demandeAttente = $justif['enAttente']->get($tache->id); @endphp
            @if ($demandeAttente && ($demandeAttente->demandeur_id === auth()->id() || auth()->user()->hasRole('ADMIN')))
              <button class="td-btn td-btn--danger" type="button" onclick="annuler_justification(this)" data-url="{{ route('feuille_de_route.justification.annuler', $demandeAttente->id) }}"><i class="fa-solid fa-ban"></i> Annuler la demande</button>
            @endif
            <a class="td-btn td-btn--glass" href="{{ route('feuille_de_route.tache') }}"><i class="fa-solid fa-arrow-left"></i> Liste</a>
          </div>
        </div>

        <h1 class="td-hero__titre">{{ $tache->libelle }}</h1>

        <div class="td-hero__chemin">
          @foreach ([['Axe', $axe?->code], ['Action clé', $ac?->code], ['Action mesurable', $am?->code]] as $k => [$nivC, $codeC])
            @if ($k) <i class="fa-solid fa-angle-right"></i> @endif
            <span>{{ $nivC }} {{ $codeC ?: '—' }}</span>
          @endforeach
        </div>

        <div class="td-hero__kpis">
          @foreach ($kpis as [$libK, $valK, $sousK, $iconeK, $tonK, $barreK])
            <div class="td-hero__kpi">
              <div class="td-hero__kpi-lib"><i class="{{ $iconeK }}"></i>{{ $libK }}</div>
              <strong class="td-hero__kpi-val">{{ $valK }}</strong>
              <span class="td-hero__kpi-sous">{{ $sousK }}</span>
              @if ($barreK !== null)
                <div class="td-hero__kpi-barre" role="progressbar" aria-valuenow="{{ $barreK }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $barreK }}%"></i></div>
              @endif
            </div>
          @endforeach
        </div>
      </section>

      {{-- ===== Onglets ===== --}}
      <div class="tabs">
        <ul class="tab-links td-tabs" role="tablist">
          @foreach ($onglets as $n => [$icone, $titre])
            <li>
              <a class="tab-link {{ $n === 1 ? 'active' : '' }}" data-tabfilter="{{ $n }}" role="tab" href="javascript:void(0)">
                <i class="fa-solid {{ $icone }}"></i><span>{{ $titre }}</span>
                @if ($comptes[$n] !== null)<em class="td-tabs__nb">{{ $comptes[$n] }}</em>@endif
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- ===== Contenu des onglets ===== --}}
      <div class="td-contenu">
        <div class="tab-content">

                {{-- ================= 1. RÉSUMÉ ================= --}}
                <div class="tab-pan fade show active" data-tabcontent="1">
                  <div class="td-cols">
                    <div class="td-cols__main">

                      <div class="td-card">
                        <div class="td-card__tete">
                          <span class="td-card__ico td-card__ico--bleu"><i class="fa-solid fa-layer-group"></i></span>
                          <div><h5>Rattachement</h5><p>Exercice {{ $axe?->exercice?->libelle ?? $axe?->exercice?->annee ?? '—' }}</p></div>
                        </div>
                        <div class="td-hier">
                          @foreach ($hierarchie as [$niveau, $codeL, $libelleL, $extra, $iconeL, $tonL])
                            <div class="td-hier__item tl-{{ $tonL }}">
                              <div class="td-hier__noeud"><i class="{{ $iconeL }}"></i></div>
                              <div class="td-hier__corps">
                                <div class="td-hier__niveau">{{ $niveau }} @if ($extra)<span class="td-chip">{{ $extra }}</span>@endif</div>
                                <div class="td-hier__titre"><span class="td-code">{{ $codeL ?: '—' }}</span>{{ $libelleL ?: '—' }}</div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>

                      <div class="td-card">
                        <div class="td-card__tete">
                          <span class="td-card__ico td-card__ico--ambre"><i class="fa-regular fa-file-lines"></i></span>
                          <div><h5>Contenu et suivi</h5><p>Ce qui est attendu et les remarques du responsable</p></div>
                        </div>
                        <div class="td-notes">
                          @foreach ($notes as [$titreN, $texteN, $iconeN, $tonN])
                            <div class="td-note tl-{{ $tonN }}">
                              <div class="td-note__titre"><i class="{{ $iconeN }}"></i>{{ $titreN }}</div>
                              <div class="td-note__texte {{ filled($texteN) ? '' : 'td-note--vide' }}">{!! filled($texteN) ? nl2br(e($texteN)) : 'Non renseigné' !!}</div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>

                    <div class="td-cols__side">

                      <div class="td-card">
                        <div class="td-card__tete">
                          <span class="td-card__ico td-card__ico--violet"><i class="fa-solid fa-users"></i></span>
                          <div><h5>Équipe</h5><p>{{ $equipe->count() }} agent(s) affecté(s)</p></div>
                        </div>
                        @if ($equipe->isEmpty())
                          <div class="td-vide"><i class="fa-regular fa-user"></i>Aucun agent affecté.</div>
                        @else
                          <ul class="td-mini">
                            @foreach ($equipe as $e)
                              @php $p = $e['p']; $tonP = $tonRole($e['roles']->first()); @endphp
                              <li class="tl-{{ $tonP }}">
                                <div class="td-avatar">
                                  @if ($p->photo_url) <img src="{{ $p->photo_url }}" alt="{{ $p->nomComplet() }}">
                                  @else {{ $p->initiales() }} @endif
                                </div>
                                <div class="td-mini__corps">
                                  <a class="td-mini__nom" href="{{ route('dgepn.personnel.fiche', $p->id) }}">{{ $p->nomComplet() }}</a>
                                  <div class="td-mini__roles">
                                    @foreach ($e['roles'] as $r) <span class="tl-badge tl-{{ $tonRole($r) }}">{{ $r }}</span> @endforeach
                                  </div>
                                </div>
                                <a class="td-mini__lien" href="{{ route('dgepn.personnel.fiche', $p->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Fiche agent"><i class="fa-regular fa-id-card"></i></a>
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </div>

                      <div class="td-card">
                        <div class="td-card__tete">
                          <span class="td-card__ico td-card__ico--sarcelle"><i class="fa-regular fa-calendar-check"></i></span>
                          <div><h5>Calendrier et avancement</h5><p>{{ $echeanceTexte }}</p></div>
                        </div>
                        <div class="td-chrono tl-{{ $tonEcheance }}">
                          <div class="td-chrono__dates">
                            <div><small>Début</small><strong>{{ optional($tache->date_debut)->format('d/m/Y') ?: '—' }}</strong></div>
                            <div class="text-end"><small>Échéance</small><strong>{{ optional($tache->date_fin)->format('d/m/Y') ?: '—' }}</strong></div>
                          </div>
                          <div class="td-chrono__piste">
                            <div class="td-chrono__fill" style="width: {{ $avancement }}%"></div>
                            @if ($tempsEcoule !== null)
                              <span class="td-chrono__repere" style="left: {{ $tempsEcoule }}%"></span>
                              <span class="td-chrono__marqueur" style="left: clamp(9%, {{ $tempsEcoule }}%, 91%)">Aujourd'hui</span>
                            @endif
                          </div>
                          <p class="td-chrono__legende">
                            <span><i class="td-dot" style="background: #2E7D5B;"></i>Avancement : <strong>{{ $avancement }} %</strong></span>
                            @if ($tempsEcoule !== null)
                              <span><i class="td-dot" style="background: #6D5BD0;"></i>Temps écoulé : <strong>{{ $tempsEcoule }} %</strong></span>
                            @else
                              <span>Renseignez le début et l'échéance pour voir le temps écoulé.</span>
                            @endif
                          </p>
                        </div>

                        <div class="td-faits">
                          <div class="td-fait"><small>Priorité</small><span class="tl-badge tl-avenir">{{ $prioriteLib ?? '—' }}</span></div>
                          <div class="td-fait"><small>Statut</small><span class="tl-badge tl-{{ $tonTache }}">{{ $tache->statutExecution?->libelle ?? '—' }}</span></div>
                          <div class="td-fait"><small>Durée prévue</small><strong>{{ $tache->duree_jours !== null ? $tache->duree_jours.' j' : '—' }}</strong></div>
                          <div class="td-fait"><small>Dernière mise à jour</small><strong>{{ optional($tache->derniere_mise_a_jour_le)->format('d/m/Y H:i') ?: '—' }}</strong></div>
                        </div>

                        <div class="td-directions">
                          <small>Direction(s)</small>
                          @forelse ($directions as $dir)
                            <span class="tl-badge tl-teal">{{ $dir->nom }}</span>
                          @empty
                            <span class="c-o-light">—</span>
                          @endforelse
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- ================= 2. ÉQUIPE ================= --}}
                <div class="tab-pan fade" data-tabcontent="2">
                  <div class="td-panel">
                    <div class="td-panel__tete">
                      <div class="td-card__tete">
                        <span class="td-card__ico td-card__ico--violet"><i class="fa-solid fa-users"></i></span>
                        <div><h5>Équipe de la tâche</h5><p>{{ $equipe->count() }} agent(s) affecté(s), avec leur charge de travail globale</p></div>
                      </div>
                    </div>

                    @if ($equipe->isEmpty())
                      <div class="td-vide-grand">
                        <span><i class="fa-regular fa-user"></i></span>
                        <strong>Aucun agent affecté à cette tâche</strong>
                        <p>Affectez un responsable depuis la liste des tâches.</p>
                      </div>
                    @else
                      <div class="td-eq">
                        @foreach ($equipe as $e)
                          @php
                            $p = $e['p'];
                            $s = $statsPersonnel[$p->id] ?? $vide;
                            $pct = $s['total'] ? (int) round($s['terminees'] / $s['total'] * 100) : 0;
                            $tonBarre = $pct >= 70 ? 'terminee' : ($pct >= 35 ? 'encours' : 'attente');
                            $tonCarte = $tonRole($e['roles']->first());
                          @endphp
                          <article class="td-eq__carte tl-{{ $tonCarte }}">
                            <header class="td-eq__tete">
                              <span class="td-eq__avatar">
                                @if ($p->photo_url) <img src="{{ $p->photo_url }}" alt="{{ $p->nomComplet() }}">
                                @else <b>{{ $p->initiales() }}</b> @endif
                              </span>
                              <div class="td-eq__id">
                                <h6><a href="{{ route('dgepn.personnel.fiche', $p->id) }}">{{ $p->nomComplet() }}</a></h6>
                                @if ($p->fonction) <p>{{ $p->fonction }}</p> @endif
                                <div class="td-eq__roles">
                                  @foreach ($e['roles'] as $r) <span class="tl-badge tl-{{ $tonRole($r) }}">{{ $r }}</span> @endforeach
                                </div>
                              </div>
                              <a class="td-ibtn td-ibtn--bleu" href="{{ route('dgepn.personnel.fiche', $p->id) }}" title="Fiche agent"><i class="fa-regular fa-id-card"></i></a>
                            </header>

                            <div class="td-eq__prog"><span>Tâches terminées</span><strong>{{ $s['terminees'] }} / {{ $s['total'] }}</strong></div>
                            <div class="td-bar tl-{{ $tonBarre }}"><span style="width: {{ $pct }}%"></span></div>

                            <footer class="td-eq__pied">
                              <div><strong>{{ $s['total'] }}</strong><span>Tâches</span></div>
                              <div><strong class="td-num--encours">{{ $s['en_cours'] }}</strong><span>En cours</span></div>
                              <div><strong class="{{ $s['retard'] ? 'td-num--retard' : '' }}">{{ $s['retard'] }}</strong><span>En retard</span></div>
                            </footer>
                          </article>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>

                {{-- ================= SOUS-TÂCHES ================= --}}
                <div class="tab-pan fade" data-tabcontent="3">
                  @include('feuille_de_route._sous_taches', ['tache' => $tache])
                </div>

                {{-- ================= LIVRABLES ================= --}}
                <div class="tab-pan fade" data-tabcontent="4">
                  @include('feuille_de_route._livrables', ['tache' => $tache])
                </div>

                {{-- ================= 5. ACTIVITÉ ================= --}}
                <div class="tab-pan fade" data-tabcontent="5">
                  @php
                    $iconesAct = ['primary' => 'fa-pen', 'warning' => 'fa-bell', 'success' => 'fa-paperclip', 'danger' => 'fa-ban', 'secondary' => 'fa-circle-info'];
                    $parJour = $activites->groupBy(fn ($x) => $x['date']->format('Y-m-d'));
                  @endphp
                  <div class="td-panel">
                    <div class="td-panel__tete">
                      <div class="td-card__tete">
                        <span class="td-card__ico td-card__ico--sarcelle"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <div><h5>Activité récente</h5><p>Modifications, fichiers déposés, demandes et alertes de la tâche</p></div>
                      </div>
                      <span class="td-puce-m td-puce-m--bleu">{{ $activites->count() }} événement(s)</span>
                    </div>

                    @if ($activites->isEmpty())
                      <div class="td-vide-grand">
                        <span><i class="fa-regular fa-clock"></i></span>
                        <strong>Aucune modification ni alerte enregistrée</strong>
                        <p>Les changements de la tâche apparaîtront ici.</p>
                      </div>
                    @else
                      <div class="td-act">
                        @foreach ($parJour as $jour => $items)
                          <div class="td-act__jour"><span>{{ \Carbon\Carbon::parse($jour)->locale('fr')->translatedFormat('l j F Y') }}</span></div>
                          <ol class="td-act__liste">
                            @foreach ($items as $x)
                              <li class="td-act__item td-act--{{ $x['ton'] }}">
                                <span class="td-act__pt"><i class="fa-solid {{ $iconesAct[$x['ton']] ?? 'fa-circle-info' }}"></i></span>
                                <div class="td-act__corps">
                                  <div class="td-act__titre">{{ $x['titre'] }}</div>
                                  <div class="td-act__meta">
                                    <span><i class="fa-regular fa-clock"></i> {{ $x['date']->format('H:i') }}</span>
                                    @if ($x['detail']) <span>{{ $x['detail'] }}</span> @endif
                                  </div>
                                </div>
                              </li>
                            @endforeach
                          </ol>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>

        </div>
      </div>
    </div>
    <!-- Container-fluid Ends-->
  </div>

  @include('feuille_de_route._modal_justification')
  @include('feuille_de_route.js_script')
@endsection

@section('script')
  <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  <script src="{{ asset('assets/js/touchspin_2/custom_touchspin.js') }}"></script>
  <script src="{{ asset('assets/js/custom-project.js') }}"></script>
  <script src="{{ asset('assets/js/tache-details.js') }}"></script>
@endsection
