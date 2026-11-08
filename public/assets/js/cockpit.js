/* Page « Pilotage » (cockpit) — public/assets/js/cockpit.js
 *
 * - Le cadre (filtres, indicateurs, onglets) est affiché une fois ; chaque onglet est chargé en AJAX (GET /cockpit/onglet/{onglet}),
 *   sans recharger la page. L'adresse du navigateur est mise à jour (bouton « Précédent » fonctionnel, lien partageable).
 * - Les graphiques (ApexCharts) sont reconstruits à chaque chargement à partir du JSON renvoyé par le serveur.
 * - Interactions : un clic sur un indicateur, un segment, une barre ou une direction ouvre la liste ou le périmètre correspondant. */
(function () {
	'use strict';

	var racine = document.getElementById('ck');
	var configEl = document.getElementById('ck_config');
	if (!racine || !configEl) { return; }

	var cfg = JSON.parse(configEl.textContent);
	var panneau = document.getElementById('ck_panneau');
	var formulaire = document.getElementById('ck_filtres');
	var selEx = document.getElementById('ck_ex');
	var selType = document.getElementById('ck_type');
	var selValeur = document.getElementById('ck_valeur');
	var selPortee = document.getElementById('ck_portee');
	var reduit = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var etat = { onglet: cfg.onglet, filtre: new URLSearchParams(location.search).get('filtre') || null };
	var graphiques = [];
	var cache = {};
	var requete = null;
	var numero = 0;

	function sombre() { return document.body.classList.contains('dark-only'); }
	function encre() { return sombre() ? '#EAF0FF' : '#1F2937'; }
	function nombre(v) { return String(v).replace('.', ','); }
	function echapper(t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; }

	// ------------------------------------------------------------ paramètres et adresse
	function parametres() {
		var p = new URLSearchParams();
		p.set('exercice_id', selEx.value);
		p.set('type', selType.value);
		if (selType.value !== 'annee' && selValeur.value) { p.set('valeur', selValeur.value); }
		p.set('portee', selPortee.value);
		return p;
	}

	// L'adresse reste propre (/cockpit) : l'état (onglet, filtre, période, périmètre) est gardé dans l'historique et la session
	function etatActuel(onglet, filtre) {
		return { onglet: onglet, filtre: filtre || null, exercice_id: selEx.value, type: selType.value, valeur: selValeur.value || '', portee: selPortee.value };
	}

	function memoriser(onglet, filtre, remplacer) {
		var e = etatActuel(onglet, filtre);
		try { sessionStorage.setItem('ck_etat', JSON.stringify(e)); } catch (x) { /* stockage indisponible */ }
		if (remplacer) { history.replaceState(e, '', location.pathname); } else { history.pushState(e, '', location.pathname); }
	}

	function appliquerEtat(e) {
		if (e.exercice_id) { selEx.value = e.exercice_id; }
		if (e.type) { selType.value = e.type; }
		selValeur.dataset.valeur = e.valeur || '';
		remplirValeurs();
		if (e.portee) { selPortee.value = e.portee; }
	}

	// ------------------------------------------------------------ période (mois / trimestre)
	function remplirValeurs() {
		var type = selType.value;
		var bloc = document.getElementById('ck_valeur_bloc');
		var lib = document.getElementById('ck_valeur_lib');
		var courant = selValeur.dataset.valeur || '';
		selValeur.innerHTML = '';
		bloc.hidden = type === 'annee';
		if (type === 'annee') { return; }
		var n = type === 'mois' ? 12 : 4;
		lib.textContent = type === 'mois' ? 'Mois' : 'Trimestre';
		var defaut = type === 'mois' ? (new Date().getMonth() + 1) : Math.ceil((new Date().getMonth() + 1) / 3);
		for (var i = 1; i <= n; i++) {
			var o = document.createElement('option');
			o.value = i;
			o.textContent = type === 'mois' ? cfg.moisNoms[i - 1].charAt(0).toUpperCase() + cfg.moisNoms[i - 1].slice(1) : (i === 1 ? '1er' : i + 'e') + ' trimestre';
			if (String(i) === String(courant || defaut)) { o.selected = true; }
			selValeur.appendChild(o);
		}
	}

	function majContexte() {
		var type = selType.value;
		var annee = selEx.options[selEx.selectedIndex].textContent.trim();
		var periode = type === 'annee' ? 'Exercice ' + annee : (type === 'mois' ? selValeur.options[selValeur.selectedIndex].textContent + ' ' + annee : selValeur.options[selValeur.selectedIndex].textContent + ' ' + annee);
		var portee = selPortee.value === 'global' ? selPortee.options[0].textContent : selPortee.options[selPortee.selectedIndex].textContent.trim();
		document.getElementById('ck_contexte').textContent = portee + ' · ' + periode;
	}

	// ------------------------------------------------------------ indicateurs
	function animer(el, vers, decimales) {
		var de = parseFloat(el.dataset.valeurActuelle || el.textContent.replace(',', '.')) || 0;
		el.dataset.valeurActuelle = vers;
		var format = function (v) { return decimales ? nombre(v.toFixed(decimales)) : String(Math.round(v)); };
		if (reduit || de === vers) { el.textContent = format(vers); return; }
		var debut = null;
		function pas(t) {
			if (debut === null) { debut = t; }
			var k = Math.min(1, (t - debut) / 700);
			var e = 1 - Math.pow(1 - k, 3);
			el.textContent = format(de + (vers - de) * e);
			if (k < 1) { requestAnimationFrame(pas); }
		}
		requestAnimationFrame(pas);
	}

	function majKpis(k) {
		if (!k) { return; }
		var valeurs = { taches: k.taches, avancement: k.avancement, taux: k.taux, retards: k.retards, diligences_retard: k.diligences_retard, echeances: k.echeances };
		var sous = {
			taches: k.non_demarrees + ' pas encore démarrée(s)', retards: k.non_justifies + ' sans justification',
			diligences_retard: k.diligences_ouvertes + ' diligence(s) ouverte(s)'
		};
		Object.keys(valeurs).forEach(function (cle) {
			var carte = racine.querySelector('[data-kpi="' + cle + '"]');
			if (!carte || valeurs[cle] === null || valeurs[cle] === undefined) { return; }
			animer(carte.querySelector('[data-compteur]'), Number(valeurs[cle]), (cle === 'avancement' || cle === 'taux') ? 1 : 0);
			if (sous[cle]) { carte.querySelector('[data-kpi-sous]').textContent = sous[cle]; }
		});
		var barre = racine.querySelector('[data-kpi="avancement"] .ck-kpi__barre i');
		if (barre) { barre.style.width = Math.min(100, k.avancement) + '%'; }
		var nb = racine.querySelector('[data-nb-retards]');
		if (nb) { nb.textContent = k.retards; nb.toggleAttribute('data-zero', !k.retards); }
	}

	// ------------------------------------------------------------ chargement des onglets
	function detruireGraphiques() {
		graphiques.forEach(function (g) { try { g.destroy(); } catch (e) { /* déjà détruit */ } });
		graphiques = [];
	}

	function marquerOnglet(onglet) {
		racine.querySelectorAll('.ck-onglet').forEach(function (b) {
			var actif = b.dataset.onglet === onglet;
			b.classList.toggle('actif', actif);
			b.setAttribute('aria-selected', actif ? 'true' : 'false');
			b.tabIndex = actif ? 0 : -1;
		});
	}

	function erreur(message, onglet, filtre) {
		panneau.classList.remove('charge');
		panneau.setAttribute('aria-busy', 'false');
		panneau.innerHTML = '<div class="ck-erreur"><i class="fa-solid fa-triangle-exclamation"></i> ' + echapper(message) + ' <button type="button" class="ck-btn" id="ck_reessayer">Réessayer</button></div>';
		document.getElementById('ck_reessayer').addEventListener('click', function () { charger(onglet, { filtre: filtre, forcer: true, historique: false }); });
	}

	function charger(onglet, options) {
		options = options || {};
		var filtre = options.filtre !== undefined ? options.filtre : (onglet === etat.onglet ? etat.filtre : null);
		if (onglet !== 'taches') { filtre = null; }
		var p = parametres();
		if (cfg.syntheseId && onglet === 'synthese') { p.set('synthese_id', cfg.syntheseId); }
		if (filtre) { p.set('filtre', filtre); }
		var cle = onglet + '?' + p.toString();
		var mon = ++numero;

		etat.onglet = onglet;
		etat.filtre = filtre;
		marquerOnglet(onglet);

		if (options.historique !== false) {
			memoriser(onglet, filtre, options.remplacer);
		}

		if (requete) { requete.abort(); }

		if (cache[cle] && !options.forcer) {
			afficher(cache[cle], onglet, mon);
			return;
		}

		panneau.classList.add('charge');
		panneau.setAttribute('aria-busy', 'true');
		requete = typeof AbortController !== 'undefined' ? new AbortController() : null;
		fetch(cfg.urlOnglet.replace('__O__', onglet) + '?' + p.toString(), {
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', signal: requete ? requete.signal : undefined
		}).then(function (r) {
			if (r.status === 401 || r.status === 419) { location.reload(); throw new Error('session'); }
			if (!r.ok) { throw new Error('Le chargement a échoué (code ' + r.status + ').'); }
			return r.json();
		}).then(function (j) {
			cache[cle] = j;
			if (mon === numero) { afficher(j, onglet, mon); }
		}).catch(function (e) {
			if (e.name === 'AbortError' || e.message === 'session') { return; }
			if (mon === numero) { erreur(e.message || 'Impossible de charger cet onglet.', onglet, filtre); }
		});
	}

	function afficher(j, onglet) {
		detruireGraphiques();
		panneau.innerHTML = j.html;
		panneau.classList.remove('charge');
		panneau.setAttribute('aria-busy', 'false');
		if (!reduit) {
			panneau.classList.remove('entre');
			void panneau.offsetWidth;
			panneau.classList.add('entre');
		}
		majKpis(j.kpis);
		majContexte();
		construire(onglet, j.graphiques || {});
		if (onglet === 'taches' || onglet === 'agents') { initListe(); }
		if (onglet === 'synthese') { initSynthese(); }
	}

	// ------------------------------------------------------------ graphiques
	function base(type, hauteur, extra) {
		var o = {
			chart: { type: type, height: hauteur, fontFamily: 'Rubik, sans-serif', foreColor: encre(), toolbar: { show: false }, animations: { enabled: !reduit, easing: 'easeinout', speed: 700 }, zoom: { enabled: false } },
			dataLabels: { enabled: false }, legend: { fontSize: '13px', fontWeight: 600, markers: { radius: 4 }, itemMargin: { horizontal: 10 } },
			grid: { borderColor: sombre() ? '#2C3654' : '#E3EAF5', strokeDashArray: 4 }, tooltip: { theme: sombre() ? 'dark' : 'light', style: { fontSize: '13px' } }
		};
		return Object.assign(o, extra || {});
	}

	function tracer(el, options) {
		var g = new ApexCharts(el, options);
		g.render();
		graphiques.push(g);
		return g;
	}

	function couleurAvancement(v) { return v >= 70 ? '#16A34A' : (v >= 40 ? '#F59E0B' : '#E11D48'); }

	function courbe(el, d) {
		tracer(el, base('area', 330, {
			series: [{ name: 'Prévu', data: d.attendu }, { name: 'Réalisé', data: d.realise }],
			colors: ['#033074', '#16A34A'], stroke: { width: [3, 4], curve: 'smooth', dashArray: [7, 0] },
			fill: { type: ['solid', 'gradient'], opacity: [0, 1], gradient: { shadeIntensity: 1, opacityFrom: .5, opacityTo: .04, stops: [0, 95] } },
			markers: { size: 0, hover: { size: 7 } }, xaxis: { categories: d.labels, axisBorder: { show: false }, axisTicks: { show: false }, tooltip: { enabled: false } },
			yaxis: { min: 0, max: 100, tickAmount: 5, labels: { formatter: function (v) { return Math.round(v) + ' %'; } } },
			tooltip: { shared: true, intersect: false, theme: sombre() ? 'dark' : 'light', y: { formatter: function (v) { return v === null ? '—' : nombre(v) + ' %'; } } },
			legend: { position: 'top', horizontalAlign: 'right', fontSize: '13px', fontWeight: 600 }
		}));
	}

	function donut(el, d, clic) {
		var total = d.reduce(function (s, x) { return s + x.valeur; }, 0);
		var vide = total === 0;
		var g = tracer(el, base('donut', 260, {
			series: vide ? [1] : d.map(function (x) { return x.valeur; }), labels: vide ? ['Aucune tâche'] : d.map(function (x) { return x.libelle; }),
			colors: vide ? ['#CBD5E1'] : d.map(function (x) { return x.couleur; }),
			stroke: { width: 3, colors: [sombre() ? '#222B45' : '#FFFFFF'] },
			dataLabels: { enabled: !vide, formatter: function (v) { return v >= 7 ? Math.round(v) + ' %' : ''; }, style: { fontSize: '13px', fontWeight: 700, colors: ['#fff'] }, dropShadow: { enabled: true, blur: 2, opacity: .45 } },
			plotOptions: { pie: { expandOnClick: false, donut: { size: '66%', labels: { show: true, name: { show: true, fontSize: '13px', fontWeight: 600, offsetY: 18 },
				value: { show: true, fontSize: '34px', fontWeight: 700, offsetY: -14, formatter: function (v) { return v; } },
				total: { show: true, showAlways: true, label: 'tâches', fontSize: '14px', fontWeight: 600, color: encre(), formatter: function () { return total; } } } } } },
			states: { hover: { filter: { type: 'lighten', value: .06 } }, active: { filter: { type: 'none' } } },
			legend: { show: false }, tooltip: { fillSeriesColor: false, y: { formatter: function (v) { return v + ' (' + (total ? Math.round(v / total * 100) : 0) + ' %)'; } } },
			chart: { type: 'donut', height: 260, fontFamily: 'Rubik, sans-serif', foreColor: encre(), animations: { enabled: !reduit, speed: 800 }, dropShadow: { enabled: true, top: 3, blur: 7, opacity: .22 },
				events: vide || !clic ? {} : {
					dataPointSelection: function (e, ch, o) { clic(d[o.dataPointIndex]); },
					dataPointMouseEnter: function (e, ch, o) { e.target.style.cursor = 'pointer'; surligner(o.dataPointIndex); },
					dataPointMouseLeave: function () { surligner(-1); }
				} }
		}));
		return g;
	}

	function surligner(i) {
		panneau.querySelectorAll('.ck-legende button').forEach(function (b) {
			b.style.transform = Number(b.dataset.seg) === i ? 'translateX(4px)' : '';
			b.style.borderColor = Number(b.dataset.seg) === i ? getComputedStyle(b).getPropertyValue('--c') : '';
		});
	}

	function liaisonLegende(el) {
		panneau.querySelectorAll('.ck-legende button').forEach(function (b) {
			var i = Number(b.dataset.seg);
			function pointer(opa) {
				el.querySelectorAll('.apexcharts-pie-area').forEach(function (p) {
					p.style.transition = 'opacity .15s';
					p.style.opacity = opa < 0 || Number(p.getAttribute('j')) === i ? 1 : .3;
				});
			}
			b.addEventListener('mouseenter', function () { pointer(i); });
			b.addEventListener('focus', function () { pointer(i); });
			b.addEventListener('mouseleave', function () { pointer(-1); });
			b.addEventListener('blur', function () { pointer(-1); });
		});
	}

	function barresAvancement(el, axes) {
		if (!axes.length) { el.innerHTML = '<div class="ck-vide"><i class="fa-solid fa-bullseye"></i><strong>Aucun axe dans ce périmètre</strong></div>'; return; }
		tracer(el, base('bar', Math.max(260, axes.length * 52 + 50), {
			series: [{ name: 'Avancement', data: axes.map(function (a) { return a.avancement; }) }],
			colors: [function (o) { return couleurAvancement(o.value); }],
			plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 7, barHeight: '62%' } },
			dataLabels: { enabled: true, formatter: function (v) { return nombre(v) + ' %'; }, style: { fontSize: '12.5px', fontWeight: 700, colors: ['#0F1B33'] }, offsetX: 6, textAnchor: 'start' },
			xaxis: { categories: axes.map(function (a) { return a.libelle; }), min: 0, max: 100, labels: { formatter: function (v) { return Math.round(v) + ' %'; } } },
			yaxis: { labels: { maxWidth: 200, formatter: function (v) { v = String(v); return v.length > 30 ? v.slice(0, 29) + '…' : v; } } },
			legend: { show: false }, tooltip: { y: { formatter: function (v, o) { var a = axes[o.dataPointIndex]; return nombre(v) + ' % · ' + a.terminees + '/' + a.total + ' terminées'; } } },
			chart: { type: 'bar', height: Math.max(260, axes.length * 52 + 50), fontFamily: 'Rubik, sans-serif', foreColor: encre(), toolbar: { show: false }, animations: { enabled: !reduit, speed: 700 },
				events: { dataPointSelection: function (e, ch, o) { if (axes[o.dataPointIndex].id) { definirPortee('axe:' + axes[o.dataPointIndex].id); } },
					dataPointMouseEnter: function (e) { e.target.style.cursor = 'pointer'; } } }
		}));
	}

	function construire(onglet, g) {
		var zone = function (nom) { return panneau.querySelector('[data-graphe="' + nom + '"]'); };
		var el;
		if (onglet === 'ensemble') {
			if ((el = zone('courbe'))) { courbe(el, g.courbe); }
			if ((el = zone('repartition'))) {
				donut(el, g.repartition, function (seg) { charger('taches', { filtre: seg.cle }); });
				liaisonLegende(el);
			}
			if ((el = zone('axes'))) { barresAvancement(el, g.axes || []); }
		} else if (onglet === 'courrier' && g.mois) {
			if ((el = zone('courrier'))) {
				tracer(el, base('bar', 330, { series: [{ name: 'Courriers reçus', data: g.recus }, { name: 'Courriers départ', data: g.departs }], colors: ['#2563EB', '#F26A0F'],
					plotOptions: { bar: { columnWidth: '58%', borderRadius: 6 } }, xaxis: { categories: g.mois, axisBorder: { show: false } }, yaxis: { labels: { formatter: function (v) { return Math.round(v); } } },
					legend: { position: 'top', horizontalAlign: 'right' } }));
			}
			if ((el = zone('diligences'))) { donut(el, g.statuts, null); }
		} else if (onglet === 'structure') {
			if ((el = zone('directions'))) {
				var dirs = g.directions;
				tracer(el, base('bar', Math.max(300, dirs.length * 46 + 70), {
					series: [{ name: 'Terminées', data: dirs.map(function (x) { return x.terminees; }) }, { name: 'En cours', data: dirs.map(function (x) { return x.en_cours; }) },
						{ name: 'À démarrer', data: dirs.map(function (x) { return x.non_demarrees; }) }, { name: 'Bloquées / suspendues', data: dirs.map(function (x) { return x.autres; }) }],
					colors: ['#16A34A', '#2563EB', '#F59E0B', '#94A3B8'], plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '64%' } },
					chart: { type: 'bar', stacked: true, height: Math.max(300, dirs.length * 46 + 70), fontFamily: 'Rubik, sans-serif', foreColor: encre(), toolbar: { show: false }, animations: { enabled: !reduit } },
					xaxis: { categories: dirs.map(function (x) { return x.libelle; }) }, yaxis: { labels: { maxWidth: 190, formatter: function (v) { v = String(v); return v.length > 28 ? v.slice(0, 27) + '…' : v; } } },
					legend: { position: 'top', horizontalAlign: 'left' }
				}));
			}
			if ((el = zone('axes'))) {
				barresAvancement(el, g.axes.map(function (a) { return { id: a.id, libelle: a.libelle, avancement: a.avancement, terminees: a.terminees, total: a.total }; }));
			}
		} else if (onglet === 'agents') {
			if ((el = zone('charge')) && g.charge.length) {
				var ag = g.charge;
				tracer(el, base('bar', Math.max(280, ag.length * 42 + 50), {
					series: [{ name: 'Charge', data: ag.map(function (x) { return x.valeur; }) }], colors: [function (o) { return ag[o.dataPointIndex].surcharge ? '#E11D48' : '#2563EB'; }],
					plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 6, barHeight: '60%' } },
					dataLabels: { enabled: true, style: { fontSize: '12.5px', fontWeight: 700, colors: ['#fff'] } }, legend: { show: false },
					xaxis: { categories: ag.map(function (x) { return x.libelle; }) }
				}));
			} else if ((el = zone('charge'))) { el.innerHTML = '<div class="ck-vide"><i class="fa-regular fa-face-smile"></i><strong>Aucune charge en cours</strong></div>'; }
		} else if (onglet === 'evolution') {
			if ((el = zone('courbe'))) { courbe(el, g.courbe); }
			if ((el = zone('semaines'))) {
				tracer(el, base('bar', 330, { series: [{ name: 'Échéances', data: g.semaines.valeurs }], colors: ['#F26A0F'], plotOptions: { bar: { columnWidth: '56%', borderRadius: 7 } },
					dataLabels: { enabled: true, style: { fontSize: '12.5px', fontWeight: 700, colors: ['#0F1B33'] }, offsetY: -22 }, xaxis: { categories: g.semaines.labels, labels: { rotate: -35 } },
					yaxis: { labels: { formatter: function (v) { return Math.round(v); } } }, legend: { show: false } }));
			}
			if ((el = zone('evolution')) && g.evolution) {
				tracer(el, base('line', 330, { series: [{ name: 'En retard', data: g.evolution.retards }, { name: 'Non justifiés', data: g.evolution.non_justifies }], colors: ['#E11D48', '#F59E0B'],
					stroke: { width: 3, curve: 'smooth' }, markers: { size: 0, hover: { size: 6 } }, xaxis: { categories: g.evolution.labels, tickAmount: 10 },
					chart: { type: 'line', height: 330, fontFamily: 'Rubik, sans-serif', foreColor: encre(), toolbar: { show: true, tools: { download: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } }, zoom: { enabled: true } },
					legend: { position: 'bottom', horizontalAlign: 'center', offsetY: 4 }, tooltip: { shared: true, intersect: false } }));
			}
		}
	}

	// ------------------------------------------------------------ listes avec recherche + pagination (Tâches, Agents)
	function initListe() {
		var conteneur = panneau.querySelector('[data-liste]');
		if (!conteneur) { return; }
		var lignes = Array.prototype.slice.call(conteneur.querySelectorAll('[data-ligne]'));
		var taille = parseInt(conteneur.dataset.taille, 10) || 10;
		var page = 1;
		var q = '';
		var champ = panneau.querySelector('[data-liste-recherche]');
		var compte = panneau.querySelector('[data-liste-compte]');
		var pagination = panneau.querySelector('[data-liste-pagination]');

		function visibles() { return lignes.filter(function (l) { return !q || l.dataset.recherche.indexOf(q) !== -1; }); }

		function dessiner() {
			var v = visibles();
			var pages = Math.max(1, Math.ceil(v.length / taille));
			page = Math.min(page, pages);
			lignes.forEach(function (l) { l.hidden = true; });
			v.slice((page - 1) * taille, page * taille).forEach(function (l) { l.hidden = false; });
			if (compte) { compte.textContent = q ? v.length + ' sur ' + lignes.length : lignes.length + ' au total'; }
			pagination.innerHTML = '';
			if (pages <= 1) { return; }
			function bouton(texte, p, actif, off) {
				var b = document.createElement('button');
				b.type = 'button'; b.innerHTML = texte; b.disabled = !!off;
				if (actif) { b.className = 'actif'; b.setAttribute('aria-current', 'page'); }
				b.addEventListener('click', function () { page = p; dessiner(); conteneur.scrollIntoView({ block: 'nearest', behavior: reduit ? 'auto' : 'smooth' }); });
				pagination.appendChild(b);
			}
			bouton('<i class="fa-solid fa-chevron-left"></i>', page - 1, false, page === 1);
			var debut = Math.max(1, Math.min(page - 2, pages - 4));
			var fin = Math.min(pages, debut + 4);
			if (debut > 1) { bouton('1', 1, false); }
			for (var i = debut; i <= fin; i++) { bouton(String(i), i, i === page); }
			if (fin < pages) { bouton(String(pages), pages, false); }
			bouton('<i class="fa-solid fa-chevron-right"></i>', page + 1, false, page === pages);
		}

		var minuterie;
		if (champ) {
			champ.addEventListener('input', function () {
				clearTimeout(minuterie);
				minuterie = setTimeout(function () { q = champ.value.trim().toLowerCase(); page = 1; dessiner(); }, 120);
			});
		}
		dessiner();
	}

	// ------------------------------------------------------------ onglet Synthèse
	function initSynthese() {
		var bloc = document.getElementById('ck_synthese');
		if (!bloc) { return; }
		var bouton = document.getElementById('ck_syn_generer');
		var copier = document.getElementById('ck_syn_copier');
		var texte = document.getElementById('ck_syn_texte');
		var vide = document.getElementById('ck_syn_vide');
		var pied = document.getElementById('ck_syn_pied');
		var alerte = document.getElementById('ck_syn_alerte');
		var libelle = bouton.querySelector('span');

		bouton.addEventListener('click', function () {
			var p = parametres();
			p.set('forcer', '1');
			bouton.disabled = true;
			bouton.querySelector('i').className = 'fa-solid fa-spinner fa-spin';
			libelle.textContent = 'Rédaction en cours…';
			bloc.classList.add('charge');
			alerte.hidden = true;
			fetch(cfg.urlSynthese, {
				method: 'POST', credentials: 'same-origin', body: p,
				headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': cfg.jeton }
			}).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); }).then(function (x) {
				if (!x.ok || !x.j.ok) { throw new Error((x.j && x.j.message) || 'La synthèse n’a pas pu être générée.'); }
				texte.innerHTML = x.j.html; texte.hidden = false; vide.hidden = true; pied.hidden = false; copier.hidden = false;
				document.getElementById('ck_syn_pied_g').textContent = x.j.pied_gauche;
				document.getElementById('ck_syn_chip').innerHTML = '<span class="ck-puce" style="--c:' + echapper(x.j.couleur) + '">' + echapper(x.j.source_libelle) + '</span>';
				if (x.j.avertissement) { alerte.textContent = x.j.avertissement; alerte.hidden = false; }
				libelle.textContent = 'Régénérer';
				cfg.syntheseId = null;
				if (corriger && x.j.edit_url) { corriger.dataset.url = x.j.edit_url; corriger.hidden = false; document.getElementById('ck_syn_contenu').value = x.j.texte || ''; }
				Object.keys(cache).forEach(function (k) { if (k.indexOf('synthese') === 0 || k.indexOf('ensemble') === 0) { delete cache[k]; } });
			}).catch(function (e) {
				alerte.textContent = e.message; alerte.hidden = false;
				libelle.textContent = texte.hidden ? 'Générer la synthèse' : 'Régénérer';
			}).finally(function () {
				bouton.disabled = false; bloc.classList.remove('charge');
				bouton.querySelector('i').className = 'fa-solid fa-wand-magic-sparkles';
			});
		});

		var corriger = document.getElementById('ck_syn_corriger');
		var edition = document.getElementById('ck_syn_edition');
		if (corriger && edition) {
			corriger.addEventListener('click', function () { edition.hidden = false; texte.hidden = true; corriger.hidden = true; document.getElementById('ck_syn_contenu').focus(); });
			document.getElementById('ck_syn_annuler').addEventListener('click', function () { edition.hidden = true; texte.hidden = false; corriger.hidden = false; alerte.hidden = true; });
			edition.addEventListener('submit', function (ev) {
				ev.preventDefault();
				var champ = document.getElementById('ck_syn_contenu');
				if (champ.value.trim().length < 20) { alerte.textContent = 'La synthèse est trop courte.'; alerte.hidden = false; return; }
				var d = new FormData();
				d.set('contenu', champ.value);
				d.set('_token', cfg.jeton);
				bloc.classList.add('charge');
				fetch(corriger.dataset.url, { method: 'POST', credentials: 'same-origin', body: d, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': cfg.jeton } })
					.then(function (r) { if (!r.ok) { throw new Error('Enregistrement impossible (code ' + r.status + ').'); } cfg.syntheseId = null; cache = {}; charger('synthese', { filtre: null, forcer: true, historique: false }); })
					.catch(function (e) { bloc.classList.remove('charge'); alerte.textContent = e.message; alerte.hidden = false; });
			});
		}

		copier.addEventListener('click', function () {
			var t = texte.innerText;
			var fait = function () { copier.innerHTML = '<i class="fa-solid fa-check"></i> Copié'; setTimeout(function () { copier.innerHTML = '<i class="fa-regular fa-copy"></i> Copier'; }, 1800); };
			if (navigator.clipboard) { navigator.clipboard.writeText(t).then(fait); } else { fait(); }
		});
	}

	// ------------------------------------------------------------ périmètre et filtres
	function definirPortee(valeur) {
		var existe = Array.prototype.some.call(selPortee.options, function (o) { return o.value === valeur; });
		if (!existe) { return; }
		selPortee.value = valeur;
		cache = {};
		charger(etat.onglet, { filtre: etat.filtre, forcer: true });
		window.scrollTo({ top: 0, behavior: reduit ? 'auto' : 'smooth' });
	}

	function filtresChanges(id) {
		cfg.syntheseId = null;
		if (id === 'ck_type') { selValeur.dataset.valeur = ''; remplirValeurs(); }
		if (id === 'ck_ex' && selPortee.value.indexOf('axe:') === 0) { selPortee.value = 'global'; }
		cache = {};
		charger(etat.onglet, { filtre: etat.filtre, forcer: true });
	}

	['ck_ex', 'ck_type', 'ck_valeur', 'ck_portee'].forEach(function (id) {
		document.getElementById(id).addEventListener('change', function () { filtresChanges(id); });
	});

	document.getElementById('ck_actualiser').addEventListener('click', function () {
		var b = this;
		b.classList.add('tourne');
		cache = {};
		charger(etat.onglet, { filtre: etat.filtre, forcer: true, historique: false });
		setTimeout(function () { b.classList.remove('tourne'); }, 900);
	});

	// ------------------------------------------------------------ clics et clavier
	racine.addEventListener('click', function (e) {
		var cible = e.target.closest('[data-ck-onglet], [data-ck-filtre], [data-ck-portee], .ck-onglet');
		if (!cible || !racine.contains(cible)) { return; }
		if (cible.matches('.ck-onglet')) { charger(cible.dataset.onglet, { filtre: null }); return; }
		if (cible.dataset.ckPortee) { e.preventDefault(); definirPortee(cible.dataset.ckPortee); return; }
		var onglet = cible.dataset.ckOnglet || 'taches';
		if (cible.tagName === 'A' && !cible.dataset.ckOnglet) { return; }
		e.preventDefault();
		charger(onglet, { filtre: onglet === 'taches' ? (cible.dataset.ckFiltre || 'retards') : null });
		if (cible.dataset.ckOnglet) { window.scrollTo({ top: Math.max(0, racine.querySelector('.ck-onglets').offsetTop - 80), behavior: reduit ? 'auto' : 'smooth' }); }
	});

	racine.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && e.target.matches('tr[data-ck-portee]')) { definirPortee(e.target.dataset.ckPortee); return; }
		if (!e.target.matches('.ck-onglet') || (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft')) { return; }
		var liste = Array.prototype.slice.call(racine.querySelectorAll('.ck-onglet'));
		var i = liste.indexOf(e.target) + (e.key === 'ArrowRight' ? 1 : -1);
		var suivant = liste[(i + liste.length) % liste.length];
		suivant.focus();
		charger(suivant.dataset.onglet, { filtre: null });
		e.preventDefault();
	});

	window.addEventListener('popstate', function (ev) {
		var e = ev.state;
		if (!e || !e.onglet) { return; }
		appliquerEtat(e);
		cache = {};
		charger(e.onglet, { filtre: e.filtre, historique: false });
	});

	remplirValeurs();
	// Retour sur la page sans paramètre : on retrouve l'onglet et les filtres de la dernière visite
	if (!location.search) {
		try {
			var memo = JSON.parse(sessionStorage.getItem('ck_etat') || 'null');
			if (memo && memo.onglet && cfg.onglets[memo.onglet]) { appliquerEtat(memo); etat.onglet = memo.onglet; etat.filtre = memo.filtre || null; }
		} catch (x) { /* état illisible : on ignore */ }
	}
	marquerOnglet(etat.onglet);
	charger(etat.onglet, { filtre: etat.filtre, remplacer: true });
})();
