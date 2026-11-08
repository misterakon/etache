@extends('layout.master')
@section('title', 'Organigramme interactif')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      @php
        $icones = [\App\Models\MandatResponsable::NIVEAU_DG => 'fa-landmark', \App\Models\MandatResponsable::NIVEAU_DIRECTION => 'fa-building', \App\Models\MandatResponsable::NIVEAU_SOUS_DIRECTION => 'fa-building-columns', \App\Models\MandatResponsable::NIVEAU_SERVICE => 'fa-people-group'];
        $niveaux = [\App\Models\MandatResponsable::NIVEAU_DG => 'Direction Générale', \App\Models\MandatResponsable::NIVEAU_DIRECTION => 'Direction', \App\Models\MandatResponsable::NIVEAU_SOUS_DIRECTION => 'Sous-direction', \App\Models\MandatResponsable::NIVEAU_SERVICE => 'Service'];
        $etats = ['titulaire' => ['Titulaire', '#2E7D5B'], 'interim' => ['Intérim', '#7B6BE0'], 'sans' => ['Poste vacant', '#C77D0A'], 'inactif' => ['Titulaire inactif', '#C77D0A']];
        $conflit = session('affectation_conflit');
        $rouvrir = $conflit || $errors->has('affectation') || $errors->has('personnel_id') || $errors->has('fonction_id') || $errors->has('date_debut');
      @endphp
      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Organigramme interactif</h1>
          <p class="cr-hero__sous">Toute la hiérarchie, de la Direction Générale aux services, avec le responsable en poste, les intérims et les <strong>postes vacants</strong>. Cliquez une unité pour voir ses agents.</p>
        </div>
        {{-- <div class="cr-hero__actions"><a class="cr-btn cr-btn--fantome" href="{{ route('dgepn.responsable') }}"><i class="fa-solid fa-clock-rotate-left"></i> Historique des nominations</a></div> --}}
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      @if ($conflit)
        <div class="cr-alerte-conf" style="background:rgba(199,125,10,.1);border-color:rgba(199,125,10,.4);color:#7a4e07;"><i class="fa-solid fa-triangle-exclamation"></i><div style="flex:1;">{{ $conflit }}</div><button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="rh_confirmer_conflit()">Confirmer et appliquer</button></div>
      @endif

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-sitemap"></i></span><div><span class="cr-kpi__valeur">{{ $synthese['unites'] }}</span><span class="cr-kpi__libelle">Unités</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-user-tie"></i></span><div><span class="cr-kpi__valeur">{{ $synthese['titulaires'] }}</span><span class="cr-kpi__libelle">Titulaires en poste</span></div></div>
        <div class="cr-kpi" style="--c:#7B6BE0"><span class="cr-kpi__icone"><i class="fa-solid fa-user-clock"></i></span><div><span class="cr-kpi__valeur">{{ $synthese['interims'] }}</span><span class="cr-kpi__libelle">Intérims</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-user-slash"></i></span><div><span class="cr-kpi__valeur">{{ $synthese['sans'] + $synthese['inactifs'] }}</span><span class="cr-kpi__libelle">Postes vacants</span></div></div>
      </div>

      <div class="rh-org">
        <div class="cr-carte" style="margin-bottom:0;">
          <div class="rh-org__barre">
            <div class="cr-recherche" style="flex:1;min-width:200px;"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control form-control-sm" id="rh_q" type="search" placeholder="Unité ou responsable…" oninput="rh_filtrer()"></div>
            <label style="display:flex;align-items:center;gap:8px;margin:0;font-size:13px;font-weight:600;cursor:pointer;"><input type="checkbox" class="rh-case" id="rh_vacants" onchange="rh_filtrer()"> Postes vacants seulement</label>
            <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="rh_tout(false)"><i class="fa-solid fa-angles-up"></i> Replier</button>
            <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="rh_tout(true)"><i class="fa-solid fa-angles-down"></i> Déplier</button>
          </div>
          <div class="rh-org__arbre" id="rh_arbre">
            @foreach ($lignes as $i => $l)
              @php
                [$libEtat] = $etats[$l['etat']];
                $vacant = in_array($l['etat'], ['sans', 'inactif'], true);
              @endphp
              <div class="rh-noeud rh-noeud--{{ $l['etat'] }}" id="noeud_{{ $i }}" data-i="{{ $i }}" data-p="{{ $l['profondeur'] }}" data-vacant="{{ $vacant ? 1 : 0 }}" data-texte="{{ \Illuminate\Support\Str::lower($l['nom'].' '.($l['responsable']?->nomComplet() ?? '')) }}" style="--p: {{ $l['profondeur'] }}">
                <div class="rh-noeud__carte" onclick="rh_ouvrir({{ $i }})">
                  <button type="button" class="rh-noeud__plie" id="plie_{{ $i }}" onclick="event.stopPropagation(); rh_plier({{ $i }})" title="Replier / déplier"><i class="fa-solid fa-chevron-down"></i></button>
                  <span class="rh-noeud__icone"><i class="fa-solid {{ $icones[$l['niveau']] ?? 'fa-building' }}"></i></span>
                  <span class="rh-noeud__texte">
                    <span class="rh-noeud__nom">{{ $l['nom'] }}</span>
                    <span class="rh-noeud__qui {{ $vacant ? 'rh-noeud__qui--vacant' : '' }}">
                      @if ($vacant)<i class="fa-solid fa-user-slash"></i> {{ $l['etat'] === 'inactif' ? 'Titulaire inactif' : 'Poste vacant' }}
                      @else {{ $l['responsable']?->nomComplet() }}@if ($l['etat'] === 'interim') · intérim @endif @endif
                    </span>
                  </span>
                  <span class="rh-noeud__effectif" title="Agents rattachés à cette unité et à celles en dessous"><i class="fa-solid fa-user"></i> {{ $l['effectif'] }}</span>
                </div>
              </div>
            @endforeach
          </div>
          <div class="cr-carte__corps" style="border-top:1px solid var(--cr-bord);"><div class="rh-legende">
            @foreach ($etats as $cle => [$lib, $couleur]) @if ($cle !== 'inactif')<span><i style="background:{{ $couleur }}"></i>{{ $lib }}</span>@endif @endforeach
          </div></div>
        </div>

        <div class="cr-carte rh-panneau" id="rh_panneau" style="margin-bottom:0;">
          <div class="rh-panneau__vide"><i class="fa-solid fa-hand-pointer"></i><strong>Choisissez une unité</strong><br>Cliquez sur une carte de l'organigramme pour voir son responsable et ses agents.</div>
        </div>
      </div>
    </div>

    {{-- Fenêtre : pourvoir un poste (affectation existante : mêmes règles, historique et confirmation de remplacement) --}}
    @if ($peutAffecter)
      <div class="modal fade panel-modal cs-modal" id="modal_rh_pourvoir" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog"><div class="modal-content">
          <div class="modal-header"><h4 class="modal-title" id="rh_pourvoir_titre"><i class="fa-solid fa-user-tie"></i> Pourvoir ce poste</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
          <form id="form_rh_pourvoir" method="post" action="{{ route('dgepn.personnel.affectation') }}">
            @csrf
            <input type="hidden" name="unite_id" id="rh_p_unite" value="{{ old('unite_id') }}">
            <input type="hidden" name="rh_niveau" id="rh_p_niveau" value="{{ old('rh_niveau') }}">
            <input type="hidden" name="confirmer" id="rh_p_confirmer" value="0">
            <div class="modal-body dark-modal">
              <p id="rh_p_unite_nom" style="font-weight:700;margin:0 0 12px;"></p>
              <div class="cs-champ"><label for="rh_p_agent">Agent <span class="cs-requis">*</span></label>
                <select class="form-select form-select-sm" id="rh_p_agent" name="personnel_id" required><option value="">Choisir…</option>@foreach ($agents as $a)<option value="{{ $a->id }}">{{ $a->nomComplet() }}@if ($a->matricule) — {{ $a->matricule }}@endif</option>@endforeach</select></div>
              <div class="cs-champ"><label for="rh_p_fonction">Fonction <span class="cs-requis">*</span></label><select class="form-select form-select-sm" id="rh_p_fonction" name="fonction_id" required></select></div>
              <div class="cr-champs-2">
                <div class="cs-champ"><label for="rh_p_date">Prise de fonction <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="rh_p_date" type="date" name="date_debut" value="{{ old('date_debut', today()->toDateString()) }}" required></div>
                <div class="cs-champ"><label for="rh_p_acte">Référence de l'acte</label><input class="form-control form-control-sm" id="rh_p_acte" type="text" name="reference_acte" maxlength="100" value="{{ old('reference_acte') }}"></div>
              </div>
              <label class="cr-interrupteur"><input type="checkbox" name="interim" value="1" id="rh_p_interim" @checked(old('interim'))><span class="cr-interrupteur__piste"></span><span class="cr-interrupteur__texte"><strong>Intérim</strong><small>Le titulaire en place est conservé : l'agent assure l'intérim.</small></span></label>
              <div class="cs-info" style="margin-top:12px;"><i class="fa-solid fa-circle-info"></i><div>Si le poste est occupé, la modification n'est appliquée qu'après votre confirmation ; l'ancien titulaire n'est pas supprimé (mandat fermé la veille, historique conservé).</div></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Affecter</button></div>
          </form>
        </div></div>
      </div>
    @endif
  </div>

  <script type="text/javascript">
	const PANNEAUX = @js($panneaux);
	const FONCTIONS = @js($fonctions);
	const PEUT_AFFECTER = @js($peutAffecter);
	const ETATS = { titulaire: ['Titulaire', '#2E7D5B'], interim: ['Intérim', '#7B6BE0'], sans: ['Poste vacant', '#C77D0A'], inactif: ['Titulaire inactif', '#C77D0A'] };
	const NIVEAUX = @js($niveaux);
	const NOEUDS = Array.from(document.querySelectorAll('.rh-noeud'));
	const PROF = NOEUDS.map(function (n) { return parseInt(n.dataset.p, 10); });
	const PARENT = []; (function () { const pile = []; NOEUDS.forEach(function (n, i) { while (pile.length && PROF[pile[pile.length - 1]] >= PROF[i]) { pile.pop(); } PARENT[i] = pile.length ? pile[pile.length - 1] : -1; pile.push(i); }); })();
	const ENFANTS = NOEUDS.map(function (n, i) { return PARENT.indexOf(i) >= 0; });
	const fermes = new Set();
	let actif = -1;

	function esc(t) { const d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; }

	function rh_visibilite() {
		const q = document.getElementById('rh_q').value.trim().toLowerCase();
		const vac = document.getElementById('rh_vacants').checked;
		const filtre = q !== '' || vac;
		const visible = NOEUDS.map(function (n) { return !filtre || ((q === '' || n.dataset.texte.indexOf(q) >= 0) && (!vac || n.dataset.vacant === '1')); });
		if (filtre) { NOEUDS.forEach(function (n, i) { if (visible[i]) { let j = PARENT[i]; while (j >= 0 && !visible[j]) { visible[j] = true; j = PARENT[j]; } } }); }
		const cache = [];
		NOEUDS.forEach(function (n, i) {
			const p = PARENT[i];
			cache[i] = !filtre && p >= 0 && (cache[p] || fermes.has(p));
			n.hidden = filtre ? !visible[i] : cache[i];
			const b = document.getElementById('plie_' + i);
			b.classList.toggle('rh-noeud__plie--vide', !ENFANTS[i]);
			b.classList.toggle('rh-noeud__plie--ferme', fermes.has(i));
		});
	}
	function rh_filtrer() { rh_visibilite(); }
	function rh_plier(i) { if (fermes.has(i)) { fermes.delete(i); } else { fermes.add(i); } rh_visibilite(); }
	function rh_tout(deplier) { fermes.clear(); if (!deplier) { NOEUDS.forEach(function (n, i) { if (ENFANTS[i] && PROF[i] >= 1) { fermes.add(i); } }); } rh_visibilite(); }

	function rh_ouvrir(i) {
		if (actif >= 0) { NOEUDS[actif].classList.remove('rh-noeud--actif'); }
		actif = i; NOEUDS[i].classList.add('rh-noeud--actif');
		const p = PANNEAUX[i]; const e = ETATS[p.etat];
		let h = '<div class="cr-carte__tete"><h2 class="cr-carte__titre" style="font-size:15px;">' + esc(p.nom) + '</h2><span class="cr-chip" style="--c:' + e[1] + '">' + e[0] + '</span></div><div class="cr-carte__corps">';
		if (p.responsable) {
			const photo = p.responsable_photo
				? '<span class="rh-org__photo-cadre"><img src="' + esc(p.responsable_photo) + '" alt="Photo de ' + esc(p.responsable) + '"></span>'
				: '<span class="rh-org__photo-cadre rh-org__photo-cadre--initiales">' + esc(p.responsable_initiales || '?') + '</span>';
			h += '<div class="rh-org__occupant">' + photo + '<dl class="rh-org__occupant-infos">';
			h += '<div><dt>Responsable</dt><dd>' + esc(p.responsable) + (p.interim ? ' <span class="c-o-light">(intérim)</span>' : '') + '</dd></div>';
			h += '<div><dt>Email</dt><dd>' + (p.responsable_email ? '<a href="mailto:' + esc(p.responsable_email) + '">' + esc(p.responsable_email) + '</a>' : 'Non renseigné') + '</dd></div>';
			h += '<div><dt>Depuis le</dt><dd>' + esc(p.depuis || '—') + '</dd></div></dl></div>';
		} else {
			h += '<p class="rh-org__occupant-vacant"><i class="fa-solid fa-user-slash"></i> Aucun responsable : poste vacant</p>';
		}
		h += '<p class="cs-aide" style="margin-bottom:10px;">' + esc(NIVEAUX[p.niveau] || '') + ' · ' + p.effectif + ' agent(s) rattaché(s)</p>';
		if (!p.responsable) { h += '<dl class="cr-fiche"><div class="cr-fiche__ligne"><dt>Responsable</dt><dd><b style="color:#8a5605">Aucun : poste vacant</b></dd></div></dl>'; }
		h += '<div style="display:flex;gap:8px;flex-wrap:wrap;margin:14px 0;">';
		h += '<button type="button" class="cr-btn cr-btn--petit" style="color:#000; background-color:#db4500" data-rh-historique="1" data-niveau="' + esc(p.niveau) + '" data-unite-id="' + (p.unite_id === null ? '' : Number(p.unite_id)) + '" data-nom="' + esc(p.nom) + '"><i class="fa-solid fa-clock-rotate-left"></i> Historique</button>';
		if (p.responsable_id) { h += '<a class="cr-btn btn-success cr-btn--petit" style="color:#000" href="' + "{{ route('dgepn.personnel.fiche', ':id') }}".replace(':id', p.responsable_id) + '"><i class="fa-solid fa-eye"></i> Fiche du responsable</a>'; }
		if (PEUT_AFFECTER && FONCTIONS[p.niveau]) { h += '<button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="rh_pourvoir(' + i + ')"><i class="fa-solid fa-user-tie"></i> ' + (p.responsable ? 'Changer de titulaire' : 'Pourvoir ce poste') + '</button>'; }
		h += '</div>';
		if (p.membres.length) {
			h += '<p class="cs-section" style="margin-top:6px;">Agents (' + p.membres.length + ')</p><ul class="rh-membres">';
			p.membres.forEach(function (m) { h += '<li><span class="rh-avatar" style="width:30px;height:30px;font-size:11px;">' + esc((m.nom.split(' ').map(function (x) { return x[0]; }).join('').substring(0, 2)).toUpperCase()) + '</span><span style="flex:1;"><a href="' + m.url + '">' + esc(m.nom) + '</a>' + (m.fonction ? '<small class="cs-aide" style="display:block;">' + esc(m.fonction) + '</small>' : '') + '</span></li>'; });
			h += '</ul>';
		} else { h += '<p class="cs-aide">Aucun agent directement rattaché à cette unité.</p>'; }
		document.getElementById('rh_panneau').innerHTML = h + '</div>';
		const historique = document.querySelector('#rh_panneau [data-rh-historique]');
		if (historique) { historique.addEventListener('click', function () { voir_historique(this.dataset.niveau, this.dataset.uniteId || null, this.dataset.nom); }); }
	}

	function rh_pourvoir(i, valeurs) {
		const m = document.getElementById('modal_rh_pourvoir'); if (!m) { return; }
		if (m.parentElement !== document.body) { document.body.appendChild(m); }
		const p = PANNEAUX[i];
		document.getElementById('rh_p_unite').value = p.unite_id || '';
		document.getElementById('rh_p_niveau').value = p.niveau;
		document.getElementById('rh_p_confirmer').value = '0';
		document.getElementById('rh_p_unite_nom').textContent = p.nom + (p.responsable ? ' (actuellement : ' + p.responsable + ')' : ' (vacant)');
		const f = document.getElementById('rh_p_fonction'); f.innerHTML = '';
		(FONCTIONS[p.niveau] || []).forEach(function (x) { const o = document.createElement('option'); o.value = x.id; o.textContent = x.libelle; f.appendChild(o); });
		if (valeurs) { ['personnel_id', 'fonction_id', 'date_debut', 'reference_acte'].forEach(function (k) { const el = m.querySelector('[name="' + k + '"]'); if (el && valeurs[k]) { el.value = valeurs[k]; } }); document.getElementById('rh_p_interim').checked = !!valeurs.interim; }
		$('#modal_rh_pourvoir').modal('show');
	}
	function rh_confirmer_conflit() { document.getElementById('rh_p_confirmer').value = '1'; document.getElementById('form_rh_pourvoir').submit(); }

	window.addEventListener('load', function () {
		rh_visibilite();
		@if ($rouvrir)
		const anciennes = @js(old());
		const k = PANNEAUX.findIndex(function (p) { return p.niveau === anciennes.rh_niveau && String(p.unite_id || '') === String(anciennes.unite_id || ''); });
		if (k >= 0) { rh_ouvrir(k); rh_pourvoir(k, anciennes); }
		@endif
	});
  </script>
  @include('dgepn.form_responsable')
  @include('dgepn.js_responsable')
@endsection
