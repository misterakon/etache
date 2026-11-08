<script type="text/javascript">

	/* Page Responsables : consultation de l'historique d'un poste (aucune action : tout se fait dans la page Personnel) */
	const RS_URL_HISTORIQUE = "{{ route('dgepn.responsable.historique') }}";

	function rs_echapper(texte) {
		return String(texte == null ? '' : texte).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function voir_historique(niveau, uniteId, nomUnite) {
		$('#hist_unite').text(nomUnite);
		$('#hist_corps').html('<tr><td colspan="7" class="text-center text-muted">Chargement…</td></tr>');
		$('#modal_historique').modal('show');

		$.ajax({
			url: RS_URL_HISTORIQUE,
			type: 'GET',
			dataType: 'JSON',
			data: { niveau: niveau, unite_id: uniteId },
			success: function (donnees) { rs_afficher_historique(donnees.mandats); },
			error: function () {
				$('#hist_corps').html('<tr><td colspan="7" class="text-center text-muted">Erreur lors du chargement de l\'historique.</td></tr>');
			}
		});
	}

	function rs_afficher_historique(mandats) {
		if (!mandats.length) {
			$('#hist_corps').html('<tr><td colspan="7" class="text-center text-muted">Aucune nomination enregistrée pour cette unité.</td></tr>');
			return;
		}

		const lignes = mandats.map(function (m) {
			const avatar = m.photo
				? '<img class="common-circle" src="' + rs_echapper(m.photo) + '" alt="">'
				: '<div class="common-circle bg-lighter-primary">' + rs_echapper(m.initiales) + '</div>';
			const nom = m.fiche
				? '<a class="font-medium" href="' + rs_echapper(m.fiche) + '">' + rs_echapper(m.personne) + '</a>'
				: rs_echapper(m.personne);
			const type = m.type === 'interim'
				? '<span class="tl-badge tl-avenir">Intérim</span>'
				: '<span class="tl-badge tl-encours">Titulaire</span>';
			const etats = {
				en_poste: '<span class="tl-badge tl-terminee">En poste</span>',
				supplee:  '<span class="tl-badge tl-avenir" title="Mandat en cours, couvert par un intérim">Suppléé</span>',
				a_venir:  '<span class="tl-badge tl-attente">À venir</span>',
				remplace: '<span class="tl-badge tl-retard">Remplacé</span>',
				termine:  '<span class="tl-badge tl-neutre">Terminé</span>'
			};
			const etat = etats[m.etat] || '';
			const periode = 'Du ' + rs_echapper(m.debut) + (m.fin ? ' au ' + rs_echapper(m.fin) : ' <small class="text-muted">(sans date de fin)</small>');
			const details = [m.acte ? 'Acte : ' + rs_echapper(m.acte) : '', m.motif ? 'Fin : ' + rs_echapper(m.motif) : '']
				.filter(Boolean).join('<br>') || '—';

			return '<tr>'
				+ '<td><div class="flex items-center gap-2">' + avatar + '<div>' + nom + '</div></div></td>'
				+ '<td>' + type + '</td>'
				+ '<td>' + periode + '</td>'
				+ '<td>' + etat + '</td>'
				+ '<td>' + details + '</td>'
				+ '<td>' + rs_echapper(m.par || '—') + '</td>'
				+ '</tr>';
		});

		$('#hist_corps').html(lignes.join(''));
	}

</script>
