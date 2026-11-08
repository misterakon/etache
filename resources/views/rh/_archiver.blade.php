{{-- Fenêtre d'archivage d'un agent (jamais de suppression). Fonction JS : rh_archiver(id, nom) --}}
<div class="modal fade panel-modal cs-modal" id="modal_rh_archiver" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-box-archive"></i> Archiver un agent</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form id="form_rh_archiver" method="post" action="#">
        @csrf
        <div class="modal-body dark-modal">
          <p id="rh_archiver_nom" style="font-weight:700;margin:0 0 12px;"></p>
          <div class="cs-champ"><label for="rh_motif">Motif <span class="cs-requis">*</span></label>
            <select class="form-select form-select-sm" id="rh_motif" name="motif" required>@foreach (\App\Support\ArchivageAgent::MOTIFS as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach</select>
          </div>
          <div class="cs-info"><i class="fa-solid fa-circle-info"></i><div>Aucune fiche n'est supprimée. Son poste prend fin, son compte est désactivé et ses sessions fermées ; tout l'historique reste consultable dans « Archives ». Les tâches ouvertes de l'agent devront être réaffectées.</div></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-danger text-white" type="submit">Archiver</button></div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
	function rh_archiver(id, nom) {
		const m = document.getElementById('modal_rh_archiver');
		if (m.parentElement !== document.body) { document.body.appendChild(m); }
		document.getElementById('form_rh_archiver').action = "{{ route('rh.archiver', ':id') }}".replace(':id', id);
		document.getElementById('rh_archiver_nom').textContent = nom;
		$('#modal_rh_archiver').modal('show');
	}
</script>
