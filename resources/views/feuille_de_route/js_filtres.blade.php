<script type="text/javascript">

	/* Barre de filtres : le choix "porteuse / impliquée" n'a de sens que si une direction est choisie */
	$(function () {
		const $direction = $('#f_direction_id');
		const $role = $('#f_direction_role');
		if (!$direction.length || !$role.length) { return; }

		const maj = function () { $role.prop('disabled', !$direction.val()); };
		$direction.on('change', maj);
		maj();
	});

</script>
