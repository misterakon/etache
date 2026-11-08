<script type="text/javascript">

	/* ============================================================
	   NOUVEAUX CHAMPS DU FORMULAIRE AGENT (civilite, genre, statut matrimonial, enfants, pere, mere, telephone 2).
	   edit_personnel() et edit_info() chargent l'agent puis remplissent le formulaire : on les complete pour
	   renseigner ces champs a leur tour. A inclure APRES dgepn.js_script.
	   ============================================================ */
	(function () {
		const URL_AGENT = "{{ route('dgepn.personnel.get', ':id') }}";
		const CHAMPS = ['civilite_id', 'genre_id', 'statut_matrimonial_id', 'nombre_enfants', 'nom_pere', 'nom_mere', 'telephone_2'];

		function remplirNouveauxChamps(id) {
			fetch(URL_AGENT.replace(':id', id), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
				.then(function (r) { return r.ok ? r.json() : null; })
				.then(function (agent) {
					if (!agent) { return; }
					CHAMPS.forEach(function (nom) {
						const champ = document.querySelector('#tooltipmodal [name="' + nom + '"]');
						if (champ) { champ.value = agent[nom] === null || agent[nom] === undefined ? '' : agent[nom]; }
					});
				})
				.catch(function () { /* le formulaire reste utilisable sans ces champs prechargees */ });
		}

		['edit_personnel', 'edit_info'].forEach(function (nom) {
			const original = window[nom];
			if (typeof original !== 'function') { return; }
			window[nom] = function (id) {
				const resultat = original.apply(this, arguments);
				remplirNouveauxChamps(id);
				return resultat;
			};
		});
	})();

</script>
