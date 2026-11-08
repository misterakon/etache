<script type="text/javascript">

	function add_form() {
		$('.modal_form')[0].reset();
		$('[name="id_"]').val('');
		$('#BtnEnregistrer').html('ENREGISTRER').val('Enregistrer');
		$('.submit').attr('disabled', false);
		$('#dependances option').prop('disabled', false); // sans effet si absent de la page

		$('#tooltipmodal').modal('show');
	}

	function formatDatePourInput(valeur) {
		return valeur ? valeur.substring(0, 10) : '';
	}

	/* ============================================================
	   Helper generique : precharge les multi-select d'affectations
	   (Responsable/Pilote/Contributeur), reutilise par ActionCle,
	   ActionMesurable et Tache. `affectationsParRole` est un objet
	   du type { "3": [12, 7], "4": [12] } (role_affectation_id => [personnel_id,...])
	   ============================================================ */
	function preremplirAffectations(affectationsParRole) {
		$('select[name^="affectations["]').val([]); // tout decocher d'abord
		$.each(affectationsParRole || {}, function (roleId, personnelIds) {
			$('select[name="affectations[' + roleId + '][]"]').val(personnelIds);
		});
	}


	/* ============================================================
	   AXE
	   ============================================================ */
	function edit_axe(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('feuille_de_route.axe.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="exercice_id"]').val(data.exercice_id);
				$('[name="ordre"]').val(data.ordre);
				$('[name="libelle"]').val(data.libelle);
				$('[name="resultat_attendu"]').val(data.resultat_attendu);
				$('[name="description"]').val(data.description);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}

	function deletes_axe(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet axe ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('feuille_de_route.axe.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('e-Tache', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					// Le controleur renvoie un code 422 (avec le meme format status/message)
					// quand la suppression est bloquee car des actions cles sont encore rattachees.
					error: function (jqXHR) {
						const message = jqXHR.responseJSON?.message || "Une erreur s'est produite lors de la suppression.";
						Swal.fire('Erreur', message, 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   ACTION CLE
	   ============================================================ */
	function edit_action_cle(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('feuille_de_route.action_cle.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="axe_id"]').val(data.axe_id);
				$('[name="direction_id"]').val(data.direction_id);
				$('[name="code"]').val(data.code);
				$('[name="ordre"]').val(data.ordre);
				$('[name="libelle"]').val(data.libelle);
				$('[name="description"]').val(data.description);

				preremplirAffectations(data.affectations);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_action_cle(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cette action clé ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('feuille_de_route.action_cle.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('e-Tache', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function (jqXHR) {
						Swal.fire('Erreur', jqXHR.responseJSON?.message || "Une erreur s'est produite.", 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   ACTION MESURABLE
	   ============================================================ */
	function edit_action_mesurable(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('feuille_de_route.action_mesurable.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="action_cle_id"]').val(data.action_cle_id);
				$('[name="ordre"]').val(data.ordre);
				$('[name="libelle"]').val(data.libelle);
				$('[name="description"]').val(data.description);

				preremplirAffectations(data.affectations);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_action_mesurable(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cette action mesurable ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('feuille_de_route.action_mesurable.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('e-Tache', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function (jqXHR) {
						Swal.fire('Erreur', jqXHR.responseJSON?.message || "Une erreur s'est produite.", 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   TACHE
	   ============================================================ */
	function edit_tache(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		// Reactive toutes les options du multi-select "depend de" avant de charger
		// (au cas ou une precedente edition aurait desactive l'option courante)
		$('#dependances option').prop('disabled', false);

		$.ajax({
			url: "{{ route('feuille_de_route.tache.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="action_mesurable_id"]').val(data.action_mesurable_id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="indicateur"]').val(data.indicateur);
				$('[name="livrable"]').val(data.livrable);
				$('[name="date_debut"]').val(formatDatePourInput(data.date_debut));
				$('[name="date_fin"]').val(formatDatePourInput(data.date_fin));
				$('[name="duree_jours"]').val(data.duree_jours);
				$('[name="ordre"]').val(data.ordre);
				$('[name="priorite_id"]').val(data.priorite_id);
				$('[name="statut_execution_id"]').val(data.statut_execution_id);
				$('[name="avancement_pourcentage"]').val(data.avancement_pourcentage);
				$('[name="budget_estime"]').val(data.budget_estime);
				$('[name="observations"]').val(data.observations);

				// Une tache ne peut pas dependre d'elle-meme : on desactive son option
				$('#dependances option[value="' + data.id + '"]').prop('disabled', true);
				$('[name="dependances[]"]').val(data.dependances_ids || []);

				preremplirAffectations(data.affectations);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_tache(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cette tâche ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('feuille_de_route.tache.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('e-Tache', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function (jqXHR) {
						Swal.fire('Erreur', jqXHR.responseJSON?.message || "Une erreur s'est produite.", 'error');
					}
				});
			}
		});
	}

</script>
