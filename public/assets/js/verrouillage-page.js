/* Page « Session verrouillée » : afficher / masquer le mot de passe. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var zone = document.querySelector('.vr-page .show-hide');
		var champ = document.getElementById('password');
		if (!zone || !champ) { return; }

		function basculer() {
			var visible = champ.type === 'text';
			champ.type = visible ? 'password' : 'text';
			zone.classList.toggle('visible', !visible);
			champ.focus();
		}

		zone.addEventListener('click', basculer);
		zone.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); basculer(); }
		});
	});
})();
