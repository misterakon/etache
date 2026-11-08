<script type="text/javascript">

	/* ============================================================
	   add_form() generique, add_form_service() dediee (Service a
	   deux boutons d'ajout selon le type de rattachement, voir plus bas)
	   ============================================================ */
	function add_form() {
		$('.modal_form')[0].reset();
		$('[name="id_"]').val('');
		$('#BtnEnregistrer').html('ENREGISTRER').val('Enregistrer');
		$('.submit').attr('disabled', false);
		if (typeof reset_photo_apercu === 'function') { reset_photo_apercu(''); } // sans effet hors page Personnel

		$('#tooltipmodal').modal('show');
	}

	function formatDatePourInput(valeur) {
		return valeur ? valeur.substring(0, 10) : '';
	}


	/* ============================================================
	   DIRECTION
	   ============================================================ */
	function edit_direction(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('dgepn.direction.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="code"]').val(data.code);
				$('[name="nom"]').val(data.nom);
				$('[name="description"]').val(data.description);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_direction(id) {
		Swal.fire({
			title: 'DGEPN',
			text: "Êtes-vous sûr de vouloir supprimer cette direction ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('dgepn.direction.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('DGEPN', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function () { Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error'); }
				});
			}
		});
	}

	/* ============================================================
	   SOUS-DIRECTION
	   ============================================================ */
	function edit_sous_direction(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('dgepn.sous_direction.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="direction_id"]').val(data.direction_id);
				$('[name="code"]').val(data.code);
				$('[name="nom"]').val(data.nom);
				$('[name="description"]').val(data.description);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_sous_direction(id) {
		Swal.fire({
			title: 'DGEPN',
			text: "Êtes-vous sûr de vouloir supprimer cette sous-direction ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('dgepn.sous_direction.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('DGEPN', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function () { Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error'); }
				});
			}
		});
	}

	/* ============================================================
	   SERVICE (rattachement sous-direction OU direction)
	   ============================================================ */
	function toggle_rattachement_service() {
		const type = $('input[name="rattachement"]:checked').val();

		if (type === 'sous_direction') {
			$('#bloc_sous_direction').removeClass('hidden');
			$('#bloc_direction_directe').addClass('hidden');
			$('[name="direction_id"]').val('');
		} else if (type === 'direction') {
			$('#bloc_direction_directe').removeClass('hidden');
			$('#bloc_sous_direction').addClass('hidden');
			$('[name="sous_direction_id"]').val('');
		} else {
			// "dg" : aucun des deux selects n'est necessaire
			$('#bloc_sous_direction').addClass('hidden');
			$('#bloc_direction_directe').addClass('hidden');
			$('[name="sous_direction_id"]').val('');
			$('[name="direction_id"]').val('');
		}
	}

	function edit_service(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('dgepn.service.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="code"]').val(data.code);
				$('[name="nom"]').val(data.nom);
				$('[name="description"]').val(data.description);

				$('input[name="rattachement"][value="' + data.rattachement + '"]').prop('checked', true);
				toggle_rattachement_service();

				if (data.rattachement === 'sous_direction') {
					$('[name="sous_direction_id"]').val(data.sous_direction_id);
				} else if (data.rattachement === 'direction') {
					$('[name="direction_id"]').val(data.direction_id);
				}
				// "dg" : rien a preremplir, les deux selects restent vides/caches

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_service(id) {
		Swal.fire({
			title: 'DGEPN',
			text: "Êtes-vous sûr de vouloir supprimer ce service ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('dgepn.service.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('DGEPN', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function () { Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error'); }
				});
			}
		});
	}

	/* ============================================================
	   PHOTO (formulaire Personnel)
	   ============================================================ */
	function reset_photo_apercu(url) {
		$('#photo').val('');
		$('#supprimer_photo').val('0');

		if (url) {
			$('#photo_apercu').attr('src', url).removeClass('hidden');
			$('#photo_placeholder').addClass('hidden');
			$('#btn_retirer_photo').removeClass('hidden');
		} else {
			$('#photo_apercu').attr('src', '').addClass('hidden');
			$('#photo_placeholder').removeClass('hidden');
			$('#btn_retirer_photo').addClass('hidden');
		}
	}

	function apercu_photo(input) {
		const fichier = input.files && input.files[0];
		if (!fichier) { return; }

		if (fichier.size > 2 * 1024 * 1024) {
			Swal.fire('DGEPN', 'La photo ne doit pas dépasser 2 Mo.', 'warning');
			input.value = '';
			return;
		}

		const lecteur = new FileReader();
		lecteur.onload = function (e) {
			$('#photo_apercu').attr('src', e.target.result).removeClass('hidden');
			$('#photo_placeholder').addClass('hidden');
			$('#btn_retirer_photo').removeClass('hidden');
			$('#supprimer_photo').val('0');
		};
		lecteur.readAsDataURL(fichier);
	}

	function retirer_photo() {
		$('#photo').val('');
		$('#supprimer_photo').val('1'); // le serveur supprimera le fichier existant
		$('#photo_apercu').attr('src', '').addClass('hidden');
		$('#photo_placeholder').removeClass('hidden');
		$('#btn_retirer_photo').addClass('hidden');
	}

	/* ============================================================
	   PERSONNEL
	   ============================================================ */
	function edit_personnel(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('dgepn.personnel.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="service_id"]').val(data.service_id);
				$('[name="type_agent_id"]').val(data.type_agent_id);
				$('[name="emploi_id"]').val(data.emploi_id);
				$('[name="matricule"]').val(data.matricule);
				$('[name="nom"]').val(data.nom);
				$('[name="prenom"]').val(data.prenom);
				$('[name="civilite_id"]').val(data.civilite_id);
				$('[name="genre_id"]').val(data.genre_id);
				$('[name="date_naissance"]').val(formatDatePourInput(data.date_naissance));
				$('[name="lieu_naissance"]').val(data.lieu_naissance);
				$('[name="nom_pere"]').val(data.nom_pere);
				$('[name="nom_mere"]').val(data.nom_mere);
				$('[name="statut_matrimonial_id"]').val(data.statut_matrimonial_id);
				$('[name="nombre_enfants"]').val(data.nombre_enfants);
				$('[name="fonction"]').val(data.fonction);
				$('[name="email"]').val(data.email);
				$('[name="telephone"]').val(data.telephone);
				$('[name="lieu_habitation"]').val(data.lieu_habitation);
				$('[name="actif"]').val(data.actif ? "1" : "0");
				reset_photo_apercu(data.photo_url);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function edit_info(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		$.ajax({
			url: "{{ route('dgepn.personnel.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="service_id"]').val(data.service_id);
				$('[name="type_agent_id"]').val(data.type_agent_id);
				$('[name="emploi_id"]').val(data.emploi_id);
				$('[name="matricule"]').val(data.matricule);
				$('[name="nom"]').val(data.nom);
				$('[name="prenom"]').val(data.prenom);
				$('[name="date_naissance"]').val(formatDatePourInput(data.date_naissance));
				$('[name="lieu_naissance"]').val(data.lieu_naissance);
				$('[name="fonction"]').val(data.fonction);
				$('[name="email"]').val(data.email);
				$('[name="telephone"]').val(data.telephone);
				$('[name="lieu_habitation"]').val(data.lieu_habitation);
				$('[name="actif"]').val(data.actif ? "1" : "0");
				reset_photo_apercu(data.photo_url);

				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () { alert('Erreur lors du chargement des données.'); }
		});
	}

	function deletes_personnel(id) {
		Swal.fire({
			title: 'DGEPN',
			text: "Archiver cet agent ? Sa fiche n'est jamais supprimée : son poste prend fin, son compte est désactivé et tout son historique est conservé.",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, archiver'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('dgepn.personnel.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('DGEPN', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () { $(this).remove(); });
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function (jqXHR) {
						Swal.fire('Erreur', jqXHR.responseJSON?.message || "Une erreur s'est produite lors de l'archivage.", 'error');
					}
				});
			}
		});
	}

	// Depuis la fiche d'un agent : /dgepn/personnel?edit=12 ouvre directement sa modification.
	$(function () {
		const idAModifier = new URLSearchParams(window.location.search).get('edit');
		if (idAModifier && document.getElementById('photo_apercu')) { edit_personnel(idAModifier); }
	});

</script>
