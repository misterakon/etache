<script type="text/javascript">

	/* ============================================================
	   AFFECTATION D'UN AGENT A UN POSTE (liste du personnel et fiche de l'agent).
	   La fonction choisie determine la liste des postes ; un poste deja occupe demande une confirmation.
	   JS natif pour les liaisons (jQuery n'est pas encore charge quand ce script s'execute).
	   ============================================================ */
	function aff_fonction_courante() {
		const id = document.getElementById('aff_fonction').value;
		return AFF.fonctions.find(function (f) { return String(f.id) === String(id); }) || null;
	}

	function aff_ouvrir_modal() {
		const modal = document.getElementById('modal_affectation');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_affectation').modal('show');
	}

	// Les postes proposes dependent de la fonction : directions, sous-directions ou services
	function aff_maj_postes(uniteChoisie) {
		const f = aff_fonction_courante();
		const bloc = document.getElementById('aff_bloc_unite');
		const select = document.getElementById('aff_unite');
		select.innerHTML = '<option value="">Choisir…</option>';

		let liste = null, etiquette = 'Poste';
		if (f) {
			if (f.categorie === 'mandat' && f.niveau === 'direction') { liste = AFF.unites.direction; etiquette = 'Direction'; }
			else if (f.categorie === 'mandat' && f.niveau === 'sous_direction') { liste = AFF.unites.sous_direction; etiquette = 'Sous-direction'; }
			else if (f.categorie === 'mandat' && f.niveau === 'service') { liste = AFF.unites.service; etiquette = 'Service'; }
			else if (f.categorie === 'service') { liste = AFF.unites.service; etiquette = 'Service de rattachement'; }
		}

		bloc.style.display = liste ? '' : 'none';
		select.required = !!liste;
		document.getElementById('aff_etiquette_unite').innerHTML = '<b>' + etiquette + '</b>';
		if (liste) {
			liste.forEach(function (u) {
				const option = document.createElement('option');
				option.value = u.id; option.textContent = u.libelle;
				if (uniteChoisie && String(uniteChoisie) === String(u.id)) { option.selected = true; }
				select.appendChild(option);
			});
		}

		document.getElementById('aff_bloc_interim').style.display = f && f.categorie === 'mandat' ? '' : 'none';
		if (!f || f.categorie !== 'mandat') { document.getElementById('aff_interim').checked = false; }
		aff_maj_occupation();
	}

	// Poste a titulaire unique : on previent tout de suite si quelqu'un l'occupe deja
	function aff_maj_occupation() {
		const f = aff_fonction_courante();
		const zone = document.getElementById('aff_occupation');
		const bloc = document.getElementById('aff_bloc_confirmer');
		const confirmer = document.getElementById('aff_confirmer');
		zone.style.display = 'none'; zone.innerHTML = '';

		if (!f || f.categorie !== 'mandat') { bloc.style.display = 'none'; confirmer.required = false; return; }

		const unite = f.niveau === 'direction_generale' || f.niveau === 'dga' ? 0 : document.getElementById('aff_unite').value;
		if (unite === '') { bloc.style.display = 'none'; confirmer.required = false; return; }

		const occ = AFF.occupants[f.niveau + ':' + unite] || {};
		const interim = document.getElementById('aff_interim').checked;
		const agent = parseInt(document.querySelector('#form_affectation [name="personnel_id"]').value, 10);
		const concernes = [];
		['titulaire', 'interim'].forEach(function (type) {
			const o = occ[type];
			if (!o || o.id === agent) { return; }
			if (!interim || type === 'interim') { concernes.push(o.nom + ' (' + (type === 'interim' ? 'intérim' : 'titulaire') + ', depuis le ' + o.depuis + ')'); }
		});

		if (!concernes.length) { bloc.style.display = 'none'; confirmer.required = false; return; }
		zone.style.display = '';
		zone.innerHTML = '<div class="alert alert-warning" style="margin:0;"><i class="fa-solid fa-triangle-exclamation"></i> Ce poste est déjà occupé : <b>'
			+ concernes.join(', ') + '</b>. En confirmant, '
			+ (interim ? "l'intérimaire actuel sera remplacé." : "l'agent devient titulaire et les occupants actuels sont remplacés (historique conservé).") + '</div>';
		bloc.style.display = '';
		confirmer.required = true;
	}

	// Postes occupes par l'agent : chacun a un bouton « Mettre fin » (fin d'interim ou de fonction)
	function aff_afficher_postes(personnelId) {
		const zone = document.getElementById('aff_postes_actuels');
		const liste = document.getElementById('aff_postes_liste');
		const postes = (AFF.mandatsAgents && AFF.mandatsAgents[personnelId]) || [];
		liste.innerHTML = '';
		zone.style.display = postes.length ? '' : 'none';

		postes.forEach(function (p) {
			const interim = p.type === 'interim';
			const ligne = document.createElement('div');
			ligne.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:10px;padding:6px 0;border-bottom:1px dashed rgba(91,107,132,.25);';

			const texte = document.createElement('div');
			const nom = document.createElement('b');
			nom.textContent = p.libelle;
			const info = document.createElement('div');
			info.className = 'text-muted';
			info.style.fontSize = '12.5px';
			info.textContent = (interim ? 'Intérim' : 'Titulaire') + ' depuis le ' + p.debut;
			texte.appendChild(nom); texte.appendChild(info);

			const bouton = document.createElement('button');
			bouton.type = 'button';
			bouton.className = 'btn btn-danger btn-sm';
			bouton.innerHTML = '<i class="fa-solid fa-user-xmark text-white"></i> ' + (interim ? "Mettre fin à l'intérim" : 'Mettre fin');
			bouton.addEventListener('click', function () {
				$('#modal_affectation').modal('hide'); // pas de fenetres empilees
				aff_fin_ouvrir(p.id, p.libelle, interim, p.debut_iso);
			});

			ligne.appendChild(texte); ligne.appendChild(bouton);
			liste.appendChild(ligne);
		});
	}

	// nomAgent : nom complet ; fonctionId / uniteId : valeurs actuelles de l'agent, pour pre-remplir
	function aff_ouvrir(personnelId, nomAgent, fonctionId, uniteId) {
		const f = document.getElementById('form_affectation');
		f.reset();
		f.querySelector('[name="personnel_id"]').value = personnelId;
		document.getElementById('aff_agent').value = nomAgent;
		document.getElementById('aff_fonction').value = fonctionId || '';
		document.getElementById('aff_confirmer').checked = false;
		aff_maj_postes(uniteId);
		aff_afficher_postes(personnelId);
		aff_ouvrir_modal();
	}

	/* ---- Mettre fin a une affectation (interim ou titulaire) ---- */
	function aff_fin_ouvrir(mandatId, libelle, interim, debutIso) {
		const f = document.getElementById('form_fin_affectation');
		f.reset();
		f.querySelector('[name="mandat_id"]').value = mandatId;
		f.querySelector('[name="libelle_poste"]').value = libelle;
		f.querySelector('[name="est_interim"]').value = interim ? '1' : '';
		f.querySelector('[name="date_fin"]').min = debutIso || '';
		document.getElementById('fin_titre').textContent = interim ? "Mettre fin à l'intérim" : 'Mettre fin à la fonction';
		document.getElementById('fin_poste').textContent = libelle;
		document.getElementById('fin_consequence').textContent = interim
			? "L'intérim se termine : le titulaire du poste reprend la main. Les diligences ne sont pas modifiées."
			: "Le poste devient vacant et la fonction de l'agent est vidée. L'historique est conservé.";
		f.querySelector('[name="motif_fin"]').value = interim ? "Fin d'intérim" : 'Fin de fonction';
		const modal = document.getElementById('modal_fin_affectation');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_fin_affectation').modal('show');
	}

	window.addEventListener('load', function () {
		const modalFin = document.getElementById('modal_fin_affectation');
		if (modalFin && modalFin.dataset.ouvrir === '1') {
			const f = document.getElementById('form_fin_affectation');
			const motif = f.querySelector('[name="motif_fin"]').value;
			const date = f.querySelector('[name="date_fin"]').value;
			aff_fin_ouvrir(f.querySelector('[name="mandat_id"]').value, f.querySelector('[name="libelle_poste"]').value, f.querySelector('[name="est_interim"]').value === '1', '');
			f.querySelector('[name="motif_fin"]').value = motif; f.querySelector('[name="date_fin"]').value = date;
		}

		const formulaire = document.getElementById('form_affectation');
		if (!formulaire) { return; }

		document.getElementById('aff_fonction').addEventListener('change', function () { aff_maj_postes(null); });
		document.getElementById('aff_unite').addEventListener('change', aff_maj_occupation);
		document.getElementById('aff_interim').addEventListener('change', aff_maj_occupation);

		formulaire.addEventListener('submit', function () {
			const bouton = document.getElementById('aff_btn');
			bouton.disabled = true; bouton.textContent = 'Affectation…';
		});

		// Retour apres une erreur ou un conflit signale par le serveur : on rouvre la fenetre avec la saisie
		const modal = document.getElementById('modal_affectation');
		if (modal && modal.dataset.ouvrir === '1') {
			aff_maj_postes(@json(old('unite_id')));
			aff_ouvrir_modal();
		}
	});

</script>
