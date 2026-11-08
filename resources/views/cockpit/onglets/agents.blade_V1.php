<div class="ck-mini ck-mini--grand">
  <div><b>{{ $d['mobilises'] }}</b><span>agents mobilisés</span></div>
  <div class="rouge"><b>{{ $d['surcharges'] }}</b><span>agents surchargés</span></div>
</div>

<div class="ck-grille ck-grille--2-1">
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-bar"></i> Charge des 10 agents les plus sollicités</h2><span class="ck-aide">Tâches + diligences + actions ouvertes</span></header><div class="ck-graphe ck-graphe--haut" data-graphe="charge"></div></section>
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-circle-info"></i> Comment lire</h2></header>
    <p class="ck-texte">La charge additionne les tâches ouvertes, les diligences du courrier et les actions de traitement en cours. Un agent est signalé comme <b>surchargé</b> quand ce total atteint le seuil fixé dans Réglages (actuellement {{ $d['lignes'][0]['seuil'] ?? '—' }}).</p></section>
</div>

<section class="ck-carte">
  <header class="ck-carte__tete"><h2><i class="fa-solid fa-users"></i> Détail par agent</h2><span class="ck-aide">Cliquez un nom pour ouvrir sa fiche</span></header>
  @if (empty($d['lignes']))
    <div class="ck-vide"><i class="fa-regular fa-face-smile"></i><strong>Aucune charge en cours</strong></div>
  @else
    <div class="ck-table-enveloppe"><table class="ck-table">
      <thead><tr><th>Agent</th><th>Tâches</th><th>En retard</th><th>Diligences</th><th>Actions</th><th>Charge</th></tr></thead>
      <tbody>
        @foreach ($d['lignes'] as $l)
          <tr>
            <td class="ck-table__tache"><a href="{{ $l['url'] }}"><span class="ck-avatar ck-avatar--petit">{{ $l['initiales'] }}</span>{{ $l['nom'] }}@if ($l['service'])<small class="ck-axe">{{ $l['service'] }}</small>@endif</a></td>
            <td>{{ $l['taches'] }}</td><td>@if ($l['retard'])<span class="ck-puce ck-puce--rouge">{{ $l['retard'] }}</span>@else<span class="ck-zero">0</span>@endif</td><td>{{ $l['diligences'] }}</td><td>{{ $l['actions'] }}</td>
            <td><span class="ck-prog"><i class="ck-prog--{{ $l['surcharge'] ? 'rouge' : 'bleu' }}" style="width: {{ min(100, round($l['charge'] / max(1, $l['seuil']) * 100)) }}%"></i></span><small class="ck-prog__nb">{{ $l['charge'] }}@if ($l['surcharge']) · surcharge @endif</small></td>
          </tr>
        @endforeach
      </tbody></table></div>
  @endif
</section>
