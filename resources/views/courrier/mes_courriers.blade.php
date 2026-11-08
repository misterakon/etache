@extends('layout.master')
@section('title', 'Mes courriers à traiter')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  @php
    $ouvertes = $diligences->filter->estOuverte();
    $terminees = $diligences->reject->estOuverte();
  @endphp
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--violet">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Mes courriers à traiter</h1>
          <p class="cr-hero__sous">Les courriers qui vous sont imputés, avec leur échéance. Prenez-les en charge dès réception : c'est votre accusé de prise en charge.</p>
        </div>
        <div class="cr-hero__actions">
          <span class="cr-num cr-num--grand">{{ $ouvertes->count() }} à traiter</span>
        </div>
      </div>

      {{-- Ce qui attend l'utilisateur : visas, validations, délais, actions confiées --}}
      @if ($annulationsADecider->isNotEmpty() || $aViser->isNotEmpty() || $actionsAValider->isNotEmpty() || $delaisADecider->isNotEmpty() || $mesActions->isNotEmpty())
        <div class="cs-afaire">
          @if ($annulationsADecider->isNotEmpty())
            <div class="cs-afaire__carte" style="--c:#A73B34">
              <h3><i class="fa-solid fa-ban"></i> Annulations de réponse à décider <span class="cs-compte">{{ $annulationsADecider->count() }}</span></h3>
              @foreach ($annulationsADecider as $x)
                <div class="cs-afaire__ligne">
                  <div><b>{{ $x->courrier->numero }}</b><small>{{ \Illuminate\Support\Str::limit($x->annulation_motif, 52) }}</small></div>
                  <a class="cr-btn cr-btn--primaire cr-btn--petit" href="{{ route('courrier.diligence.show', $x->diligence_id) }}#reponses">Décider</a>
                </div>
              @endforeach
            </div>
          @endif
          @if ($aViser->isNotEmpty())
            <div class="cs-afaire__carte" style="--c:#C77D0A">
              <h3><i class="fa-solid fa-file-signature"></i> Réponses à viser ou signer <span class="cs-compte">{{ $aViser->count() }}</span></h3>
              @foreach ($aViser as $v)
                <div class="cs-afaire__ligne">
                  <div><b>{{ $v->reponse->courrier->numero }}</b><small>{{ $v->signataire ? 'À signer' : 'À viser' }} · {{ $v->libelle }}</small></div>
                  <a class="cr-btn cr-btn--primaire cr-btn--petit" href="{{ route('courrier.diligence.show', $v->reponse->diligence_id) }}#reponses">Ouvrir</a>
                </div>
              @endforeach
            </div>
          @endif
          @if ($actionsAValider->isNotEmpty())
            <div class="cs-afaire__carte" style="--c:#2B6CB0">
              <h3><i class="fa-solid fa-list-check"></i> Actions à valider <span class="cs-compte">{{ $actionsAValider->count() }}</span></h3>
              @foreach ($actionsAValider as $a)
                <div class="cs-afaire__ligne">
                  <div><b>{{ \Illuminate\Support\Str::limit($a->libelle, 48) }}</b><small>{{ $a->diligence->code }} · {{ $a->diligence->responsable?->nomComplet() ?? '—' }}</small></div>
                  <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $a->diligence_id) }}#traitement">Examiner</a>
                </div>
              @endforeach
            </div>
          @endif
          @if ($delaisADecider->isNotEmpty())
            <div class="cs-afaire__carte" style="--c:#7B6BE0">
              <h3><i class="fa-solid fa-hourglass-half"></i> Délais à décider <span class="cs-compte">{{ $delaisADecider->count() }}</span></h3>
              @foreach ($delaisADecider as $dl)
                <div class="cs-afaire__ligne">
                  <div><b>{{ $dl->diligence->code }}</b><small>{{ $dl->echeance_actuelle->format('d/m/Y') }} → {{ $dl->echeance_souhaitee->format('d/m/Y') }}</small></div>
                  <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $dl->diligence_id) }}#delais">Décider</a>
                </div>
              @endforeach
            </div>
          @endif
          @if ($mesActions->isNotEmpty())
            <div class="cs-afaire__carte" style="--c:#0F8B8D">
              <h3><i class="fa-solid fa-clipboard-check"></i> Actions qui me sont confiées <span class="cs-compte">{{ $mesActions->count() }}</span></h3>
              @foreach ($mesActions as $a)
                <div class="cs-afaire__ligne">
                  <div><b>{{ \Illuminate\Support\Str::limit($a->libelle, 48) }}</b><small>{{ $a->diligence->code }}@if ($a->echeance) · avant le {{ $a->echeance->format('d/m/Y') }}@endif</small></div>
                  <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $a->diligence_id) }}#traitement">Ouvrir</a>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @endif

      @if ($sansAgent)
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-info"></i><div>Votre compte n'est lié à aucune fiche agent : aucun courrier ne peut vous être imputé. Demandez à l'administrateur de relier votre compte à votre fiche.</div></div>
      @endif

      @if ($ouvertes->isEmpty() && ! $sansAgent)
        <div class="cr-carte"><div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Rien à traiter pour le moment</strong>Les courriers qui vous seront imputés apparaîtront ici.</div></div>
      @endif

      <div class="cr-dili-grille" data-pagine="12">
        @foreach ($ouvertes as $d)
          @php $jours = (int) today()->diffInDays($d->echeance, false); @endphp
          <div class="cr-dili {{ $d->estEnRetard() ? 'cr-dili--retard' : '' }}" style="--c: {{ $d->couleurStatut() }}">
            <div class="cr-dili__tete">
              <span class="cr-num">{{ $d->courrier->numero }}</span>
              <span class="cr-chip" style="--c: {{ $d->couleurStatut() }}">{{ $d->libelleStatut() }}</span>
            </div>
            <p class="cr-dili__objet">{{ $d->courrier->objet }}</p>
            <p class="cr-dili__meta"><i class="fa-regular fa-building"></i> {{ $d->courrier->expediteur }}</p>
            <p class="cr-dili__meta"><i class="fa-regular fa-user"></i> {{ $d->libelleRole() }} · {{ $d->destinataire_libelle }}</p>
            <p class="cr-dili__echeance">
              <i class="fa-regular fa-calendar"></i> Échéance {{ $d->echeance->format('d/m/Y') }}
              <strong>@if ($d->estEnRetard()) · en retard de {{ abs($jours) }} j @elseif ($jours === 0) · aujourd'hui @else · dans {{ $jours }} j @endif</strong>
            </p>
            <div class="cr-dili__actions">
              <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $d->id) }}">Ouvrir</a>
              @if ($d->statut === 'a_prendre_en_charge')
                <form method="post" action="{{ route('courrier.diligence.prendre', $d->id) }}" style="display:inline;">@csrf<button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-hand-holding"></i> Prendre en charge</button></form>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      @if ($terminees->isNotEmpty())
        <div class="cr-carte" style="margin-top:24px;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clock-rotate-left"></i> Transmises, traitées ou annulées</h2></div>
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Courrier</th><th>Objet</th><th>Rôle</th><th>Statut</th><th>Entre les mains de</th>  {{-- dans le thead --}}<th></th></tr></thead>
              <tbody data-pagine="10">
                @foreach ($terminees as $d)
                  <tr onclick="window.location='{{ route('courrier.diligence.show', $d->id) }}'">
                    <td><span class="cr-num">{{ $d->courrier->numero }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($d->courrier->objet, 90) }}</td>
                    <td>{{ $d->libelleRole() }}</td>
                    <td><span class="cr-chip" style="--c: {{ $d->couleurStatut() }}">{{ $d->libelleStatut() }}</span></td>
                    <td>@if ($d->statut === 'reimputee'){{ $d->suivi()['bout']->destinataire_libelle }}@endif</td>  {{-- dans la ligne --}}
                    <td>{{ optional($d->solde_le ?? $d->annule_le)->format('d/m/Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      @if ($copies->isNotEmpty())
        <div class="cr-carte" style="margin-top:24px;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-regular fa-eye"></i> Courriers reçus en copie / pour information</h2></div>
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Courrier</th><th>Objet</th><th>Expéditeur</th><th>Reçu le</th><th></th></tr></thead>
              <tbody data-pagine="10">
                @foreach ($copies as $courrierCopie)
                  <tr>
                    <td><span class="cr-num">{{ $courrierCopie->numero }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($courrierCopie->objet, 90) }}</td>
                    <td>{{ $courrierCopie->expediteur }}</td>
                    <td>{{ $courrierCopie->date_reception->format('d/m/Y') }}</td>
                    <td><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.show', $courrierCopie->id) }}"><i class="fa-regular fa-eye"></i> Lire</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      @if ($avis->isNotEmpty())
        <div class="cr-carte" style="margin-top:24px;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-regular fa-comment-dots"></i> Avis / aides demandés</h2></div>
          <div class="overflow-x-auto">
            <table class="cr-table">
              <thead><tr><th>Courrier</th><th>Objet</th><th>Diligence concernée</th><th>Échéance</th><th>Avis déposé</th><th></th></tr></thead>
              <tbody data-pagine="10">
                @foreach ($avis as $destinataireAvis)
                  @php($diligenceAvis = $destinataireAvis->diligence)
                  <tr>
                    <td><span class="cr-num">{{ $diligenceAvis->courrier->numero }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($diligenceAvis->courrier->objet, 90) }}</td>
                    <td>{{ $diligenceAvis->destinataire_libelle }}</td>
                    <td>{{ $diligenceAvis->echeance->format('d/m/Y') }}</td>
                    <td>{{ $destinataireAvis->avis->isNotEmpty() ? 'Oui' : 'En attente' }}</td>
                    <td><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $diligenceAvis->id) }}">Ouvrir</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
    <script src="{{ asset('assets/js/pagination-tableau.js') }}"></script>
  </div>
@endsection
