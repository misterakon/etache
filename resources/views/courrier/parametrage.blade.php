@extends('layout.master')
@section('title', 'Paramétrage du courrier')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container cr-pliable">

      <div class="cr-hero">
        <div>
          <p class="cr-hero__sur">Courrier</p>
          <h1 class="cr-hero__titre">Paramétrage du courrier</h1>
          <p class="cr-hero__sous">Types de courrier, niveaux d'urgence et délais de traitement proposés à l'imputation. La numérotation, les profils et les scans se règlent dans <a href="{{ route('reglages.index', ['module' => 'courrier']) }}" style="color:#fff;text-decoration:underline;">Réglages</a>.</p>
        </div>
      </div>

      {{-- Types de courrier --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-tags"></i> Types de courrier</h2>
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="pc_nouveau_type()"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </div>
        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Ordre</th><th>Code</th><th>Libellé</th><th>Courriers</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($types as $t)
                <tr>
                  <td>{{ $t->ordre }}</td>
                  <td><span class="cr-num">{{ $t->code }}</span></td>
                  <td><strong>{{ $t->libelle }}</strong></td>
                  <td>{{ $t->courriers_count }}</td>
                  <td>@if ($t->actif) <span class="cr-chip" style="--c:#2E7D5B">Actif</span> @else <span class="cr-chip" style="--c:#8A94A6">Inactif</span> @endif</td>
                  <td style="text-align:right;">
                    <span class="cr-actions">
                      <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" title="Modifier"
                              data-id="{{ $t->id }}" data-code="{{ $t->code }}" data-libelle="{{ $t->libelle }}" data-ordre="{{ $t->ordre }}" data-actif="{{ $t->actif ? 1 : 0 }}" onclick="pc_modifier_type(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                      <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" title="Supprimer" onclick="pc_supprimer('type', {{ $t->id }}, @js($t->libelle))"><i class="fa-solid fa-trash-can"></i></button>
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Urgences --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-bolt"></i> Urgences et délais</h2>
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="pc_nouvelle_urgence()"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </div>
        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Ordre</th><th>Urgence</th><th>Code</th><th>Délai proposé</th><th>Courriers</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($urgences as $u)
                <tr>
                  <td>{{ $u->ordre }}</td>
                  <td><span class="cr-pastille-couleur" style="--c: {{ $u->couleur }}"></span><strong>{{ $u->libelle }}</strong></td>
                  <td><span class="cr-num">{{ $u->code }}</span></td>
                  <td>{{ $u->delai_jours }} jour(s)</td>
                  <td>{{ $u->courriers_count }}</td>
                  <td>@if ($u->actif) <span class="cr-chip" style="--c:#2E7D5B">Actif</span> @else <span class="cr-chip" style="--c:#8A94A6">Inactif</span> @endif</td>
                  <td style="text-align:right;">
                    <span class="cr-actions">
                      <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" title="Modifier"
                              data-id="{{ $u->id }}" data-code="{{ $u->code }}" data-libelle="{{ $u->libelle }}" data-delai="{{ $u->delai_jours }}" data-couleur="{{ $u->couleur }}" data-ordre="{{ $u->ordre }}" data-actif="{{ $u->actif ? 1 : 0 }}" onclick="pc_modifier_urgence(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                      <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" title="Supprimer" onclick="pc_supprimer('urgence', {{ $u->id }}, @js($u->libelle))"><i class="fa-solid fa-trash-can"></i></button>
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Instructions du DG --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-list-check"></i> Instructions du Directeur Général</h2>
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="pc_nouvelle_instruction()"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </div>
        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Ordre</th><th>Instruction</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($instructions as $i)
                <tr>
                  <td>{{ $i->ordre }}</td><td><strong>{{ $i->libelle }}</strong></td>
                  <td>@if ($i->actif) <span class="cr-chip" style="--c:#2E7D5B">Active</span> @else <span class="cr-chip" style="--c:#8A94A6">Inactive</span> @endif</td>
                  <td style="text-align:right;"><span class="cr-actions">
                    <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" data-id="{{ $i->id }}" data-libelle="{{ $i->libelle }}" data-ordre="{{ $i->ordre }}" data-actif="{{ $i->actif ? 1 : 0 }}" onclick="pc_modifier_instruction(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="pc_supprimer('instruction', {{ $i->id }}, @js($i->libelle))"><i class="fa-solid fa-trash-can"></i></button>
                  </span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Délais de traitement --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-stopwatch"></i> Délais de traitement (fixés par le DG)</h2>
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="pc_nouveau_delai()"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </div>
        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Ordre</th><th>Délai</th><th>Code</th><th>Échéance</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($delais as $dl)
                <tr>
                  <td>{{ $dl->ordre }}</td><td><strong>{{ $dl->libelle }}</strong></td><td><span class="cr-num">{{ $dl->code }}</span></td>
                  <td>{{ $dl->saisie_libre ? "Date saisie à l'imputation" : $dl->delai_jours.' jour(s) après l\'imputation' }}</td>
                  <td>@if ($dl->actif) <span class="cr-chip" style="--c:#2E7D5B">Actif</span> @else <span class="cr-chip" style="--c:#8A94A6">Inactif</span> @endif</td>
                  <td style="text-align:right;"><span class="cr-actions">
                    <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" data-id="{{ $dl->id }}" data-code="{{ $dl->code }}" data-libelle="{{ $dl->libelle }}" data-jours="{{ $dl->delai_jours }}" data-libre="{{ $dl->saisie_libre ? 1 : 0 }}" data-ordre="{{ $dl->ordre }}" data-actif="{{ $dl->actif ? 1 : 0 }}" onclick="pc_modifier_delai(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="pc_supprimer('delai', {{ $dl->id }}, @js($dl->libelle))"><i class="fa-solid fa-trash-can"></i></button>
                  </span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Postes destinataires --}}
      <div class="cr-carte">
        <div class="cr-carte__tete">
          <h2 class="cr-carte__titre"><i class="fa-solid fa-user-tie"></i> Postes destinataires d'une imputation</h2>
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="pc_nouveau_poste()"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </div>
        <p class="c-o-light" style="margin:0;padding:12px 22px 0;font-size:13px;">Choisissez un poste de l'organigramme et indiquez si son titulaire actuel peut réimputer le courrier. Le titulaire ou l'intérimaire est résolu automatiquement depuis les mandats en cours.</p>
        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Ordre</th><th>Poste</th><th>Titulaire</th><th>Réimpute</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($postes as $po)
                <tr>
                  @php($titulairePoste = $po->titulaireActuel())
                  <td>{{ $po->ordre }}</td><td><strong>{{ $po->libelleActuel() }}</strong></td>
                  <td>@if ($titulairePoste) {{ $titulairePoste->nomComplet() }} @if ($po->mandatActuel()?->type === \App\Models\MandatResponsable::INTERIM)<span class="cr-chip" style="--c:#7B6BE0">Intérim</span>@endif @else <span class="cr-sans-resp"><i class="fa-solid fa-triangle-exclamation"></i> Poste vacant</span> @endif</td>
                  <td>{{ $po->peut_reimputer ? 'Oui' : 'Non' }}</td>
                  <td>@if ($po->actif) <span class="cr-chip" style="--c:#2E7D5B">Actif</span> @else <span class="cr-chip" style="--c:#8A94A6">Inactif</span> @endif</td>
                  <td style="text-align:right;"><span class="cr-actions">
                    <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" data-id="{{ $po->id }}" data-position="{{ $po->niveau.':'.($po->unite_id ?? 0) }}" data-reimpute="{{ $po->peut_reimputer ? 1 : 0 }}" data-ordre="{{ $po->ordre }}" data-actif="{{ $po->actif ? 1 : 0 }}" onclick="pc_modifier_poste(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="pc_supprimer('poste', {{ $po->id }}, @js($po->libelleActuel()))"><i class="fa-solid fa-trash-can"></i></button>
                  </span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @include('courrier.form_type_courrier')
    @include('courrier.form_urgence_courrier')
    @include('courrier.form_parametrage_imputation', ['agents' => $agents, 'positionsOrganigramme' => $positionsOrganigramme])
    @include('courrier.js_parametrage_courrier')
  </div>
@endsection
