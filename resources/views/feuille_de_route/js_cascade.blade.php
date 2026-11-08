<script type="text/javascript">

	/* ============================================================
	   CASCADE Exercice > Axe > Action clé > Action mesurable
	   Inclus une seule fois par page (voir _cascade.blade.php). Chaque cascade vit dans un
	   ".cascade-groupe" : le formulaire (#tooltipmodal) et la barre de filtres sont indépendants.
	   La structure d'un exercice est chargée une seule fois (cache). Aucune dépendance à jQuery au
	   chargement du script : l'initialisation attend DOMContentLoaded (jQuery est alors disponible).
	   En cas d'échec de chargement : message dans la liste + détail dans la console (F12).
	   API : cascade_init()                  -> formulaire : exercice par défaut
	         cascade_positionner(ids[, $g])  -> préremplit la chaîne ({exercice_id, axe_id, action_cle_id, action_mesurable_id})
	   ============================================================ */
	const CASCADE_URL = "{{ route('feuille_de_route.matrice', ':id') }}";
	const CASCADE_NIVEAUX = ['exercice', 'axe', 'action_cle', 'action_mesurable'];
	const CASCADE_LIBELLES = {
		axe: ['un axe', 'Tous les axes'],
		action_cle: ['une action clé', 'Toutes les actions clés'],
		action_mesurable: ['une action mesurable', 'Toutes les actions mesurables']
	};
	const cascade_cache = {}; // exercice_id => Promise de la structure

	function cascade_sel($g, niveau) {
		return $g.find('.cascade-select[data-niveau="' + niveau + '"]');
	}

	function cascade_formulaire() {
		return $('#tooltipmodal .cascade-groupe');
	}

	function cascade_texte(texte) {
		const t = String(texte == null ? '' : texte);
		const court = t.length > 140 ? t.substring(0, 140) + '…' : t;
		return $('<div>').text(court).html();
	}

	function cascade_matrice(exerciceId) {
		if (!cascade_cache[exerciceId]) {
			cascade_cache[exerciceId] = fetch(CASCADE_URL.replace(':id', exerciceId), {
				headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
				credentials: 'same-origin'
			}).then(function (reponse) {
				if (!reponse.ok) { throw new Error('HTTP ' + reponse.status); }
				return reponse.json();
			}).catch(function (erreur) {
				delete cascade_cache[exerciceId]; // permet de réessayer
				throw erreur;
			});
		}
		return cascade_cache[exerciceId];
	}

	// Recherche (Select2) sur le dernier niveau du formulaire, si Select2 est chargé dans la page
	function cascade_sans_accents(texte) {
		return String(texte || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
	}

	function cascade_recherche($sel) {
		if (!$.fn.select2 || $sel.data('final') != 1) { return; }
		if ($sel.hasClass('select2-hidden-accessible')) { $sel.select2('destroy'); }
		$sel.select2({
			dropdownParent: $('#tooltipmodal'),
			width: '100%',
			language: { noResults: function () { return 'Aucun résultat'; } },
			matcher: function (params, data) {
				const terme = (params.term || '').trim();
				if (terme === '') { return data; }
				return cascade_sans_accents(data.text).indexOf(cascade_sans_accents(terme)) > -1 ? data : null;
			}
		});
	}

	// Pose une valeur et rafraîchit l'affichage Select2 s'il y en a un
	function cascade_valeur($sel, valeur) {
		$sel.val(valeur == null ? '' : valeur);
		if ($sel.hasClass('select2-hidden-accessible')) { $sel.trigger('change.select2'); }
	}

	function cascade_remplir($g, niveau, items) {
		const filtre = $g.data('filtre') == 1;
		const libelle = CASCADE_LIBELLES[niveau][filtre ? 1 : 0];
		const premier = items.length ? (filtre ? libelle : 'Sélectionnez ' + libelle) : 'Aucun élément à ce niveau';
		let html = '<option value="">' + premier + '</option>';
		items.forEach(function (item) { html += '<option value="' + item.id + '">' + cascade_texte(item.libelle) + '</option>'; });
		const $sel = cascade_sel($g, niveau);
		$sel.html(html).prop('disabled', !items.length);
		cascade_recherche($sel);
	}

	function cascade_erreur($g, niveau) {
		const $sel = cascade_sel($g, niveau);
		$sel.html('<option value="">Erreur de chargement (voir la console)</option>').prop('disabled', true);
		cascade_recherche($sel);
	}

	// Vide et désactive les niveaux à partir de l'index donné
	function cascade_reinitialiser($g, depuis) {
		CASCADE_NIVEAUX.slice(depuis).forEach(function (niveau) {
			const $sel = cascade_sel($g, niveau);
			$sel.html('<option value="">—</option>').prop('disabled', true);
			cascade_recherche($sel);
		});
	}

	// Remplit le niveau qui suit `niveau` selon la sélection courante
	async function cascade_suivant($g, niveau) {
		const i = CASCADE_NIVEAUX.indexOf(niveau);
		cascade_reinitialiser($g, i + 1);

		const suivant = CASCADE_NIVEAUX[i + 1];
		const valeur = cascade_sel($g, niveau).val();
		const exerciceId = cascade_sel($g, 'exercice').val();
		if (!suivant || !cascade_sel($g, suivant).length || !valeur || !exerciceId) { return; }

		let axes;
		try {
			axes = await cascade_matrice(exerciceId);
		} catch (erreur) {
			console.error('[cascade] chargement impossible', CASCADE_URL.replace(':id', exerciceId), erreur);
			cascade_erreur($g, suivant);
			return;
		}
		if (cascade_sel($g, niveau).val() !== valeur) { return; } // la sélection a changé pendant le chargement

		const trouver = function (liste, id) { return (liste || []).find(function (x) { return String(x.id) === String(id); }); };
		const axe = trouver(axes, cascade_sel($g, 'axe').val());
		const action = axe ? trouver(axe.actions_cles, cascade_sel($g, 'action_cle').val()) : null;

		const items = { exercice: axes, axe: axe ? axe.actions_cles : [], action_cle: action ? action.actions_mesurables : [] }[niveau];
		cascade_remplir($g, suivant, items || []);
	}

	// Formulaire, nouvelle saisie : exercice par défaut présélectionné, niveaux suivants chargés
	async function cascade_init() {
		const $g = cascade_formulaire();
		const $ex = cascade_sel($g, 'exercice');
		if (!$ex.length) { return; }
		cascade_valeur($ex, $ex.data('defaut'));
		await cascade_suivant($g, 'exercice');
	}

	// Modification (formulaire) ou état initial des filtres : chaîne préremplie niveau par niveau
	async function cascade_positionner(ids, $g) {
		$g = $g || cascade_formulaire();
		cascade_valeur(cascade_sel($g, 'exercice'), ids.exercice_id);
		await cascade_suivant($g, 'exercice');
		cascade_valeur(cascade_sel($g, 'axe'), ids.axe_id);
		await cascade_suivant($g, 'axe');
		cascade_valeur(cascade_sel($g, 'action_cle'), ids.action_cle_id);
		await cascade_suivant($g, 'action_cle');
		cascade_valeur(cascade_sel($g, 'action_mesurable'), ids.action_mesurable_id);
	}

	function cascade_demarrer() {
		if (!window.jQuery) { console.error('[cascade] jQuery est introuvable : la cascade est désactivée.'); return; }

		$(document).off('change.cascade').on('change.cascade', '.cascade-select', function () {
			cascade_suivant($(this).closest('.cascade-groupe'), $(this).data('niveau'));
		});

		// Filtres : état initial = valeurs de la requête
		$('.cascade-groupe[data-filtre="1"]').each(function () {
			const $g = $(this);
			cascade_positionner($g.data('valeurs') || {}, $g);
		});

		// Formulaires : recherche sur le dernier niveau
		$('.cascade-select[data-final="1"]').each(function () { cascade_recherche($(this)); });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', cascade_demarrer);
	} else {
		cascade_demarrer();
	}

</script>
