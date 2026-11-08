<script type="text/javascript">

	function add_form() {
		$('.modal_form')[0].reset();
		$('[name="id_"]').val('');
		$('#BtnEnregistrer').html('ENREGISTRER').val('Enregistrer');
		// Regles d'alerte : par defaut, le responsable et le pilote sont destinataires
		if ($('input[name="destinataires[]"]').length) { $('#dest_responsable, #dest_pilote').prop('checked', true); }
		// Profil d'accès : aucun droit par défaut, champs déverrouillés, création (jamais « système »)
		if ($('#pa_catalogue').length) {
			pa_initialiser();
			$('#pa_catalogue input[type=radio][value=aucun]').prop('checked', true);
			$('#pa_catalogue input, #libelle, #slug, #actif').prop('disabled', false);
			$('#pa_catalogue .pa-page').each(function () { maj_speciales_page($(this)); });
			$('#pa_catalogue .pa-menu').removeAttr('open');
			$('#pa_alerte_systeme').attr('hidden', true);
			maj_nb_pages_profil();
			maj_avatar_apercu();
		}
		$('.submit').attr('disabled', false);

		$('#tooltipmodal').modal('show');
	}
 
 
	/* ======================= EXERCICE =====================================
	   EXERCICE
	   ============================================================ */
	function edit_exercice(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.exercice.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="annee"]').val(data.annee);
				$('[name="libelle"]').val(data.libelle);
				$('[name="date_debut"]').val(formatDatePourInput(data.date_debut));
				$('[name="date_fin"]').val(formatDatePourInput(data.date_fin));
				$('[name="actif"]').val(data.actif ? "1" : "0");
				$('[name="mode_avancement"]').val(data.mode_avancement || 'manuel');
 
				$('.submit').removeClass('disabled').attr('disabled', false);
 
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_exercice(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet exercice ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.exercice.delete', ':id') }}".replace(':id', id),
					type: "DELETE",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						if (response.status) {
							Swal.fire('e-Tache', response.message, 'success');
							$("#row-" + id).fadeOut(500, function () {
								$(this).remove();
							});
						} else {
							Swal.fire('Erreur', response.message, 'error');
						}
					},
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   TYPE AGENT
	   ============================================================ */
	function edit_type_agent(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.type_agent.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_type_agent(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer ce type d'agent ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.type_agent.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   FAMILLE D'EMPLOI
	   ============================================================ */
	function edit_famille_emploi(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.famille_emploi.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_famille_emploi(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cette famille d'emploi ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.famille_emploi.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   EMPLOI
	   ============================================================ */
	function edit_emploi(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.emploi.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="famille_emploi_id"]').val(data.famille_emploi_id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_emploi(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet emploi ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.emploi.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   PROFIL D'ACCÈS
	   ============================================================ */
	/* Les droits spéciaux d'une page ne comptent que si son niveau n'est pas « Aucun accès » :
	   on grise et décoche leurs interrupteurs tant qu'aucun niveau n'est choisi. */
	function maj_speciales_page($page)
	{
		var aucun = $page.find('input[type=radio][value=aucun]').is(':checked');
		var $switches = $page.find('.pa-speciale input[type=checkbox]');
		if (aucun) { $switches.prop('checked', false); }
		$switches.prop('disabled', aucun);
		$page.find('.pa-speciale').toggleClass('pa-speciale--verrouillee', aucun);
	}

	function maj_nb_pages_profil()
	{
		var total = 0;
		$('#pa_catalogue .pa-menu').each(function () {
			var $menu = $(this);
			var $pages = $menu.find('.pa-page');
			var n = 0, complet = 0;
			$pages.each(function () {
				var $page = $(this);
				var $radios = $page.find('.pa-niveaux input[type=radio]');
				var $coche = $radios.filter(':checked');
				if ($coche.length && $coche.val() !== 'aucun') { n++; }
				var auMax = $coche.length && $coche.is($radios.last());
				var $speciales = $page.find('.pa-speciale input[type=checkbox]');
				if (auMax && $speciales.length === $speciales.filter(':checked').length) { complet++; }
			});
			var $compteur = $menu.find('.pa-menu__compteur');
			$compteur.text(n + '/' + $pages.length).toggleClass('pa-actif', n > 0);
			total += n;

			// Interrupteur du menu : coché si tout est au maximum, « partiel » (piste ambrée, curseur au milieu) sinon dès qu'il y a un accès
			var $interrupteur = $menu.find('.pa-menu-tout');
			var complets = $pages.length > 0 && complet === $pages.length;
			var partiel = ! complets && n > 0;
			$interrupteur.prop('checked', complets);
			if ($interrupteur.length) {
				$interrupteur[0].indeterminate = partiel;
				$interrupteur.closest('.pa-switch').toggleClass('pa-switch--partiel', partiel);
			}
		});
		$('#pa_nb_pages').text(total + ' sous-menu' + (total > 1 ? 's' : '') + ' accessible' + (total > 1 ? 's' : ''));
	}

	// Place chaque sous-menu d'un menu à son niveau le plus haut (ou « Aucun ») et ses droits spéciaux
	function pa_basculer_menu(menu, etat)
	{
		$('#pa_menu_' + menu + ' .pa-page').each(function () {
			var $page = $(this);
			var $radios = $page.find('.pa-niveaux input[type=radio]');
			(etat ? $radios.last() : $radios.filter('[value=aucun]')).prop('checked', true);
			$page.find('.pa-speciale input[type=checkbox]').prop('checked', etat);
			maj_speciales_page($page);
		});
		maj_nb_pages_profil();
	}

	// jQuery n'est pas encore chargé au moment où ce script s'exécute (il l'est plus bas dans le layout) :
	// ces écouteurs ne peuvent donc être posés qu'à la première ouverture du formulaire, pas tout de suite.
	function maj_avatar_apercu()
	{
		var texte = $('#libelle').val().trim();
		$('#pa_avatar_apercu').text(texte ? texte.charAt(0).toUpperCase() : '?');
	}

	var pa_pret = false;
	function pa_initialiser()
	{
		if (pa_pret) { return; }
		pa_pret = true;
		$(document).on('change', '#pa_catalogue input[type=radio]', function () {
			maj_speciales_page($(this).closest('.pa-page'));
			maj_nb_pages_profil();
		});
		$(document).on('change', '#pa_catalogue .pa-speciale input[type=checkbox]', function () {
			maj_nb_pages_profil();
		});
		$(document).on('change', '.pa-menu-tout', function () {
			pa_basculer_menu($(this).data('menu'), this.checked);
		});
		$('#pa_tout_ouvrir').on('click', function () { $('#pa_catalogue .pa-menu').attr('open', true); });
		$('#pa_tout_fermer').on('click', function () { $('#pa_catalogue .pa-menu').removeAttr('open'); });
		$('#libelle').on('input', maj_avatar_apercu);
		// Tout accorder : place chaque sous-menu à son niveau le plus haut et coche tous les droits spéciaux
		$('#pa_tout_accorder').on('click', function () {
			$('#pa_catalogue .pa-page').each(function () {
				var $page = $(this);
				$page.find('.pa-niveaux input[type=radio]').last().prop('checked', true);
				$page.find('.pa-speciales input[type=checkbox]').prop('checked', true);
				maj_speciales_page($page);
			});
			maj_nb_pages_profil();
			$('#pa_catalogue .pa-menu').attr('open', true);
		});
	}

	function edit_profil_acces(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
		if ($('#pa_catalogue').length) { pa_initialiser(); }

		$.ajax({
			url: "{{ route('parametre.profil_acces.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="slug"]').val(data.slug);
				$('[name="actif"]').val(data.actif ? "1" : "0");
				maj_avatar_apercu();

				// Droits : niveau par sous-menu, puis droits spéciaux
				$('#pa_catalogue input[type=radio][value=aucun]').prop('checked', true);
				$('#pa_catalogue input[type=checkbox]').prop('checked', false);
				$.each(data.niveaux || {}, function (page, niveau) {
					$('input[name="niveaux[' + page + ']"][value="' + niveau + '"]').prop('checked', true);
				});
				$.each(data.speciales || [], function (i, cle) {
					$('input[name="speciales[]"][value="' + cle + '"]').prop('checked', true);
				});
				$('#pa_catalogue .pa-page').each(function () { maj_speciales_page($(this)); });
				maj_nb_pages_profil();

				// Déplie automatiquement les menus où ce profil a déjà au moins un accès
				$('#pa_catalogue .pa-menu').each(function () {
					var $menu = $(this);
					var aDesDroits = $menu.find('input[type=radio]:checked').toArray().some(function (r) { return r.value !== 'aucun'; });
					$menu.prop('open', aDesDroits);
				});

				// Le profil « système » (Admin) a tous les droits d'office : non modifiable, non supprimable
				var verrouille = !!data.systeme;
				$('#pa_catalogue input, #libelle, #slug, #actif').prop('disabled', verrouille);
				if (verrouille) { $('#pa_catalogue .pa-speciale input').prop('disabled', true); }
				$('#pa_alerte_systeme').attr('hidden', !verrouille);
				$('.submit').toggleClass('disabled', verrouille).attr('disabled', verrouille);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_profil_acces(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet élément ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.profil_acces.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   RÔLE D'AFFECTATION
	   ============================================================ */
	function edit_role_affectation(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.role_affectation.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_role_affectation(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet élément ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.role_affectation.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   PRIORITÉ
	   ============================================================ */
	function edit_priorite(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.priorite.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="couleur"]').val(data.couleur || '#5B6B84');
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_priorite(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet élément ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.priorite.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   STATUT D'EXÉCUTION
	   ============================================================ */
	function edit_statut_execution(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.statut_execution.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="couleur"]').val(data.couleur || '#5B6B84');
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_statut_execution(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet élément ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.statut_execution.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   SITUATION TEMPORELLE
	   ============================================================ */
	function edit_situation_temporelle(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.situation_temporelle.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="couleur"]').val(data.couleur || '#5B6B84');
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_situation_temporelle(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cet élément ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.situation_temporelle.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}

	/* ============================================================
	   CANAL DE NOTIFICATION
	   ============================================================ */
	function edit_canal_notification(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		$.ajax({
			url: "{{ route('parametre.canal_notification.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="libelle"]').val(data.libelle);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_canal_notification(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer ce canal de notification ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.canal_notification.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}
 
	/* ============================================================
	   REGLE D'ALERTE (relation many-to-many avec les canaux)
	   ============================================================ */
	function edit_regle_alerte(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');
 
		// Decoche systematiquement toutes les cases avant de recocher les bonnes
		$('input[name="canaux[]"]').prop('checked', false);
		$('input[name="destinataires[]"]').prop('checked', false);
 
		$.ajax({
			url: "{{ route('parametre.regle_alerte.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="code"]').val(data.code);
				$('[name="libelle"]').val(data.libelle);
				$('[name="decalage_jours"]').val(data.decalage_jours);
				$('[name="actif"]').val(data.actif ? "1" : "0");
 
				(data.canaux_ids || []).forEach(function (canalId) {
					$('#canal_' + canalId).prop('checked', true);
				});

				(data.destinataires || []).forEach(function (cle) {
					$('#dest_' + cle).prop('checked', true);
				});
 
				$('.submit').removeClass('disabled').attr('disabled', false);
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}
 
	function deletes_regle_alerte(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer cette règle d'alerte ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('parametre.regle_alerte.delete', ':id') }}".replace(':id', id),
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
					error: function () {
						Swal.fire('Erreur', "Une erreur s'est produite lors de la suppression.", 'error');
					}
				});
			}
		});
	}

	function formatDatePourInput(valeur) {
		return valeur ? valeur.substring(0, 10) : '';
	}

</script>



