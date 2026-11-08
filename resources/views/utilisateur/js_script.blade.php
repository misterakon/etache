<script type="text/javascript">

	/* ============================================================
	   RECHERCHE D'AGENT (Select2) pour le champ « Agent lié »
	   ============================================================ */
	function cpt_sans_accents(texte) {
		return String(texte || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
	}

	// Reconstruit la liste des agents : ceux qui ont deja un compte en sont retires,
	// sauf l'agent du compte en cours de modification (agentCourantId).
	function cpt_peupler_agents(agentCourantId) {
		const $s = $('.cpt-select-agent');
		if ($s.hasClass('select2-hidden-accessible')) { $s.select2('destroy'); }
		$s.empty().append(new Option('Aucun', '', true, false));
		CPT_AGENTS.forEach(function (a) {
			if (a.compte && String(a.id) !== String(agentCourantId || '')) { return; }
			$s.append(new Option(a.texte, a.id, false, false));
		});
	}

	// A la selection d'un agent (creation d'un compte) : nom et email se remplissent, ils restent modifiables.
	function cpt_remplir_depuis_agent() {
		const id = $('[name="personnel_id"]').val();
		const a = CPT_AGENTS.find(function (x) { return String(x.id) === String(id); });
		$('#cpt_info_agent').text(a ? a.info : '');
		if (!a || $('[name="id_"]').val()) { return; } // modification d'un compte existant : rien n'est ecrase
		$('[name="name"]').val(a.nom_complet);
		if (a.email) { $('[name="email"]').val(a.email); }
	}

	// (Ré)initialise la recherche : détruit un éventuel Select2 déjà posé, puis le recrée dans le modal.
	// À appeler une fois les valeurs du formulaire posées, juste avant l'ouverture du modal.
	function cpt_init_select_agent() {
		$('.cpt-select-agent').off('change.cptagent').on('change.cptagent', cpt_remplir_depuis_agent);
		if (!window.jQuery || !$.fn.select2) { // Select2 non chargé : le select classique reste utilisable
			console.warn("[comptes] Select2 non chargé : recherche d'agent indisponible");
			return;
		}

		const $s = $('.cpt-select-agent');
		if (!$s.length) { return; }
		if ($s.hasClass('select2-hidden-accessible')) { $s.select2('destroy'); }

		$s.select2({
			dropdownParent: $('#tooltipmodal'),   // indispensable dans un modal, sinon la recherche ne prend pas le focus
			width: '100%',
			language: {
				noResults: function () { return 'Aucun agent trouvé'; },
				searching: function () { return 'Recherche…'; }
			},
			matcher: function (params, data) {
				const terme = (params.term || '').trim();
				if (terme === '') { return data; }
				return cpt_sans_accents(data.text).indexOf(cpt_sans_accents(terme)) > -1 ? data : null;
			}
		});
	}

	function add_form() {
		$('.modal_form')[0].reset();
		$('[name="id_"]').val('');
		cpt_peupler_agents(null);
		$('#cpt_info_agent').text('');
		mdp_reinitialiser();
		$('#password').prop('required', true);
		mdp_definir_aide('(min. 8 caractères)');
		$('#BtnEnregistrer').html('ENREGISTRER').val('Enregistrer');
		$('.submit').attr('disabled', false);

		cpt_init_select_agent();
		$('#tooltipmodal').modal('show');
	}


	/* ============================================================
	   MOT DE PASSE : afficher / masquer, saisir ou generer automatiquement
	   ============================================================ */
	var mdp_aide_base = '';   // texte d'aide du mode "saisir" (differe entre creation et modification)

	function mdp_definir_aide(texte) {
		mdp_aide_base = texte;
		$('#password_hint').text(texte);
	}

	// Remet le champ dans son etat initial : mode "saisir", mot de passe masque, boutons caches
	function mdp_reinitialiser() {
		$('#mdp_saisir').prop('checked', true);
		$('#password').attr('type', 'password');
		$('#btn_voir_mdp i').attr('class', 'fa-regular fa-eye');
		$('#mdp_actions').addClass('hidden');
	}

	function mdp_basculer() {
		const visible = $('#password').attr('type') === 'text';
		$('#password').attr('type', visible ? 'password' : 'text');
		$('#btn_voir_mdp i').attr('class', visible ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash');
	}

	// Entier aleatoire 0..max-1, sans biais (generateur cryptographique du navigateur)
	function mdp_entier_aleatoire(max) {
		const tirage = new Uint32Array(1);
		const limite = Math.floor(0x100000000 / max) * max;
		do { window.crypto.getRandomValues(tirage); } while (tirage[0] >= limite);
		return tirage[0] % max;
	}

	// 12 caracteres : au moins une minuscule, une majuscule, un chiffre et un symbole ;
	// sans caracteres ambigus (0 O 1 l I) pour pouvoir le dicter ou le recopier sans erreur.
	function mdp_generer(longueur) {
		longueur = longueur || 12;
		const minuscules = 'abcdefghijkmnpqrstuvwxyz';
		const majuscules = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		const chiffres = '23456789';
		const symboles = '!@#$%*?-_+=';
		const tous = minuscules + majuscules + chiffres + symboles;
		const un = (jeu) => jeu.charAt(mdp_entier_aleatoire(jeu.length));

		const caracteres = [un(minuscules), un(majuscules), un(chiffres), un(symboles)];
		while (caracteres.length < longueur) { caracteres.push(un(tous)); }

		for (let i = caracteres.length - 1; i > 0; i--) {   // melange (Fisher-Yates)
			const j = mdp_entier_aleatoire(i + 1);
			[caracteres[i], caracteres[j]] = [caracteres[j], caracteres[i]];
		}
		return caracteres.join('');
	}

	function mdp_changer_mode(mode) {
		if (mode === 'generer') {
			mdp_regenerer();
			$('#mdp_actions').removeClass('hidden');
			$('#password_hint').text('(généré automatiquement : notez-le ou copiez-le avant d\'enregistrer)');
		} else {
			$('#password').val('').attr('type', 'password');
			$('#btn_voir_mdp i').attr('class', 'fa-regular fa-eye');
			$('#mdp_actions').addClass('hidden');
			$('#password_hint').text(mdp_aide_base);
		}
	}

	function mdp_regenerer() {
		// Le mot de passe genere est affiche en clair, pour pouvoir le communiquer a l'utilisateur
		$('#password').val(mdp_generer(12)).attr('type', 'text');
		$('#btn_voir_mdp i').attr('class', 'fa-regular fa-eye-slash');
	}

	function mdp_copier() {
		const champ = document.getElementById('password');
		if (!champ.value) { return; }

		const confirme = function () {
			$('#btn_copier_mdp i').attr('class', 'fa-solid fa-check');
			setTimeout(function () { $('#btn_copier_mdp i').attr('class', 'fa-regular fa-copy'); }, 1500);
		};

		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(champ.value).then(confirme);
		} else {
			// Repli (page en http sur un reseau interne) : copie via la selection du champ
			const typeInitial = champ.type;
			champ.type = 'text';
			champ.select();
			try { if (document.execCommand('copy')) { confirme(); } } catch (e) { /* copie impossible */ }
			champ.type = typeInitial;
			champ.blur();
		}
	}


	/* ============================================================
	   COMPTE UTILISATEUR
	   ============================================================ */
	function edit_compte(id)
	{
		$('.modal_form')[0].reset();
		$('#BtnEnregistrer').html('MODIFIER').val('Modifier');

		// En edition, le mot de passe n'est jamais precharge (il n'est meme
		// pas renvoye par le serveur) : laisser vide = ne pas le changer.
		mdp_reinitialiser();
		$('#password').prop('required', false);
		mdp_definir_aide('(laisser vide pour ne pas modifier le mot de passe actuel)');

		$.ajax({
			url: "{{ route('utilisateur.compte.get', ':id') }}".replace(':id', id),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('[name="id_"]').val(data.id);
				$('[name="name"]').val(data.name);
				$('[name="email"]').val(data.email);
				cpt_peupler_agents(data.personnel_id);
				$('[name="personnel_id"]').val(data.personnel_id || ''); // « Aucun » si le compte n'est lié à aucun agent
				$('[name="profil_acces_id"]').val(data.profil_acces_id);
				$('[name="actif"]').val(data.actif ? "1" : "0");

				$('.submit').removeClass('disabled').attr('disabled', false);
				cpt_init_select_agent(); // une fois l'agent sélectionné, pour que la recherche affiche la bonne valeur
				$('#tooltipmodal').modal('show');
			},
			error: function () {
				alert('Erreur lors du chargement des données.');
			}
		});
	}

	function deletes_compte(id) {
		Swal.fire({
			title: 'e-Tache',
			text: "Êtes-vous sûr de vouloir supprimer ce compte utilisateur ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Oui, supprimer'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('utilisateur.compte.delete', ':id') }}".replace(':id', id),
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

</script>
