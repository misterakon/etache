@php $fr = fn ($v) => str_replace('.', ',', (string) $v); @endphp
<div class="ck-grille ck-grille--1-1">
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-sitemap"></i> Tâches par direction</h2><span class="ck-aide">Terminées, en cours, à démarrer</span></header><div class="ck-graphe ck-graphe--haut" data-graphe="directions"></div></section>
  <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-bullseye"></i> Avancement par axe</h2><span class="ck-aide">Cliquez une barre pour filtrer</span></header><div class="ck-graphe ck-graphe--haut" data-graphe="axes"></div></section>
</div>

<section class="ck-carte">
  <header class="ck-carte__tete"><h2><i class="fa-solid fa-table-list"></i> Détail par direction</h2><span class="ck-aide">Cliquez une ligne pour isoler la direction</span></header>
  <div class="ck-table-enveloppe"><table class="ck-table ck-table--cliquable">
    <thead><tr><th>Direction</th><th>Tâches</th><th>Terminées</th><th>En cours</th><th>En retard</th><th>Non justifiés</th><th>Avancement</th></tr></thead>
    <tbody>
      @foreach ($d['directions'] as $r)
        <tr @if ($r['id']) data-ck-portee="direction:{{ $r['id'] }}" tabindex="0" @endif>
          <td class="ck-table__tache"><strong>{{ $r['libelle'] }}</strong></td><td>{{ $r['total'] }}</td><td>{{ $r['terminees'] }}</td><td>{{ $r['en_cours'] }}</td>
          <td>@if ($r['en_retard'])<span class="ck-puce ck-puce--rouge">{{ $r['en_retard'] }}</span>@else <span class="ck-zero">0</span>@endif</td>
          <td>{{ $r['non_justifies'] }}</td>
          <td><span class="ck-prog"><i class="ck-prog--{{ $r['avancement'] >= 70 ? 'vert' : ($r['avancement'] >= 40 ? 'ambre' : 'rouge') }}" style="width: {{ min(100, $r['avancement']) }}%"></i></span><small class="ck-prog__nb">{{ $fr($r['avancement']) }} %</small></td>
        </tr>
      @endforeach
    </tbody></table></div>
</section>

<section class="ck-carte">
  <header class="ck-carte__tete"><h2><i class="fa-solid fa-flag"></i> Détail par axe stratégique</h2></header>
  <div class="ck-table-enveloppe"><table class="ck-table ck-table--cliquable">
    <thead><tr><th>Axe</th><th>Tâches</th><th>Terminées</th><th>En retard</th><th>Avancement</th></tr></thead>
    <tbody>
      @foreach ($d['axes'] as $r)
        <tr data-ck-portee="axe:{{ $r['id'] }}" tabindex="0">
          <td class="ck-table__tache"><span class="ck-code">{{ $r['code'] }}</span>{{ $r['libelle'] }}</td><td>{{ $r['total'] }}</td><td>{{ $r['terminees'] }}</td>
          <td>@if ($r['en_retard'])<span class="ck-puce ck-puce--rouge">{{ $r['en_retard'] }}</span>@else <span class="ck-zero">0</span>@endif</td>
          <td><span class="ck-prog"><i class="ck-prog--{{ $r['avancement'] >= 70 ? 'vert' : ($r['avancement'] >= 40 ? 'ambre' : 'rouge') }}" style="width: {{ min(100, $r['avancement']) }}%"></i></span><small class="ck-prog__nb">{{ $fr($r['avancement']) }} %</small></td>
        </tr>
      @endforeach
    </tbody></table></div>
</section>
