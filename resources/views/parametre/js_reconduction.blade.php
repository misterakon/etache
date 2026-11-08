<script type="text/javascript">

	/* Reconduction (JS natif : jQuery n'est pas encore charge quand ce script s'execute)
	   1) taches : la destination et l'echeance ne sont actives (et obligatoires) que pour les taches cochees ;
	   2) structure : cocher un axe / une action cle coche tout ce qu'il contient, et inversement. */
	window.addEventListener('load', function () {

		// ---- Taches ----
		const cases = document.querySelectorAll('.rc-case');
		const tout = document.getElementById('rc-tout');

		function majLigne(caseTache) {
			const ligne = caseTache.closest('tr');
			const date = ligne.querySelector('.rc-date');
			const dest = ligne.querySelector('.rc-dest');
			if (date) { date.disabled = !caseTache.checked; date.required = caseTache.checked; }
			if (dest) { dest.disabled = !caseTache.checked; dest.required = caseTache.checked && dest.dataset.requis === '1'; }
		}

		cases.forEach(function (c) {
			majLigne(c);
			c.addEventListener('change', function () { majLigne(c); });
		});

		if (tout) {
			tout.addEventListener('change', function () {
				cases.forEach(function (c) { if (!c.disabled) { c.checked = tout.checked; majLigne(c); } });
			});
		}

		// ---- Structure ----
		const arbre = document.getElementById('form_structure');
		if (arbre) {
			arbre.addEventListener('change', function (e) {
				if (!e.target.classList.contains('rc-n')) { return; }
				const noeud = e.target.closest('li');
				noeud.querySelectorAll('ul .rc-n').forEach(function (enfant) { enfant.checked = e.target.checked; });
			});
		}
	});

</script>
