<script type="text/javascript">

	/* Paramétrage du courrier : types et urgences (formulaires classiques ; suppression en AJAX). JS natif pour les liaisons. */
	function pc_ouvrir(idModal) {
		const modal = document.getElementById(idModal);
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#' + idModal).modal('show');
	}

	function pc_champ(form, nom, valeur) { form.querySelector('[name="' + nom + '"]').value = valeur; }

	/* ---- Types ---- */
	function pc_nouveau_type() {
		const f = document.getElementById('form_type_courrier');
		f.reset();
		pc_champ(f, 'id_', ''); pc_champ(f, 'ordre', 1); pc_champ(f, 'actif', 1);
		document.getElementById('pc_titre_type').textContent = 'Nouveau type de courrier';
		pc_ouvrir('modal_type_courrier');
	}

	function pc_modifier_type(b) {
		const f = document.getElementById('form_type_courrier');
		pc_champ(f, 'id_', b.dataset.id); pc_champ(f, 'code', b.dataset.code); pc_champ(f, 'libelle', b.dataset.libelle);
		pc_champ(f, 'ordre', b.dataset.ordre); pc_champ(f, 'actif', b.dataset.actif);
		document.getElementById('pc_titre_type').textContent = 'Modifier le type de courrier';
		pc_ouvrir('modal_type_courrier');
	}

	/* ---- Urgences ---- */
	function pc_nouvelle_urgence() {
		const f = document.getElementById('form_urgence_courrier');
		f.reset();
		pc_champ(f, 'id_', ''); pc_champ(f, 'delai_jours', 15); pc_champ(f, 'couleur', '#2B6CB0'); pc_champ(f, 'ordre', 1); pc_champ(f, 'actif', 1);
		document.getElementById('pc_titre_urgence').textContent = 'Nouvelle urgence';
		pc_ouvrir('modal_urgence_courrier');
	}

	function pc_modifier_urgence(b) {
		const f = document.getElementById('form_urgence_courrier');
		pc_champ(f, 'id_', b.dataset.id); pc_champ(f, 'code', b.dataset.code); pc_champ(f, 'libelle', b.dataset.libelle);
		pc_champ(f, 'delai_jours', b.dataset.delai); pc_champ(f, 'couleur', b.dataset.couleur); pc_champ(f, 'ordre', b.dataset.ordre); pc_champ(f, 'actif', b.dataset.actif);
		document.getElementById('pc_titre_urgence').textContent = "Modifier l'urgence";
		pc_ouvrir('modal_urgence_courrier');
	}

	/* ---- Instructions du DG ---- */
	function pc_nouvelle_instruction() {
		const f = document.getElementById('form_instruction');
		f.reset(); pc_champ(f, 'id_', ''); pc_champ(f, 'ordre', 1); pc_champ(f, 'actif', 1);
		document.getElementById('pc_titre_instruction').textContent = 'Nouvelle instruction';
		pc_ouvrir('modal_instruction');
	}
	function pc_modifier_instruction(b) {
		const f = document.getElementById('form_instruction');
		pc_champ(f, 'id_', b.dataset.id); pc_champ(f, 'libelle', b.dataset.libelle); pc_champ(f, 'ordre', b.dataset.ordre); pc_champ(f, 'actif', b.dataset.actif);
		document.getElementById('pc_titre_instruction').textContent = "Modifier l'instruction";
		pc_ouvrir('modal_instruction');
	}

	/* ---- Delais de traitement ---- */
	function pc_nouveau_delai() {
		const f = document.getElementById('form_delai');
		f.reset(); pc_champ(f, 'id_', ''); pc_champ(f, 'delai_jours', 1); pc_champ(f, 'saisie_libre', 0); pc_champ(f, 'ordre', 1); pc_champ(f, 'actif', 1);
		document.getElementById('pc_titre_delai').textContent = 'Nouveau délai';
		pc_ouvrir('modal_delai');
	}
	function pc_modifier_delai(b) {
		const f = document.getElementById('form_delai');
		pc_champ(f, 'id_', b.dataset.id); pc_champ(f, 'code', b.dataset.code); pc_champ(f, 'libelle', b.dataset.libelle);
		pc_champ(f, 'saisie_libre', b.dataset.libre); pc_champ(f, 'delai_jours', b.dataset.jours || 0); pc_champ(f, 'ordre', b.dataset.ordre); pc_champ(f, 'actif', b.dataset.actif);
		document.getElementById('pc_titre_delai').textContent = 'Modifier le délai';
		pc_ouvrir('modal_delai');
	}

	/* ---- Postes destinataires ---- */
	function pc_nouveau_poste() {
		const f = document.getElementById('form_poste');
		f.reset(); pc_champ(f, 'id_', ''); pc_champ(f, 'position', ''); pc_champ(f, 'peut_reimputer', 0); pc_champ(f, 'ordre', 1); pc_champ(f, 'actif', 1);
		pc_afficher_titulaire_poste();
		document.getElementById('pc_titre_poste').textContent = 'Nouveau poste destinataire';
		pc_ouvrir('modal_poste');
	}
	function pc_modifier_poste(b) {
		const f = document.getElementById('form_poste');
		pc_champ(f, 'id_', b.dataset.id); pc_champ(f, 'position', b.dataset.position);
		const option = f.querySelector('[name="position"] option[value="' + b.dataset.position + '"]');
		if (option) { option.disabled = false; }
		pc_champ(f, 'peut_reimputer', b.dataset.reimpute); pc_champ(f, 'ordre', b.dataset.ordre); pc_champ(f, 'actif', b.dataset.actif);
		pc_afficher_titulaire_poste();
		document.getElementById('pc_titre_poste').textContent = 'Modifier le poste destinataire';
		pc_ouvrir('modal_poste');
	}
	function pc_afficher_titulaire_poste() {
		const select = document.getElementById('pc_pos_position');
		const option = select && select.options[select.selectedIndex];
		const champ = document.getElementById('pc_pos_titulaire');
		const aide = document.getElementById('pc_pos_titulaire_aide');
		if (!champ || !aide) { return; }
		champ.value = option && option.value ? option.dataset.titulaire : '';
		aide.textContent = option && option.dataset.interim === '1' ? 'La personne affichée occupe actuellement le poste par intérim.' : 'Le titulaire ou l’intérimaire est déterminé automatiquement.';
	}

	/* ---- Suppression ---- */
	function pc_supprimer(nature, id, libelle) {
		const routes = {
			type: "{{ route('courrier.parametrage.type.delete', ':id') }}",
			urgence: "{{ route('courrier.parametrage.urgence.delete', ':id') }}",
			instruction: "{{ route('courrier.parametrage.instruction.delete', ':id') }}",
			delai: "{{ route('courrier.parametrage.delai.delete', ':id') }}",
			poste: "{{ route('courrier.parametrage.poste.delete', ':id') }}"
		};
		const url = routes[nature].replace(':id', id);

		Swal.fire({
			title: 'e-Tache', text: 'Supprimer « ' + libelle + ' » ?', icon: 'warning',
			showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer'
		}).then(function (r) {
			if (!r.isConfirmed) { return; }
			fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
				.then(function (res) { return res.json().then(function (d) { return { ok: res.ok, d: d }; }); })
				.then(function (x) {
					if (x.ok) { window.location.reload(); } else { Swal.fire('Impossible', x.d.message || 'Suppression impossible.', 'error'); }
				})
				.catch(function () { Swal.fire('Erreur', 'Impossible de contacter le serveur.', 'error'); });
		});
	}

	/* ---- Blocs pliables : un clic sur l'en-tete plie ou deplie le bloc (les boutons de l'en-tete ne declenchent rien) ---- */
	window.addEventListener('load', function () {
		document.querySelectorAll('.cr-pliable .cr-carte').forEach(function (carte) {
			const tete = carte.querySelector('.cr-carte__tete');
			if (!tete) { return; }
			const chevron = document.createElement('i');
			chevron.className = 'fa-solid fa-chevron-down cr-chevron';
			tete.appendChild(chevron);
			tete.addEventListener('click', function (e) {
				if (e.target.closest('button, a')) { return; }
				carte.classList.toggle('cr-carte--pliee');
			});
		});
	});

	/* ---- Retour apres une erreur de validation : on rouvre la bonne fenetre ---- */
	window.addEventListener('load', function () {
		['modal_type_courrier', 'modal_urgence_courrier', 'modal_instruction', 'modal_delai', 'modal_poste'].forEach(function (idModal) {
			const modal = document.getElementById(idModal);
			if (modal && modal.dataset.ouvrir === '1') { pc_ouvrir(idModal); }
		});
	});

</script>
