/* Menu principal : repère la page en cours (public/assets/js/menu-actif.js).
 *
 * Le script du thème ne sait pas le faire en menu horizontal. Ici : on compare l'adresse de la page avec celle de chaque
 * lien du menu. Le lien le plus précis est « actif » ; ses menus parents le sont aussi, pour qu'on voie
 * d'un coup d'œil où l'on se trouve. Les pages de détail (ex. /feuille-de-route/tache/9/details) activent le lien de leur
 * liste. À défaut de lien précis, on active la rubrique (ex. Courrier) qui contient la page.
 * Les couleurs sont dans public/assets/css/header-design.css (classe .menu-actif). */
(function () {
	'use strict';

	// Anciens liens du menu « Tâches » (?vue=...) : leur page réelle
	var VUES = { tableau: '/taches/matrice', calendrier: '/taches/calendrier', gantt: '/taches/gantt' };

	function nettoyer(p) {
		p = p.replace(/\/+$/, '');
		return p === '' ? '/' : p;
	}

	function lancer() {
		var nav = document.querySelector('.sidebar-wrapper nav');
		if (!nav) { return; }

		var tableau = nav.querySelector('a[href$="/dashboard"]');
		var base = tableau ? nettoyer(new URL(tableau.href, location.href).pathname).replace(/\/dashboard$/, '') : '';
		var relatif = function (p) { p = nettoyer(p); return base && p.indexOf(base) === 0 ? (p.slice(base.length) || '/') : p; };
		var courant = relatif(location.pathname);

		nav.querySelectorAll('.menu-actif').forEach(function (e) { e.classList.remove('menu-actif'); e.removeAttribute('aria-current'); });

		var meilleur = null;
		var score = -1;
		nav.querySelectorAll('.sidebar-list a[href]').forEach(function (a) {
			var h = a.getAttribute('href');
			if (!h || h === '#' || h.indexOf('javascript') === 0) { return; }
			var u;
			try { u = new URL(a.href, location.href); } catch (e) { return; }
			var p = relatif(u.pathname);
			var vue = u.searchParams.get('vue');
			if (vue && VUES[vue]) { p = relatif(u.pathname.replace(/\/tache$/, '') + VUES[vue]); }
			if (p === '/') { return; }

			var s = -1;
			if (p === courant) { s = 1000 + p.length; }
			else if (courant.indexOf(p + '/') === 0) { s = p.length; }
			if (s > score) { score = s; meilleur = a; }
		});

		if (meilleur) { marquer(nav, meilleur); return; }

		// Rubrique : même premier segment d'adresse que la page (ex. /courrier/...)
		var segment = courant.split('/')[1];
		if (!segment) { return; }
		var trouve = null;
		nav.querySelectorAll('li.sidebar-list').forEach(function (li) {
			if (trouve) { return; }
			li.querySelectorAll('a[href]').forEach(function (a) {
				if (trouve) { return; }
				var h = a.getAttribute('href');
				if (!h || h === '#') { return; }
				try { if (relatif(new URL(a.href, location.href).pathname).split('/')[1] === segment) { trouve = li; } } catch (e) { /* adresse invalide */ }
			});
		});
		if (trouve) {
			trouve.classList.add('menu-actif');
			var lien = trouve.querySelector(':scope > a');
			if (lien) { lien.classList.add('menu-actif'); }
		}
	}

	function marquer(nav, lien) {
		lien.classList.add('menu-actif');
		lien.setAttribute('aria-current', 'page');
		var li = lien.closest('li');
		while (li && nav.contains(li)) {
			li.classList.add('menu-actif');
			var a = li.querySelector(':scope > a');
			if (a) { a.classList.add('menu-actif'); }
			var parent = li.parentElement ? li.parentElement.closest('li') : null;
			li = parent;
		}
	}

	window.etacheMenuActif = lancer;
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', lancer); } else { lancer(); }
	window.addEventListener('load', lancer);
})();
