<script type="text/javascript">

	// Affichage des messages d'erreur et succès
	@if ($errors->any())
		@foreach ($errors->all() as $error)
			Swal.fire({
				icon: 'error',
				title: 'e-Tache',
				text: '{{ $error }}',
			})
		@endforeach
	@endif

	@if(session('error'))
		Swal.fire(
			title:'e-Tache',
			html:'{{ session("error") }}',
			icon:'error'
		)
    @endif

	@if(session('info'))
		Swal.fire(
			'e-Tache',
			'{{ session("info") }}',
			'info'
		)
    @endif

	@if(session('success'))
		Swal.fire(
			'e-Tache',
			'{{ session("success") }}',
			'success'
		)
    @endif
			
</script>



