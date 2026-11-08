@extends('layout.master')
@section('title', $vuePersonnelle ? 'Mon tableau de bord' : 'Tableau de bord')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/dashboard-ed.css') }}?v={{ filemtime(public_path('assets/css/dashboard-ed.css')) }}">
@endsection

@php
  /*
   * Une seule vue pour les deux tableaux de bord :
   *  - $vuePersonnelle = false : ADMIN, DG, RD (tout l'exercice) ;
   *  - $vuePersonnelle = true  : agent, limité aux tâches qui lui sont affectées (liens vers les demandes retirés).
   */

  // Pile de photos des acteurs (photo, sinon initiales) : $max visibles puis « +N »
  $avatars = function ($agents, int $max = 4) {
      if ($agents->isEmpty()) {
          return '<span class="ed-tiret">—</span>';
      }
      $html = '<span class="ed-avs">';
      foreach ($agents->take($max) as $p) {
          $nom = e($p->nomComplet());
          $html .= $p->photo_url
              ? '<img class="ed-av" src="'.e($p->photo_url).'" alt="'.$nom.'" title="'.$nom.'">'
              : '<span class="ed-av ed-av-'.(crc32($p->nomComplet()) % 6).'" title="'.$nom.'">'.e($p->initiales()).'</span>';
      }
      if ($agents->count() > $max) {
          $reste = $agents->count() - $max;
          $html .= '<span class="ed-av ed-av--reste" title="'.$reste.' de plus">+'.$reste.'</span>';
      }

      return $html.'</span>';
  };
  $delai = fn (int $j) => $j === 0 ? "Aujourd'hui" : ($j === 1 ? 'Demain' : 'Dans '.$j.' j');
  $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ').' FCFA';
  // Montant abrégé pour la légende : 425 k, 1,25 M, 2,3 Md (le montant exact apparaît au survol)
  $court = function ($v) {
      $f = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ',');
      $v = (float) $v;

      return match (true) {
          $v >= 999500000 => $f($v / 1e9).' Md',
          $v >= 999500 => $f($v / 1e6).' M',
          $v >= 10000 => $f($v / 1e3).' k',
          default => number_format($v, 0, ',', ' '),
      };
  };

  // Répartition des tâches (chaque tâche compte dans une seule catégorie) ; le reste = annulées
  $repartition = [
      ['Terminées', 'terminee', $stats['terminees']],
      ['En cours', 'encours', $stats['encours']],
      ['En attente', 'attente', $stats['attente']],
      ['En retard', 'retard', $stats['retards']],
  ];
  $autres = max(0, $stats['total'] - array_sum(array_column($repartition, 2)));
  if ($autres > 0) {
      $repartition[] = ['Annulées', 'neutre', $autres];
  }
  $totalRepartition = max(1, $stats['total']);

  // Demandes de justification : [statut, libellé, ton]
  $jTuiles = [
      ['en_attente', 'En attente', 'attente'],
      ['approuvee', 'Approuvées', 'terminee'],
      ['refusee', 'Refusées', 'retard'],
      ['annulee', 'Annulées', 'neutre'],
  ];
  $jLien = ! $vuePersonnelle && \Illuminate\Support\Facades\Route::has('feuille_de_route.justification.index');
  $nonJustifies = $stats['retardsNonJustifies'];
@endphp

@section('main-content')
<div class="page-body">
  <div class="container">
    <div class="page-title">
      <div class="grid grid-cols-12 mx-2 items-center">
        <div class="col-span-6 sm:col-span-12">
          <div class="ed-titre">
            <h3>{{ $vuePersonnelle ? 'Mon tableau de bord' : 'Tableau de bord' }}</h3>
            @if (isset($exercice) && $exercice)
              <span class="ed-exo"><i class="fa-regular fa-calendar"></i> Exercice {{ $exercice->annee ?? $exercice->libelle }}</span>
            @endif
          </div>
        </div>
        <div class="col-span-6 sm:col-span-12">
          <ol class="breadcrumb flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">e-Tache</li>
            <li class="breadcrumb-item active">Tableau de bord</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container ed-page ed-charge">
    <div class="ed-squelette" role="status" aria-live="polite">
      <span class="sr-only">Chargement du tableau de bord…</span>
      <div class="eds-grille">
        <div class="eds-gauche">
          <div class="eds-tuiles"><i></i><i></i><i></i><i></i><i></i><i></i></div>
          <div class="eds-carte eds-carte--haute">
            <div class="eds-ligne"><b class="eds-rond"></b><span class="eds-trait eds-trait--m"></span></div>
            <div class="eds-ligne"><span class="eds-trait"></span></div><div class="eds-ligne"><span class="eds-trait"></span></div><div class="eds-ligne"><span class="eds-trait eds-trait--c"></span></div>
          </div>
        </div>
        <div class="eds-droite">
          <div class="eds-carte"><span class="eds-trait eds-trait--m"></span><b class="eds-anneau"></b></div>
          <div class="eds-carte"><span class="eds-trait eds-trait--m"></span><span class="eds-trait"></span><span class="eds-trait"></span><span class="eds-trait eds-trait--c"></span></div>
        </div>
      </div>
      <div class="eds-carte eds-carte--bande"><i></i><i></i><i></i></div>
    </div>

    @if ($vuePersonnelle)
      @include('feuille_de_route._alerte_rd_sans_mandat')
      @unless (auth()->user()->personnel_id)
        <div class="ed-info"><i class="fa-solid fa-circle-info"></i> Votre compte n'est lié à aucun agent : aucune tâche ne peut vous être affectée. Contactez l'administrateur pour relier votre compte à votre fiche agent.</div>
      @endunless
    @endif

    {{-- Tâches prioritaires, répartition, demandes --}}
    <div class="ed-ligne ed-ligne--etire">
      <div class="ed-s8">
        <div class="ed-colg">
        <div class="ed-t6">
          <div class="ed-t ed-t--ambre">
            <span class="ed-t__ico"><i class="fa-regular fa-hourglass-half"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">En attente</div>
              <div class="ed-t__ligne"><span class="ed-t__val ed-compteur" data-valeur="{{ $stats['attente'] }}">{{ $stats['attente'] }}</span><span class="ed-t__chip">{{ $stats['pctAttente'] }} % du total</span></div></div>
          </div>

          <div class="ed-t ed-t--vert">
            <span class="ed-t__ico"><i class="fa-solid fa-circle-check"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">Terminées</div>
              <div class="ed-t__ligne"><span class="ed-t__val ed-compteur" data-valeur="{{ $stats['terminees'] }}">{{ $stats['terminees'] }}</span><span class="ed-t__chip">{{ $stats['pctTerminees'] }} % du total</span></div></div>
          </div>

          <div class="ed-t ed-t--bleu">
            <span class="ed-t__ico"><i class="fa-solid fa-bars-progress"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">En cours</div>
              <div class="ed-t__ligne"><span class="ed-t__val ed-compteur" data-valeur="{{ $stats['encours'] }}">{{ $stats['encours'] }}</span><span class="ed-t__chip">{{ $stats['pctEncours'] }} % du total</span></div></div>
          </div>

          <div class="ed-t ed-t--rouge">
            <span class="ed-t__ico"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">En retard</div>
              <div class="ed-t__ligne">
                <span class="ed-t__val ed-compteur" data-valeur="{{ $stats['retards'] }}">{{ $stats['retards'] }}</span>
                <span class="ed-t__chip" title="du total des tâches">{{ $stats['pctRetards'] }} %</span>
                @if ($stats['retards'] > 0)
                  @if ($vuePersonnelle)
                    <span class="ed-t__chip" title="Retards sans justification">{{ $nonJustifies }} non justifié{{ $nonJustifies > 1 ? 's' : '' }}</span>
                  @else
                    <a class="ed-t__chip" href="{{ route('feuille_de_route.tache', ['en_retard' => 1, 'non_justifie' => 1]) }}" title="Voir les tâches en retard sans justification">{{ $nonJustifies }} non justifié{{ $nonJustifies > 1 ? 's' : '' }}</a>
                  @endif
                @endif
              </div></div>
          </div>

          <div class="ed-t ed-t--violet">
            <span class="ed-t__ico"><i class="fa-regular fa-calendar-check"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">{{ $vuePersonnelle ? 'Mes diligences' : 'Diligences' }}</div>
              <div class="ed-t__ligne"><span class="ed-t__val ed-compteur" data-valeur="{{ $stats['diligences'] }}">{{ $stats['diligences'] }}</span><span class="ed-t__chip">ce mois + retards</span></div></div>
          </div>

          <div class="ed-t ed-t--sarcelle">
            <span class="ed-t__ico"><i class="fa-solid fa-list-check"></i></span>
            <div class="ed-t__corps"><div class="ed-t__lib">Total des tâches</div>
              <div class="ed-t__ligne"><span class="ed-t__val ed-compteur" data-valeur="{{ $stats['total'] }}">{{ $stats['total'] }}</span><span class="ed-t__chip">avancement {{ $stats['avancement'] }} %</span></div></div>
          </div>
        </div>

        <div class="ed-panel">
          <div class="ed-tete">
            <div>
              <h5><span class="ed-puce" style="--c: var(--t-retard)"><i class="fa-solid fa-flag"></i></span>Tâches prioritaires</h5>
              <small>Les {{ max(1, count($prioritaires)) }} plus urgentes : priorité, puis retard, puis échéance</small>
            </div>
            <div class="ed-fleches">
              @if (count($prioritaires) > 4)
                <button type="button" class="ed-fleche" data-prio="haut" aria-label="Tâche précédente"><i class="fa-solid fa-chevron-up"></i></button>
                <button type="button" class="ed-fleche" data-prio="bas" aria-label="Tâche suivante"><i class="fa-solid fa-chevron-down"></i></button>
              @endif
              <a class="ed-lien" href="{{ route('feuille_de_route.'.($vuePersonnelle ? 'tache_mes_taches' : 'tache')) }}">Tout voir <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="ed-prios" data-fenetre="4">
            <div class="ed-prios__piste">
            @forelse ($prioritaires as $p)
              @php $t = $p['tache']; @endphp
              <a class="ed-prio ed-ton-{{ $p['tonPriorite'] }}" href="{{ route('feuille_de_route.tache.details', $t->id) }}">
                <span class="ed-prio__barre"></span>
                <div style="min-width:0">
                  <div class="ed-prio__titre"><span class="ed-code">{{ $t->code }}</span><span>{{ $t->libelle }}</span></div>
                  <div class="ed-sous">{{ $p['chemin'] ?: '—' }}</div>
                </div>
                <div>{!! $avatars($p['agents'], 3) !!}</div>
                <div class="ed-prio__prog">
                  <span>{{ $t->avancement_pourcentage }} % réalisé</span>
                  <div class="ed-jauge" role="progressbar" aria-valuenow="{{ $t->avancement_pourcentage }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $t->avancement_pourcentage }}%"></i></div>
                </div>
                <div class="ed-prio__fin">
                  <span class="ed-badge ed-badge--plein">{{ $p['priorite'] ?? 'Priorité non définie' }}</span>
                  <div>
                    <span>{{ optional($t->date_fin)->format('d/m/Y') ?: '—' }}</span>
                    @if ($p['ton'] === 'retard')<span class="ed-badge ed-badge--plein ed-ton-retard">{{ abs($p['jours']) }} j</span>@endif
                  </div>
                </div>
              </a>
            @empty
              <div class="ed-vide">Aucune tâche prioritaire pour cet exercice.</div>
            @endforelse
            </div>
          </div>
        </div>
        </div>
      </div>

      <div class="ed-s4">
        <div class="ed-colonne">
        <div class="ed-sombre">
          <div class="ed-sombre__tete">
            <div>
              <h5>{{ $vuePersonnelle ? 'Budget de mes tâches' : 'Budget des tâches' }}</h5>
              <small>Répartition du budget estimé</small>
            </div>
            <div class="ed-sombre__icone"><i class="fa-solid fa-coins"></i></div>
          </div>

          <div class="ed-sombre__corps">
          <div class="ed-anneau" role="img" aria-label="Budget par catégorie : {{ $budget['pctRealise'] }} % réalisé">
            <svg viewBox="0 0 42 42">
              <g transform="rotate(-90 21 21)">
                <circle class="ed-anneau__fond" cx="21" cy="21" r="15.9155" fill="none" stroke-width="4.5"/>
                @foreach ($budget['arcs'] as [$tonA, $longueurA, $debutA])
                  <circle class="ed-arc ed-arc--{{ $tonA }}" cx="21" cy="21" r="15.9155" fill="none" stroke-width="4.5" pathLength="100" stroke-dasharray="{{ $longueurA }} {{ round(100 - $longueurA, 2) }}" stroke-dashoffset="{{ -$debutA }}"/>
                @endforeach
              </g>
            </svg>
            <div class="ed-anneau__centre"><strong>{{ $budget['pctRealise'] }}%</strong><span>réalisé</span></div>
          </div>

          <div class="ed-legende">
            @foreach ($budget['segments'] as [$libelleL, $tonL, $montantL, $aideL])
              <div class="ed-legende__ligne" title="{{ $aideL ? $aideL.' : ' : '' }}{{ $fcfa($montantL) }}">
                <i class="ed-point ed-pt-{{ $tonL }}"></i>
                <span class="ed-legende__nom">{{ $libelleL }}</span>
                <b class="ed-legende__montant">{{ $court($montantL) }}</b>
                <em class="ed-legende__pct">{{ $budget['total'] > 0 ? round($montantL / $budget['total'] * 100) : 0 }} %</em>
              </div>
            @endforeach
          </div>
          </div>

          <div class="ed-total">
            <div><b>{{ $fcfa($budget['total']) }}</b><span>Budget total</span></div>
            <div><b>{{ $fcfa($budget['termine']) }}</b><span>Tâches terminées</span></div>
          </div>

          @if ($budget['total'] > 0)
            @if ($budget['retard'] > 0)
              <div class="ed-alerte-budget"><i class="fa-solid fa-triangle-exclamation"></i> Budget en retard : <b>{{ $court($budget['retard']) }}</b> ({{ $budget['pctRetard'] }} %)</div>
            @else
              <div class="ed-alerte-budget ed-alerte-budget--ok"><i class="fa-solid fa-circle-check"></i> Aucun budget en retard</div>
            @endif
          @endif
        </div>

        <div class="ed-panel">
          <div class="ed-bloc">
            <div class="ed-tete" style="margin-bottom:14px">
              <div>
                <h5><span class="ed-puce" style="--c: var(--t-teal)"><i class="fa-solid fa-chart-pie"></i></span>Répartition</h5>
                <small>{{ $stats['total'] }} tâche{{ $stats['total'] > 1 ? 's' : '' }} au total</small>
              </div>
            </div>
            <div class="ed-pile" role="img" aria-label="Répartition des tâches par situation">
              @foreach ($repartition as [$libelleR, $tonR, $nR])
                @if ($nR > 0)<i class="ed-ton-{{ $tonR }}" style="flex: {{ $nR }} 1 0; min-width: 6px" title="{{ $libelleR }} : {{ $nR }}"></i>@endif
              @endforeach
            </div>
            <div class="ed-statuts">
              @foreach ($repartition as [$libelleR, $tonR, $nR])
                <div class="ed-statut ed-ton-{{ $tonR }}">
                  <i class="ed-point"></i>
                  <span>{{ $libelleR }}</span>
                  <b>{{ $nR }}</b>
                  <em>{{ round($nR / $totalRepartition * 100) }} %</em>
                </div>
              @endforeach
            </div>
          </div>

          @if ($justifications['visible'])
            <div class="ed-bloc">
              <div class="ed-tete" style="margin-bottom:14px">
                <div>
                  <h5><span class="ed-puce" style="--c: var(--t-avenir)"><i class="fa-solid fa-scale-balanced"></i></span>Demandes de justification</h5>
                  <small>{{ $justifications['total'] > 0 ? $justifications['total'].' sur l\'exercice' : 'Aucune demande pour l\'instant' }}</small>
                </div>
                @if ($jLien)
                  <a class="ed-lien" href="{{ route('feuille_de_route.justification.index') }}">Tout voir <i class="fa-solid fa-arrow-right"></i></a>
                @endif
              </div>
              <div class="ed-just">
                @foreach ($jTuiles as [$cleJ, $libelleJ, $tonJ])
                  @php $nJ = $justifications['statuts'][$cleJ]; @endphp
                  @if ($jLien)
                    <a class="ed-tuile ed-ton-{{ $tonJ }}" href="{{ route('feuille_de_route.justification.index', ['statut' => $cleJ]) }}" title="Voir les demandes : {{ mb_strtolower($libelleJ) }}">
                  @else
                    <div class="ed-tuile ed-ton-{{ $tonJ }}">
                  @endif
                      <b>{{ $nJ }}</b>
                      <span>{{ $libelleJ }}</span>
                      @if ($cleJ === 'en_attente' && $nJ > 0 && ! $vuePersonnelle)<em>à traiter</em>@endif
                  @if ($jLien) </a> @else </div> @endif
                @endforeach
              </div>
            </div>
          @endif

        </div>
        </div>
      </div>
    </div>

    {{-- Diligences --}}
    <div class="ed-ligne">
      <div class="ed-s12">
        <div class="ed-panel">
          <div class="ed-tete">
            <div>
              <h5><span class="ed-puce" style="--c: var(--t-attente)"><i class="fa-solid fa-calendar-check"></i></span>{{ $vuePersonnelle ? 'Mes diligences' : 'Diligences' }}</h5>
              <small>À venir ce mois, puis les retards · {{ count($diligences) }} tâche{{ count($diligences) > 1 ? 's' : '' }}</small>
            </div>
          </div>

          @if (count($diligences) === 0)
            <div class="ed-vide">Aucune tâche à venir ce mois ni en retard.</div>
          @else
            <div class="ed-dil" id="ed-diligences" data-taille="6">
              <div class="ed-dil__grille">
                @foreach ($diligences as $d)
                  @php
                      $t = $d['tache'];
                      $mois = mb_strtoupper(rtrim($t->date_fin->locale('fr')->translatedFormat('M'), '.'));
                  @endphp
                  <article class="ed-dil__carte ed-ton-{{ $d['ton'] }}">
                    <span class="ed-dil__date"><b>{{ $t->date_fin->format('d') }}</b><small>{{ $mois }}.</small></span>
                    <div class="ed-dil__texte">
                      <div class="ed-dil__titre"><span class="ed-code">{{ $t->code }}</span><span>{{ $d['chemin'] ?: '—' }}</span></div>
                      <div class="ed-dil__lib" title="{{ $t->libelle }}">{{ $t->libelle }}</div>
                      <div class="ed-dil__pied">
                        @if ($d['ton'] === 'retard')
                          <span class="ed-badge ed-badge--plein ed-ton-retard">{{ abs($d['jours']) }} j de retard</span>
                        @else
                          <span class="ed-badge ed-ton-{{ $d['jours'] <= 1 ? 'attente' : 'encours' }}">{{ $delai($d['jours']) }}</span>
                        @endif
                        <span class="ed-badge ed-ton-neutre"><i class="fa-regular fa-calendar"></i> {{ $t->date_fin->format('d/m/Y') }}</span>
                        {!! $avatars($d['agents'], 3) !!}
                      </div>
                    </div>
                    <a class="ed-dil__ouvrir" href="{{ route('feuille_de_route.tache.details', $t->id) }}"><i class="fa-solid fa-folder-open"></i> Ouvrir</a>
                  </article>
                @endforeach
              </div>

              <div class="ed-dil__bas">
                <label class="ed-dil__taille">Afficher
                  <select data-taille-select aria-label="Nombre de diligences par page">
                    <option value="4">4</option><option value="6" selected>6</option><option value="8">8</option><option value="12">12</option>
                  </select>
                  par page
                </label>
                <span data-info></span>
                <nav class="ed-pages" data-pages aria-label="Pagination des diligences"></nav>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/dashboard-ed.js') }}?v={{ filemtime(public_path('assets/js/dashboard-ed.js')) }}"></script>
@endsection
