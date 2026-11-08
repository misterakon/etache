@php
  // Valeurs actuelles de l'agent et échelle de traitement (indices), embarquées : aucune requête au chargement du formulaire
  $carAgent = [
      'grade_id' => $personnel->grade_id, 'classe_id' => $personnel->classe_id, 'echelon_id' => $personnel->echelon_id,
      'date_premiere_prise_service' => $personnel->date_premiere_prise_service?->format('Y-m-d'),
      'date_prise_service' => $personnel->date_prise_service?->format('Y-m-d'),
      'date_depart_retraite' => $personnel->date_depart_retraite?->format('Y-m-d'),
      'date_deces' => $personnel->date_deces?->format('Y-m-d'),
  ];
  $carEchelle = \App\Models\EchelleTraitement::all(['grade_id', 'classe_id', 'echelon_id', 'indice'])
      ->mapWithKeys(fn ($e) => [$e->grade_id.'-'.$e->classe_id.'-'.$e->echelon_id => $e->indice]);
@endphp
<script type="text/javascript">

	/* Informations administratives de l'agent : formulaire classique, valeurs prechargees. JS natif pour les liaisons. */
	const CAR_AGENT = @json($carAgent);
	const CAR_ECHELLE = @json($carEchelle);

	function car_maj_indice() {
		const f = document.getElementById('form_carriere');
		const g = f.querySelector('[name="grade_id"]').value, c = f.querySelector('[name="classe_id"]').value, e = f.querySelector('[name="echelon_id"]').value;
		const sortie = document.getElementById('car_indice');
		const aide = document.getElementById('car_aide_indice');
		if (!g && !c && !e) { sortie.textContent = '—'; aide.style.color = ''; return; }
		const indice = CAR_ECHELLE[g + '-' + c + '-' + e];
		if (indice !== undefined) {
			sortie.textContent = indice; aide.style.color = '';
			aide.textContent = "Indice d'après l'échelle de traitement.";
		} else {
			sortie.textContent = '—';
			aide.style.color = '#A73B34';
			aide.textContent = (g && c && e) ? "Cette combinaison n'existe pas dans l'échelle de traitement." : "Renseignez ensemble le grade, la classe et l'échelon.";
		}
	}

	function car_ouvrir() {
		const f = document.getElementById('form_carriere');
		Object.keys(CAR_AGENT).forEach(function (nom) {
			const champ = f.querySelector('[name="' + nom + '"]');
			if (champ) { champ.value = CAR_AGENT[nom] === null ? '' : CAR_AGENT[nom]; }
		});
		car_maj_indice();
		const modal = document.getElementById('modal_carriere');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_carriere').modal('show');
	}

	window.addEventListener('load', function () {
		const f = document.getElementById('form_carriere');
		if (!f) { return; }
		['grade_id', 'classe_id', 'echelon_id'].forEach(function (nom) { f.querySelector('[name="' + nom + '"]').addEventListener('change', car_maj_indice); });
		f.addEventListener('submit', function () {
			const b = document.getElementById('car_btn');
			b.disabled = true; b.textContent = 'Enregistrement…';
		});

		// Retour apres une erreur de validation : on rouvre la fenetre avec la saisie
		const modal = document.getElementById('modal_carriere');
		if (modal && modal.dataset.ouvrir === '1') {
			@foreach (old() as $nom => $valeur)
				@if (is_scalar($valeur) && preg_match('/^[a-z_0-9]+$/', (string) $nom) && ! in_array($nom, ['_token', '_form'], true))
			{ const c = f.querySelector('[name="{{ $nom }}"]'); if (c) { c.value = @js($valeur); } }
				@endif
			@endforeach
			car_maj_indice();
			if (modal.parentElement !== document.body) { document.body.appendChild(modal); }
			$('#modal_carriere').modal('show');
		}
	});

</script>
