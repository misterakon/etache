{{-- Fenêtre « Classer » (archivage) : n° d'archives saisi à la main, durée de conservation. Réutilisée par la fiche, le départ et les archives. --}}
@php
  $ansConservation = (int) \App\Support\Reglages::get('courrier.conservation_ans');
  $rouvrirClasser = session('ouvrir_classement') && $errors->any();
@endphp
<div class="modal fade panel-modal cs-modal" id="modal_cs_classer" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirClasser ? 1 : 0 }}" data-url="{{ old('_classer_url') }}">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title" id="cs_classer_titre"><i class="fa-solid fa-box-archive"></i> Classer</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form id="form_cs_classer" method="post" action="#">
        @csrf
        <input type="hidden" name="_classer_url" id="cs_classer_url" value="{{ old('_classer_url') }}">
        <div class="modal-body dark-modal">
          @if ($rouvrirClasser)
            <div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
          @endif
          <div class="cs-champ">
            <label for="cs_c_numero">N° d'archives <span class="cs-requis">*</span></label>
            <input class="form-control form-control-sm" id="cs_c_numero" name="numero_archives" type="text" maxlength="60" value="{{ old('numero_archives') }}" placeholder="ex. Boîte 14 — Rayon C" required>
            <small class="cs-aide">Référence de rangement du document papier (boîte, classeur, rayon…), saisie à la main.</small>
          </div>
          <div class="cs-champ">
            <label for="cs_c_conserver">À conserver jusqu'au</label>
            <input class="form-control form-control-sm" id="cs_c_conserver" name="a_conserver_jusqu_au" type="date" min="{{ today()->toDateString() }}" value="{{ old('a_conserver_jusqu_au') }}">
            <small class="cs-aide">Proposé : {{ $ansConservation }} ans. Information seulement : aucun document n'est jamais supprimé automatiquement.</small>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-box-archive"></i> Classer</button></div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
	const CS_CONSERVATION_DEFAUT = @js(now()->addYears($ansConservation)->toDateString());
	function cs_classer_ouvrir(url, titre, numero, conserver) {
		const m = document.getElementById('modal_cs_classer');
		if (m.parentElement !== document.body) { document.body.appendChild(m); }
		document.getElementById('form_cs_classer').action = url;
		document.getElementById('cs_classer_url').value = url;
		document.getElementById('cs_classer_titre').innerHTML = '<i class="fa-solid fa-box-archive"></i> ' + (titre || 'Classer');
		document.getElementById('cs_c_numero').value = numero || '';
		document.getElementById('cs_c_conserver').value = conserver || CS_CONSERVATION_DEFAUT;
		$('#modal_cs_classer').modal('show');
	}
	window.addEventListener('load', function () {
		const m = document.getElementById('modal_cs_classer');
		if (m && m.dataset.ouvrir === '1' && m.dataset.url) {
			if (m.parentElement !== document.body) { document.body.appendChild(m); }
			document.getElementById('form_cs_classer').action = m.dataset.url;
			$('#modal_cs_classer').modal('show');
		}
	});
</script>
