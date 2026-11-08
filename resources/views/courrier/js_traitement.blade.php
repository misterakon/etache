{{-- Scripts du traitement d'une diligence (plan, délais, réponses). JS natif pour les liaisons : jQuery n'est chargé qu'après. --}}
{{-- Fenêtre générique « motif » (refus d'une action, d'un délai, d'une réponse) --}}
<div class="modal fade panel-modal cs-modal" id="modal_cs_motif" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title" id="cs_motif_titre">Motif</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form id="form_cs_motif" method="post" action="#">
        @csrf
        <input type="hidden" id="cs_motif_cache" name="" value="">
        <div class="modal-body dark-modal">
          <div class="cs-champ"><label for="cs_motif_texte" id="cs_motif_etiquette">Motif</label><textarea class="form-control form-control-sm" id="cs_motif_texte" name="motif" rows="4" maxlength="2000" required></textarea></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-danger text-white" type="submit">Confirmer</button></div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
	function cs_modal(id) {
		const m = document.getElementById(id);
		if (!m) { return false; }
		if (m.parentElement !== document.body) { document.body.appendChild(m); }
		$('#' + id).modal('show');
		return true;
	}

	function cs_confirmer(form, texte) {
		Swal.fire({ title: 'e-Tâche', text: texte, icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui', cancelButtonText: 'Annuler' })
			.then(function (r) { if (r.isConfirmed) { form.submit(); } });
		return false;
	}

	/* Fenêtre « motif » : nom du champ de texte, champ caché facultatif (ex. decision=refuser) */
	function cs_motif_ouvrir(url, titre, etiquette, champ, cacheNom, cacheValeur) {
		const form = document.getElementById('form_cs_motif');
		form.action = url;
		document.getElementById('cs_motif_titre').textContent = titre;
		document.getElementById('cs_motif_etiquette').textContent = etiquette;
		const texte = document.getElementById('cs_motif_texte');
		texte.name = champ; texte.value = '';
		const cache = document.getElementById('cs_motif_cache');
		cache.name = cacheNom || ''; cache.value = cacheValeur || '';
		cs_modal('modal_cs_motif');
	}

	/* ---- Plan de traitement ---- */
	function cs_action_nouvelle() {
		const f = document.getElementById('form_cs_action');
		if (!f) { return; }
		f.reset();
		f.querySelector('[name="id_"]').value = '';
		document.getElementById('cs_action_btn').textContent = 'Enregistrer';
		cs_modal('modal_cs_action');
	}
	function cs_action_modifier(bouton) {
		const d = JSON.parse(bouton.dataset.action);
		const f = document.getElementById('form_cs_action');
		f.reset();
		f.querySelector('[name="id_"]').value = d.id;
		f.querySelector('[name="libelle"]').value = d.libelle || '';
		f.querySelector('[name="description"]').value = d.description || '';
		f.querySelector('[name="personnel_id"]').value = d.personnel_id || '';
		f.querySelector('[name="echeance"]').value = d.echeance || '';
		f.querySelector('[name="modalites"]').value = d.modalites || '';
		f.querySelector('[name="conditions"]').value = d.conditions || '';
		document.getElementById('cs_action_btn').textContent = 'Modifier';
		cs_modal('modal_cs_action');
	}

	/* ---- Réponses ---- */
	function cs_reponse_nouvelle(objet) {
		const f = document.getElementById('form_cs_reponse');
		if (!f) { return; }
		f.reset();
		f.querySelector('[name="id_"]').value = '';
		f.querySelector('[name="objet"]').value = objet || '';
		cs_modal('modal_cs_reponse');
	}
	function cs_reponse_modifier(bouton) {
		const d = JSON.parse(bouton.dataset.reponse);
		const f = document.getElementById('form_cs_reponse');
		f.reset();
		f.querySelector('[name="id_"]').value = d.id;
		f.querySelector('[name="objet"]').value = d.objet || '';
		f.querySelector('[name="commentaire"]').value = d.commentaire || '';
		cs_modal('modal_cs_reponse');
	}
	function cs_viser(visaId) {
		document.getElementById('form_cs_viser').action = "{{ route('courrier.reponse.viser', ':id') }}".replace(':id', visaId);
		cs_modal('modal_cs_viser');
	}
	function cs_signer(visaId) {
		document.getElementById('form_cs_signer').action = "{{ route('courrier.reponse.signer', ':id') }}".replace(':id', visaId);
		cs_modal('modal_cs_signer');
	}

	/* Réouverture d'une fenêtre après une erreur de validation (aux données d'origine) ; ancre de section */
	window.addEventListener('load', function () {
		['modal_cs_action', 'modal_cs_delai', 'modal_cs_reponse'].forEach(function (id) {
			const m = document.getElementById(id);
			if (m && m.dataset.ouvrir === '1') { cs_modal(id); }
		});
		if (window.location.hash && document.querySelector(window.location.hash)) {
			setTimeout(function () { document.querySelector(window.location.hash).scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 200);
		}
	});
</script>
