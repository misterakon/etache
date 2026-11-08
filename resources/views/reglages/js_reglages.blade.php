<script type="text/javascript">

	/* Reglages (JS natif : jQuery n'est pas encore charge quand ce script s'execute).
	   Les ecouteurs sont poses au chargement de la page ; Swal n'est utilise qu'au clic. */

	window.addEventListener('load', function () {
		const boutons = document.querySelectorAll('.rg-rub');
		const panneaux = document.querySelectorAll('.rg-panneau');
		const champ = document.getElementById('rg-recherche');
		const aucun = document.getElementById('rg-aucun');
		let actif = (document.querySelector('.rg-rub.is-actif') || {}).dataset ? document.querySelector('.rg-rub.is-actif').dataset.rub : null;

		/* ---- Rubriques : un seul panneau visible a la fois ---- */
		function afficherRubrique(cle) {
			actif = cle;
			boutons.forEach(function (b) {
				const on = b.dataset.rub === cle;
				b.classList.toggle('is-actif', on);
				b.setAttribute('aria-selected', on ? 'true' : 'false');
			});
			panneaux.forEach(function (p) { p.hidden = (p.dataset.rub !== cle); });

			const url = new URL(window.location.href);
			url.searchParams.set('module', cle);
			window.history.replaceState(null, '', url.toString());

			// Page déjà défilée : on remonte en haut des réglages pour voir le début de la rubrique
			const grille = document.querySelector('.rg-grille');
			if (grille && grille.getBoundingClientRect().top < 80) {
				window.scrollTo({ top: window.scrollY + grille.getBoundingClientRect().top - 110, behavior: 'smooth' });
			}
		}

		boutons.forEach(function (b) {
			b.addEventListener('click', function () {
				if (champ && champ.value.trim() !== '') { champ.value = ''; appliquerRecherche(''); }
				afficherRubrique(b.dataset.rub);
			});
		});

		/* ---- Recherche : montre, rubrique par rubrique, les seuls reglages qui correspondent ---- */
		function appliquerRecherche(q) {
			let total = 0;

			panneaux.forEach(function (p) {
				let visibles = 0;
				p.querySelectorAll('.rg-ligne').forEach(function (ligne) {
					const ok = q === '' || (ligne.dataset.recherche || '').indexOf(q) !== -1;
					ligne.hidden = !ok;
					if (ok) { visibles++; }
				});
				p.dataset.resultats = visibles;
				total += visibles;
				if (q === '') { p.hidden = (p.dataset.rub !== actif); } else { p.hidden = (visibles === 0); }
			});

			boutons.forEach(function (b) {
				const p = document.getElementById('rub-' + b.dataset.rub);
				b.hidden = (q !== '' && p && Number(p.dataset.resultats || 0) === 0);
			});

			if (aucun) { aucun.hidden = !(q !== '' && total === 0); }
		}

		if (champ) {
			champ.addEventListener('input', function () { appliquerRecherche(champ.value.trim().toLowerCase()); });
		}

		/* ---- Retablir la valeur par defaut : un reglage, ou toute une rubrique ---- */
		function confirmer(texte, fonctionOk) {
			Swal.fire({
				title: 'e-Tache',
				text: texte,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#033074',
				cancelButtonColor: '#d33',
				cancelButtonText: 'Annuler',
				confirmButtonText: 'Oui, rétablir'
			}).then(function (result) { if (result.isConfirmed) { fonctionOk(); } });
		}

		function envoyerRetablir(cle, module) {
			const form = document.getElementById('form_retablir');
			form.querySelector('[name="cle"]').value = cle;
			form.querySelector('[name="module"]').value = module;
			form.submit();
		}

		document.addEventListener('click', function (e) {
			const un = e.target.closest('[data-retablir]');
			if (un) {
				confirmer('Rétablir « ' + un.dataset.libelle + ' » à sa valeur par défaut ?', function () { envoyerRetablir(un.dataset.retablir, ''); });
				return;
			}
			const tous = e.target.closest('[data-retablir-module]');
			if (tous) {
				confirmer('Rétablir tous les réglages de « ' + tous.dataset.libelle + ' » à leur valeur par défaut ? Les valeurs que vous aviez choisies seront perdues.', function () { envoyerRetablir('', tous.dataset.retablirModule); });
			}
		});
	});

</script>
