<script type="text/javascript">

	/* Fonctions : formulaire classique (enregistrement) + suppression en AJAX. JS natif pour les liaisons. */
	function fn_ouvrir() {
		const modal = document.getElementById('modal_fonction');
		if (modal && modal.parentElement !== document.body) { document.body.appendChild(modal); }
		$('#modal_fonction').modal('show');
	}

	function fn_nouvelle() {
		const f = document.getElementById('form_fonction');
		f.reset();
		f.querySelector('[name="id_"]').value = '';
		f.querySelector('[name="code"]').readOnly = false;
		f.querySelector('[name="categorie"]').style.pointerEvents = '';
		f.querySelector('[name="categorie"]').style.opacity = '';
		document.getElementById('fn_titre').textContent = 'Nouvelle fonction';
		fn_ouvrir();
	}

	function fn_modifier(b) {
		const f = document.getElementById('form_fonction');
		f.querySelector('[name="id_"]').value = b.dataset.id;
		f.querySelector('[name="code"]').value = b.dataset.code;
		f.querySelector('[name="libelle"]').value = b.dataset.libelle;
		f.querySelector('[name="categorie"]').value = b.dataset.categorie;
		f.querySelector('[name="ordre"]').value = b.dataset.ordre;
		f.querySelector('[name="actif"]').value = b.dataset.actif;
		const systeme = b.dataset.systeme === '1';
		f.querySelector('[name="code"]').readOnly = systeme;          // la logique s'appuie sur les codes d'origine
		f.querySelector('[name="categorie"]').style.pointerEvents = systeme ? 'none' : '';   // non modifiable pour les fonctions d'origine
		f.querySelector('[name="categorie"]').style.opacity = systeme ? '.6' : '';
		document.getElementById('fn_titre').textContent = systeme ? 'Modifier la fonction (origine)' : 'Modifier la fonction';
		fn_ouvrir();
	}

	function fn_supprimer(id, libelle) {
		Swal.fire({
			title: 'DGEPN', text: 'Supprimer la fonction « ' + libelle + ' » ?', icon: 'warning',
			showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer'
		}).then(function (r) {
			if (!r.isConfirmed) { return; }
			fetch("{{ route('dgepn.fonction.delete', ':id') }}".replace(':id', id), {
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
		const modal = document.getElementById('modal_fonction');
		if (modal && modal.dataset.ouvrir === '1') { fn_ouvrir(); }
	});

</script>
