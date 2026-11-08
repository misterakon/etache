<script type="text/javascript">

	/* ============================================================
	   SOUS-TACHES (onglet du detail d'une tache)
	   - ajout / modification : formulaire classique envoye au controleur (redirection vers #sous-taches)
	   - coche / suppression : AJAX, le serveur renvoie la liste rafraichie (html)
	   ============================================================ */

	/* Apres l'enregistrement d'une sous-tache, la redirection arrive avec #sous-taches : on rouvre l'onglet */
	window.addEventListener('load', function () {
		if (window.location.hash !== '#sous-taches') { return; }

		const lien = document.querySelector('.tab-link[data-tabfilter="3"]');
		if (lien) { lien.click(); }

		// Repli : si le script du theme n'a pas bascule l'onglet, on le fait nous-memes
		setTimeout(function () {
			const $panneau = $('.tab-pan[data-tabcontent="3"]');
			if ($panneau.hasClass('active') && $panneau.is(':visible')) { return; }
			$('.tab-link').removeClass('active');
			$('.tab-link[data-tabfilter="3"]').addClass('active');
			$('.tab-pan').removeClass('show active');
			$panneau.addClass('show active');
		}, 150);
	});

	function st_rafraichir(reponse) {
		$('#zone-sous-taches').html(reponse.html);
		if (reponse.cloture_soumise) {
			Swal.fire('Clôture soumise', "Toutes les sous-tâches sont faites : la clôture de la tâche est soumise à la validation de votre supérieur hiérarchique. La tâche reste « En cours » jusqu'à sa décision.", 'info');
		}
	}

	function st_erreur(xhr) {
		const json = xhr.responseJSON || {};
		const premiere = json.errors ? Object.values(json.errors)[0][0] : null;
		Swal.fire('Erreur', premiere || json.message || "Une erreur s'est produite.", 'error');
	}

	/* Ouvre le modal apres l'avoir place sous <body> (il est dans un onglet) ; signale clairement tout probleme */
	function st_ouvrir_modal() {
		const modal = document.getElementById('modal_sous_tache');
		if (!modal) { alert("Formulaire de sous-tâche introuvable : vérifiez que la vue form_sous_tache est bien incluse."); return false; }
		if (modal.parentElement !== document.body) { document.body.appendChild(modal); }
		try {
			$(modal).modal('show'); // $.fn.modal est redefini dans layout/script.blade.php : on ne passe jamais par l'API native de Bootstrap
		} catch (e) {
			alert("Impossible d'ouvrir le formulaire : " + e.message);
			return false;
		}
		return true;
	}

	function st_add_form(tacheId) {
		const form = document.getElementById('form_sous_tache');
		if (!form) { st_ouvrir_modal(); return; }
		form.reset();
		$('#form_sous_tache [name="id_"]').val('');
		$('#form_sous_tache [name="tache_id"]').val(tacheId);
		$('#st_btn_enregistrer').html('ENREGISTRER');
		st_ouvrir_modal();
	}

	// Les donnees de la sous-tache sont portees par le bouton crayon (data-*) : aucune requete, le formulaire s'ouvre directement
	function st_edit(bouton) {
		const form = document.getElementById('form_sous_tache');
		if (!form) { st_ouvrir_modal(); return; }
		form.reset();
		$('#form_sous_tache [name="id_"]').val(bouton.dataset.id);
		$('#form_sous_tache [name="libelle"]').val(bouton.dataset.libelle || '');
		$('#form_sous_tache [name="description"]').val(bouton.dataset.description || '');
		$('#form_sous_tache [name="echeance"]').val(bouton.dataset.echeance || '');
		$('#form_sous_tache [name="personnel_id"]').val(bouton.dataset.agent || '');
		$('#form_sous_tache [name="modalites"]').val(bouton.dataset.modalites || '');
		$('#form_sous_tache [name="conditions"]').val(bouton.dataset.conditions || '');
		$('#st_btn_enregistrer').html('MODIFIER');
		st_ouvrir_modal();
	}

	function st_cocher(id, coche, caseACocher) {
		$.ajax({
			url: "{{ route('feuille_de_route.sous_tache.cocher', ':id') }}".replace(':id', id),
			type: 'POST',
			data: { _token: "{{ csrf_token() }}", cochee: coche ? 1 : 0 },
			dataType: 'JSON',
			success: st_rafraichir,
			error: function (xhr) {
				if (caseACocher) { caseACocher.checked = !coche; }   // on annule le clic si le serveur refuse
				st_erreur(xhr);
			}
		});
	}

	function st_valider(id) {
		$.ajax({
			url: "{{ route('feuille_de_route.sous_tache.valider', ':id') }}".replace(':id', id),
			type: 'POST', data: { _token: "{{ csrf_token() }}" }, dataType: 'JSON', success: st_rafraichir, error: st_erreur
		});
	}

	function st_refuser(id) {
		Swal.fire({
			title: 'Refuser la sous-tâche', input: 'textarea', inputLabel: 'Motif du refus (obligatoire)',
			showCancelButton: true, confirmButtonText: 'Refuser', cancelButtonText: 'Annuler',
			inputValidator: function (v) { if (!v || v.trim().length < 5) { return 'Précisez le motif (5 caractères minimum).'; } }
		}).then(function (r) {
			if (!r.isConfirmed) { return; }
			$.ajax({
				url: "{{ route('feuille_de_route.sous_tache.refuser', ':id') }}".replace(':id', id),
				type: 'POST', data: { _token: "{{ csrf_token() }}", motif: r.value }, dataType: 'JSON', success: st_rafraichir, error: st_erreur
			});
		});
	}

	function st_delete(id, estCochee) {
		Swal.fire({
			title: 'e-Tache',
			text: estCochee ? 'Cette sous-tâche est déjà faite. La supprimer fera baisser l\'avancement. Continuer ?' : 'Supprimer cette sous-tâche ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (!result.isConfirmed) { return; }
			$.ajax({
				url: "{{ route('feuille_de_route.sous_tache.delete', ':id') }}".replace(':id', id),
				type: 'DELETE',
				data: { _token: "{{ csrf_token() }}" },
				dataType: 'JSON',
				success: st_rafraichir,
				error: st_erreur
			});
		});
	}

</script>
