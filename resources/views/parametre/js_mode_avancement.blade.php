<script type="text/javascript">

	/* Exercice : avant de changer le mode d'avancement, apercu d'impact + confirmation.
	   JS natif pour la liaison (jQuery n'est pas encore charge quand ce script s'execute). */
	window.addEventListener('load', function () {
		const form = document.querySelector('.modal_form');
		if (!form) { return; }
		let confirme = false;

		form.addEventListener('submit', function (e) {
			const champId = form.querySelector('[name="id_"]');
			const mode = document.getElementById('mode_avancement');
			if (confirme || !champId || !champId.value || !mode) { return; }   // creation : rien a recalculer
			e.preventDefault();

			const url = "{{ route('parametre.exercice.apercu_mode', ':id') }}".replace(':id', champId.value) + '?mode=' + encodeURIComponent(mode.value);
			fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
				.then(function (res) { if (!res.ok) { throw new Error(res.status); } return res.json(); })
				.then(function (r) {
					if (!r.change) { confirme = true; form.submit(); return; }
					Swal.fire({
						title: 'e-Tache',
						html: r.message,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						cancelButtonText: 'Annuler',
						confirmButtonText: 'Oui, changer le mode'
					}).then(function (res) {
						if (res.isConfirmed) { confirme = true; form.submit(); }
					});
				})
				.catch(function () { Swal.fire('Erreur', "Impossible de calculer l'aperçu d'impact.", 'error'); });
		});
	});

</script>
