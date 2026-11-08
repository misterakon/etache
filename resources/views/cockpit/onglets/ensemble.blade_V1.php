@php
  $cap = $d['cap'];
  $phrases = [
      'rouge' => ['fa-triangle-exclamation', 'Situation préoccupante'],
      'ambre' => ['fa-circle-exclamation', 'À surveiller'],
      'vert' => ['fa-circle-check', 'Situation maîtrisée'],
  ][$cap['ton']];
  $fr = fn ($v) => str_replace('.', ',', (string) $v);
  $ecartTexte = $cap['ecart'] >= 0 ? '+'.$fr($cap['ecart']).' pt' : $fr($cap['ecart']).' pt';
  $retardsDirections = collect($d['directions']);
  $maxRetard = max(1, (int) $retardsDirections->max('retards'));
@endphp

{{-- Phrase de synthèse --}}
<section class="ck-cap ck-cap--{{ $cap['ton'] }}">
  <span class="ck-cap__ico"><i class="fa-solid {{ $phrases[0] }}"></i></span>
  <div class="ck-cap__texte">
    <small>{{ $phrases[1] }}</small>
    <p>
      <b>{{ $cap['retards'] }}</b> tâche{{ $cap['retards'] > 1 ? 's' : '' }} en retard sur <b>{{ $cap['total'] }}</b> ({{ $cap['pct_retard'] }} %)@if ($cap['retards'] > 0), dont <b>{{ $cap['non_justifies'] }} sans justification</b>@endif. L'avancement réel est de <b>{{ $fr($cap['realise']) }} %</b> pour <b>{{ $fr($cap['attendu']) }} %</b> attendus à ce jour.
    </p>
  </div>
  <div class="ck-ecart" title="Réalisé par rapport à ce qui était prévu">
    <div class="ck-ecart__tete"><span>Écart au plan</span><strong class="{{ $cap['ecart'] >= 0 ? 'bon' : 'mauvais' }}">{{ $ecartTexte }}</strong></div>
    <div class="ck-ecart__piste">
      <i class="ck-ecart__realise" style="width: {{ min(100, $cap['realise']) }}%"></i>
      <b class="ck-ecart__repere" style="left: {{ min(100, $cap['attendu']) }}%" title="Attendu : {{ $fr($cap['attendu']) }} %"></b>
    </div>
    <div class="ck-ecart__legende"><span><i class="pt pt--vert"></i>Réalisé</span><span><i class="pt pt--repere"></i>Attendu</span></div>
  </div>
</section>

<div class="ck-grille ck-grille--2-1">
  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-line"></i> Avancement : prévu et réalisé</h2><span class="ck-aide">Cumul des tâches terminées, mois par mois</span></header>
    <div class="ck-graphe ck-graphe--haut" data-graphe="courbe"></div>
  </section>

  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-chart-pie"></i> Statut des tâches</h2><span class="ck-aide">Cliquez pour voir la liste</span></header>
    <div class="ck-donut">
      <div class="ck-graphe ck-graphe--donut" data-graphe="repartition"></div>
      <ul class="ck-legende">
        @foreach ($d['repartition'] as $r)
          <li><button type="button" data-ck-onglet="taches" data-ck-filtre="{{ $r['cle'] }}" data-seg="{{ $loop->index }}" style="--c: {{ $r['couleur'] }}">
            <i></i><span>{{ $r['libelle'] }}</span><b>{{ $r['valeur'] }}</b><em>{{ $r['pct'] }} %</em>
          </button></li>
        @endforeach
      </ul>
    </div>
  </section>
</div>

<div class="ck-grille ck-grille--1-1">
  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-bolt"></i> À traiter maintenant</h2><span class="ck-aide">Par ordre d'urgence</span></header>
    <ul class="ck-actions">
      @foreach ($d['a_traiter'] as $t)
        @php $url = str_starts_with($t['cle'], 'url:') ? substr($t['cle'], 4) : null; [$o, $f] = $url ? [null, null] : (str_contains($t['cle'], ':') ? explode(':', $t['cle']) : [$t['cle'], null]); @endphp
        <li>
          <a href="{{ $url ?? '#' }}" class="ck-action ck-action--{{ $t['ton'] }} {{ $t['nb'] === 0 ? 'ck-action--vide' : '' }}" @if (! $url) data-ck-onglet="{{ $o }}" @if ($f) data-ck-filtre="{{ $f }}" @endif @endif>
            <div class="ck-action__ico"><i class="fa-solid {{ $t['icone'] }}"></i></div>
            <div class="ck-action__txt"><strong>{{ $t['libelle'] }}</strong><small>{{ $t['nb'] === 0 ? 'Rien à signaler' : $t['aide'] }}</small></div>
            <span class="ck-action__nb">{{ $t['nb'] }}</span>
            <i class="fa-solid fa-chevron-right ck-action__fleche"></i>
          </a>
        </li>
      @endforeach
    </ul>
  </section>

  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-sitemap"></i> Où sont les retards ?</h2><span class="ck-aide">Cliquez une direction pour la isoler</span></header>
    @if ($retardsDirections->isEmpty())
      <div class="ck-vide"><i class="fa-solid fa-champagne-glasses"></i><strong>Aucun retard</strong><span>Aucune direction n'a de tâche en retard.</span></div>
    @else
      <ul class="ck-barres">
        @foreach ($retardsDirections as $r)
          <li><button type="button" data-ck-portee="direction:{{ $r['id'] }}" title="Isoler cette direction">
            <span class="ck-barres__lib">{{ $r['libelle'] }}</span>
            <span class="ck-barres__piste"><i style="width: {{ round($r['retards'] / $maxRetard * 100) }}%"></i></span>
            <b>{{ $r['retards'] }}</b><em>{{ $r['non_justifies'] }} non justifié(s)</em>
          </button></li>
        @endforeach
      </ul>
    @endif
  </section>
</div>

<div class="ck-grille ck-grille--1-1">
  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-bullseye"></i> Avancement par axe</h2><span class="ck-aide">Cliquez une barre pour filtrer</span></header>
    <div class="ck-graphe" data-graphe="axes"></div>
  </section>

  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-users"></i> Agents les plus sollicités</h2><button type="button" class="ck-lien" data-ck-onglet="agents">Voir tous</button></header>
    @if (empty($d['agents']))
      <div class="ck-vide"><i class="fa-regular fa-face-smile"></i><strong>Aucune charge en cours</strong></div>
    @else
      <ul class="ck-agents">
        @foreach ($d['agents'] as $ag)
          <li class="{{ $ag['surcharge'] ? 'surcharge' : '' }}">
            <span class="ck-avatar">{{ \Illuminate\Support\Str::of($ag['nom'])->explode(' ')->map(fn ($m) => mb_substr($m, 0, 1))->take(2)->implode('') }}</span>
            <span class="ck-agents__nom">{{ $ag['nom'] }}@if ($ag['surcharge'])<em>surchargé</em>@endif</span>
            <span class="ck-agents__charge"><b>{{ $ag['charge'] }}</b> en cours</span>
            @if ($ag['retard'])<span class="ck-puce ck-puce--rouge">{{ $ag['retard'] }} en retard</span>@endif
          </li>
        @endforeach
      </ul>
    @endif
    @if ($d['courrier'])
      <div class="ck-mini">
        <button type="button" data-ck-onglet="courrier"><b>{{ $d['courrier']['total_recus'] }}</b><span>courriers reçus</span></button>
        <button type="button" data-ck-onglet="courrier"><b>{{ $d['courrier']['diligences_ouvertes'] }}</b><span>diligences ouvertes</span></button>
        <button type="button" data-ck-onglet="courrier" class="rouge"><b>{{ $d['courrier']['diligences_en_retard'] }}</b><span>en retard</span></button>
      </div>
    @endif
  </section>
</div>

@if ($d['synthese'])
  <section class="ck-carte ck-extrait">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-feather-pointed"></i> Dernière synthèse</h2><button type="button" class="ck-lien" data-ck-onglet="synthese">Lire en entier</button></header>
    <p>{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags(\App\Support\RedacteurSynthese::versHtml($d['synthese']->contenu)), ENT_QUOTES), 420) }}</p>
  </section>
@endif
