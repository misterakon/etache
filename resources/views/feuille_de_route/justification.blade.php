@extends('layout.master')
@section('title', 'Demandes à valider')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}">
@endsection

@php
  $ico = fn ($nom) => asset('assets/svg/icon-sprite.svg#'.$nom);
  $nomDe = fn ($u) => $u?->personnel?->nomComplet() ?? $u?->name ?? '—';
  $ecart = fn ($j) => ($j->estReport() && $j->echeance_actuelle && $j->echeance_proposee)
      ? (int) $j->echeance_actuelle->diffInDays($j->echeance_proposee) : null;
  $niveauLib = ['RD' => 'Responsable de direction', 'DG' => 'Directeur Général'];

  // Filtre venant du tableau de bord (?statut=...) : null, ou en_attente / approuvee / refusee / annulee
  $filtre = $filtre ?? null;
  $statutsLib = [
      'en_attente' => ['En attente', 'attente'],
      'approuvee' => ['Approuvée', 'terminee'],
      'refusee' => ['Refusée', 'retard'],
      'annulee' => ['Annulée', 'neutre'],
  ];
  $titresTraitees = ['approuvee' => 'Demandes approuvées', 'refusee' => 'Demandes refusées', 'annulee' => 'Demandes annulées'];
@endphp

@section('main-content')
  <div class="page-body">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12"><h3>Demandes de report d'échéance à valider</h3></div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ $ico('stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Demandes à valider</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container data-source-wrapper">
      <div class="grid grid-cols-12 card-gap">

        @if ($filtre)
          <div class="col-span-12">
            <div class="card">
              <div class="card-body" style="display:flex; align-items:center; gap:10px; padding:12px 20px;">
                <span class="c-o-light">Filtre :</span>
                <span class="tl-badge tl-{{ $statutsLib[$filtre][1] }}" style="color:#070000">{{ $statutsLib[$filtre][0] }}</span>
                <a href="{{ route('feuille_de_route.justification.index') }}" style="margin-left:auto; font-weight:600;">Tout voir <i class="fa-solid fa-xmark"></i></a>
              </div>
            </div>
          </div>
        @endif

        @if (! $filtre || $filtre === 'en_attente')
        <div class="col-span-12">
          <div class="card">
            <div class="card-header card-no-border">
              <h5 class="m-0">En attente de décision <span class="badge badge-primary ms-2 text-white">{{ $aValider->count() }}</span></h5>
            </div>
            <div class="card-body">
              <div class="table-responsive custom-scrollbar">
                <table class="display" id="data-source-1">
                  <thead>
                    <tr>
                      <th>Reçue le</th>
                      <th>Tâche</th>
                      <th>Demandeur</th>
                      <th>Type</th>
                      <th>Motif</th>
                      <th>Échéance</th>
                      <th>Décideur</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($aValider as $j)
                      @php $e = $ecart($j); @endphp
                      <tr id="row-{{ $j->id }}">
                        <td data-order="{{ $j->created_at->format('Y-m-d H:i') }}">{{ $j->created_at->format('d/m/Y') }}</td>
                        <td>
                          <a href="{{ route('feuille_de_route.tache.details', $j->tache_id) }}" ><b>{{ $j->tache->code }}</b></a>
                          <div class="c-o-light" style="color: #070000">{{ \Illuminate\Support\Str::limit($j->tache->libelle, 70) }}</div>
                          @if ($j->tache->estEnRetard()) <span class="tl-badge tl-retard tl-solid" style="color:#fff">{{ (int) $j->tache->date_fin->diffInDays(today()) }} j de retard</span> @endif
                        </td>
                        <td><b>{{ $nomDe($j->demandeur) }}</b></td>
                        <td><span class="tl-badge {{ $j->estReport() ? 'tl-attente' : 'tl-avenir' }}" style="{{ $j->estReport() ? 'color:#242323' : 'color:#000' }}">{{ $j->estReport() ? 'Report' : 'Justification' }}</span></td>
                        <td><b>{{ $motifs[$j->motif] ?? $j->motif }}</b></td>
                        <td data-order="{{ optional($j->echeance_proposee ?? $j->echeance_actuelle)->format('Y-m-d') }}" style="white-space:nowrap;">
                          @if ($j->estReport())
                            {{ $j->echeance_actuelle->format('d/m/Y') }} → <b><font color="green">{{ $j->echeance_proposee->format('d/m/Y') }}</font></b> ({{ $e > 0 ? '+' : '' }}{{ $e }} j)
                          @else
                            {{ optional($j->echeance_actuelle)->format('d/m/Y') ?: '—' }}
                          @endif
                        </td>
                        <td>{{ $niveauLib[$j->niveau_decision] ?? $j->niveau_decision }}</td>
                        <td>
                          <button class="btn btn-primary" type="button" onclick="examiner_justification(this)" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Examiner" style="width:30px; padding-left:5px; padding-right:5px;"
                                  data-url="{{ route('feuille_de_route.justification.decision', $j->id) }}"
                                  data-tache="{{ $j->tache->code }} — {{ $j->tache->libelle }}"
                                  data-demandeur="{{ $nomDe($j->demandeur) }}"
                                  data-type="{{ $j->estReport() ? 'Demande de report' : 'Justification seule' }}"
                                  data-motif="{{ $motifs[$j->motif] ?? $j->motif }}"
                                  data-echeance="{{ $j->estReport() ? $j->echeance_actuelle->format('d/m/Y').' → '.$j->echeance_proposee->format('d/m/Y').' ('.($e > 0 ? '+' : '').$e.' j)' : (optional($j->echeance_actuelle)->format('d/m/Y') ?: '—') }}"
                                  data-commentaire="{{ $j->commentaire }}"><i class="text-white fa-regular fa-eye"></i></button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @if ($aValider->isEmpty())
                <p class="text-muted text-center" style="padding:16px 0;">Aucune demande en attente.</p>
              @endif
            </div>
          </div>
        </div>

        @endif

        @if (! $filtre || $filtre !== 'en_attente')
        <div class="col-span-12">
          <div class="card">
            <div class="card-header card-no-border"><h5 class="m-0">{{ $titresTraitees[$filtre] ?? 'Dernières décisions' }} <span class="badge badge-danger ms-2 text-white">{{ $traitees->count() }}</span></h5></div>
            <div class="card-body">
              <div class="table-responsive custom-scrollbar">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ $filtre === 'annulee' ? 'Annulée le' : 'Décidée le' }}</th>
                      <th>Tâche</th>
                      <th>Demandeur</th>
                      <th>Type</th>
                      <th>Décision</th>
                      <th>Par</th>
                      <th>Commentaire</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($traitees as $j)
                      <tr>
                        <td><b>{{ optional($j->decide_le)->format('d/m/Y H:i') }}</b></td>
                        <td><a href="{{ route('feuille_de_route.tache.details', $j->tache_id) }}">{{ $j->tache->code }}</a></td>
                        <td><b>{{ $nomDe($j->demandeur) }}</b></td>
                        <td>{{ $j->estReport() ? 'Report' : 'Justification' }}</td>
                        @php [$libStatut, $tonStatut] = $statutsLib[$j->statut] ?? [$j->statut, 'neutre']; @endphp
                        <td><span class="tl-badge tl-{{ $tonStatut }}" style="color:#070000">{{ $libStatut }}</span></td>
                        <td>{{ $nomDe($j->decideur) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($j->decision_commentaire, 80) ?: '—' }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-muted text-center">{{ $filtre ? 'Aucune demande dans cette catégorie.' : 'Aucune décision pour le moment.' }}</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- Modal d'examen et de décision --}}
  <div class="modal fade panel-modal" id="modalDecision" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Examiner la demande</h4>
          <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formDecision" method="post" action="#">
          @csrf
          <div class="modal-body dark-modal">
            <p class="mb-3"><b id="decTache"></b></p>
            <ul class="mb-3">
              <li><b>Demandeur :</b> <span id="decDemandeur"></span></li>
              <li><b>Type :</b> <span id="decType"></span></li>
              <li><b>Motif :</b> <span id="decMotif"></span></li>
              <li><b>Échéance :</b> <span id="decEcheance"></span></li>
            </ul>
            <label class="form-label"><b>Commentaire du demandeur</b></label>
            <div class="alert alert-light" id="decCommentaire" style="white-space:pre-line;color:black"></div>
            <label class="form-label" for="dec_commentaire"><b>Votre commentaire</b> <small class="c-o-light">(obligatoire pour un refus)</small></label>
            <textarea class="form-control" id="dec_commentaire" name="commentaire" rows="3" maxlength="2000"></textarea>
            <div class="alert alert-danger mt-3 mb-0" id="decErreur" style="display:none;"></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Fermer</button>
            <button class="btn btn-danger text-white" type="button" data-decision="refuser" onclick="decider_justification('refuser')">Refuser</button>
            <button class="btn btn-success text-white" type="button" data-decision="approuver" onclick="decider_justification('approuver')">Approuver</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @include('feuille_de_route.js_script')
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom2.js') }}"></script>
@endsection
