<script type="text/javascript">

	/* Dossier de l'agent : ajout (formulaire classique) et retrait (AJAX). JS natif pour les liaisons. */
	function dos_ouvrir() {
		const f = document.getElementById('form_dossier');
		f.reset();
		document.getElementById('dos_message').textContent = '';
		const modal = document.getElementById('modal_dossier');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_dossier').modal('show');
	}

	function dos_supprimer(id, nom) {
		Swal.fire({
			title: 'DGEPN', text: 'Retirer « ' + nom + ' » du dossier ?', icon: 'warning',
			showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, retirer'
		}).then(function (r) {
			if (!r.isConfirmed) { return; }
			fetch("{{ route('dgepn.personnel.dossier.delete', ':id') }}".replace(':id', id), {
				method: 'DELETE',
				headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
				credentials: 'same-origin'
			})
				.then(function (res) { return res.json().then(function (d) { return { ok: res.ok, d: d }; }); })
				.then(function (x) { if (x.ok) { window.location.reload(); } else { Swal.fire('Impossible', x.d.message || 'Suppression impossible.', 'error'); } })
				.catch(function () { Swal.fire('Erreur', 'Impossible de contacter le serveur.', 'error'); });
		});
	}

	window.addEventListener('load', function () {
		const f = document.getElementById('form_dossier');
		if (!f) { return; }

		// Controle de la taille avant l'envoi
		const champ = document.getElementById('dos_fichier');
		champ.addEventListener('change', function () {
			const fichier = champ.files && champ.files[0];
			const max = parseInt(champ.dataset.maxKo, 10) * 1024;
			const message = document.getElementById('dos_message');
			message.textContent = '';
			if (fichier && fichier.size > max) { message.textContent = 'Le document dépasse la taille maximale autorisée.'; champ.value = ''; }
		});
		f.addEventListener('submit', function () {
			const b = document.getElementById('dos_btn');
			b.disabled = true; b.textContent = 'Envoi…';
		});

		const modal = document.getElementById('modal_dossier');
		if (modal && modal.dataset.ouvrir === '1') {
			if (modal.parentElement !== document.body) { document.body.appendChild(modal); }
			$('#modal_dossier').modal('show');
		}
	});

</script>
