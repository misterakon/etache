@extends('layout.master')
@section('title', 'Matrice des tâches')

@use('App\Support\VuesTachesDonnees')
@php
  // Pile d'avatars (3 au plus, puis "+N") : photo ou initiales
  $pile = function ($agents) {
      if ($agents->isEmpty()) {
          return '<span class="mx-vide-agent">—</span>';
      }
      $html = '<div class="mx-pile">';
      foreach ($agents->take(3) as $p) {
          $nom = e($p->nomComplet());
          $html .= '<span class="mx-avatar" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="'.$nom.'">'
              .($p->photo_url ? '<img src="'.e($p->photo_url).'" alt="'.$nom.'">' : e($p->initiales())).'</span>';
      }
      if ($agents->count() > 3) {
          $html .= '<span class="mx-avatar mx-avatar--plus">+'.($agents->count() - 3).'</span>';
      }

      return $html.'</div>';
  };
  $legende = [['retard', 'En retard'], ['encours', 'En cours'], ['attente', 'En attente'], ['terminee', 'Terminée'], ['neutre', 'Annulée']];
@endphp

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/taches-vues.css') }}?v={{ filemtime(public_path('assets/css/taches-vues.css')) }}">
  <link rel="stylesheet" type="text/css" href="{{asset('assets/select/select2.min.css')}}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Matrice des tâches</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item"><a href="{{ route('feuille_de_route.tache') }}">Tâches</a></li>
              <li class="breadcrumb-item active">Matrice</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      @include('feuille_de_route._vues_taches')

      <div class="vt-outils">
        <div class="mx-recherche">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" id="mx-recherche" placeholder="Rechercher une tâche, un code, un agent…" autocomplete="off">
        </div>
        <button type="button" class="vt-btn-doux" data-mx-tout="ouvrir"><i class="fa-solid fa-angles-down"></i>Tout déplier</button>
        <button type="button" class="vt-btn-doux" data-mx-tout="fermer"><i class="fa-solid fa-angles-up"></i>Tout replier</button>
        <div class="vt-legende">
          @foreach ($legende as [$tonL, $libL]) <span class="vt-leg tl-{{ $tonL }}">{{ $libL }}</span> @endforeach
        </div>
      </div>

      <div id="matrice-taches">
        @forelse ($matrice as $n)
          <section class="mx-axe ax-{{ $n['teinte'] }}">
            <a class="mx-axe__tete" href="#!" data-mx-toggle>
              <span class="mx-axe__code">{{ $n['axe']->code }}</span>
              <div class="mx-axe__titre">
                <h5>{{ $n['axe']->libelle }}</h5>
                <small>{{ $n['nbTaches'] }} tâche(s) · {{ $n['actionsCles']->count() }} action(s) clé(s)@if (! ($exerciceId ?? null) && $n['axe']->exercice) · Exercice {{ $n['axe']->exercice->annee ?? $n['axe']->exercice->libelle }}@endif</small>
              </div>
              <div class="mx-axe__stats">
                <span class="mx-mini"><i class="fa-solid fa-circle-check"></i> {{ $n['terminees'] }}/{{ $n['nbTaches'] }} terminées</span>
                @if ($n['retards'] > 0) <span class="mx-mini mx-mini--retard"><i class="fa-solid fa-triangle-exclamation"></i> {{ $n['retards'] }} en retard</span> @endif
                <div class="mx-axe__jauge">
                  <div class="vt-meter {{ $n['avancement'] > 0 ? '' : 'vt-meter--vide' }}" style="--p: {{ $n['avancement'] }}%"></div>
                  <b>{{ $n['avancement'] }}%</b>
                </div>
              </div>
              <i class="fa-solid fa-chevron-down mx-chevron"></i>
            </a>

            <div class="mx-axe__corps">
              <div class="mx-table-wrap">
                <table class="mx-table">
                  <thead>
                    <tr>
                      <th>Code</th><th>Tâche</th><th>Responsable</th><th>Pilote</th><th>Contributeurs</th>
                      <th>Échéance</th><th>Avancement</th><th>Statut</th><th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($n['actionsCles'] as $ac)
                      <tr class="mx-ligne-ac">
                        <td colspan="9"><i class="fa-solid fa-key"></i>&nbsp; {{ $ac['actionCle']->code }} — {{ $ac['actionCle']->libelle }}
                          <small>{{ $ac['nbTaches'] }} tâche(s) · {{ $ac['avancement'] }}%</small></td>
                      </tr>
                      @foreach ($ac['actionsMesurables'] as $am)
                        <tr class="mx-ligne-am">
                          <td colspan="9"><i class="fa-regular fa-clock"></i>&nbsp; {{ $am['actionMesurable']->code }} — {{ $am['actionMesurable']->libelle }}
                            <small>{{ $am['taches']->count() }} tâche(s) · {{ $am['avancement'] }}%</small></td>
                        </tr>
                        @foreach ($am['taches'] as $t)
                          @php
                            $ton = VuesTachesDonnees::ton($t);
                            $tonS = VuesTachesDonnees::tonStatut($t);
                            $resp = VuesTachesDonnees::agents($t, $roleIds['responsable']);
                            $pilotes = VuesTachesDonnees::agents($t, $roleIds['pilote']);
                            $contribs = VuesTachesDonnees::agents($t, $roleIds['contributeur']);
                            $recherche = mb_strtolower($t->code.' '.$t->libelle.' '.($t->statutExecution?->libelle ?? '').' '
                                .$resp->merge($pilotes)->merge($contribs)->map(fn ($p) => $p->nomComplet())->implode(' '));
                            $joursRetard = $ton === 'retard' ? (int) $t->date_fin->diffInDays(today()) : 0;
                          @endphp
                          <tr class="mx-ligne-tache tl-{{ $ton }}" id="row-{{ $t->id }}" data-recherche="{{ $recherche }}">
                            <td><a class="mx-code" href="{{ route('feuille_de_route.tache.details', $t->id) }}">{{ $t->code }}</a></td>
                            <td class="mx-lib">{{ $t->libelle }}
                              @if ($t->priorite?->libelle)<br><span class="tl-badge tl-avenir">{{ $t->priorite->libelle }}</span>@endif
                            </td>
                            <td>{!! $pile($resp) !!}</td>
                            <td>{!! $pile($pilotes) !!}</td>
                            <td>{!! $pile($contribs) !!}</td>
                            <td class="mx-echeance">{{ optional($t->date_fin)->format('d/m/Y') ?: '—' }}
                              @if ($joursRetard) <small>{{ $joursRetard }} j de retard</small> @endif</td>
                            <td>
                              <div class="mx-prog">
                                <div class="vt-meter {{ $t->avancement_pourcentage > 0 ? '' : 'vt-meter--vide' }}" style="--p: {{ $t->avancement_pourcentage }}%"></div>
                                <b>{{ $t->avancement_pourcentage }}%</b>
                              </div>
                            </td>
                            <td><span class="tl-badge tl-{{ $tonS }}">{{ $t->statutExecution?->libelle ?? '—' }}</span> @include('feuille_de_route._badge_sous_taches', ['t' => $t])</td>
                            <td>
                              <div class="mx-actions">
                                <a class="mx-btn mx-btn--voir" href="{{ route('feuille_de_route.tache.details', $t->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Détails"><i class="fa-regular fa-eye"></i></a>
                                @hasanyrole('ADMIN|DG')
                                <button class="mx-btn mx-btn--edit" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" onclick="edit_tache({{ $t->id }})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="mx-b tn mx-btn--suppr" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Supprimer" onclick="deletes_tache({{ $t->id }})"><i class="fa-solid fa-trash-can"></i></button>
                                @endhasanyrole
                              </div>
                            </td>
                          </tr>
                        @endforeach
                      @endforeach
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        @empty
          <div class="card"><div class="card-body vt-vide"><i class="fa-regular fa-folder-open"></i>Aucune tâche à afficher.</div></div>
        @endforelse

        <div class="card" id="mx-aucun" style="display:none;"><div class="card-body vt-vide"><i class="fa-solid fa-magnifying-glass"></i>Aucune tâche ne correspond à cette recherche.</div>
      @include('feuille_de_route._pagination_serveur')</div>
      </div>
    </div>

    @include('feuille_de_route.form_tache')
    @include('feuille_de_route.js_script')
  </div>
@endsection

@section('script')
  <script src="{{ asset('assets/js/taches-vues.js') }}?v={{ filemtime(public_path('assets/js/taches-vues.js')) }}"></script>

  <script src="{{asset('assets/select/select2.min.js')}}"></script>
  <script src="{{asset('assets/js/select.js')}}"></script>
@endsection
