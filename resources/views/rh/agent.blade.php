@extends('layout.master')
@section('title', 'Profil RH de '.$agent->nomComplet())

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      @php
        $r = $rattachement;
        $etats = ['expire' => ['Expiré', '#A73B34'], 'bientot' => ['Bientôt expiré', '#C77D0A'], 'valide' => ['Valide', '#2E7D5B'], 'sans_date' => ['Sans date', '#8A94A6']];
        $pct = min(100, round($charge['charge'] / max(1, $charge['seuil']) * 100));
        $rouvrirCompetence = old('_form') === 'competence' && $errors->any();
        $rouvrirFormation = old('_form') === 'formation' && $errors->any();
      @endphp
      <div class="cr-hero cr-hero--ambre">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
          <span class="rh-avatar rh-avatar--grand" style="border:3px solid rgba(255,255,255,.55);">{{ $agent->initiales() }}</span>
          <div>
            <p class="cr-hero__sur">Profil RH</p>
            <h1 class="cr-hero__titre">{{ $agent->nomComplet() }}</h1>
            <p class="cr-hero__sous" style="margin-top:4px;">{{ $agent->fonction ?: 'Agent' }} · {{ $r['direction']?->nom ?? 'Non rattaché' }}@if ($r['service']) · {{ $r['service']->nom }}@endif · matricule {{ $agent->matricule ?: '—' }}</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
              @if ($agent->estArchive())<span class="cr-chip" style="--c:#fff;background:#8A94A6">Archivé le {{ $agent->archive_le->format('d/m/Y') }}</span>
              @else<span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $agent->actif ? 'Actif' : 'Inactif' }}</span>@endif
              <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $agent->user ? 'Compte '.($agent->user->actif ? 'actif' : 'désactivé') : 'Sans compte' }}</span>
              @if ($charge['surcharge'])<span class="cr-chip" style="--c:#fff;background:#A73B34">Sur-affectation</span>@endif
            </div>
          </div>
        </div>
        <div class="cr-hero__actions">
          <a class="cr-btn cr-btn--blanc" href="{{ route('dgepn.personnel.fiche', $agent->id) }}"><i class="fa-solid fa-id-card"></i> Fiche complète</a>
          @if ($estAdmin && ! $agent->estArchive())<button type="button" class="cr-btn cr-btn--fantome" onclick="rh_archiver({{ $agent->id }}, @js($agent->nomComplet()))"><i class="fa-solid fa-box-archive"></i> Archiver</button>@endif
        </div>
      </div>
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-list-check"></i></span><div><span class="cr-kpi__valeur">{{ $charge['taches'] }}</span><span class="cr-kpi__libelle">Tâches ouvertes</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $charge['retard'] }}</span><span class="cr-kpi__libelle">En retard</span></div></div>
        <div class="cr-kpi" style="--c:#7B6BE0"><span class="cr-kpi__icone"><i class="fa-solid fa-envelope-open-text"></i></span><div><span class="cr-kpi__valeur">{{ $charge['diligences'] }}</span><span class="cr-kpi__libelle">Diligences du courrier</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-clipboard-check"></i></span><div><span class="cr-kpi__valeur">{{ $charge['actions'] }}</span><span class="cr-kpi__libelle">Actions de traitement</span></div></div>
      </div>

      <div class="cr-carte">
        <div class="rh-donut-bloc" style="padding:18px 22px;">
          <div style="flex:1;">
            <p style="margin:0 0 8px;font-weight:700;color:var(--cr-texte);">Charge de travail : {{ $charge['charge'] }} élément(s) pour un seuil de {{ $charge['seuil'] }}</p>
            <span class="rh-barre" style="height:14px;--c: {{ $charge['surcharge'] ? '#A73B34' : ($pct >= 70 ? '#C77D0A' : '#2E7D5B') }}"><span style="width: {{ $pct }}%"></span></span>
            @if ($charge['surcharge'])<p style="margin:10px 0 0;color:#8a2c26;font-weight:600;"><i class="fa-solid fa-triangle-exclamation"></i> Sur-affectation : pensez à rééquilibrer ou à réaffecter des éléments.@if ($superieur) Supérieur direct : {{ $superieur->nomComplet() }}.@endif</p>@endif
          </div>
          <div class="rh-donut" style="--p: {{ $completude['score'] }};--c: {{ $completude['score'] >= 85 ? '#2E7D5B' : ($completude['score'] >= 60 ? '#C77D0A' : '#A73B34') }};width:96px;height:96px;"><span style="font-size:19px;">{{ $completude['score'] }}%</span></div>
          <div style="max-width:300px;"><p style="margin:0 0 4px;font-weight:700;color:var(--cr-texte);">Fiche complète à {{ $completude['score'] }} %</p>@forelse ($completude['manquants'] as $m)<span class="rh-manque {{ in_array($m, $completude['essentiels'], true) ? 'rh-manque--essentiel' : '' }}">{{ $m }}</span>@empty<span class="rh-ok"><i class="fa-solid fa-circle-check"></i> Aucune information manquante</span>@endforelse</div>
        </div>
      </div>

      <div class="rh-profil" style="margin-bottom:20px;">
        {{-- Compétences --}}
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-star"></i> Compétences <span class="cs-compte cs-compte--neutre">{{ $agent->competences->count() }}</span></h2>@if ($peutModifier)<button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="rh_modal('modal_rh_competence')"><i class="fa-solid fa-plus"></i> Ajouter</button>@endif</div>
          @forelse ($agent->competences as $c)
            <div class="rh-competence">
              <div><b>{{ $c->libelle }}</b>@if ($c->commentaire)<small class="cs-aide" style="display:block;">{{ $c->commentaire }}</small>@endif</div>
              <div style="display:flex;align-items:center;gap:12px;"><span class="rh-niveau" title="{{ $c->libelleNiveau() }}">@for ($n = 1; $n <= 4; $n++)<i class="{{ $n <= $c->niveau ? 'on' : '' }}"></i>@endfor</span><span class="cs-aide" style="min-width:86px;">{{ $c->libelleNiveau() }}</span>
                @if ($peutModifier)<form method="post" action="{{ route('rh.competence.delete', $c->id) }}" onsubmit="return rh_confirmer(this, 'Retirer cette compétence ?')">@csrf @method('DELETE')<button class="cs-outil cs-outil--danger" title="Retirer"><i class="fa-regular fa-trash-can"></i></button></form>@endif</div>
            </div>
          @empty
            <div class="cs-vide"><i class="fa-regular fa-star"></i><strong>Aucune compétence renseignée</strong>Savoir-faire, outils maîtrisés, langues…</div>
          @endforelse
        </div>

        {{-- Formations --}}
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-graduation-cap"></i> Formations <span class="cs-compte cs-compte--neutre">{{ $agent->formations->count() }}</span></h2>@if ($peutModifier)<button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="rh_modal('modal_rh_formation')"><i class="fa-solid fa-plus"></i> Ajouter</button>@endif</div>
          @forelse ($agent->formations as $f)
            <div class="rh-competence">
              <div><b>{{ $f->intitule }}</b>@if ($f->certifiante) <span class="cr-chip" style="--c:#2E7D5B">Certifiante</span>@endif<small class="cs-aide" style="display:block;">{{ $f->organisme ?: 'Organisme non précisé' }}@if ($f->date_debut) · {{ $f->date_debut->format('d/m/Y') }}@if ($f->date_fin) → {{ $f->date_fin->format('d/m/Y') }}@endif @endif @if ($f->duree_heures) · {{ $f->duree_heures }} h @endif</small></div>
              @if ($peutModifier)<form method="post" action="{{ route('rh.formation.delete', $f->id) }}" onsubmit="return rh_confirmer(this, 'Retirer cette formation ?')">@csrf @method('DELETE')<button class="cs-outil cs-outil--danger" title="Retirer"><i class="fa-regular fa-trash-can"></i></button></form>@endif
            </div>
          @empty
            <div class="cs-vide"><i class="fa-solid fa-graduation-cap"></i><strong>Aucune formation renseignée</strong>Formations suivies, certifications.</div>
          @endforelse
        </div>
      </div>

      {{-- Dossier : validité --}}
      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-folder-open"></i> Dossier et validité des documents</h2><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('dgepn.personnel.fiche', $agent->id) }}">Déposer un document</a></div>
        @forelse ($documents as $d)
          <div class="rh-doc">
            <div><b>{{ $d['type'] }}</b> <span class="cs-aide">· <a href="{{ route('dgepn.personnel.dossier.apercu', $d['piece']->id) }}" target="_blank" rel="noopener">{{ $d['piece']->nom_original }}</a></span><small class="cs-aide" style="display:block;">@if ($d['info']->valide_jusqu_au)Valide jusqu'au {{ $d['info']->valide_jusqu_au->format('d/m/Y') }}@else Sans date de validité renseignée @endif</small></div>
            <span class="cr-chip" style="--c: {{ $etats[$d['etat']][1] }}">{{ $etats[$d['etat']][0] }}</span>
          </div>
        @empty
          <div class="cs-vide"><i class="fa-regular fa-folder-open"></i><strong>Aucun document dans le dossier</strong></div>
        @endforelse
        @if ($estAdmin && $documents->isNotEmpty())<div class="cs-carte-pied"><span class="cs-aide"><i class="fa-solid fa-circle-info"></i> Les dates de validité se renseignent dans <a href="{{ route('rh.dossiers') }}">Validité des dossiers</a>.</span></div>@endif
      </div>

      @if ($voirJournal)
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clock-rotate-left"></i> Journal des modifications</h2></div>
          <div class="cr-carte__corps">
            <ul class="cr-parcours">
              @forelse ($journal as $e)
                <li class="cr-parcours__item" style="--c: {{ in_array($e->evenement, ['archivage'], true) ? '#8A94A6' : (in_array($e->evenement, ['creation', 'restauration'], true) ? '#2E7D5B' : '#2B6CB0') }}">
                  <span class="cr-parcours__texte">{{ $e->resume }}</span>
                  <span class="cr-parcours__meta">{{ $e->created_at->format('d/m/Y à H:i') }}@if ($e->acteur_nom) · {{ $e->acteur_nom }}@endif</span>
                  @if (! empty($e->details['champs']) && is_array($e->details['champs']))<span class="cr-parcours__meta">@foreach ($e->details['champs'] as $champ => $valeur){{ $champ }} : {{ $valeur }}@if (! $loop->last) · @endif @endforeach</span>@endif
                </li>
              @empty
                <li class="cr-parcours__item"><span class="cr-parcours__texte">Aucune modification enregistrée.</span></li>
              @endforelse
            </ul>
          </div>
        </div>
      @endif
    </div>

    @if ($peutModifier)
      <div class="modal fade panel-modal cs-modal" id="modal_rh_competence" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirCompetence ? 1 : 0 }}">
        <div class="modal-dialog"><div class="modal-content">
          <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-star"></i> Ajouter une compétence</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
          <form method="post" action="{{ route('rh.competence.store', $agent->id) }}">
            @csrf <input type="hidden" name="_form" value="competence">
            <div class="modal-body dark-modal">
              <div class="cs-champ"><label for="rh_c_lib">Compétence <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="rh_c_lib" name="libelle" type="text" maxlength="150" value="{{ old('libelle') }}" required placeholder="ex. Gestion de projet, Excel avancé, Anglais"></div>
              <div class="cs-champ"><label for="rh_c_niv">Niveau</label><select class="form-select form-select-sm" id="rh_c_niv" name="niveau">@foreach (\App\Models\PersonnelCompetence::NIVEAUX as $k => $lib)<option value="{{ $k }}" @selected((int) old('niveau', 2) === $k)>{{ $lib }}</option>@endforeach</select></div>
              <div class="cs-champ"><label for="rh_c_com">Commentaire <span class="cs-opt">(facultatif)</span></label><input class="form-control form-control-sm" id="rh_c_com" name="commentaire" type="text" maxlength="255" value="{{ old('commentaire') }}"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Ajouter</button></div>
          </form>
        </div></div>
      </div>
      <div class="modal fade panel-modal cs-modal" id="modal_rh_formation" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirFormation ? 1 : 0 }}">
        <div class="modal-dialog"><div class="modal-content">
          <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-graduation-cap"></i> Ajouter une formation</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
          <form method="post" action="{{ route('rh.formation.store', $agent->id) }}">
            @csrf <input type="hidden" name="_form" value="formation">
            <div class="modal-body dark-modal">
              <div class="cs-champ"><label for="rh_f_int">Intitulé <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="rh_f_int" name="intitule" type="text" maxlength="200" value="{{ old('intitule') }}" required></div>
              <div class="cs-champ"><label for="rh_f_org">Organisme</label><input class="form-control form-control-sm" id="rh_f_org" name="organisme" type="text" maxlength="200" value="{{ old('organisme') }}"></div>
              <div class="cr-champs-2">
                <div class="cs-champ"><label for="rh_f_deb">Début</label><input class="form-control form-control-sm" id="rh_f_deb" name="date_debut" type="date" value="{{ old('date_debut') }}"></div>
                <div class="cs-champ"><label for="rh_f_fin">Fin</label><input class="form-control form-control-sm" id="rh_f_fin" name="date_fin" type="date" value="{{ old('date_fin') }}"></div>
              </div>
              <div class="cs-champ"><label for="rh_f_h">Durée (heures)</label><input class="form-control form-control-sm" id="rh_f_h" name="duree_heures" type="number" min="1" max="5000" value="{{ old('duree_heures') }}"></div>
              <label class="cr-interrupteur"><input type="checkbox" name="certifiante" value="1" @checked(old('certifiante'))><span class="cr-interrupteur__piste"></span><span class="cr-interrupteur__texte"><strong>Formation certifiante</strong><small>Délivre un diplôme ou une attestation reconnue.</small></span></label>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Ajouter</button></div>
          </form>
        </div></div>
      </div>
    @endif
    @if ($estAdmin && ! $agent->estArchive()) @include('rh._archiver') @endif
  </div>
  <script type="text/javascript">
	function rh_modal(id) { const m = document.getElementById(id); if (m.parentElement !== document.body) { document.body.appendChild(m); } $('#' + id).modal('show'); }
	function rh_confirmer(form, texte) { Swal.fire({ title: 'e-Tâche', text: texte, icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui', cancelButtonText: 'Annuler' }).then(function (r) { if (r.isConfirmed) { form.submit(); } }); return false; }
	window.addEventListener('load', function () { ['modal_rh_competence', 'modal_rh_formation'].forEach(function (id) { const m = document.getElementById(id); if (m && m.dataset.ouvrir === '1') { rh_modal(id); } }); });
  </script>
@endsection
