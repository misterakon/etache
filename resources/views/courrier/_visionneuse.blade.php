{{-- Visionneuse du scan dans l'application. Variables : $courrier, $scan (PieceJointe ou null), $peutModifier --}}
<div class="cr-visionneuse" id="cr_visionneuse">
  <div class="cr-visionneuse__barre">
    <div class="cr-visionneuse__nom">
      <i class="fa-regular {{ $scan && $scan->extension === 'pdf' ? 'fa-file-pdf' : 'fa-file-image' }}"></i>
      <span>{{ $scan ? $scan->nom_original : 'Scan du courrier' }}@if ($scan && $scan->version > 1) · v{{ $scan->version }}@endif</span>
    </div>
    @if ($scan)
      <div class="cr-visionneuse__outils">
        <button type="button" class="cr-btn cr-btn--fantome" onclick="cr_plein_ecran()" id="cr_btn_plein"><i class="fa-solid fa-expand"></i> Plein écran</button>
        <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.scan.apercu', $courrier->id) }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Nouvel onglet</a>
        <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.scan.telecharger', $courrier->id) }}"><i class="fa-solid fa-download"></i> Télécharger</a>
      </div>
    @endif
  </div>

  <div class="cr-visionneuse__cadre">
    @if ($scan && $scan->extension === 'pdf')
      <iframe src="{{ route('courrier.scan.apercu', $courrier->id) }}#toolbar=1&navpanes=0&view=Fit" title="Scan du courrier {{ $courrier->numero }}"></iframe>
    @elseif ($scan)
      <div class="cr-visionneuse__image"><img src="{{ route('courrier.scan.apercu', $courrier->id) }}" alt="Scan du courrier {{ $courrier->numero }}"></div>
    @else
      <div class="cr-visionneuse__vide">
        <i class="fa-regular fa-file-lines"></i>
        <strong>Aucun scan joint à ce courrier</strong>
        @if ($peutModifier)
          <button type="button" class="cr-btn cr-btn--primaire" onclick="cr_modifier(document.getElementById('cr_btn_modifier'))"><i class="fa-solid fa-cloud-arrow-up"></i> Ajouter le scan</button>
        @endif
      </div>
    @endif
  </div>
</div>

<script type="text/javascript">
	/* Plein ecran de la visionneuse (touche Echap pour revenir) */
	function cr_plein_ecran() {
		const v = document.getElementById('cr_visionneuse');
		const actif = v.classList.toggle('cr-visionneuse--plein');
		document.getElementById('cr_btn_plein').innerHTML = actif ? '<i class="fa-solid fa-compress"></i> Quitter le plein écran' : '<i class="fa-solid fa-expand"></i> Plein écran';
	}
	document.addEventListener('keydown', function (e) {
		const v = document.getElementById('cr_visionneuse');
		if (e.key === 'Escape' && v && v.classList.contains('cr-visionneuse--plein')) { cr_plein_ecran(); }
	});
</script>
