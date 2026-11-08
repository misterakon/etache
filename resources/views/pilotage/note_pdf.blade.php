{{-- Note de synthèse du pilotage (dompdf). Couleurs : config/export_theme.php --}}
@php
  $c = fn ($k) => \App\Support\ExportTheme::hex($k);
  $s = $a['situation']; $p = $a['periode_chiffres'];
  $f1 = fn ($v) => str_replace('.', ',', (string) $v);
  $cartes1 = [
    [$s['total'], 'Tâches'],
    [$s['terminees'].' · '.$f1($s['taux']).' %', 'Terminées'],
    [$s['en_retard'], 'En retard ('.$s['non_justifies'].' non justifiés)'],
    [$f1($s['avancement']).' %', 'Avancement moyen'],
  ];
  $cartes2 = [
    [$p['echeances'], 'Échéances de la période'],
    [$p['terminees'].' ('.$p['a_temps'].' à temps)', 'Terminées sur la période'],
    [$p['taux_respect'] !== null ? $f1($p['taux_respect']).' %' : '—', 'Échéances respectées'],
    [$p['retard_fin_periode'], 'En retard fin de période'],
  ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
@include('exports._style')
<style>
  .entete { width: 100%; margin-bottom: 2px; }
  .entete td { text-align: center; font-size: 8.5px; font-weight: bold; line-height: 1.5; color: {{ $c('texte_doux') }}; }
  h1.titre { font-size: 19px; }
  table.cartes { margin: 10px 0 0; }
  table.cartes td { padding-bottom: 8px; }
  .carte .lib { white-space: nowrap; }
  .bloc { page-break-inside: avoid; }
  .synthese { padding: 10px 14px; background: {{ $c('fond_carte') }}; border-left: 4px solid {{ $c('accent') }}; font-size: 10px; line-height: 1.55; }
  .synthese h4 { margin: 10px 0 4px; font-size: 9.5px; letter-spacing: .06em; text-transform: uppercase; color: {{ $c('primaire') }}; }
  .synthese h4:first-child { margin-top: 0; }
  .synthese p { margin: 0 0 5px; } .synthese ul { margin: 0 0 4px; padding-left: 16px; } .synthese li { margin-bottom: 3px; }
  img.g { width: 100%; }
  .centre { text-align: center; }
</style>
</head>
<body>
@include('exports._cadre', ['pied' => ($mention ?: 'e-Tâche').' · Note de synthèse du pilotage · '.now()->format('d/m/Y')])

<table class="entete"><tr><td>{{ $ministere }}<br>{{ $direction }}</td></tr></table>
<h1 class="titre">Note de synthèse : pilotage de la feuille de route</h1>
<p class="meta">Édité le {{ now()->format('d/m/Y à H:i') }} par {{ $editeur }}</p>
<p class="filtre">{{ $a['portee'] }} · {{ $a['periode']['libelle'] }}@if ($a['periode']['type'] !== 'annee') · exercice {{ $a['exercice']->annee }}@endif</p>

@foreach ([$cartes1, $cartes2] as $rang)
  <table class="cartes"><tr>
    @foreach ($rang as $i => $carte)
      <td class="{{ $i === 0 ? 'premiere' : '' }} {{ $i === 3 ? 'derniere' : '' }}">
        <div class="carte"><div class="barre" style="background: {{ \App\Support\ExportTheme::hex(\App\Support\ExportTheme::carte($i)) }};"></div>
          <div class="lib">{{ $carte[1] }}</div><div class="val">{{ $carte[0] }}</div></div>
      </td>
    @endforeach
  </tr></table>
@endforeach

<div class="bloc"><h2 class="section">Synthèse</h2><div class="trait"></div>
<div class="synthese">@if ($texte){!! $texte !!}@else <p>Aucune synthèse n'a été générée pour ce périmètre et cette période.</p>@endif</div></div>

<div class="bloc"><h2 class="section">Avancement cumulé : attendu et réalisé</h2><div class="trait"></div>
@if ($images['courbe'])<img class="g" src="{{ $images['courbe'] }}" alt="">@endif</div>

<div class="bloc"><h2 class="section">Répartition par statut</h2><div class="trait"></div>
@if ($images['statuts'])<div class="centre"><img style="width:70%;" src="{{ $images['statuts'] }}" alt=""></div>@endif</div>

<div class="bloc"><h2 class="section">Avancement moyen par axe</h2><div class="trait"></div>{!! $barresAxes !!}</div>

<div class="bloc"><h2 class="section">Par axe</h2><div class="trait"></div>
<table class="t"><thead><tr><th>Axe</th><th class="num">Tâches</th><th class="num">Term.</th><th class="num">En cours</th><th class="num">En retard</th><th class="num">Avancement</th><th class="num">Réalisation</th></tr></thead>
<tbody>@foreach ($a['par_axe'] as $n => $r)<tr class="{{ $n % 2 ? 'alt' : '' }}"><td>{{ $r['code'] }} · {{ $r['libelle'] }}</td><td class="num">{{ $r['total'] }}</td><td class="num">{{ $r['terminees'] }}</td><td class="num">{{ $r['en_cours'] }}</td><td class="num">{{ $r['en_retard'] }}</td><td class="num">{{ $f1($r['avancement']) }} %</td><td class="num">{{ $f1($r['taux']) }} %</td></tr>@endforeach</tbody></table></div>

<div class="bloc"><h2 class="section">Par direction</h2><div class="trait"></div>
<table class="t"><thead><tr><th>Direction</th><th class="num">Tâches</th><th class="num">Term.</th><th class="num">En cours</th><th class="num">En retard</th><th class="num">Non justifiés</th><th class="num">Avancement</th></tr></thead>
<tbody>@foreach ($a['par_direction'] as $n => $r)<tr class="{{ $n % 2 ? 'alt' : '' }}"><td>{{ $r['libelle'] }}</td><td class="num">{{ $r['total'] }}</td><td class="num">{{ $r['terminees'] }}</td><td class="num">{{ $r['en_cours'] }}</td><td class="num">{{ $r['en_retard'] }}</td><td class="num">{{ $r['non_justifies'] }}</td><td class="num">{{ $f1($r['avancement']) }} %</td></tr>@endforeach</tbody></table></div>

@if ($barresRetards)<div class="bloc"><h2 class="section">Retards par direction</h2><div class="trait"></div>{!! $barresRetards !!}</div>@endif

@if (! empty($a['retards']))
<div class="bloc"><h2 class="section">Tâches en retard les plus anciennes</h2><div class="trait"></div>
<table class="t"><thead><tr><th>Tâche</th><th>Direction</th><th>Échéance</th><th class="num">Retard</th><th>Justification</th></tr></thead>
<tbody>@foreach (array_slice($a['retards'], 0, 12) as $n => $r)<tr class="{{ $n % 2 ? 'alt' : '' }}"><td>{{ $r['code'] }} {{ \Illuminate\Support\Str::limit($r['libelle'], 60) }}</td><td>{{ $r['direction'] ?? '—' }}</td><td>{{ $r['echeance'] }}</td><td class="num">{{ $r['jours'] }} j</td><td>{{ $r['justification'] ?: '—' }}</td></tr>@endforeach</tbody></table></div>
@endif
</body>
</html>
