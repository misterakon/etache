<script type="text/javascript">

	/* Reouverture d'un exercice clos : le formulaire est envoye au controleur (formulaire classique) */
	function reouverture_form(id) {
		const form = document.getElementById('form_reouverture');
		if (!form) { return; }
		form.reset();
		form.action = "{{ route('parametre.exercice.rouvrir', ':id') }}".replace(':id', id);
		$('#modal_reouverture').modal('show');
	}

</script>
