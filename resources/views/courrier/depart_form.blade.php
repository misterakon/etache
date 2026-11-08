@extends('layout.master')
@section('title', $depart ? 'Modifier '.$depart->numero : 'Nouveau courrier départ')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  @php
    $estEdition = $depart !== null;
    $val = fn (string $cle, $defaut = '') => old($cle, $depart?->{$cle} ?? ($prefill[$cle] ?? $defaut));
    $dateDepart = old('date_depart', $depart?->date_depart?->toDateString() ?? now()->toDateString());
    $destinataires = old('dest_nom') !== null
        ? collect(old('dest_nom'))->map(fn ($n, $i) => ['nom' => $n, 'precision' => (old('dest_precision')[$i] ?? '')])->values()->all()
        : ($depart ? [['nom' => $depart->destinataire, 'precision' => $depart->destinataire_precision]] : ($prefill['destinataires'] ?? [['nom' => '', 'precision' => '']]));
    $courrierId = old('courrier_id', $depart?->courrier_id ?? ($prefill['courrier_id'] ?? ''));
    $reponseId = old('reponse_id', $prefill['reponse_id'] ?? ($depart?->reponse_id ?? ''));
    $modeEnvoi = old('mode_envoi', $depart?->mode_envoi ?? '');
    $confidentiel = old('_token') !== null ? old('confidentialite') === 'confidentiel' : (($depart?->confidentialite ?? ($prefill['confidentialite'] ?? 'normal')) === 'confidentiel');
    $scanExistant = $depart?->scan();
  @endphp

  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--vert">
        <div>
          <p class="cr-hero__sur"><a href="{{ route('courrier.depart') }}" style="color:#fff;opacity:.85;text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Registre des courriers départ</a></p>
          <h1 class="cr-hero__titre">{{ $estEdition ? 'Modifier le courrier départ '.$depart->numero : 'Nouveau courrier départ' }}</h1>
          <p class="cr-hero__sous">L'objet est saisi une seule fois. Un numéro de départ est attribué à <strong>chaque destinataire</strong>, comme dans le registre papier.</p>
        </div>
      </div>

      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      <form id="form_depart" method="post" action="{{ route('courrier.depart.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($estEdition) <input type="hidden" name="id_" value="{{ $depart->id }}"> @endif
        <input type="hidden" name="reponse_id" value="{{ $reponseId }}">

        <div class="cs-form-page">
          <div>
            <div class="cr-carte"><div class="cr-carte__corps">
              <p class="cs-section">Origine</p>
              <div class="cs-champ">
                <label for="d_origine">Courrier arrivée auquel ce départ répond <span class="cs-opt">(facultatif)</span></label>
                <select class="form-select form-select-sm" id="d_origine" name="courrier_id">
                  <option value="">— Courrier envoyé de notre initiative —</option>
                  @foreach ($courriersOrigine as $c)
                    <option value="{{ $c->id }}" @selected((string) $courrierId === (string) $c->id)>{{ $c->numero }} — {{ $c->expediteur }}@if (! $c->estConfidentiel()) · {{ \Illuminate\Support\Str::limit($c->objet, 60) }}@endif</option>
                  @endforeach
                </select>
              </div>
              @if ($reponseId)
                <div class="cs-origine"><i class="fa-solid fa-file-circle-check"></i><div><strong>Réponse signée</strong><div class="cs-aide">La réponse signée est liée à ce départ et servira de scan si vous n'en déposez pas d'autre.</div></div></div>
              @endif
            </div></div>

            <div class="cr-carte"><div class="cr-carte__corps">
              <p class="cs-section">Objet et expédition</p>
              <div class="cs-champ"><label for="d_objet">Objet <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="d_objet" name="objet" type="text" maxlength="500" value="{{ $val('objet') }}" required></div>
              <div class="cr-champs-2">
                <div class="cs-champ"><label for="d_date">Date de départ <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="d_date" name="date_depart" type="date" max="{{ today()->toDateString() }}" value="{{ $dateDepart }}" required></div>
                <div class="cs-champ"><label for="d_pieces">Nombre de pièces <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="d_pieces" name="nombre_pieces" type="number" min="0" max="999" value="{{ $val('nombre_pieces', 1) }}" required></div>
              </div>
              <div class="cr-champs-2">
                <div class="cs-champ">
                  <label for="d_mode">Mode d'envoi</label>
                  <select class="form-select form-select-sm" id="d_mode" name="mode_envoi">
                    <option value="">—</option>
                    @foreach (\App\Models\Courrier::MODES_TRANSMISSION as $cle => $libelle) <option value="{{ $cle }}" @selected($modeEnvoi === $cle)>{{ $libelle }}</option> @endforeach
                  </select>
                </div>
                <div class="cs-champ" id="d_mode_precision_bloc" style="{{ $modeEnvoi === 'autre' ? '' : 'display:none;' }}"><label for="d_mode_precision">Précisez</label><input class="form-control form-control-sm" id="d_mode_precision" name="mode_envoi_precision" type="text" maxlength="150" value="{{ $val('mode_envoi_precision') }}"></div>
              </div>
              <div class="cs-champ"><label for="d_obs">Observations <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="d_obs" name="observations" rows="2" maxlength="2000">{{ $val('observations') }}</textarea></div>
              <label class="cr-interrupteur">
                <input type="checkbox" name="confidentialite" value="confidentiel" @checked($confidentiel)>
                <span class="cr-interrupteur__piste"></span>
                <span class="cr-interrupteur__texte"><strong>Courrier confidentiel</strong><small>Réservé à l'ADMIN, au DG et à la personne qui l'enregistre ; chaque consultation est tracée.</small></span>
              </label>
            </div></div>

            <div class="cr-carte"><div class="cr-carte__corps">
              <p class="cs-section">{{ $estEdition ? 'Destinataire' : 'Destinataires' }}</p>
              <div class="cs-dest-liste" id="d_destinataires">
                @foreach ($destinataires as $i => $dest)
                  <div class="cs-dest-ligne">
                    <span class="cs-dest-rang">{{ $i + 1 }}</span>
                    <input class="form-control form-control-sm" type="text" name="dest_nom[]" maxlength="255" value="{{ $dest['nom'] }}" placeholder="Nom du destinataire ou de l'organisme" required>
                    <input class="form-control form-control-sm" type="text" name="dest_precision[]" maxlength="255" value="{{ $dest['precision'] }}" placeholder="Adresse, service… (facultatif)">
                    <button type="button" class="cs-outil cs-outil--danger" title="Retirer" onclick="cs_dest_retirer(this)" @if ($estEdition) style="display:none;" @endif><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                @endforeach
              </div>
              @unless ($estEdition)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                  <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="cs_dest_ajouter()"><i class="fa-solid fa-plus"></i> Ajouter un destinataire</button>
                  <span class="cs-numero-prevu" id="d_nb_numeros"><i class="fa-solid fa-hashtag"></i> 1 numéro sera attribué</span>
                </div>
              @endunless
            </div></div>
          </div>

          <div class="cs-sticky">
            <div class="cr-carte"><div class="cr-carte__corps">
              <p class="cs-section">Scan du courrier expédié</p>
              <label class="cr-drop" id="d_drop" for="d_scan" style="min-height:150px;">
                <input type="file" id="d_scan" name="scan" accept=".pdf,.jpg,.jpeg,.png">
                <span class="cr-drop__icone"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                <span class="cr-drop__titre" id="d_drop_titre">Glissez le scan ici</span>
                <span class="cr-drop__aide" id="d_drop_aide">ou cliquez pour choisir · PDF, JPG, PNG</span>
              </label>
              @if ($scanExistant) <p class="cs-aide" style="margin-top:10px;"><i class="fa-solid fa-paperclip"></i> Un scan est déjà joint ({{ $scanExistant->nom_original }}) : en déposer un nouveau le remplace.</p> @endif
              <p class="cs-aide" style="margin-top:8px;">Un seul scan est joint à l'envoi, même avec plusieurs destinataires.</p>
            </div></div>
            <div class="cr-carte"><div class="cr-carte__corps">
              <button class="cr-btn cr-btn--primaire" style="width:100%;justify-content:center;" type="submit" id="d_btn"><i class="fa-solid fa-paper-plane"></i> {{ $estEdition ? 'Enregistrer les modifications' : 'Enregistrer le courrier départ' }}</button>
              <a class="cr-btn cr-btn--clair" style="width:100%;justify-content:center;margin-top:10px;" href="{{ $estEdition ? route('courrier.depart.show', $depart->id) : route('courrier.depart') }}">Annuler</a>
            </div></div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script type="text/javascript">
	function cs_dest_rangs() {
		const lignes = document.querySelectorAll('#d_destinataires .cs-dest-ligne');
		lignes.forEach(function (l, i) {
			l.querySelector('.cs-dest-rang').textContent = i + 1;
			const retirer = l.querySelector('.cs-outil--danger');
			if (retirer && {{ $estEdition ? 'false' : 'true' }}) { retirer.style.display = lignes.length > 1 ? '' : 'none'; }
		});
		const n = document.getElementById('d_nb_numeros');
		if (n) { n.innerHTML = '<i class="fa-solid fa-hashtag"></i> ' + lignes.length + (lignes.length > 1 ? ' numéros seront attribués (un par destinataire)' : ' numéro sera attribué'); }
	}
	function cs_dest_ajouter() {
		const liste = document.getElementById('d_destinataires');
		const l = document.createElement('div');
		l.className = 'cs-dest-ligne';
		l.innerHTML = '<span class="cs-dest-rang"></span>'
			+ '<input class="form-control form-control-sm" type="text" name="dest_nom[]" maxlength="255" placeholder="Nom du destinataire ou de l\'organisme" required>'
			+ '<input class="form-control form-control-sm" type="text" name="dest_precision[]" maxlength="255" placeholder="Adresse, service… (facultatif)">'
			+ '<button type="button" class="cs-outil cs-outil--danger" title="Retirer" onclick="cs_dest_retirer(this)"><i class="fa-regular fa-trash-can"></i></button>';
		liste.appendChild(l);
		cs_dest_rangs();
		l.querySelector('input').focus();
	}
	function cs_dest_retirer(bouton) {
		const lignes = document.querySelectorAll('#d_destinataires .cs-dest-ligne');
		if (lignes.length <= 1) { return; }
		bouton.closest('.cs-dest-ligne').remove();
		cs_dest_rangs();
	}

	window.addEventListener('load', function () {
		cs_dest_rangs();

		const mode = document.getElementById('d_mode');
		mode.addEventListener('change', function () { document.getElementById('d_mode_precision_bloc').style.display = mode.value === 'autre' ? '' : 'none'; });

		const drop = document.getElementById('d_drop');
		const champ = document.getElementById('d_scan');
		['dragenter', 'dragover'].forEach(function (e) { drop.addEventListener(e, function () { drop.classList.add('cr-drop--actif'); }); });
		['dragleave', 'drop'].forEach(function (e) { drop.addEventListener(e, function () { drop.classList.remove('cr-drop--actif'); }); });
		champ.addEventListener('change', function () {
			const f = champ.files[0];
			drop.classList.toggle('cr-drop--ok', !!f);
			document.getElementById('d_drop_titre').textContent = f ? f.name : 'Glissez le scan ici';
			document.getElementById('d_drop_aide').textContent = f ? (Math.max(1, Math.round(f.size / 1024)) + ' Ko · cliquez pour changer') : 'ou cliquez pour choisir · PDF, JPG, PNG';
		});

		document.getElementById('form_depart').addEventListener('submit', function () {
			const b = document.getElementById('d_btn'); b.disabled = true; b.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';
		});
	});
  </script>
@endsection
