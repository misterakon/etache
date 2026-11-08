@if (! $d['c'])
  <section class="ck-carte"><div class="ck-vide"><i class="fa-solid fa-envelope"></i><strong>Courrier disponible pour toute la structure</strong>
    <span>Le courrier et les diligences ne sont pas rattachés à une direction ou à un axe : choisissez « Toute la structure » comme périmètre pour les voir.</span>
    @if ($c['porteeType'] !== 'global' && $c['dirs'] === null)<button type="button" class="ck-btn" data-ck-portee="global">Voir toute la structure</button>@endif
  </div></section>
@else
  @php $x = $d['c']; @endphp
  <div class="ck-mini ck-mini--grand">
    <div><b>{{ $x['total_recus'] }}</b><span>courriers reçus</span></div>
    <div><b>{{ $x['total_departs'] }}</b><span>courriers départ</span></div>
    <div><b>{{ $x['diligences_ouvertes'] }}</b><span>diligences ouvertes</span></div>
    <div class="rouge"><b>{{ $x['diligences_en_retard'] }}</b><span>diligences en retard</span></div>
    <div class="vert"><b>{{ $x['diligences_traitees'] }}</b><span>traitées cette année</span></div>
    <div><b>{{ $x['delai_moyen'] !== null ? str_replace('.', ',', $x['delai_moyen']).' j' : '—' }}</b><span>délai moyen de traitement</span></div>
  </div>

  <div class="ck-grille ck-grille--2-1">
    <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-column"></i> Courriers reçus et départs par mois</h2></header><div class="ck-graphe ck-graphe--haut" data-graphe="courrier"></div></section>
    <section class="ck-carte"><header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-pie"></i> Statut des diligences</h2></header><div class="ck-graphe ck-graphe--donut" data-graphe="diligences"></div></section>
  </div>

  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-hourglass-end"></i> Diligences en retard</h2><a class="ck-lien" href="{{ route('courrier.diligences.export') }}">Exporter les diligences</a></header>
    @if (empty($d['en_retard']))
      <div class="ck-vide"><i class="fa-regular fa-circle-check"></i><strong>Aucune diligence en retard</strong></div>
    @else
      <div class="ck-table-enveloppe"><table class="ck-table">
        <thead><tr><th>Diligence</th><th>Responsable</th><th>Échéance</th><th>Statut</th></tr></thead>
        <tbody>
          @foreach ($d['en_retard'] as $l)
            <tr><td class="ck-table__tache"><a href="{{ $l['url'] }}"><span class="ck-code">{{ $l['code'] }}</span></a></td><td>{{ $l['responsable'] ?? '—' }}</td>
              <td class="ck-table__date">{{ $l['echeance'] }}<small class="neg">{{ $l['jours'] }} j de retard</small></td><td><span class="ck-puce ck-puce--rouge">{{ $l['statut'] }}</span></td></tr>
          @endforeach
        </tbody></table></div>
    @endif
  </section>
@endif
