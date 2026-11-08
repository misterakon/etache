<script type="text/javascript">

	/* ============================================================
	   COURRIER : enregistrement, glisser-deposer du scan, apercu, detection de doublon.
	   JS natif pour tout ce qui se lie au chargement : jQuery n'est pas encore charge quand ce script s'execute.
	   ============================================================ */
	const CR_URL_DOUBLON = "{{ route('courrier.doublon') }}";

	/* ---------- Ouverture du formulaire ---------- */
	function cr_vers_body() {
		const modal = document.getElementById('modal_courrier');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
	}

	function cr_ouvrir() {
		cr_vers_body();
		$('#modal_courrier').modal('show');
	}

	/* Mode de transmission : le champ « précisez » n'apparait que pour « Autre » */
	function cr_maj_mode() {
		const autre = document.getElementById('cr_mode').value === 'autre';
		document.getElementById('cr_mode_precision_bloc').style.display = autre ? '' : 'none';
		document.getElementById('cr_mode_precision').required = autre;
	}

	function cr_remettre_a_zero() {
		const form = document.getElementById('form_courrier');
		form.reset();
		form.querySelector('[name="id_"]').value = '';
		form.querySelectorAll('.cr-erreur').forEach(function (e) { e.remove(); });
		form.querySelector('#cr_date_reception').value = form.querySelector('#cr_date_reception').max;
		const urgence = form.querySelector('[name="urgence_id"]');
		if (urgence.dataset.defaut) { urgence.value = urgence.dataset.defaut; }
		cr_retirer_scan();
		cr_doublon_masquer();
		cr_maj_mode();
		document.getElementById('cr_scan_modif').style.display = 'none';
		document.getElementById('cr_scan').required = document.getElementById('cr_drop').dataset.requis === '1';
	}

	function cr_nouveau() {
		cr_vers_body();
		cr_remettre_a_zero();
		document.getElementById('cr_titre_form').innerHTML = '<i class="fa-solid fa-envelope-open-text"></i> Enregistrer un courrier arrivée';
		document.getElementById('cr_btn_enregistrer').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer le courrier';
		cr_ouvrir();
	}

	// Modification : les donnees du courrier sont portees par le bouton (data-courrier), aucune requete
	function cr_modifier(bouton) {
		const d = JSON.parse(bouton.dataset.courrier);
		cr_vers_body();
		cr_remettre_a_zero();
		const f = document.getElementById('form_courrier');
		f.querySelector('[name="id_"]').value = d.id;
		f.querySelector('[name="origine"]').value = d.origine;
		f.querySelector('[name="date_reception"]').value = d.date_reception || '';
		f.querySelector('[name="date_courrier"]').value = d.date_courrier || '';
		f.querySelector('[name="reference_courrier"]').value = d.reference_courrier || '';
		f.querySelector('[name="expediteur"]').value = d.expediteur || '';
		f.querySelector('[name="objet"]').value = d.objet || '';
		f.querySelector('[name="type_courrier_id"]').value = d.type_courrier_id;
		f.querySelector('[name="observations"]').value = d.observations || '';
		f.querySelector('[name="nombre_pieces"]').value = d.nombre_pieces || 0;
		f.querySelector('[name="mode_transmission"]').value = d.mode_transmission || '';
		f.querySelector('[name="mode_transmission_precision"]').value = d.mode_transmission_precision || '';
		cr_maj_mode();
		f.querySelector('[name="confidentialite"]').checked = d.confidentialite === 'confidentiel';
		f.querySelector('[name="urgence_id"]').value = d.urgence_id;
		document.getElementById('cr_scan').required = false; // le scan existant est conserve s'il n'est pas remplace
		document.getElementById('cr_scan_modif').style.display = d.a_un_scan ? '' : 'none';
		document.getElementById('cr_titre_form').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Modifier le courrier ' + d.numero;
		document.getElementById('cr_btn_enregistrer').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications';
		cr_ouvrir();
	}

	/* ---------- Zone de depot du scan ---------- */
	let cr_url_apercu = null;

	function cr_taille_lisible(octets) {
		return octets >= 1048576 ? (octets / 1048576).toFixed(1).replace('.', ',') + ' Mo' : Math.max(1, Math.round(octets / 1024)) + ' Ko';
	}

	function cr_message_scan(texte) {
		document.getElementById('cr_drop_message').textContent = texte || '';
		document.getElementById('cr_drop').classList.toggle('cr-drop--erreur', !!texte);
	}

	function cr_retirer_scan() {
		const champ = document.getElementById('cr_scan');
		if (champ) { champ.value = ''; }
		if (cr_url_apercu) { URL.revokeObjectURL(cr_url_apercu); cr_url_apercu = null; }
		const drop = document.getElementById('cr_drop');
		if (!drop) { return; }
		drop.classList.remove('cr-drop--ok', 'cr-drop--actif', 'cr-drop--erreur');
		document.getElementById('cr_drop_invite').hidden = false;
		document.getElementById('cr_drop_fichier').hidden = true;
		document.getElementById('cr_apercu').hidden = true;
		document.getElementById('cr_apercu_corps').innerHTML = '';
		cr_message_scan('');
	}

	function cr_afficher_scan(fichier) {
		const drop = document.getElementById('cr_drop');
		const extensions = drop.dataset.ext.split(',');
		const maxKo = parseInt(drop.dataset.maxKo, 10);
		const ext = (fichier.name.split('.').pop() || '').toLowerCase();

		if (extensions.indexOf(ext) === -1) {
			cr_retirer_scan();
			cr_message_scan('Type de fichier non accepté (' + extensions.join(', ').toUpperCase() + ').');
			return;
		}
		if (fichier.size > maxKo * 1024) {
			cr_retirer_scan();
			cr_message_scan('Le fichier dépasse la taille maximale (' + (maxKo / 1024).toFixed(1).replace('.', ',') + ' Mo).');
			return;
		}

		cr_message_scan('');
		drop.classList.add('cr-drop--ok');
		document.getElementById('cr_drop_invite').hidden = true;
		document.getElementById('cr_drop_fichier').hidden = false;
		document.getElementById('cr_drop_nom').textContent = fichier.name;
		document.getElementById('cr_drop_taille').textContent = cr_taille_lisible(fichier.size) + ' · ' + ext.toUpperCase();
		document.getElementById('cr_drop_icone').className = ext === 'pdf' ? 'fa-regular fa-file-pdf' : 'fa-regular fa-file-image';

		// Apercu immediat dans la fenetre, avant l'enregistrement
		if (cr_url_apercu) { URL.revokeObjectURL(cr_url_apercu); }
		cr_url_apercu = URL.createObjectURL(fichier);
		const corps = document.getElementById('cr_apercu_corps');
		corps.innerHTML = '';
		if (ext === 'pdf') {
			const cadre = document.createElement('iframe');
			cadre.src = cr_url_apercu + '#toolbar=0&navpanes=0&view=FitH';
			cadre.title = 'Aperçu du scan';
			corps.appendChild(cadre);
		} else {
			const image = document.createElement('img');
			image.src = cr_url_apercu;
			image.alt = 'Aperçu du scan';
			corps.appendChild(image);
		}
		document.getElementById('cr_apercu').hidden = false;
	}

	/* ---------- Detection de doublon en direct ---------- */
	function cr_doublon_masquer() {
		const bloc = document.getElementById('cr_doublon');
		if (bloc) { bloc.classList.remove('cr-doublon--visible'); }
		const confirmation = document.getElementById('cr_confirmer_doublon');
		if (confirmation) { confirmation.checked = false; }
	}

	function cr_verifier_doublon() {
		const f = document.getElementById('form_courrier');
		const expediteur = f.querySelector('[name="expediteur"]').value.trim();
		const reference = f.querySelector('[name="reference_courrier"]').value.trim();
		const date = f.querySelector('[name="date_courrier"]').value;
		if (!expediteur || !reference || !date) { cr_doublon_masquer(); return; }

		const adresse = CR_URL_DOUBLON + '?expediteur=' + encodeURIComponent(expediteur) + '&reference=' + encodeURIComponent(reference)
			+ '&date_courrier=' + encodeURIComponent(date) + '&ignore=' + encodeURIComponent(f.querySelector('[name="id_"]').value || '');
		fetch(adresse, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
			.then(function (r) { return r.ok ? r.json() : { existe: false }; })
			.then(function (d) {
				if (!d.existe) { cr_doublon_masquer(); return; }
				document.getElementById('cr_doublon_num').textContent = d.numero;
				document.getElementById('cr_doublon_date').textContent = d.recu_le;
				document.getElementById('cr_doublon_lien').href = d.url;
				document.getElementById('cr_doublon').classList.add('cr-doublon--visible');
			})
			.catch(function () { /* la verification serveur reste faite a l'enregistrement */ });
	}

	/* ---------- Branchements (au chargement de la page) ---------- */
	window.addEventListener('load', function () {
		const drop = document.getElementById('cr_drop');
		const champ = document.getElementById('cr_scan');
		const formulaire = document.getElementById('form_courrier');
		if (!drop || !champ || !formulaire) { return; }

		['dragenter', 'dragover'].forEach(function (evt) {
			drop.addEventListener(evt, function (e) { e.preventDefault(); drop.classList.add('cr-drop--actif'); });
		});
		['dragleave', 'dragend'].forEach(function (evt) {
			drop.addEventListener(evt, function () { drop.classList.remove('cr-drop--actif'); });
		});
		drop.addEventListener('drop', function (e) {
			e.preventDefault();
			drop.classList.remove('cr-drop--actif');
			if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
				const un = new DataTransfer();
				un.items.add(e.dataTransfer.files[0]); // un seul scan par courrier
				champ.files = un.files;
				cr_afficher_scan(e.dataTransfer.files[0]);
			}
		});
		champ.addEventListener('change', function () {
			if (champ.files && champ.files[0]) { cr_afficher_scan(champ.files[0]); }
		});
		document.getElementById('cr_drop_retirer').addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); cr_retirer_scan(); });
		drop.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); champ.click(); } });

		// Empeche le navigateur d'ouvrir un fichier lache a cote de la zone
		['dragover', 'drop'].forEach(function (evt) { window.addEventListener(evt, function (e) { if (!drop.contains(e.target)) { e.preventDefault(); } }); });

		document.getElementById('cr_mode').addEventListener('change', cr_maj_mode);
		cr_maj_mode();

		['expediteur', 'reference_courrier', 'date_courrier'].forEach(function (nom) {
			formulaire.querySelector('[name="' + nom + '"]').addEventListener('change', cr_verifier_doublon);
		});

		// Une seule validation : on evite les doubles clics
		formulaire.addEventListener('submit', function () {
			const bouton = document.getElementById('cr_btn_enregistrer');
			bouton.disabled = true;
			bouton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';
		});

		// Retour apres une erreur de validation ou un doublon detecte par le serveur : on rouvre la fenetre
		const modal = document.getElementById('modal_courrier');
		if (modal && modal.dataset.ouvrir === '1') {
			cr_vers_body();
			if (formulaire.querySelector('[name="id_"]').value) {
				document.getElementById('cr_titre_form').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Modifier le courrier';
			}
			cr_ouvrir();
		}
	});

</script>
