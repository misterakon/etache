{{-- Formulaire de tâche : en mode "par sous-tâches", le champ Avancement est en lecture seule (valeur calculée). --}}
@php
  $modesExercice = \App\Models\Exercice::pluck('mode_avancement', 'id');
  $modeParAm = \App\Models\ActionMesurable::with('actionCle.axe')->get()
      ->mapWithKeys(fn ($am) => [$am->id => $modesExercice[$am->actionCle?->axe?->exercice_id] ?? 'manuel']);
@endphp
<script type="text/javascript">

	/* JS natif : ce script est inclus avant le chargement de jQuery, on ne lie donc aucun evenement avec $() ici. */
	const modeAvancementParAm = @json($modeParAm);

	function maj_avancement_mode() {
		const select = document.querySelector('#tooltipmodal [name="action_mesurable_id"]');
		const champ = document.getElementById('avancement_pourcentage');
		const aide = document.getElementById('avancement_aide');
		const id = document.querySelector('#tooltipmodal [name="id_"]');
		if (!select || !champ) { return; }

		const calcule = modeAvancementParAm[select.value] === 'sous_taches';

		champ.readOnly = calcule;
		champ.required = !calcule;
		champ.style.backgroundColor = calcule ? '#eef1f5' : '';
		champ.style.cursor = calcule ? 'not-allowed' : '';
		if (aide) { aide.classList.toggle('hidden', !calcule); aide.style.display = calcule ? '' : 'none'; }
		if (calcule && id && !id.value) { champ.value = 0; }   // nouvelle tâche : 0 %
	}

	window.addEventListener('load', function () {
		const modal = document.getElementById('tooltipmodal');
		if (!modal) { return; }

		// choix (ou changement) de l'action mesurable
		modal.addEventListener('change', function (e) {
			if (e.target.matches('[name="action_mesurable_id"]')) { maj_avancement_mode(); }
		});
		// ouverture du formulaire (creation ou modification) : on attend la fin du remplissage de la cascade
		modal.addEventListener('shown.bs.modal', function () {
			maj_avancement_mode();
			setTimeout(maj_avancement_mode, 300);
		});
	});

</script>
