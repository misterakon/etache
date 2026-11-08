/* Mes tâches › onglets « Toutes mes tâches » (filtre + recherche) et « Statuts » (sous-onglets) — public/assets/js/mes-taches-vues.js */
(function () {
	'use strict';

	/* Toutes mes tâches : puces d'état + recherche instantanée */
	var liste = document.getElementById('mx_liste');
	if (liste) {
		var chips = document.getElementById('mx_chips');
		var champ = document.getElementById('mx_q');
		var vide = document.getElementById('mx_vide');
		var ton = 'tous';
		var appliquer = function () {
			var q = champ.value.trim().toLowerCase();
			var visibles = 0;
			liste.querySelectorAll('.mx-ligne').forEach(function (l) {
				var ok = (ton === 'tous' || l.dataset.ton === ton) && (!q || l.dataset.txt.indexOf(q) !== -1);
				l.hidden = !ok;
				if (ok) { visibles++; }
			});
			vide.hidden = visibles > 0;
		};
		chips.addEventListener('click', function (e) {
			var b = e.target.closest('.mx-chip');
			if (!b) { return; }
			ton = b.dataset.ton;
			chips.querySelectorAll('.mx-chip').forEach(function (c) { c.classList.toggle('actif', c === b); });
			appliquer();
		});
		champ.addEventListener('input', appliquer);
	}

	/* Statuts : un sous-onglet affiche les tâches de sa catégorie */
	var liste3 = document.getElementById('mx_liste3');
	if (liste3) {
		var onglets = document.getElementById('mx_onglets');
		var vide3 = document.getElementById('mx_vide3');
		var afficher = function (grp) {
			var visibles = 0;
			liste3.querySelectorAll('.mx-ligne').forEach(function (l) {
				var ok = grp === 'retard' ? l.dataset.retard === '1' : l.dataset.grp === grp;
				l.hidden = !ok;
				if (ok) { visibles++; }
			});
			vide3.hidden = visibles > 0;
			onglets.querySelectorAll('.mx-onglet').forEach(function (o) { o.classList.toggle('actif', o.dataset.grp === grp); });
		};
		onglets.addEventListener('click', function (e) { var b = e.target.closest('.mx-onglet'); if (b) { afficher(b.dataset.grp); } });
		afficher(onglets.querySelector('.mx-onglet.actif').dataset.grp);
	}
})();
