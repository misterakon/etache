<script type="text/javascript">

	/* ============================================================
	   LIVRABLES (onglet du detail d'une tache et sous-taches)
	   - depot / remplacement : formulaire classique envoye au controleur (redirection vers #livrables)
	   - suppression : AJAX puis rechargement
	   ============================================================ */
	/* Ouvre le modal apres l'avoir place sous <body> (il est dans un onglet) ; signale clairement tout probleme */
	function lv_ouvrir_modal() {
		const modal = document.getElementById('modal_livrable');
		if (!modal) { alert("Formulaire de dépôt introuvable : vérifiez que la vue form_livrable est bien incluse."); return false; }
		if (modal.parentElement !== document.body) { document.body.appendChild(modal); }
		try {
			$(modal).modal('show'); // $.fn.modal est redefini dans layout/script.blade.php : on ne passe jamais par l'API native de Bootstrap
		} catch (e) {
			alert("Impossible d'ouvrir le formulaire : " + e.message);
			return false;
		}
		return true;
	}

	function lv_ouvrir(type, id, remplaceId, nom) {
		const form = document.getElementById('form_livrable');
		if (!form) { lv_ouvrir_modal(); return; }
		form.reset();
		$('#form_livrable [name="attachable_type"]').val(type);
		$('#form_livrable [name="attachable_id"]').val(id);
		$('#form_livrable [name="remplace_id"]').val(remplaceId || '');

		if (remplaceId) {
			$('#lv_titre').text('Remplacer le fichier');
			$('#lv_remplace_info').text('Remplace : ' + nom + ' (l\'ancienne version est conservée).').show();
		} else {
			$('#lv_titre').text('Déposer un fichier');
			$('#lv_remplace_info').hide();
		}
		lv_ouvrir_modal();
	}

	function lv_add_form(type, id) { lv_ouvrir(type, id, null, ''); }

	function lv_replace_form(pieceId, type, attachableId, nom) { lv_ouvrir(type, attachableId, pieceId, nom); }

	function lv_delete(id) {
		Swal.fire({
			title: 'e-Tache',
			text: 'Supprimer ce fichier et toutes ses versions ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (!result.isConfirmed) { return; }
			$.ajax({
				url: "{{ route('feuille_de_route.piece_jointe.delete', ':id') }}".replace(':id', id),
				type: 'DELETE',
				data: { _token: "{{ csrf_token() }}" },
				dataType: 'JSON',
				success: function () {
					window.location.hash = '#livrables';
					window.location.reload();
				},
				error: function (xhr) {
					Swal.fire('Erreur', (xhr.responseJSON && xhr.responseJSON.message) || "Une erreur s'est produite.", 'error');
				}
			});
		});
	}

	/* Apres un depot (redirection avec #livrables) : on rouvre l'onglet Livrables */
	window.addEventListener('load', function () {
		if (window.location.hash !== '#livrables') { return; }

		const lien = document.querySelector('.tab-link[data-tabfilter="4"]');
		if (lien) { lien.click(); }

		setTimeout(function () {
			const $panneau = $('.tab-pan[data-tabcontent="4"]');
			if ($panneau.hasClass('active') && $panneau.is(':visible')) { return; }
			$('.tab-link').removeClass('active');
			$('.tab-link[data-tabfilter="4"]').addClass('active');
			$('.tab-pan').removeClass('show active');
			$panneau.addClass('show active');
		}, 150);
	});

</script>
