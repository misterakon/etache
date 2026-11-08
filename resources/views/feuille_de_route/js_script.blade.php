<script type="text/javascript">

	function add_form() {
		$('.modal_form')[0].reset();
		$('[name="id_"]').val('');
		$('#BtnEnregistrer').html('ENREGISTRER').val('Enregistrer');
		$('.submit').attr('disabled', false);

		$('#dependances option').prop('disabled', false); // sans effet si absent de la page

		$('#directions_associees option').prop('disabled', false); // sans effet si absent de la page
		if (typeof cascade_init === 'function') { cascade_init(); }

		initSelect2Modal();
		$('#tooltipmodal').modal('show');
	}

	function formatDatePourInput(valeur) {
		return valeur ? valeur.substring(0, 10) : '';
	}

	/* ============================================================
	   Select2 des multi-select du modal : appelé juste AVANT modal('show'), une fois les valeurs posées.
	   destroy puis init : Select2 relit les valeurs courantes (ajout, modification, après reset()).
	   Gardé par $.fn.select2 : une page sans Select2 ne plante pas.
	   ============================================================ */
	function initSelect2Modal() {
		if (!$.fn.select2) { return; }
		$('#tooltipmodal .select-basic-multiple-four').each(function () {
			const $s = $(this);
			if ($s.hasClass('select2-hidden-accessible')) { $s.select2('destroy'); }
			$s.select2({ dropdownParent: $('#tooltipmodal'), width: '100%' });
		});
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
				initSelect2Modal();
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
	function majDirectionsAssociees() {
		const principale = $('#tooltipmodal #direction_id').val();
		$('#directions_associees option').prop('disabled', false);
		if (principale) {
			$('#directions_associees option[value="' + principale + '"]').prop({ disabled: true, selected: false });
		}
		if ($.fn.select2) { $('#directions_associees').trigger('change.select2'); } // rafraîchit l'affichage Select2
	}
	$(document).on('change', '#tooltipmodal #direction_id', majDirectionsAssociees);

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
				cascade_positionner(data);
				$('[name="direction_id"]').val(data.direction_id);
				$('[name="directions_associees[]"]').val(data.directions_associees || []);
				majDirectionsAssociees();

				$('[name="code"]').val(data.code);
				$('[name="ordre"]').val(data.ordre);
				$('[name="libelle"]').val(data.libelle);
				$('[name="description"]').val(data.description);

				preremplirAffectations(data.affectations);

				$('.submit').removeClass('disabled').attr('disabled', false);
				initSelect2Modal();
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
				cascade_positionner(data);
				$('[name="ordre"]').val(data.ordre);
				$('[name="libelle"]').val(data.libelle);
				$('[name="description"]').val(data.description);

				preremplirAffectations(data.affectations);

				$('.submit').removeClass('disabled').attr('disabled', false);
				initSelect2Modal();
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
				cascade_positionner(data);
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
				initSelect2Modal();
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

	/* ============================================================
	   JUSTIFICATION DE RETARD
	   Modals : _modal_justification (demande) et justification-a-valider (decision)
	   Pages  : tache-details, mes_taches, justification-a-valider
	   Les URL arrivent par les attributs data-url des boutons (pas de route() ici).
	   ============================================================ */

	// Message lisible a partir d'une reponse d'erreur du serveur (422 validation, 403 droits...)
	function justification_erreur(jqXHR) {
		const r = jqXHR.responseJSON || {};
		if (r.errors) return Object.values(r.errors).flat().join(' ');
		if (r.message) return r.message;
		return jqXHR.status === 403
			? "Vous n'avez pas le droit d'effectuer cette action."
			: 'Une erreur est survenue (' + jqXHR.status + ').';
	}

	// --- Demande (responsable / pilote / administrateur) ---

	// Affiche ou masque la nouvelle echeance selon le type choisi
	function justification_majType() {
		const report = $('#formJustification input[name="type"]:checked').val() === 'report';
		$('#justifBlocReport, #justifReportInfo').toggle(report);
		$('#justif_date').prop('required', report);
	}

	// Ouverture du modal : btn = bouton .btn-justifier (data-url, data-titre, data-echeance AAAA-MM-JJ)
	function justifier_tache(btn) {
		const $b = $(btn);
		const echeance = $b.attr('data-echeance') || '';

		$('#formJustification')[0].reset();
		$('#formJustification').attr('action', $b.attr('data-url'));
		$('#justifTitre').text($b.attr('data-titre'));
		$('#justifErreur').hide();

		// Un report suppose une echeance actuelle ; la nouvelle date doit lui etre posterieure
		$('#justifRadioReport').prop('disabled', !echeance);
		$('#justif_date').attr('min', echeance ? new Date(Date.parse(echeance) + 86400000).toISOString().slice(0, 10) : '');

		justification_majType();
		$('#modalJustification').modal('show');
	}

	function envoyer_justification() {
		const $f = $('#formJustification');
		$('#justifErreur').hide();
		$('#btnEnvoyerJustif').prop('disabled', true);

		$.ajax({
			url: $f.attr('action'),
			type: 'POST',
			data: $f.serialize(),
			dataType: 'JSON',
			success: function (response) {
				Swal.fire('e-Tache', response.message, 'success').then(() => window.location.reload());
			},
			error: function (jqXHR) {
				$('#justifErreurTexte').text(justification_erreur(jqXHR));
				$('#justifErreur').show();
			},
			complete: function () { $('#btnEnvoyerJustif').prop('disabled', false); }
		});
	}

	// Retrait d'une demande en attente : btn = bouton .btn-annuler-justif (data-url)
	function annuler_justification(btn) {
		const url = $(btn).attr('data-url');
		Swal.fire({
			title: 'e-Tache',
			text: 'Annuler cette demande ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Non',
			confirmButtonText: 'Oui, annuler'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: url,
					type: 'POST',
					data: { _token: "{{ csrf_token() }}" },
					success: function () { window.location.reload(); },
					error: function (jqXHR) { Swal.fire('Erreur', justification_erreur(jqXHR), 'error'); }
				});
			}
		});
	}

	// --- Decision (ADMIN / DG / RD) : page "Demandes a valider" ---

	// Ouverture du modal d'examen : btn = bouton .btn-examiner (data-url, data-tache, data-demandeur, ...)
	function examiner_justification(btn) {
		const $b = $(btn);
		$('#formDecision').attr('action', $b.attr('data-url'));
		$.each({ tache: '#decTache', demandeur: '#decDemandeur', type: '#decType', motif: '#decMotif', echeance: '#decEcheance', commentaire: '#decCommentaire' }, function (cle, cible) {
			$(cible).text($b.attr('data-' + cle));
		});
		$('#dec_commentaire').val('');
		$('#decErreur').hide();
		$('#modalDecision').modal('show');
	}

	// decision = 'approuver' | 'refuser' (un refus doit etre motive)
	function decider_justification(decision) {
		const $f = $('#formDecision');
		$('#decErreur').hide();

		if (decision === 'refuser' && !$.trim($('#dec_commentaire').val())) {
			$('#decErreur').text('Un refus doit être motivé.').show();
			return;
		}

		$f.find('[data-decision]').prop('disabled', true);
		$.ajax({
			url: $f.attr('action'),
			type: 'POST',
			data: $f.serialize() + '&decision=' + decision,
			dataType: 'JSON',
			success: function (response) {
				Swal.fire('e-Tache', response.message, 'success').then(() => window.location.reload());
			},
			error: function (jqXHR) {
				$('#decErreur').text(justification_erreur(jqXHR)).show();
			},
			complete: function () { $f.find('[data-decision]').prop('disabled', false); }
		});
	}

</script>
