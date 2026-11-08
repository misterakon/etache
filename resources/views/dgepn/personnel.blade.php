@extends('layout.master')
@section('title', 'Personnel')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/personnel-liste.css') }}">
@endsection

@section('main-content')
  @php
    // Une seule lecture des postes par agent : sert au tableau, aux filtres et aux compteurs
    $lignes = $personnels->map(fn ($p) => ['p' => $p, 'postes' => collect($p->postesActuels())]);
    $nbActifs = $personnels->where('actif', true)->count();
    $nbComptes = $personnels->filter(fn ($p) => $p->user)->count();
    $nbResponsables = $lignes->filter(fn ($l) => $l['postes']->isNotEmpty())->count();
    $services = $personnels->pluck('service')->filter()->unique('id')->sortBy('nom');
  @endphp

  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--bleu">
        <div>
          <p class="cr-hero__sur">DGEPN</p>
          <h1 class="cr-hero__titre">Personnel</h1>
          <p class="cr-hero__sous">Les agents, leurs postes en cours, leurs comptes et leur statut. Recherchez, filtrez et triez sans quitter la page.</p>
        </div>
        @hasanyrole('ADMIN|DG')
          <div class="cr-hero__actions">
            <button class="cr-btn cr-btn--blanc" type="button" onclick="add_form()"><i class="fa-solid fa-plus"></i> Ajouter un agent</button>
            <a class="cr-btn cr-btn--fantome" href="{{ route('rh.qualite') }}"><i class="fa-solid fa-clipboard-check"></i> Qualité des fiches</a>
            <a class="cr-btn cr-btn--fantome" href="{{ route('rh.import') }}"><i class="fa-solid fa-file-excel"></i> Import / export</a>
            <a class="cr-btn cr-btn--fantome" href="{{ route('rh.organigramme') }}"><i class="fa-solid fa-sitemap"></i> Organigramme</a>
          </div>
        @endhasanyrole
      </div>

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-users"></i></span><div><span class="cr-kpi__valeur">{{ $personnels->count() }}</span><span class="cr-kpi__libelle">Agents</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-user-check"></i></span><div><span class="cr-kpi__valeur">{{ $nbActifs }}</span><span class="cr-kpi__libelle">Actifs</span></div></div>
        <div class="cr-kpi" style="--c:#7B6BE0"><span class="cr-kpi__icone"><i class="fa-solid fa-user-lock"></i></span><div><span class="cr-kpi__valeur">{{ $nbComptes }}<small style="font-size:14px;color:#374151;"> / {{ $personnels->count() }}</small></span><span class="cr-kpi__libelle">Avec un compte</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-user-tie"></i></span><div><span class="cr-kpi__valeur">{{ $nbResponsables }}</span><span class="cr-kpi__libelle">Responsables en poste</span></div></div>
      </div>

      <div class="cr-carte">
        <div class="pe-barre">
          <div class="pe-ligne-outils">
            <div class="pe-recherche" id="pe_recherche">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="search" id="pe_q" placeholder="Rechercher un nom, un matricule, un poste, un service, un téléphone…" autocomplete="off" aria-label="Rechercher un agent">
              <kbd title="Touche de raccourci">/</kbd>
              <button type="button" class="pe-effacer" id="pe_effacer" aria-label="Effacer la recherche"><i class="fa-solid fa-xmark"></i></button>
            </div>
          </div>
          <div class="pe-ligne-outils">
            <div class="pe-filtres">
              <div class="pe-groupe"><span>Statut</span><div class="pe-pilules" data-filtre="statut"><button type="button" class="pe-actif" data-valeur="">Tous</button><button type="button" data-valeur="actif">Actifs</button><button type="button" data-valeur="inactif">Inactifs</button></div></div>
              <div class="pe-groupe"><span>Compte</span><div class="pe-pilules" data-filtre="compte"><button type="button" class="pe-actif" data-valeur="">Tous</button><button type="button" data-valeur="oui">Avec</button><button type="button" data-valeur="non">Sans</button></div></div>
              <div class="pe-groupe"><span>Poste</span><div class="pe-pilules" data-filtre="poste"><button type="button" class="pe-actif" data-valeur="">Tous</button><button type="button" data-valeur="oui">Responsables</button></div></div>
              @if ($services->isNotEmpty())
                <div class="pe-groupe"><span>Service</span>
                  <select class="pe-select" id="pe_service" aria-label="Filtrer par service"><option value="">Tous les services</option>@foreach ($services as $s)<option value="{{ $s->id }}">{{ $s->nom }}</option>@endforeach</select></div>
              @endif
              <button type="button" class="pe-reinit" id="pe_reinit"><i class="fa-solid fa-rotate-left"></i> Réinitialiser</button>
            </div>
            <div class="pe-compteur" id="pe_compteur" aria-live="polite"></div>
          </div>
        </div>

        <div class="pe-zone">
          <table class="pe-table" id="pe_table">
            <thead>
              <tr>
                <th data-tri="nom">Agent <i class="fa-solid fa-arrow-up pe-fleche"></i></th>
                <th>Poste et fonction</th>
                <th data-tri="service">Service <i class="fa-solid fa-arrow-up pe-fleche"></i></th>
                <th>Téléphone</th>
                <th data-tri="compte">Compte <i class="fa-solid fa-arrow-up pe-fleche"></i></th>
                <th data-tri="statut">Statut <i class="fa-solid fa-arrow-up pe-fleche"></i></th>
                @hasanyrole('ADMIN|DG')<th class="pe-col-actions">Actions</th>@endhasanyrole
              </tr>
            </thead>
            <tbody id="pe_corps">
              @foreach ($lignes as $l)
                @php
                  $personnel = $l['p'];
                  $postes = $l['postes'];
                  $recherche = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii(implode(' ', array_filter([
                      $personnel->nomComplet(), $personnel->matricule, $personnel->fonction, $personnel->service?->nom, $personnel->telephone, $personnel->email,
                      $postes->pluck('libelle')->implode(' '),
                  ]))));
                @endphp
                <tr id="row-{{ $personnel->id }}" data-fiche="{{ route('dgepn.personnel.fiche', $personnel->id) }}"
                    data-recherche="{{ $recherche }}" data-statut="{{ $personnel->actif ? 'actif' : 'inactif' }}" data-compte="{{ $personnel->user ? 'oui' : 'non' }}"
                    data-poste="{{ $postes->isNotEmpty() ? 'oui' : 'non' }}" data-service="{{ $personnel->service_id }}"
                    data-tri-nom="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($personnel->nom.' '.$personnel->prenom)) }}"
                    data-tri-service="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii((string) $personnel->service?->nom)) }}"
                    data-tri-compte="{{ $personnel->user ? 1 : 0 }}" data-tri-statut="{{ $personnel->actif ? 0 : 1 }}">
                  <td>
                    <div class="pe-agent">
                      @if ($personnel->photo_url)
                        <img class="pe-photo" src="{{ $personnel->photo_url }}" alt="" loading="lazy">
                      @else
                        <span class="rh-avatar">{{ $personnel->initiales() }}</span>
                      @endif
                      <span><b>{{ $personnel->nomComplet() }}</b><small>{{ $personnel->matricule ?: 'Sans matricule' }}</small></span>
                    </div>
                  </td>
                  <td data-label="Poste et fonction">
                    <div class="pe-postes">
                      @forelse ($postes as $poste)
                        <span class="pe-poste {{ $poste['interim'] ? 'pe-poste--interim' : '' }}" title="{{ $poste['interim'] ? 'Intérim : ' : '' }}{{ $poste['libelle'] }}">
                          <i class="fa-solid {{ $poste['interim'] ? 'fa-user-clock' : 'fa-user-tie' }}"></i>
                          <span>{{ $poste['interim'] ? 'Intérim : ' : '' }}{{ $poste['libelle'] }}</span>
                          @hasanyrole('ADMIN')
                            <button type="button" title="Mettre fin à ce poste" aria-label="Mettre fin à ce poste" onclick="event.stopPropagation(); aff_fin_ouvrir({{ $poste['id'] }}, @js($poste['libelle']), {{ $poste['interim'] ? 'true' : 'false' }}, '{{ $poste['debut_iso'] }}'); return false;"><i class="fa-solid fa-xmark"></i></button>
                          @endhasanyrole
                        </span>
                      @empty
                        <span class="pe-fonction">{{ $personnel->fonction ?: '—' }}</span>
                      @endforelse
                    </div>
                  </td>
                  <td data-label="Service"><div class="pe-service">{{ $personnel->service?->nom ?? '—' }}</div></td>
                  <td data-label="Téléphone">@if (filled($personnel->telephone))<a class="pe-tel" href="tel:{{ preg_replace('/[^0-9+]/', '', $personnel->telephone) }}" onclick="event.stopPropagation()"><i class="fa-solid fa-phone" style="font-size:11px;color:var(--cr-clair);"></i> {{ $personnel->telephone }}</a>@else<span class="c-o-light">—</span>@endif</td>
                  <td data-label="Compte">
                    @if ($personnel->user)<span class="cr-chip" style="--c:#2E7D5B"  data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="{{ $personnel->user->email }}">Oui</span>@else<span class="cr-chip" style="--c:#b31004d3">Non</span>@endif
                  </td>
                  <td data-label="Statut">
                    @if ($personnel->actif)<span class="cr-chip" style="--c:#2E7D5B">Actif</span>@else<span class="cr-chip" style="--c:#A73B34">Inactif</span>@endif
                  </td>
                  @hasanyrole('ADMIN|DG')
                    <td class="pe-cellule-actions" onclick="event.stopPropagation()">
                      <div class="pe-actions">
                        <a class="pe-bouton pe-bouton--ok" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Voir la fiche" aria-label="Voir la fiche" href="{{ route('dgepn.personnel.fiche', $personnel->id) }}"><i class="fa-regular fa-eye"></i></a>
                        <a class="pe-bouton pe-bouton--rh" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Profil RH (charge, compétences, journal)" aria-label="Profil RH" href="{{ route('rh.agent', $personnel->id) }}"><i class="fa-solid fa-gauge-high"></i></a>
                        @hasanyrole('ADMIN')
                          <button class="pe-bouton pe-bouton--affecter" type="button"  data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Affecter à un poste" onclick="aff_ouvrir({{ $personnel->id }}, @js($personnel->nomComplet()), {{ $personnel->fonction_id ?? 'null' }}, {{ $personnel->service_id ?? 'null' }})"><i class="fa-solid fa-user-tie"></i></button>
                        @endhasanyrole
                        <button class="pe-bouton pe-bouton--modifier" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Modifier" aria-label="Modifier" onclick="edit_personnel({{ $personnel->id }})"><i class="fa-regular fa-pen-to-square"></i></button>
                        @hasanyrole('ADMIN')
                          <button class="pe-bouton pe-bouton--danger" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Archiver" aria-label="Archiver" onclick="deletes_personnel({{ $personnel->id }})"><i class="fa-solid fa-box-archive"></i></button>
                        @endhasanyrole
                      </div>
                    </td>
                  @endhasanyrole
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="pe-vide" id="pe_vide"><i class="fa-regular fa-face-meh"></i><strong>Aucun agent ne correspond</strong>Modifiez la recherche ou les filtres.<button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="pe_reinitialiser()">Réinitialiser les filtres</button></div>
        </div>

        <div class="pe-pied">
          <div class="pe-groupe"><span>Par page</span>
            <select class="pe-select" id="pe_taille" style="width:auto;" aria-label="Agents par page"><option>15</option><option selected>25</option><option>50</option><option>100</option></select></div>
          <div class="pe-compteur" id="pe_plage"></div>
          <div class="pe-pages" id="pe_pages"></div>
        </div>
      </div>
    </div>

    @include('dgepn.form_personnel')
    @include('dgepn.js_script')
    @include('dgepn.js_champs_agent')
    @hasanyrole('ADMIN')
      @include('dgepn.form_affectation')
      @include('dgepn.js_affectation')
    @endhasanyrole
  </div>

  <script type="text/javascript">
	/* Liste du personnel : recherche instantanée, filtres, tri et pagination côté navigateur */
	(function () {
		const corps = document.getElementById('pe_corps');
		const lignes = Array.from(corps.querySelectorAll('tr[data-recherche]'));
		const etat = { q: '', statut: '', compte: '', poste: '', service: '', taille: 25, page: 1, tri: 'nom', sens: 1 };
		const norme = function (s) { return (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim(); };

		function filtrer() {
			const mots = norme(etat.q).split(/\s+/).filter(Boolean);
			return lignes.filter(function (tr) {
				const d = tr.dataset;
				if (etat.statut && d.statut !== etat.statut) { return false; }
				if (etat.compte && d.compte !== etat.compte) { return false; }
				if (etat.poste && d.poste !== etat.poste) { return false; }
				if (etat.service && d.service !== etat.service) { return false; }
				return mots.every(function (m) { return d.recherche.indexOf(m) >= 0; });
			});
		}

		function actifs() { return !!(etat.q || etat.statut || etat.compte || etat.poste || etat.service); }

		function pages(total, courante) {
			const liste = []; const fen = 2;
			for (let i = 1; i <= total; i++) { if (i === 1 || i === total || (i >= courante - fen && i <= courante + fen)) { liste.push(i); } else if (liste[liste.length - 1] !== '…') { liste.push('…'); } }
			return liste;
		}

		function memoriser() {
			const p = new URLSearchParams();
			['q', 'statut', 'compte', 'poste', 'service'].forEach(function (k) { if (etat[k]) { p.set(k, etat[k]); } });
			if (etat.taille !== 25) { p.set('n', etat.taille); }
			if (etat.tri !== 'nom' || etat.sens !== 1) { p.set('tri', (etat.sens === 1 ? '' : '-') + etat.tri); }
			if (etat.page > 1) { p.set('p', etat.page); }
			const s = p.toString();
			window.history.replaceState(null, '', window.location.pathname + (s ? '?' + s : ''));
		}

		function afficher() {
			const f = filtrer();
			f.sort(function (a, b) { return a.dataset['tri' + etat.tri.charAt(0).toUpperCase() + etat.tri.slice(1)].localeCompare(b.dataset['tri' + etat.tri.charAt(0).toUpperCase() + etat.tri.slice(1)], 'fr', { numeric: true }) * etat.sens; });
			const total = f.length; const nbPages = Math.max(1, Math.ceil(total / etat.taille));
			etat.page = Math.min(Math.max(1, etat.page), nbPages);
			const debut = (etat.page - 1) * etat.taille; const visibles = f.slice(debut, debut + etat.taille);
			lignes.forEach(function (tr) { tr.hidden = true; });
			visibles.forEach(function (tr) { tr.hidden = false; corps.appendChild(tr); });

			document.getElementById('pe_compteur').innerHTML = '<b>' + total + '</b> agent' + (total > 1 ? 's' : '') + (actifs() ? ' sur ' + lignes.length : '');
			document.getElementById('pe_plage').textContent = total ? ('Agents ' + (debut + 1) + ' à ' + (debut + visibles.length) + ' sur ' + total) : '';
			document.getElementById('pe_vide').style.display = total ? 'none' : 'flex';
			document.getElementById('pe_table').style.display = total ? '' : 'none';
			document.getElementById('pe_reinit').style.display = actifs() ? 'inline-flex' : 'none';
			document.getElementById('pe_recherche').classList.toggle('pe-plein', etat.q !== '');

			const nav = document.getElementById('pe_pages'); nav.innerHTML = '';
			const bouton = function (txt, p, inactif, courant) { const b = document.createElement('button'); b.type = 'button'; b.innerHTML = txt; b.disabled = !!inactif; if (courant) { b.className = 'pe-actif'; } b.onclick = function () { etat.page = p; afficher(); }; nav.appendChild(b); };
			if (nbPages > 1) {
				bouton('<i class="fa-solid fa-chevron-left"></i>', etat.page - 1, etat.page === 1);
				pages(nbPages, etat.page).forEach(function (p) { if (p === '…') { const s = document.createElement('span'); s.textContent = '…'; nav.appendChild(s); } else { bouton(String(p), p, false, p === etat.page); } });
				bouton('<i class="fa-solid fa-chevron-right"></i>', etat.page + 1, etat.page === nbPages);
			}
			document.querySelectorAll('#pe_table th[data-tri]').forEach(function (th) {
				const on = th.dataset.tri === etat.tri; th.classList.toggle('pe-tri-actif', on);
				const i = th.querySelector('.pe-fleche'); if (i) { i.className = 'fa-solid ' + (on && etat.sens === -1 ? 'fa-arrow-down' : 'fa-arrow-up') + ' pe-fleche'; }
			});
			document.querySelectorAll('.pe-pilules').forEach(function (g) { g.querySelectorAll('button').forEach(function (b) { b.classList.toggle('pe-actif', b.dataset.valeur === (etat[g.dataset.filtre] || '')); }); });
			memoriser();
		}

		window.pe_reinitialiser = function () { etat.q = etat.statut = etat.compte = etat.poste = etat.service = ''; etat.page = 1; document.getElementById('pe_q').value = ''; const s = document.getElementById('pe_service'); if (s) { s.value = ''; } afficher(); };

		/* Évènements */
		let delai = null;
		const champ = document.getElementById('pe_q');
		champ.addEventListener('input', function () { clearTimeout(delai); delai = setTimeout(function () { etat.q = champ.value; etat.page = 1; afficher(); }, 110); });
		document.getElementById('pe_effacer').addEventListener('click', function () { champ.value = ''; etat.q = ''; etat.page = 1; afficher(); champ.focus(); });
		document.getElementById('pe_reinit').addEventListener('click', pe_reinitialiser);
		document.querySelectorAll('.pe-pilules').forEach(function (g) { g.addEventListener('click', function (e) { const b = e.target.closest('button'); if (!b) { return; } etat[g.dataset.filtre] = b.dataset.valeur; etat.page = 1; afficher(); }); });
		const sel = document.getElementById('pe_service'); if (sel) { sel.addEventListener('change', function () { etat.service = sel.value; etat.page = 1; afficher(); }); }
		document.getElementById('pe_taille').addEventListener('change', function (e) { etat.taille = parseInt(e.target.value, 10); etat.page = 1; afficher(); });
		document.querySelectorAll('#pe_table th[data-tri]').forEach(function (th) { th.addEventListener('click', function () { if (etat.tri === th.dataset.tri) { etat.sens = -etat.sens; } else { etat.tri = th.dataset.tri; etat.sens = 1; } etat.page = 1; afficher(); }); });
		corps.addEventListener('click', function (e) { const tr = e.target.closest('tr[data-fiche]'); if (tr && !e.target.closest('a, button, input, select')) { window.location = tr.dataset.fiche; } });
		document.addEventListener('keydown', function (e) {
			if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName) && !document.querySelector('.modal.show')) { e.preventDefault(); champ.focus(); champ.select(); }
			if (e.key === 'Escape' && document.activeElement === champ && champ.value) { champ.value = ''; etat.q = ''; afficher(); }
		});

		/* Rétablit l'état depuis l'adresse (recherche et filtres conservés après un rechargement) */
		const u = new URLSearchParams(window.location.search);
		etat.q = u.get('q') || ''; etat.statut = u.get('statut') || ''; etat.compte = u.get('compte') || ''; etat.poste = u.get('poste') || ''; etat.service = u.get('service') || '';
		etat.taille = [15, 25, 50, 100].indexOf(parseInt(u.get('n'), 10)) >= 0 ? parseInt(u.get('n'), 10) : 25; etat.page = parseInt(u.get('p'), 10) || 1;
		const tri = u.get('tri') || 'nom'; etat.sens = tri.charAt(0) === '-' ? -1 : 1; etat.tri = tri.replace('-', '');
		champ.value = etat.q; document.getElementById('pe_taille').value = String(etat.taille); if (sel) { sel.value = etat.service; }
		afficher();
	})();
  </script>
@endsection

@section('script')
  <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
@endsection
