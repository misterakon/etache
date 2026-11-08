{{-- « Où en est ce courrier ? » : détenteur actuel, circuit de traitement, circuit de la réponse. Variable : $courrier --}}
@php
  $p = \App\Support\ParcoursCourrier::pour($courrier);
  $icones = ['ok' => 'fa-check', 'cur' => 'fa-user', 'late' => 'fa-triangle-exclamation', 'next' => 'fa-pencil', 'nx' => 'fa-clock', 'off' => 'fa-rotate-left'];
  // couleur principale => [fond, bordure]
  $palette = ['#2B6CB0' => ['#EAF2FB', '#B8D0EA'], '#A73B34' => ['#FBEDEC', '#EBC3C0'], '#C77D0A' => ['#FDF3E1', '#F0D9A8'], '#2E7D5B' => ['#E8F5EE', '#B7DCC8']];
  $styleBanniere = fn (string $c) => '--pj-c:'.$c.';--pj-fond:'.($palette[$c][0] ?? '#EAF2FB').';--pj-bord:'.($palette[$c][1] ?? '#B8D0EA');
@endphp
<div class="cr-carte pj">
  <div class="cr-carte__tete" style="flex-wrap:wrap;gap:10px;">
    <h2 class="cr-carte__titre"><i class="fa-solid fa-location-dot"></i> Où en est ce courrier ?</h2>
    <div class="pj-puces">
      <span class="cr-chip" style="--c: {{ $p['statut_couleur'] }}">{{ $p['statut'] }}</span>
      @if ($p['echeance']) <span class="cr-chip" style="--c: {{ $p['echeance_couleur'] }}">Échéance {{ $p['echeance']->format('d/m/Y') }} · {{ $p['echeance_texte'] }}</span> @endif
    </div>
  </div>
  <div class="cr-carte__corps">

    @if ($p['detenteur'])
      @php $dt = $p['detenteur']; @endphp
      <div class="pj-banniere" style="{{ $styleBanniere($dt['retard'] ? '#A73B34' : '#2B6CB0') }}">
        <div class="pj-avatar">{{ $dt['initiales'] }}</div>
        <div class="pj-banniere__texte">
          <div class="pj-etiquette">Entre les mains de</div>
          <div class="pj-qui">{{ $dt['nom'] }}</div>
          <div class="pj-sous">{{ $dt['role'] }}@if ($dt['unite']) · {{ $dt['unite'] }}@endif</div>
        </div>
        <div class="pj-depuis">
          @if ($dt['a_prendre'])<b>À prendre en charge</b>@endif
          @if ($dt['duree'])depuis {{ $dt['duree'] }}<br>({{ $dt['depuis'] }})@endif
        </div>
      </div>
    @elseif ($p['message'])
      <div class="pj-banniere" style="{{ $styleBanniere($p['message']['couleur']) }}">
        <div class="pj-avatar"><i class="fa-solid {{ $p['message']['icone'] }}"></i></div>
        <div class="pj-banniere__texte"><div class="pj-qui" style="font-size:16px;">{{ $p['message']['texte'] }}</div></div>
      </div>
    @endif

    <div class="pj-bloc">Circuit de traitement</div>
    <div class="pj-etapes">
      @foreach ($p['etapes'] as $e)
        <div class="pj-etape pj-etape--{{ $e['etat'] }}">
          <div class="pj-noeud"><i class="fa-solid {{ $e['icone'] ?? $icones[$e['etat']] }}"></i></div>
          <div class="pj-titre">{{ $e['titre'] }}</div>
          @if ($e['nom'])<div class="pj-nom">{{ $e['nom'] }}</div>@endif
          @if ($e['unite'])<div class="pj-detail">{{ \Illuminate\Support\Str::limit($e['unite'], 42) }}</div>@endif
          @if ($e['detail'])<div class="pj-detail">{{ $e['detail'] }}</div>@endif
          @if ($e['autres'] > 0)<div class="pj-detail">+ {{ $e['autres'] }} autre(s) en parallèle</div>@endif
          @if ($e['note'])<div class="pj-note">{{ $e['note'] }}</div>@endif
        </div>
      @endforeach
    </div>

    @if (! empty($p['associes']))
      <div class="pj-bloc">Associés</div>
      <div class="pj-associes">
        @foreach ($p['associes'] as $a)
          <div class="pj-associe"><b>{{ $a['nom'] }}</b> · {{ $a['role'] }} · {{ $a['statut'] }}</div>
        @endforeach
      </div>
    @endif

    @if ($p['reponse'])
      <div class="pj-bloc">Circuit de la réponse</div>
      <div class="pj-etapes pj-etapes--reponse">
        @foreach ($p['reponse'] as $e)
          <div class="pj-etape pj-etape--{{ $e['etat'] }}">
            <div class="pj-noeud"><i class="fa-solid {{ $e['icone'] ?? $icones[$e['etat']] }}"></i></div>
            <div class="pj-titre">{{ $e['titre'] }}</div>
            @if ($e['nom'])<div class="pj-nom">{{ $e['nom'] }}</div>@endif
            @if ($e['detail'])<div class="pj-detail">{{ $e['detail'] }}</div>@endif
          </div>
        @endforeach
      </div>
    @endif

    @if ($p['avancement'] && $p['avancement']['total'] > 0)
      <div class="pj-avancement">
        <div class="pj-avancement__ligne"><div>Plan de traitement en cours</div><div>{{ $p['avancement']['faites'] }} action(s) faite(s) sur {{ $p['avancement']['total'] }} · {{ $p['avancement']['pourcentage'] }} %</div></div>
        <div class="pj-barre"><div style="width: {{ $p['avancement']['pourcentage'] }}%"></div></div>
      </div>
    @endif
  </div>
</div>
