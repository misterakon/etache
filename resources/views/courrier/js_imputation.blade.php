<script type="text/javascript">

	/* ============================================================
	   IMPUTATION D'UN COURRIER : choix des destinataires, roles, delai et echeance.
	   JS natif pour les liaisons (jQuery n'est pas encore charge quand ce script s'execute).
	   ============================================================ */
	const IMP_ROLES = { principal: 'Chef de file (responsable principal)', avis: 'Avis / aide associé', copie: 'Copie / pour information' };
	const impMemoire = new Map(); // valeur -> role choisi (conserve les choix quand la liste est reconstruite)
	const impAssociation = new Map(); // avis/aide -> clé du travail principal associé

	function imp_ouvrir() {
		const modal = document.getElementById('modal_imputation');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_imputation').modal('show');
	}

	/* ---- Destinataires selectionnes (cases + pastilles) ---- */
	function imp_selection() {
		const liste = [];
		document.querySelectorAll('#form_imputation .imp-case:checked').forEach(function (c) { liste.push({ valeur: c.value, libelle: c.dataset.libelle }); });
		document.querySelectorAll('#imp_puces .cr-puce').forEach(function (p) { liste.push({ valeur: p.dataset.valeur, libelle: p.dataset.libelle }); });
		return liste;
	}

	function imp_reconstruire_roles() {
		const liste = imp_selection();
		const corps = document.querySelector('#imp_roles tbody');
		corps.innerHTML = '';
		document.getElementById('imp_roles_vide').style.display = liste.length ? 'none' : '';

		if (liste.length && !liste.some(function (d) { return impMemoire.get(d.valeur) === 'principal'; })) {
			impMemoire.set(liste[0].valeur, 'principal');
		}
		liste.filter(function (d) { return impMemoire.get(d.valeur) === 'principal'; }).slice(1)
			.forEach(function (d) { impMemoire.set(d.valeur, 'avis'); });

		liste.forEach(function (d) {
			let role = impMemoire.get(d.valeur) || 'copie';
			impMemoire.set(d.valeur, role);

			const ligne = document.createElement('tr');
			const nom = document.createElement('td');
			nom.textContent = d.libelle;
			const choix = document.createElement('td');
			const select = document.createElement('select');
			select.className = 'form-select form-select-sm';
			select.name = 'role[' + d.valeur + ']';
			Object.keys(IMP_ROLES).forEach(function (cle) {
				const option = document.createElement('option');
				option.value = cle; option.textContent = IMP_ROLES[cle]; option.selected = cle === role;
				select.appendChild(option);
			});
			select.addEventListener('change', function () {
				if (select.value === 'principal') {
					impMemoire.forEach(function (r, k) { if (r === 'principal') { impMemoire.set(k, 'avis'); } });
				}
				impMemoire.set(d.valeur, select.value);
				imp_reconstruire_roles();
			});
			choix.appendChild(select);
			ligne.appendChild(nom); ligne.appendChild(choix);
			corps.appendChild(ligne);
		});
	}

	function imp_ajouter_puce(valeur, libelle) {
		if (document.querySelector('#imp_puces .cr-puce[data-valeur="' + valeur + '"]') || document.querySelector('#form_imputation .imp-case[value="' + valeur + '"]')) { return; }
		const puce = document.createElement('span');
		puce.className = 'cr-puce'; puce.dataset.valeur = valeur; puce.dataset.libelle = libelle;
		puce.appendChild(document.createTextNode(libelle));
		const retirer = document.createElement('button');
		retirer.type = 'button'; retirer.className = 'cr-puce__x'; retirer.setAttribute('aria-label', 'Retirer'); retirer.textContent = '×';
		retirer.addEventListener('click', function () { puce.remove(); impMemoire.delete(valeur); imp_reconstruire_roles(); });
		const champ = document.createElement('input');
		champ.type = 'hidden'; champ.name = 'dest[]'; champ.value = valeur;
		puce.appendChild(retirer); puce.appendChild(champ);
		document.getElementById('imp_puces').appendChild(puce);
	}

	/* ---- Delai et echeance ---- */
	function imp_maj_echeance() {
		const select = document.getElementById('imp_delai');
		const option = select.options[select.selectedIndex];
		const libre = option && option.dataset.libre === '1';
		document.getElementById('imp_libre_bloc').style.display = libre ? '' : 'none';
		document.getElementById('imp_libre').required = libre;

		const sortie = document.getElementById('imp_echeance_calc');
		if (!option || !option.value) { sortie.textContent = ''; return; }
		if (libre) { sortie.textContent = ''; return; }
		const debut = document.getElementById('imp_date').value;
		if (!debut) { sortie.textContent = ''; return; }
		const d = new Date(debut + 'T00:00:00');
		d.setDate(d.getDate() + parseInt(option.dataset.jours || '0', 10));
		sortie.textContent = 'Échéance : ' + d.toLocaleDateString('fr-FR');
	}

	/* ---- Classement : « Archiver » / « Pour information » seuls (pas de diligence) ---- */
	function imp_classement() {
		const c = document.querySelectorAll('#form_imputation input[name="instructions[]"]:checked');
		return c.length > 0 && Array.from(c).every(function (i) { return i.dataset.classe === '1'; });
	}
	function imp_maj_classement() {
		const c = imp_classement();
		document.getElementById('imp_delai').required = !c;
		document.getElementById('imp_classement_note').style.display = c ? '' : 'none';
	}

	window.addEventListener('load', function () {
		const formulaire = document.getElementById('form_imputation');
		if (!formulaire) { return; }

		formulaire.querySelectorAll('.imp-case').forEach(function (c) { c.addEventListener('change', imp_reconstruire_roles); });
		document.querySelectorAll('#imp_puces .cr-puce').forEach(function (p) {
			p.querySelector('.cr-puce__x').addEventListener('click', function () { p.remove(); impMemoire.delete(p.dataset.valeur); imp_reconstruire_roles(); });
		});
		document.getElementById('imp_ajouter').addEventListener('click', function () {
			const select = document.getElementById('imp_autre');
			const option = select.options[select.selectedIndex];
			if (!option || !option.value) { return; }
			imp_ajouter_puce(option.value, option.dataset.libelle);
			select.value = '';
			imp_reconstruire_roles();
		});

		// roles deja choisis avant une erreur de validation
		@foreach (old('role', []) as $cle => $role)
			impMemoire.set(@js($cle), @js($role));
		@endforeach
		@foreach (old('associe_a', []) as $cle => $associeA)
			impAssociation.set(@js($cle), @js($associeA));
		@endforeach

		document.getElementById('imp_delai').addEventListener('change', imp_maj_echeance);
		document.getElementById('imp_date').addEventListener('change', imp_maj_echeance);
		imp_reconstruire_roles();
		imp_maj_echeance();
		formulaire.querySelectorAll('input[name="instructions[]"]').forEach(function (i) { i.addEventListener('change', imp_maj_classement); });
		imp_maj_classement();

		formulaire.addEventListener('submit', function (e) {
			if (!imp_classement() && !imp_selection().some(function (d) { return impMemoire.get(d.valeur) === 'principal'; })) { e.preventDefault(); Swal.fire('Imputation', 'Désignez au moins un travail indépendant avec son responsable principal.', 'warning'); return; }
			const bouton = document.getElementById('imp_btn');
			bouton.disabled = true;
			bouton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Imputation…';
		});

		const modal = document.getElementById('modal_imputation');
		if (modal && modal.dataset.ouvrir === '1') { imp_ouvrir(); }
	});

</script>
