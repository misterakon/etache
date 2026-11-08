@php $p = $d['p']; $fr = fn ($v) => $v === null ? '—' : str_replace('.', ',', (string) $v); @endphp
<div class="ck-mini ck-mini--grand">
  <div><b>{{ $p['echeances'] }}</b><span>échéances sur la période<br><small>{{ $d['periode']['libelle'] }}</small></span></div>
  <div class="vert"><b>{{ $p['terminees'] }}</b><span>tâches terminées</span></div>
  <div><b>{{ $p['a_temps'] }}</b><span>terminées dans les délais</span></div>
  <div class="{{ ($p['taux_respect'] ?? 100) < 50 ? 'rouge' : 'vert' }}"><b>{{ $fr($p['taux_respect']) }}{{ $p['taux_respect'] !== null ? ' %' : '' }}</b><span>échéances respectées</span></div>
  <div class="rouge"><b>{{ $p['retard_fin_periode'] }}</b><span>en retard à la fin de la période</span></div>
</div>

<div class="ck-grille ck-grille--2-1">
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-line"></i> Prévu et réalisé sur l'exercice</h2></header><div class="ck-graphe ck-graphe--haut" data-graphe="courbe"></div></section>
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-calendar-week"></i> Échéances des 8 prochaines semaines</h2></header><div class="ck-graphe ck-graphe--haut" data-graphe="semaines"></div></section>
</div>

<section class="ck-carte">
  <header class="ck-carte__tete"><h2><i class="fa-solid fa-wave-square"></i> Retards jour après jour</h2><span class="ck-aide">Relevé quotidien — zoomez avec la souris</span></header>
  @if ($d['graph']['evolution'])
    <div class="ck-graphe ck-graphe--haut" data-graphe="evolution"></div>
  @else
    <div class="ck-vide"><i class="fa-regular fa-clock"></i><strong>Historique en cours de constitution</strong><span>Un relevé est enregistré chaque jour ; la courbe apparaît dès le deuxième relevé ({{ $d['points'] }} pour l'instant).</span></div>
  @endif
</section>
