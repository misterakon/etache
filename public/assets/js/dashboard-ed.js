/* Tableau de bord :
   1. les grands chiffres montent jusqu'à leur valeur ;
   2. les tâches prioritaires défilent verticalement, une ligne à la fois (pause au survol, flèches manuelles) ;
   3. les diligences sont des cartes paginées (nombre par page au choix). */
(function () {
	'use strict';

	var reduit = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---- 1. Compteurs ---- */
	function compteurs() {
		document.querySelectorAll('.ed-compteur[data-valeur]').forEach(function (el) {
			var cible = parseInt(el.getAttribute('data-valeur'), 10);
			if (isNaN(cible) || reduit || cible < 2) { return; }

			var duree = 800;
			var debut = null;
			el.textContent = '0';

			function pas(t) {
				if (debut === null) { debut = t; }
				var p = Math.min((t - debut) / duree, 1);
				el.textContent = Math.round(cible * (1 - Math.pow(1 - p, 3))).toLocaleString('fr-FR');
				if (p < 1) { window.requestAnimationFrame(pas); } else { el.textContent = cible.toLocaleString('fr-FR'); }
			}
			window.requestAnimationFrame(pas);
		});
	}

	/* ---- 2. Tâches prioritaires : le conteneur défile (scrollTop) d'une ligne toutes les 3,5 s.
	        Défiler plutôt que déplacer par transform garde le texte net. ---- */
	function prioritaires() {
		var zone = document.querySelector('.ed-prios');
		if (!zone) { return; }

		var lignes = zone.querySelectorAll('.ed-prio');
		var fenetre = parseInt(zone.getAttribute('data-fenetre'), 10) || 3;
		var maxi = Math.max(0, lignes.length - fenetre);
		if (maxi === 0) { return; }

		var position = 0;
		var minuteur = null;
		var survol = false;

		function aller(n) {
			position = n;
			zone.scrollTop = position * lignes[0].offsetHeight;
		}
		function suivant() { aller(position >= maxi ? 0 : position + 1); }
		function precedent() { aller(position <= 0 ? maxi : position - 1); }

		function arreter() { if (minuteur) { window.clearInterval(minuteur); minuteur = null; } }
		function demarrer() {
			arreter();
			if (reduit) { return; }
			minuteur = window.setInterval(function () { if (!survol && !document.hidden) { suivant(); } }, 3500);
		}

		zone.addEventListener('mouseenter', function () { survol = true; });
		zone.addEventListener('mouseleave', function () { survol = false; });
		document.querySelectorAll('[data-prio]').forEach(function (b) {
			b.addEventListener('click', function () {
				if (b.getAttribute('data-prio') === 'bas') { suivant(); } else { precedent(); }
				demarrer();
			});
		});
		window.addEventListener('resize', function () { aller(position); });

		demarrer();
	}

	/* ---- 3. Diligences : pagination des cartes ---- */
	function diligences() {
		var bloc = document.getElementById('ed-diligences');
		if (!bloc) { return; }

		var cartes = Array.prototype.slice.call(bloc.querySelectorAll('.ed-dil__carte'));
		var choix = bloc.querySelector('[data-taille-select]');
		var info = bloc.querySelector('[data-info]');
		var nav = bloc.querySelector('[data-pages]');
		var taille = parseInt(bloc.getAttribute('data-taille'), 10) || 6;
		var page = 1;

		function bouton(texte, cible, etat) {
			var b = document.createElement('button');
			b.type = 'button';
			b.textContent = texte;
			if (etat === 'actif') { b.className = 'is-actif'; b.setAttribute('aria-current', 'page'); }
			if (etat === 'inactif') { b.disabled = true; }
			if (etat !== 'inactif') { b.addEventListener('click', function () { page = cible; afficher(); }); }
			return b;
		}

		function afficher() {
			var total = cartes.length;
			var nbPages = Math.max(1, Math.ceil(total / taille));
			page = Math.min(Math.max(1, page), nbPages);
			var debut = (page - 1) * taille;
			var fin = Math.min(debut + taille, total);

			cartes.forEach(function (c, i) { c.hidden = (i < debut || i >= fin); });
			info.textContent = (debut + 1) + ' à ' + fin + ' sur ' + total;

			nav.innerHTML = '';
			nav.appendChild(bouton('‹', page - 1, page === 1 ? 'inactif' : ''));

			// 1 … (page-1) page (page+1) … dernière
			var numeros = [];
			for (var n = 1; n <= nbPages; n++) {
				if (n === 1 || n === nbPages || Math.abs(n - page) <= 1) { numeros.push(n); }
			}
			var precedent = 0;
			numeros.forEach(function (n) {
				if (precedent && n - precedent > 1) {
					var points = document.createElement('span');
					points.textContent = '…';
					nav.appendChild(points);
				}
				nav.appendChild(bouton(String(n), n, n === page ? 'actif' : ''));
				precedent = n;
			});

			nav.appendChild(bouton('›', page + 1, page === nbPages ? 'inactif' : ''));
			nav.hidden = (nbPages === 1);
		}

		if (choix) {
			choix.value = String(taille);
			choix.addEventListener('change', function () { taille = parseInt(choix.value, 10) || 6; page = 1; afficher(); });
		}
		afficher();
	}

	/* ---- 4. Squelette de chargement (comme Facebook) : retiré dès que la page et ses images sont prêtes ---- */
	function retirerSquelette() {
		var page = document.querySelector('.ed-page.ed-charge');
		if (!page) { return; }
		var sq = page.querySelector('.ed-squelette');
		page.classList.remove('ed-charge');
		page.classList.add('ed-revele');
		if (sq) {
			sq.classList.add('ed-squelette--fin');
			setTimeout(function () { if (sq.parentNode) { sq.parentNode.removeChild(sq); } page.classList.remove('ed-revele'); }, 400);
		}
	}
	if (document.readyState === 'complete') { setTimeout(retirerSquelette, 250); } else { window.addEventListener('load', function () { setTimeout(retirerSquelette, 250); }); }
	setTimeout(retirerSquelette, 5000);

	function demarrage() { compteurs(); prioritaires(); diligences(); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', demarrage); } else { demarrage(); }
})();
