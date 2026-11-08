@extends('layout.master')
@section('title', 'Courrier '.$courrier->numero)

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/parcours-dossier.css') }}">
@endsection

@section('main-content')
  @php
    $donneesEdition = [
        'id' => $courrier->id, 'numero' => $courrier->numero, 'origine' => $courrier->origine,
        'date_reception' => $courrier->date_reception->toDateString(), 'date_courrier' => optional($courrier->date_courrier)->toDateString(),
        'reference_courrier' => $courrier->reference_courrier, 'expediteur' => $courrier->expediteur, 'objet' => $courrier->objet,
        'type_courrier_id' => $courrier->type_courrier_id, 'urgence_id' => $courrier->urgence_id, 'confidentialite' => $courrier->confidentialite,
        'observations' => $courrier->observations, 'a_un_scan' => (bool) $scan,
        'mode_transmission' => $courrier->mode_transmission, 'mode_transmission_precision' => $courrier->mode_transmission_precision, 'nombre_pieces' => $courrier->nombre_pieces,
    ];
    $couleursParcours = ['enregistrement' => '#2E7D5B', 'modification' => '#C77D0A', 'scan' => '#2B6CB0', 'imputation' => '#7B6BE0', 'prise_en_charge' => '#2B6CB0', 'solde' => '#2E7D5B', 'annulation' => '#A73B34'];
  @endphp

  <div class="page-body">
    <div class="container">

      <div class="cr-hero">
        <div>
          <p class="cr-hero__sur"><a href="{{ route('courrier.arrivee') }}" style="color:#fff;opacity:.85;text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Registre des courriers arrivée</a></p>
          <h1 class="cr-hero__titre" style="margin-bottom:10px;"><span class="cr-num cr-num--grand">{{ $courrier->numero }}</span></h1>
          <p class="cr-hero__sous" style="margin-top:0;font-size:17px;opacity:1;font-weight:600;">{{ $courrier->objet }}</p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
            <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $courrier->libelleStatut() }}</span>
            @if ($courrier->urgence) <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $courrier->urgence->libelle }}</span> @endif
            @if ($courrier->estConfidentiel()) <span class="cr-chip cr-chip--secret" style="--c:#fff;background:#A73B34"><i class="fa-solid fa-lock"></i> Confidentiel</span> @endif
          </div>
        </div>
        <div class="cr-hero__actions">
          @if ($peutImputer)
            <button type="button" class="cr-btn cr-btn--blanc" onclick="imp_ouvrir()"><i class="fa-solid fa-paper-plane"></i> Imputer</button>
          @endif
          @if ($peutModifier)
            <button type="button" id="cr_btn_modifier" class="cr-btn cr-btn--blanc" data-courrier="{{ json_encode($donneesEdition) }}" onclick="cr_modifier(this)"><i class="fa-solid fa-pen-to-square"></i> Modifier</button>
          @endif
          <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.fiche', $courrier->id) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Fiche de transmission</a>
          @if ($scan)
            <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.scan.telecharger', $courrier->id) }}"><i class="fa-solid fa-download"></i> Scan</a>
          @endif
        </div>
      </div>

      @include('courrier._parcours_dossier', ['courrier' => $courrier])

      {{-- Mise en page en 12 colonnes : fiche 4 + visionneuse 8, puis imputation et diligences 8 + parcours 4 --}}
      <div class="grid grid-cols-12 card-gap cr-grille">
        <div class="col-span-5 md:col-span-12 cr-colonne-g">
          @if ($courrier->estConfidentiel())
            <div class="cr-alerte-conf"><i class="fa-solid fa-user-shield"></i><div><strong>Courrier confidentiel.</strong> Chaque consultation de ce courrier et de son scan est enregistrée dans le journal d'activité.</div></div>
          @endif

          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-circle-info"></i> Fiche du courrier</h2></div>
            <div class="cr-carte__corps">
              <dl class="cr-fiche">
                <div class="cr-fiche__ligne"><dt>Expéditeur</dt><dd>{{ $courrier->expediteur }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Objet</dt><dd>{{ $courrier->objet }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Type</dt><dd>{{ $courrier->type?->libelle ?? '—' }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Urgence</dt><dd>@if ($courrier->urgence) <span class="cr-chip" style="--c: {{ $courrier->urgence->couleur }}">{{ $courrier->urgence->libelle }}</span> <span class="c-o-light">· délai de traitement {{ $courrier->urgence->delai_jours }} j</span>@else — @endif</dd></div>
                <div class="cr-fiche__ligne"><dt>Mode de transmission</dt><dd>{{ $courrier->libelleModeTransmission() }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Pièces jointes</dt><dd>{{ $courrier->nombre_pieces }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Reçu le</dt><dd>{{ $courrier->date_reception->format('d/m/Y') }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Date du courrier</dt><dd>{{ optional($courrier->date_courrier)->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Référence</dt><dd>{{ $courrier->reference_courrier ?: '—' }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Enregistré par</dt><dd>{{ $courrier->enregistrePar?->name ?? '—' }} <span class="c-o-light">· {{ $courrier->libelleOrigine() }}</span></dd></div>
                @if ($courrier->observations) <div class="cr-fiche__ligne"><dt>Observations</dt><dd>{{ $courrier->observations }}</dd></div> @endif
              </dl>
            </div>
          </div>
        </div>

        <div class="col-span-7 md:col-span-12">
          @include('courrier._visionneuse', ['courrier' => $courrier, 'scan' => $scan, 'peutModifier' => $peutModifier])
        </div>

        <div class="col-span-6 md:col-span-12">
          @include('courrier._imputation', ['courrier' => $courrier, 'imputations' => $imputations, 'peutImputer' => $peutImputer])
          {{-- @include('courrier._liens_depart', ['courrier' => $courrier]) --}}

        </div>
        <div class="col-span-6 md:col-span-12">
          {{-- @include('courrier._imputation', ['courrier' => $courrier, 'imputations' => $imputations, 'peutImputer' => $peutImputer]) --}}
          @include('courrier._liens_depart', ['courrier' => $courrier])

        </div>

        <div class="col-span-12 md:col-span-12">
          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-route"></i> Parcours du courrier</h2></div>
            <div class="cr-carte__corps">
              <ul class="cr-parcours">
                @forelse ($parcours as $e)
                  <li class="cr-parcours__item" style="--c: {{ $couleursParcours[$e->evenement] ?? '#2B6CB0' }}">
                    <span class="cr-parcours__texte">{{ $e->resume }}</span>
                    <span class="cr-parcours__meta">{{ $e->created_at->format('d/m/Y à H:i') }}@if ($e->acteur_nom) · {{ $e->acteur_nom }}@endif</span>
                  </li>
                @empty
                  <li class="cr-parcours__item"><span class="cr-parcours__texte">Courrier enregistré le {{ $courrier->date_reception->format('d/m/Y') }}</span></li>
                @endforelse
              </ul>
              <p class="c-o-light" style="margin:16px 0 0;font-size:13px;"><i class="fa-regular fa-clock"></i> Imputation, traitement, réponse, expédition et archivage apparaissent ici au fil du dossier.</p>
            </div>
          </div>
        </div>
      </div>
    </div><br><br>

    @if ($peutModifier)
      @include('courrier.form_courrier')
      @include('courrier.js_courrier')
    @endif
    @if ($peutImputer)
      @include('courrier.form_imputation', ['courrier' => $courrier, 'groupes' => $groupes, 'instructions' => $instructions, 'delais' => $delais])
      @include('courrier.js_imputation')
    @endif
  </div>
@endsection
