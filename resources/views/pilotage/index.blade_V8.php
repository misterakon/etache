@extends('layout.master')
@section('title', 'Pilotage et analyses')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pilotage.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

@if (! $exercice)
      <div class="cr-hero cr-hero--rose"><div><p class="cr-hero__sur">Feuille de route</p><h1 class="cr-hero__titre">Pilotage et analyses</h1><p class="cr-hero__sous">Aucun exercice n'est encore créé : créez-en un dans Paramètres pour commencer.</p></div></div>
@else
@php
  $s = $a['situation']; $p = $a['periode_chiffres'];
  $params = ['exercice_id' => $exercice->id, 'type' => $periode['type'], 'valeur' => $periode['valeur'], 'portee' => $porteeBrut];
  $f1 = fn ($v) => str_replace('.', ',', (string) $v);
  $coul = fn ($v) => $v >= 70 ? '#2E7D5B' : ($v >= 40 ? '#C77D0A' : '#A73B34');
@endphp
      <div class="cr-hero cr-hero--rose">
        <div>
          <p class="cr-hero__sur">Feuille de route</p>
          <h1 class="cr-hero__titre">Pilotage et analyses</h1>
          <p class="cr-hero__sous">{{ $a['portee'] }} · {{ $periode['libelle'] }}. Situation, évolution, axes, directions, courrier, synthèse rédigée et exports pour le suivi mensuel, trimestriel et annuel.</p>
        </div>
        <div class="cr-hero__actions">
          <a class="cr-btn cr-btn--blanc" id="pl_lien_excel" href="{{ route('pilotage.export.excel', $params + ($synthese ? ['synthese_id' => $synthese->id] : [])) }}"><i class="fa-solid fa-file-excel"></i> Excel</a>
          <a class="cr-btn cr-btn--fantome" id="pl_lien_pdf" href="{{ route('pilotage.export.pdf', $params + ($synthese ? ['synthese_id' => $synthese->id] : [])) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Note PDF</a>
          <a class="cr-btn cr-btn--fantome" href="{{ route('pilotage.historique', ['exercice_id' => $exercice->id]) }}"><i class="fa-solid fa-clock-rotate-left"></i> Historique</a>
        </div>
      </div>

      @if ($errors->any())<div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>@endif

      <form class="cr-carte pl-filtres" method="GET" action="{{ route('pilotage.index') }}" id="pl_form">
        <div><label for="pl_ex">Exercice</label><select class="form-select form-select-sm" id="pl_ex" name="exercice_id">@foreach ($exercices as $e)<option value="{{ $e->id }}" @selected($e->id === $exercice->id)>{{ $e->annee }}{{ $e->estClos() ? ' (clos)' : '' }}</option>@endforeach</select></div>
        <div><label for="pl_type">Période</label><select class="form-select form-select-sm" id="pl_type" name="type">@foreach (\App\Support\AnalysesPilotage::TYPES as $k => $lib)<option value="{{ $k }}" @selected($periode['type'] === $k)>{{ $lib }}</option>@endforeach</select></div>
        <div id="pl_valeur_bloc"><label for="pl_valeur">Numéro</label><select class="form-select form-select-sm" id="pl_valeur" name="valeur" data-valeur="{{ $periode['valeur'] }}"></select></div>
        <div><label for="pl_portee">Périmètre</label>
          <select class="form-select form-select-sm" id="pl_portee" name="portee">
            <option value="global" @selected($porteeBrut === 'global')>{{ $restreint ? 'Mes directions' : 'Toute la structure' }}</option>
            @if ($axes->isNotEmpty())<optgroup label="Par axe">@foreach ($axes as $x)<option value="axe:{{ $x->id }}" @selected($porteeBrut === 'axe:'.$x->id)>{{ $x->code }} · {{ \Illuminate\Support\Str::limit($x->libelle, 60) }}</option>@endforeach</optgroup>@endif
            @if ($directions->isNotEmpty())<optgroup label="Par direction">@foreach ($directions as $d)<option value="direction:{{ $d->id }}" @selected($porteeBrut === 'direction:'.$d->id)>{{ $d->nom }}</option>@endforeach</optgroup>@endif
          </select></div>
        <button class="cr-btn cr-btn--primaire cr-btn--petit" style="width: 150px" type="submit"><i class="fa-solid fa-chart-line"></i> Analyser</button>
      </form>

      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#2B6CB0"><span class="cr-kpi__icone"><i class="fa-solid fa-list-check"></i></span><div><span class="cr-kpi__valeur">{{ $s['total'] }}</span><span class="cr-kpi__libelle">Tâches (hors annulées)</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-circle-check"></i></span><div><span class="cr-kpi__valeur">{{ $s['terminees'] }}<small style="font-size:14px;color:#374151;"> · {{ $f1($s['taux']) }} %</small></span><span class="cr-kpi__libelle">Terminées</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-triangle-exclamation"></i></span><div><span class="cr-kpi__valeur">{{ $s['en_retard'] }}<small style="font-size:14px;color:#374151;"> · {{ $s['non_justifies'] }} non justifiés</small></span><span class="cr-kpi__libelle">En retard</span></div></div>
        <div class="cr-kpi" style="--c:#0F8B8D"><span class="cr-kpi__icone"><i class="fa-solid fa-gauge-high"></i></span><div><span class="cr-kpi__valeur">{{ $f1($s['avancement']) }} %</span><span class="cr-kpi__libelle">Avancement moyen</span></div></div>
      </div>
      <div class="cr-kpis">
        <div class="cr-kpi" style="--c:#7B6BE0"><span class="cr-kpi__icone"><i class="fa-solid fa-calendar-check"></i></span><div><span class="cr-kpi__valeur">{{ $p['echeances'] }}</span><span class="cr-kpi__libelle">Échéances sur la période</span></div></div>
        <div class="cr-kpi" style="--c:#2E7D5B"><span class="cr-kpi__icone"><i class="fa-solid fa-flag-checkered"></i></span><div><span class="cr-kpi__valeur">{{ $p['terminees'] }}<small style="font-size:14px;color:#374151;"> · {{ $p['a_temps'] }} à temps</small></span><span class="cr-kpi__libelle">Terminées sur la période</span></div></div>
        <div class="cr-kpi" style="--c:#C77D0A"><span class="cr-kpi__icone"><i class="fa-solid fa-bullseye"></i></span><div><span class="cr-kpi__valeur">{{ $p['taux_respect'] !== null ? $f1($p['taux_respect']).' %' : '—' }}</span><span class="cr-kpi__libelle">Échéances respectées</span></div></div>
        <div class="cr-kpi" style="--c:#A73B34"><span class="cr-kpi__icone"><i class="fa-solid fa-hourglass-end"></i></span><div><span class="cr-kpi__valeur">{{ $p['retard_fin_periode'] }}</span><span class="cr-kpi__libelle">En retard à la fin de la période</span></div></div>
      </div>

      {{-- Synthèse rédigée : génération dynamique (sans rechargement) --}}
      <div class="cr-carte pl-synthese" id="pl_synthese" data-ia="{{ $iaActive ? 1 : 0 }}">
        <div class="pl-synthese__tete">
          <h2 class="cr-carte__titre" style="margin:0;"><i class="fa-solid fa-feather-pointed" style="color:#C94A71;"></i> Synthèse
            <span id="pl_chip">@if ($synthese)<span class="cr-chip" style="--c: {{ $synthese->couleurSource() }};margin-left:8px;">{{ $synthese->libelleSource() }}</span>@endif</span></h2>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <form method="post" action="{{ route('pilotage.synthese') }}" id="pl_gen">@csrf
              @foreach ($params as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
              <input type="hidden" name="forcer" id="pl_forcer" value="{{ $synthese ? 1 : 0 }}">
              <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit" id="pl_btn"><i class="fa-solid fa-wand-magic-sparkles" id="pl_btn_icone"></i> <span id="pl_btn_texte">{{ $synthese ? 'Régénérer' : 'Générer la synthèse' }}</span></button>
            </form>
            <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" id="pl_btn_copier" onclick="pl_copier()" @if (! $synthese) style="display:none" @endif><i class="fa-regular fa-copy"></i> Copier</button>
            @if ($peutRediger)<button type="button" class="cr-btn cr-btn--clair cr-btn--petit" id="pl_btn_corriger" onclick="pl_editer(true)" @if (! $synthese) style="display:none" @endif><i class="fa-regular fa-pen-to-square"></i> Corriger</button>@endif
          </div>
        </div>

        <div class="pl-alerte" id="pl_alerte" @if (! session('avertissement_pilotage')) style="display:none" @endif><i class="fa-solid fa-circle-info"></i><div id="pl_alerte_texte">{{ session('avertissement_pilotage') }}</div></div>

        <div class="pl-chargement" id="pl_chargement" style="display:none" aria-live="polite">
          <div class="pl-chargement__tete"><span class="pl-spinner"></span><div><strong id="pl_charge_titre">Analyse en cours…</strong><small id="pl_charge_sous">Quelques secondes suffisent en général.</small></div></div>
          <ol class="pl-etapes" id="pl_etapes"></ol>
          <div class="pl-squelette"><i style="width:94%"></i><i style="width:88%"></i><i style="width:91%"></i><i style="width:62%"></i></div>
        </div>

        <div class="pl-synthese__texte" id="pl_texte" @if (! $synthese) style="display:none" @endif>@if ($synthese){!! \App\Support\RedacteurSynthese::versHtml($synthese->contenu) !!}@endif</div>
        @if ($peutRediger)
          <form method="post" action="{{ $synthese ? route('pilotage.synthese.editer', $synthese->id) : '#' }}" id="pl_edition" style="display:none;padding:18px 26px;">@csrf
            <textarea class="form-control" name="contenu" id="pl_contenu">{{ $synthese?->contenu }}</textarea>
            <p class="cs-aide" style="margin:8px 0 12px;">Une correction crée une nouvelle version : l'originale reste dans l'historique. Titres : « ## », puces : « - ».</p>
            <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-check"></i> Enregistrer la correction</button>
            <button class="cr-btn cr-btn--clair cr-btn--petit" type="button" onclick="pl_editer(false)">Annuler</button>
          </form>
        @endif
        <div class="pl-synthese__pied" id="pl_pied" @if (! $synthese) style="display:none" @endif>
          <span id="pl_pied_gauche">@if ($synthese)Générée le {{ $synthese->created_at->format('d/m/Y à H:i') }}@if ($synthese->auteur) par {{ $synthese->auteur->name }}@else automatiquement @endif @if ($synthese->modele) · {{ $synthese->modele }}@endif @endif</span>
          <span id="pl_pied_droite"><i class="fa-solid fa-shield-halved"></i> <span id="pl_pied_droite_texte">@if ($synthese){{ $synthese->source === 'ia' ? "Rédigée à partir des seuls totaux : aucun nom, courrier ni document n'est transmis." : 'Aucun appel externe.' }}@endif</span></span>
        </div>
        <div class="pl-invitation" id="pl_vide" @if ($synthese) style="display:none" @endif><i class="fa-regular fa-file-lines"></i><strong>Aucune synthèse pour ce périmètre et cette période</strong>
          <span>@if ($iaReglee && $iaActive){{ $quota }} synthèse(s) par IA encore possible(s) aujourd'hui. @elseif ($iaReglee)IA activée mais clé d'API absente : modèles de phrases. @else Rédaction par modèles de phrases (IA désactivée dans les Réglages). @endif</span></div>
      </div>

      {{-- Graphiques --}}
      <div class="pl-grille">
        <div class="cr-carte pl-carte pl-pleine"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-chart-line"></i> Avancement cumulé : attendu et réalisé</h2><button type="button" class="pl-img" title="Télécharger l'image" onclick="pl_image(this, 'avancement_cumule')"><i class="fa-solid fa-image"></i></button></div>
          <div class="cr-carte__corps">{!! $graphiques['courbe'] !!}<p class="pl-note" style="margin-top:10px;">Attendu : part des tâches dont l'échéance est passée. Réalisé : part des tâches terminées (date prise dans l'historique des statuts).</p></div></div>
        <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-chart-pie"></i> Tâches par statut</h2><button type="button" class="pl-img" onclick="pl_image(this, 'statuts')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['statuts'] !!}</div></div>
        <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-bars-progress"></i> Avancement par axe</h2><button type="button" class="pl-img" onclick="pl_image(this, 'avancement_par_axe')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['axes'] !!}</div></div>
        <div class="cr-carte pl-carte pl-pleine"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-building"></i> Statut des tâches par direction</h2><button type="button" class="pl-img" onclick="pl_image(this, 'statuts_par_direction')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['directions'] !!}</div></div>
        <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-triangle-exclamation"></i> Retards par direction</h2><button type="button" class="pl-img" onclick="pl_image(this, 'retards_par_direction')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['retards'] !!}</div></div>
        <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-calendar-week"></i> Échéances à venir (8 semaines)</h2><button type="button" class="pl-img" onclick="pl_image(this, 'echeances_a_venir')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['semaines'] !!}</div></div>
        <div class="cr-carte pl-carte pl-pleine"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-chart-area"></i> Évolution des retards</h2><button type="button" class="pl-img" onclick="pl_image(this, 'evolution_retards')"><i class="fa-solid fa-image"></i></button></div>
          <div class="cr-carte__corps">@if ($graphiques['evolution_retards']){!! $graphiques['evolution_retards'] !!}@else<div class="cs-vide"><i class="fa-regular fa-clock"></i><strong>L'historique se constitue jour après jour</strong>{{ $graphiques['evolution_points'] }} photo(s) enregistrée(s) pour ce périmètre : la courbe apparaît dès la deuxième.</div>@endif</div></div>
        @if (! empty($graphiques['courrier']))
          <div class="cr-carte pl-carte pl-pleine"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-envelope"></i> Courrier et diligences</h2><button type="button" class="pl-img" onclick="pl_image(this, 'courrier')"><i class="fa-solid fa-image"></i></button></div>
            <div class="cr-carte__corps">{!! $graphiques['courrier'] !!}
              <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;"><span class="cr-chip" style="--c:#2B6CB0">{{ $a['courrier']['total_recus'] }} reçus</span><span class="cr-chip" style="--c:#2E7D5B">{{ $a['courrier']['total_departs'] }} expédiés</span><span class="cr-chip" style="--c:#7B6BE0">{{ $a['courrier']['diligences_ouvertes'] }} diligences ouvertes</span><span class="cr-chip" style="--c:#A73B34">{{ $a['courrier']['diligences_en_retard'] }} en retard</span>@if ($a['courrier']['delai_moyen'] !== null)<span class="cr-chip" style="--c:#0F8B8D">délai moyen : {{ $f1($a['courrier']['delai_moyen']) }} j</span>@endif</div></div></div>
          <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-envelope-open-text"></i> Diligences par statut</h2><button type="button" class="pl-img" onclick="pl_image(this, 'diligences')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['diligences'] !!}</div></div>
          <div class="cr-carte pl-carte"><div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-user-clock"></i> Agents les plus chargés</h2><button type="button" class="pl-img" onclick="pl_image(this, 'charge_agents')"><i class="fa-solid fa-image"></i></button></div><div class="cr-carte__corps">{!! $graphiques['charge'] !!}</div></div>
        @endif
      </div>

      {{-- Tableaux --}}
      @foreach ([['Par axe', 'fa-bars-progress', $a['par_axe'], 'axe'], ['Par direction (porteuse principale)', 'fa-building', $a['par_direction'], 'direction']] as [$titreT, $iconeT, $lignesT, $typeT])
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid {{ $iconeT }}"></i> {{ $titreT }}</h2></div>
          @if (empty($lignesT))<div class="cs-vide"><i class="fa-regular fa-folder-open"></i>Aucune donnée.</div>@else
            <div class="overflow-x-auto"><table class="cr-table pl-tableau">
              <thead><tr><th>{{ $typeT === 'axe' ? 'Axe' : 'Direction' }}</th><th>Tâches</th><th>Terminées</th><th>En cours</th><th>En retard</th><th>Non justifiés</th><th>Avancement</th><th>Taux de réalisation</th></tr></thead>
              <tbody>@foreach ($lignesT as $r)
                <tr style="cursor:default;">
                  <td>@if ($typeT === 'axe' || $r['id'] > 0)<a href="{{ route('pilotage.index', array_merge($params, ['portee' => $typeT.':'.$r['id']])) }}">{{ $typeT === 'axe' ? $r['code'].' · ' : '' }}{{ $r['libelle'] }}</a>@else{{ $r['libelle'] }}@endif</td>
                  <td>{{ $r['total'] }}</td><td>{{ $r['terminees'] }}</td><td>{{ $r['en_cours'] }}</td>
                  <td>@if ($r['en_retard'])<span class="cr-chip" style="--c:#A73B34">{{ $r['en_retard'] }}</span>@else<span class="c-o-light">0</span>@endif</td>
                  <td>{{ $r['non_justifies'] ?: '0' }}</td>
                  <td><div class="rh-pourcent" style="min-width:140px;"><span class="rh-barre" style="--c: {{ $coul($r['avancement']) }}"><span style="width: {{ min(100, $r['avancement']) }}%"></span></span><strong>{{ $f1($r['avancement']) }} %</strong></div></td>
                  <td><strong>{{ $f1($r['taux']) }} %</strong></td>
                </tr>
              @endforeach</tbody>
            </table></div>
          @endif
        </div>
      @endforeach

      <div class="cr-carte">
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-triangle-exclamation"></i> Tâches en retard ({{ count($a['retards']) }}{{ count($a['retards']) >= 80 ? '+' : '' }})</h2><span class="cs-aide">Liste complète dans l'export Excel</span></div>
        @if (empty($a['retards']))<div class="cs-vide"><i class="fa-regular fa-circle-check"></i><strong>Aucune tâche en retard</strong>sur ce périmètre.</div>@else
          <div class="overflow-x-auto"><table class="cr-table pl-tableau">
            <thead><tr><th>Tâche</th><th>Direction</th><th>Échéance</th><th>Retard</th><th>Justification</th><th>Avancement</th></tr></thead>
            <tbody>@foreach (array_slice($a['retards'], 0, 15) as $r)
              <tr style="cursor:default;"><td><span class="cr-num">{{ $r['code'] }}</span> {{ \Illuminate\Support\Str::limit($r['libelle'], 80) }}</td><td>{{ $r['direction'] ?? '—' }}</td><td>{{ $r['echeance'] }}</td><td><span class="cr-chip" style="--c:#A73B34">{{ $r['jours'] }} j</span></td><td>{{ $r['justification'] ?: '—' }}</td><td>{{ $f1($r['avancement']) }} %</td></tr>
            @endforeach</tbody>
          </table></div>
        @endif
      </div>

      @if ($historique->isNotEmpty())
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clock-rotate-left"></i> Dernières synthèses</h2><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('pilotage.historique', ['exercice_id' => $exercice->id]) }}">Tout voir</a></div>
          @foreach ($historique as $h)
            <div class="cs-afaire__ligne" style="padding:10px 22px;{{ $loop->first ? 'border-top:0;' : '' }}"><div><b>{{ $h->created_at->format('d/m/Y H:i') }}</b><small>{{ $h->portee_type === 'global' ? 'Structure' : ($h->portee_type === 'axe' ? 'Axe' : 'Direction') }} · {{ \App\Support\AnalysesPilotage::TYPES[$h->periode_type] ?? $h->periode_type }}{{ $h->periode_valeur ? ' '.$h->periode_valeur : '' }}</small></div>
              <span><span class="cr-chip" style="--c: {{ $h->couleurSource() }}">{{ $h->libelleSource() }}</span> <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('pilotage.index', ['exercice_id' => $h->exercice_id, 'type' => $h->periode_type, 'valeur' => $h->periode_valeur, 'portee' => $h->portee_type === 'global' ? 'global' : $h->portee_type.':'.$h->portee_id, 'synthese_id' => $h->id]) }}">Ouvrir</a></span></div>
          @endforeach
        </div>
      @endif
@endif
    </div>
  </div>
  <script type="text/javascript">
	const MOIS = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
	function pl_valeurs() {
		const type = document.getElementById('pl_type'); const sel = document.getElementById('pl_valeur'); const bloc = document.getElementById('pl_valeur_bloc');
		if (!type || !sel) { return; }
		const courante = sel.dataset.valeur || '';
		sel.innerHTML = '';
		const liste = type.value === 'mois' ? MOIS.map(function (m, i) { return [i + 1, m.charAt(0).toUpperCase() + m.slice(1)]; }) : (type.value === 'trimestre' ? [1, 2, 3, 4].map(function (q) { return [q, (q === 1 ? '1er' : q + 'e') + ' trimestre']; }) : []);
		liste.forEach(function (x) { const o = document.createElement('option'); o.value = x[0]; o.textContent = x[1]; if (String(x[0]) === String(courante)) { o.selected = true; } sel.appendChild(o); });
		bloc.style.display = liste.length ? '' : 'none';
		if (liste.length && !sel.value) { sel.selectedIndex = 0; }
	}
	function pl_editer(oui) { const e = document.getElementById('pl_edition'); if (!e) { return; } e.style.display = oui ? '' : 'none'; document.getElementById('pl_texte').style.display = oui ? 'none' : ''; }
	function pl_copier() { const t = document.getElementById('pl_texte').innerText; if (navigator.clipboard) { navigator.clipboard.writeText(t).then(function () { Swal.fire({ title: 'Synthèse copiée', icon: 'success', timer: 1400, showConfirmButton: false }); }); } }
	function pl_image(bouton, nom) {
		const svg = bouton.closest('.pl-carte').querySelector('svg'); if (!svg) { return; }
		const c = svg.cloneNode(true); const vb = svg.viewBox.baseVal; c.setAttribute('width', vb.width); c.setAttribute('height', vb.height); c.removeAttribute('style');
		const img = new Image();
		img.onload = function () { const cv = document.createElement('canvas'); cv.width = vb.width * 2; cv.height = vb.height * 2; const g = cv.getContext('2d'); g.fillStyle = '#fff'; g.fillRect(0, 0, cv.width, cv.height); g.drawImage(img, 0, 0, cv.width, cv.height); const a = document.createElement('a'); a.download = 'pilotage_' + nom + '.png'; a.href = cv.toDataURL('image/png'); a.click(); };
		img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(new XMLSerializer().serializeToString(c));
	}
	/* ---- Génération dynamique de la synthèse : indicateur de progression, puis affichage sans recharger ---- */
	let plTimer = null;
	function pl_montrer(id, oui) { const el = document.getElementById(id); if (el) { el.style.display = oui ? '' : 'none'; } }
	function pl_etapes(ia) {
		const liste = ia ? ['Collecte des indicateurs', 'Analyse des écarts et des retards', "Rédaction de la note par l'IA", 'Mise en forme de la synthèse'] : ['Collecte des indicateurs', 'Analyse des écarts et des retards', 'Rédaction par modèles de phrases'];
		const ol = document.getElementById('pl_etapes'); ol.innerHTML = '';
		liste.forEach(function (t, i) { const li = document.createElement('li'); li.textContent = t; li.className = i === 0 ? 'pl-etape--active' : ''; ol.appendChild(li); });
		let courant = 0; clearInterval(plTimer);
		plTimer = setInterval(function () {
			const lis = ol.querySelectorAll('li'); if (courant >= lis.length - 2 && ia) { return; } /* l'étape de rédaction dure jusqu'à la réponse */
			if (courant < lis.length - 1) { lis[courant].className = 'pl-etape--faite'; courant++; lis[courant].className = 'pl-etape--active'; }
		}, ia ? 3200 : 700);
	}
	function pl_fin_etapes() { clearInterval(plTimer); document.querySelectorAll('#pl_etapes li').forEach(function (li) { li.className = 'pl-etape--faite'; }); }
	function pl_reveler() { document.querySelectorAll('#pl_texte > *').forEach(function (el, i) { el.classList.add('pl-apparait'); el.style.animationDelay = (i * 160) + 'ms'; }); }
	function pl_lien(id, synthese) { const a = document.getElementById(id); if (!a) { return; } const u = new URL(a.href, window.location.origin); u.searchParams.set('synthese_id', synthese); a.href = u.toString(); }

	function pl_generer(e) {
		e.preventDefault();
		const form = document.getElementById('pl_gen'); const bloc = document.getElementById('pl_synthese'); const ia = bloc.dataset.ia === '1';
		const btn = document.getElementById('pl_btn');
		btn.disabled = true; document.getElementById('pl_btn_icone').className = 'fa-solid fa-spinner fa-spin'; document.getElementById('pl_btn_texte').textContent = 'Génération…';
		['pl_texte', 'pl_pied', 'pl_vide', 'pl_alerte', 'pl_edition'].forEach(function (id) { pl_montrer(id, false); });
		document.getElementById('pl_charge_titre').textContent = ia ? "L'IA analyse les chiffres…" : 'Rédaction de la synthèse…';
		document.getElementById('pl_charge_sous').textContent = ia ? "Comptez entre 10 et 40 secondes. Seuls des totaux sont transmis." : 'Quelques instants.';
		pl_etapes(ia); pl_montrer('pl_chargement', true);

		const ctrl = new AbortController(); const delai = setTimeout(function () { ctrl.abort(); }, 150000);
		fetch(form.action, { method: 'POST', body: new FormData(form), signal: ctrl.signal, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
			.then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
			.then(function (x) {
				clearTimeout(delai);
				if (!x.ok || !x.j.ok) { throw new Error((x.j && x.j.message) || 'Erreur inattendue'); }
				pl_fin_etapes(); const j = x.j;
				setTimeout(function () {
					pl_montrer('pl_chargement', false);
					document.getElementById('pl_chip').innerHTML = '<span class="cr-chip" style="--c:' + j.couleur + ';margin-left:8px;">' + j.source_libelle.replace(/</g, '&lt;') + '</span>';
					const t = document.getElementById('pl_texte'); t.innerHTML = j.html; pl_montrer('pl_texte', true); pl_reveler();
					document.getElementById('pl_pied_gauche').textContent = j.pied_gauche; document.getElementById('pl_pied_droite_texte').textContent = j.pied_droite; pl_montrer('pl_pied', true);
					const al = document.getElementById('pl_alerte'); if (j.avertissement) { document.getElementById('pl_alerte_texte').textContent = j.avertissement; pl_montrer('pl_alerte', true); } else { pl_montrer('pl_alerte', false); }
					const ed = document.getElementById('pl_edition'); if (ed && j.edit_url) { ed.action = j.edit_url; document.getElementById('pl_contenu').value = j.texte; }
					pl_montrer('pl_btn_copier', true); pl_montrer('pl_btn_corriger', true);
					document.getElementById('pl_forcer').value = '1';
					pl_lien('pl_lien_pdf', j.id); pl_lien('pl_lien_excel', j.id);
					const u = new URL(window.location.href); u.searchParams.set('synthese_id', j.id); window.history.replaceState(null, '', u.toString());
					btn.disabled = false; document.getElementById('pl_btn_icone').className = 'fa-solid fa-wand-magic-sparkles'; document.getElementById('pl_btn_texte').textContent = 'Régénérer';
				}, 450);
			})
			.catch(function (err) {
				clearTimeout(delai); pl_fin_etapes(); pl_montrer('pl_chargement', false);
				document.getElementById('pl_alerte_texte').textContent = err.name === 'AbortError' ? "La génération prend trop de temps : réessayez dans un instant." : err.message;
				pl_montrer('pl_alerte', true); pl_montrer('pl_vide', document.getElementById('pl_texte').innerHTML.trim() === ''); pl_montrer('pl_texte', document.getElementById('pl_texte').innerHTML.trim() !== ''); pl_montrer('pl_pied', document.getElementById('pl_texte').innerHTML.trim() !== '');
				btn.disabled = false; document.getElementById('pl_btn_icone').className = 'fa-solid fa-wand-magic-sparkles'; document.getElementById('pl_btn_texte').textContent = document.getElementById('pl_forcer').value === '1' ? 'Régénérer' : 'Générer la synthèse';
			});
	}

	window.addEventListener('load', function () {
		document.getElementById('pl_gen').addEventListener('submit', pl_generer);
		pl_valeurs();
		['pl_ex', 'pl_type', 'pl_valeur', 'pl_portee'].forEach(function (id) { const el = document.getElementById(id); if (el) { el.addEventListener('change', function () { if (id === 'pl_type') { document.getElementById('pl_valeur').dataset.valeur = ''; pl_valeurs(); } document.getElementById('pl_form').submit(); }); } });
	});
  </script>
@endsection
