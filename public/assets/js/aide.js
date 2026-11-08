/* Aide d'e-Tâche (public/assets/js/aide.js)
 *  1. tiroir d'aide « ? » (F1) avec l'aide de la page en cours et une recherche dans le manuel ;
 *  2. recherche de l'en-tête dans toute l'application (touche « / ») ;
 *  3. recherche du sommaire du manuel ;
 *  4. visite guidée : lancée seule à la première connexion, relançable depuis le tiroir. */
(function () {
	'use strict';

	var tiroir = document.getElementById('aide-tiroir');
	if (!tiroir) { return; }
	var fond = document.getElementById('aide-fond');
	var corps = document.getElementById('aide-tiroir-corps');
	var champAide = document.getElementById('aide-tiroir-q');
	var reduit = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
	function surligner(texte, mots) {
		var t = esc(texte);
		mots.forEach(function (m) { if (m.length > 1) { t = t.replace(new RegExp('(' + esc(m).replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig'), '<mark>$1</mark>'); } });
		return t;
	}

	/* ------------------------------------------------------------ 1. tiroir d'aide */
	var charge = false;
	var accueil = '';
	var minuteur = null;
	var numero = 0;

	function ouvrir() {
		tiroir.classList.add('is-ouvert');
		fond.classList.add('is-ouvert');
		tiroir.setAttribute('aria-hidden', 'false');
		if (!charge) {
			fetch(tiroir.dataset.contexte + '?page=' + encodeURIComponent(tiroir.dataset.page || ''), { credentials: 'same-origin' })
				.then(function (r) { return r.text(); })
				.then(function (h) {
					corps.innerHTML = h; accueil = h; charge = true;
					var c = corps.firstElementChild;
					if (c && c.dataset.titre) {
						document.getElementById('aide-tiroir-titre').innerHTML = esc(c.dataset.titre) + '<small>Aide de la page en cours</small>';
						document.getElementById('aide-tiroir-lien').href = c.dataset.lien;
					}
				})
				.catch(function () { corps.innerHTML = '<div class="aide-vide">Impossible de charger l’aide. Réessayez.</div>'; });
		}
		setTimeout(function () { champAide.focus(); }, 350);
	}

	function fermer() {
		tiroir.classList.remove('is-ouvert');
		fond.classList.remove('is-ouvert');
		tiroir.setAttribute('aria-hidden', 'true');
	}

	document.addEventListener('click', function (e) {
		if (e.target.closest('#aide-ouvrir, [data-aide-ouvrir]')) { e.preventDefault(); ouvrir(); }
		if (e.target.closest('[data-aide-visite]')) { e.preventDefault(); fermer(); lancerVisite(); }
	});
	document.getElementById('aide-fermer').addEventListener('click', fermer);
	fond.addEventListener('click', fermer);

	document.addEventListener('keydown', function (e) {
		var saisie = /^(INPUT|TEXTAREA|SELECT)$/.test((document.activeElement || {}).tagName || '') || (document.activeElement || {}).isContentEditable;
		if (e.key === 'F1') { e.preventDefault(); if (tiroir.classList.contains('is-ouvert')) { fermer(); } else { ouvrir(); } }
		if (e.key === 'Escape' && tiroir.classList.contains('is-ouvert')) { fermer(); }
		if (e.key === '/' && !saisie && !e.ctrlKey && !e.metaKey) {
			var rg = document.getElementById('aide-q') || document.getElementById('rg-q');
			if (rg) { e.preventDefault(); rg.focus(); }
		}
	});

	function resultatsAide(r) { return '<a class="aide-c-' + esc(r.couleur) + '" href="' + esc(r.url) + '"><span class="aide-resultat__ico"><i class="' + esc(r.icone) + '"></i></span><div><b>' + esc(r.chapitre) + ' › ' + esc(r.section) + '</b><small>' + r.extrait + '</small></div></a>'; }

	champAide.addEventListener('input', function () {
		clearTimeout(minuteur);
		minuteur = setTimeout(function () {
			var v = champAide.value.trim();
			if (v.length < 2) { corps.innerHTML = accueil; return; }
			var k = ++numero;
			fetch(tiroir.dataset.recherche + '?q=' + encodeURIComponent(v), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
				.then(function (r) { return r.json(); })
				.then(function (j) {
					if (k !== numero) { return; }
					corps.innerHTML = j.resultats.length
						? '<div class="aide-liste-chap">' + j.resultats.map(resultatsAide).join('') + '</div>'
						: '<div class="aide-vide"><i class="fa-regular fa-face-frown" style="font-size:26px"></i><p>Aucun résultat pour « ' + esc(v) + ' ».</p></div>';
				});
		}, 220);
	});

	/* ------------------------------------------------------------ 2. recherche de l'en-tête */
	(function () {
		var q = document.getElementById('rg-q');
		var liste = document.getElementById('rg-resultats');
		if (!q || !liste) { return; }
		var t = null;
		var n = 0;
		var courant = -1;

		function fermerListe() { liste.hidden = true; courant = -1; }
		function liens() { return Array.prototype.slice.call(liste.querySelectorAll('.rg-lien')); }
		function marquer(i) {
			var l = liens();
			l.forEach(function (a, j) { a.classList.toggle('actif', j === i); });
			courant = i;
			if (l[i]) { l[i].scrollIntoView({ block: 'nearest' }); }
		}

		q.addEventListener('input', function () {
			clearTimeout(t);
			var v = q.value.trim();
			if (v.length < 2) { fermerListe(); return; }
			t = setTimeout(function () {
				var k = ++n;
				fetch(tiroir.dataset.suggestions + '?q=' + encodeURIComponent(v), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
					.then(function (r) { return r.json(); })
					.then(function (j) {
						if (k !== n) { return; }
						var mots = v.split(/\s+/);
						var html = j.rubriques.map(function (r) {
							return '<div class="rg-rub"><i class="fa-solid ' + esc(r.icone) + '"></i>' + esc(r.titre) + (r.nb > r.items.length ? ' · ' + r.nb : '') + '</div>'
								+ r.items.map(function (it) { return '<a class="rg-lien" href="' + esc(it.url) + '"><b>' + surligner(it.titre, mots) + '</b><small>' + esc(it.sous) + '</small></a>'; }).join('');
						}).join('');
						liste.innerHTML = (html || '<div class="rg-vide">Aucun résultat pour « ' + esc(v) + ' ».</div>')
							+ '<div class="rg-pied"><span>↑ ↓ pour choisir · Entrée pour ouvrir</span><a href="' + esc(tiroir.dataset.pageRecherche) + '?q=' + encodeURIComponent(v) + '">Tous les résultats</a></div>';
						liste.hidden = false;
						courant = -1;
					});
			}, 200);
		});

		q.addEventListener('keydown', function (e) {
			var l = liens();
			if (e.key === 'ArrowDown') { e.preventDefault(); marquer(Math.min(l.length - 1, courant + 1)); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); marquer(Math.max(0, courant - 1)); }
			else if (e.key === 'Enter' && courant >= 0 && l[courant]) { e.preventDefault(); window.location.href = l[courant].href; }
			else if (e.key === 'Escape') { fermerListe(); q.blur(); }
		});
		document.addEventListener('click', function (e) { if (!e.target.closest('#rg')) { fermerListe(); } });
	})();

	/* ------------------------------------------------------------ 3. recherche du sommaire du manuel */
	(function () {
		var q = document.getElementById('aide-q');
		var liste = document.getElementById('aide-resultats');
		if (!q || !liste) { return; }
		var t = null;
		var n = 0;
		q.addEventListener('input', function () {
			clearTimeout(t);
			var v = q.value.trim();
			if (v.length < 2) { liste.hidden = true; return; }
			t = setTimeout(function () {
				var k = ++n;
				fetch(tiroir.dataset.recherche + '?q=' + encodeURIComponent(v), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
					.then(function (r) { return r.json(); })
					.then(function (j) {
						if (k !== n) { return; }
						liste.innerHTML = j.resultats.length ? '<div class="aide-liste-chap">' + j.resultats.map(resultatsAide).join('') + '</div>'
							: '<div class="rg-vide">Aucun résultat pour « ' + esc(v) + ' ».</div>';
						liste.hidden = false;
					});
			}, 200);
		});
		document.addEventListener('click', function (e) { if (!e.target.closest('#aide-recherche')) { liste.hidden = true; } });
	})();

	/* ------------------------------------------------------------ 4. visite guidée */
	var etapeCourante = 0;
	var etapes = [];
	var voile = null;
	var spot = null;
	var carte = null;

	function construireEtapes() {
		var nom = tiroir.dataset.nom ? ' ' + tiroir.dataset.nom : '';
		var tout = [
			{ cible: null, icone: 'fa-hand-sparkles', titre: 'Bienvenue' + nom + ' !', texte: 'Voici une visite de 1 minute pour découvrir e-Tâche : le menu, la recherche, l’aide et votre espace. Vous pouvez la quitter à tout moment.' },
			{ cible: '.sidebar-wrapper', icone: 'fa-bars', titre: 'Le menu principal', texte: 'Toutes les rubriques sont ici. Vous ne voyez que celles qui correspondent à votre profil.' },
			{ cible: { menu: 'Tableau de bord' }, icone: 'fa-house', titre: 'Tableau de bord', texte: 'Votre page d’accueil : l’état de vos tâches, les retards, les priorités du moment et le budget.' },
			{ cible: { menu: 'Pilotage' }, icone: 'fa-chart-pie', titre: 'Pilotage', texte: 'Pour la direction : l’avancement, les retards, les directions, le courrier et la synthèse rédigée, avec des exports Excel et PDF.' },
			{ cible: { menu: 'Feuille de route' }, icone: 'fa-list-check', titre: 'Feuille de route', texte: 'Vos tâches, leurs sous-tâches et livrables, les alertes et les demandes de justification de retard.' },
			{ cible: '#rg', icone: 'fa-magnifying-glass', titre: 'Rechercher partout', texte: 'Tapez un code de tâche, un nom d’agent, un courrier ou une page. Appuyez sur la touche « / » pour y aller directement.' },
			{ cible: '#aide-ouvrir', icone: 'fa-circle-question', titre: 'L’aide de chaque page', texte: 'Ce bouton « ? » (ou la touche F1) explique la page où vous êtes. Le manuel complet est aussi disponible.' },
			{ cible: '.nav-menus li.onhover-dropdown:not(.profile-nav)', icone: 'fa-bell', titre: 'Vos notifications', texte: 'Alertes de retard, demandes à traiter, courriers imputés : tout ce qui vous concerne arrive ici.' },
			{ cible: '.profile-nav', icone: 'fa-user', titre: 'Votre compte', texte: 'Votre fiche, vos tâches, le verrouillage de l’écran et la déconnexion. L’écran se verrouille tout seul après un temps d’inactivité.' },
			{ cible: null, icone: 'fa-flag-checkered', titre: 'C’est à vous !', texte: 'Pour revoir cette visite : « ? » puis « Visite guidée ». Bon travail avec e-Tâche.' }
		];
		return tout.filter(function (e) { return !e.cible || cibleVisible(e.cible); });
	}

	/* La cible est un sélecteur CSS, ou { menu: 'Libellé' } : une rubrique de premier niveau du menu principal */
	function elements(cible) {
		if (typeof cible === 'string') { return document.querySelectorAll(cible); }
		return Array.prototype.filter.call(document.querySelectorAll('.sidebar-wrapper li.sidebar-list > a'), function (a) { return a.textContent.replace(/\s+/g, ' ').trim() === cible.menu; });
	}

	function cibleVisible(sel) {
		var els = elements(sel);
		for (var i = 0; i < els.length; i++) { var r = els[i].getBoundingClientRect(); if (r.width > 0 && r.height > 0) { return els[i]; } }
		return null;
	}

	var cibleCourante = null;

	/* Replace le halo et la carte d'après la position ACTUELLE de la cible (le menu est collé en haut : sa position change au défilement) */
	function placer() {
		if (!carte) { return; }
		var r = null;
		if (cibleCourante) {
			r = cibleCourante.getBoundingClientRect();
			spot.className = 'vg-spot';
			spot.style.cssText = 'top:' + (r.top - 6) + 'px;left:' + (r.left - 6) + 'px;width:' + (r.width + 12) + 'px;height:' + (r.height + 12) + 'px';
		} else {
			spot.className = 'vg-spot vg-spot--plein';
			spot.style.cssText = 'top:50%;left:50%;width:0;height:0';
		}
		positionnerCarte(r);
	}

	function collee(el) {
		for (var n = el; n && n !== document.body; n = n.parentElement) {
			var p = getComputedStyle(n).position;
			if (p === 'fixed' || p === 'sticky') { return true; }
		}
		return false;
	}

	function afficherEtape() {
		var e = etapes[etapeCourante];
		var cible = e.cible ? cibleVisible(e.cible) : null;
		if (e.cible && !cible) { suivante(1); return; }
		cibleCourante = cible;
		if (cible && !collee(cible)) { cible.scrollIntoView({ block: 'center', behavior: 'auto' }); }
		var dernier = etapeCourante === etapes.length - 1;
		carte.innerHTML = '<button type="button" class="vg-passer" data-vg="passer">' + (dernier ? '' : 'Passer la visite') + '</button>'
			+ '<div class="vg-carte__ico"><i class="fa-solid ' + e.icone + '"></i></div><h3>' + esc(e.titre) + '</h3><p>' + esc(e.texte) + '</p>'
			+ '<div class="vg-pied"><div class="vg-points">' + etapes.map(function (_, i) { return '<i class="' + (i === etapeCourante ? 'actif' : '') + '"></i>'; }).join('') + '</div>'
			+ '<div class="vg-boutons">' + (etapeCourante > 0 ? '<button type="button" class="vg-btn" data-vg="prec">Précédent</button>' : '') + '<button type="button" class="vg-btn vg-btn--suivant" data-vg="suiv">' + (dernier ? 'Terminer' : 'Suivant') + '</button></div></div>';
		placer();
		setTimeout(placer, 120);
		setTimeout(placer, 450);
	}

	function positionnerCarte(r) {
		var w = carte.offsetWidth;
		var h = carte.offsetHeight;
		var vw = window.innerWidth;
		var vh = window.innerHeight;
		var haut;
		var gauche;
		if (!r) { haut = (vh - h) / 2; gauche = (vw - w) / 2; }
		else {
			haut = r.bottom + 18;
			if (haut + h > vh - 12) { haut = Math.max(12, r.top - h - 18); }
			gauche = Math.min(Math.max(12, r.left + r.width / 2 - w / 2), vw - w - 12);
		}
		carte.style.top = Math.max(12, haut) + 'px';
		carte.style.left = gauche + 'px';
	}

	function suivante(sens) {
		var n = etapeCourante + sens;
		if (n < 0) { return; }
		if (n >= etapes.length) { terminerVisite(); return; }
		etapeCourante = n;
		afficherEtape();
	}

	function terminerVisite() {
		[voile, spot, carte].forEach(function (x) { if (x && x.parentNode) { x.parentNode.removeChild(x); } });
		voile = spot = carte = null;
		document.removeEventListener('keydown', toucheVisite);
		window.removeEventListener('resize', replacer);
		window.removeEventListener('scroll', replacer, true);
		fetch(tiroir.dataset.visiteFin, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': tiroir.dataset.jeton, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).catch(function () { /* sans conséquence */ });
	}

	function toucheVisite(e) {
		if (e.key === 'Escape') { terminerVisite(); }
		if (e.key === 'ArrowRight') { suivante(1); }
		if (e.key === 'ArrowLeft') { suivante(-1); }
	}
	function replacer() { placer(); }

	function lancerVisite() {
		if (voile) { return; }
		etapes = construireEtapes();
		etapeCourante = 0;
		voile = document.createElement('div'); voile.className = 'vg-voile';
		spot = document.createElement('div'); spot.className = 'vg-spot vg-spot--plein';
		carte = document.createElement('div'); carte.className = 'vg-carte'; carte.setAttribute('role', 'dialog'); carte.setAttribute('aria-modal', 'true');
		document.body.appendChild(voile); document.body.appendChild(spot); document.body.appendChild(carte);
		carte.addEventListener('click', function (e) {
			var a = e.target.closest('[data-vg]');
			if (!a) { return; }
			if (a.dataset.vg === 'suiv') { suivante(1); } else if (a.dataset.vg === 'prec') { suivante(-1); } else { terminerVisite(); }
		});
		document.addEventListener('keydown', toucheVisite);
		window.addEventListener('resize', replacer);
		window.addEventListener('scroll', replacer, true);
		afficherEtape();
	}

	window.ETacheAide = { ouvrir: ouvrir, fermer: fermer, visite: lancerVisite };

	// Première connexion : la visite se lance seule, une fois la page affichée (jamais sur une page de formulaire en cours de saisie)
	if (tiroir.dataset.visite === '1') {
		window.addEventListener('load', function () { setTimeout(lancerVisite, 1400); });
	}
})();
