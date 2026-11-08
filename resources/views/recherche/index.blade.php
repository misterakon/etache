@extends('layout.master')
@section('title', 'Recherche')

@section('main-content')
  <div class="page-body">
    <div class="container"><x-titre-page titre="Recherche" :fil="['Recherche']" /></div>
    <div class="container aide-page aide">
      <div class="rg-page">
        <form class="rg-page__champ" method="get" action="{{ route('recherche') }}" role="search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" name="q" value="{{ $q }}" placeholder="Tâche, agent, courrier, direction, page, aide…" maxlength="120" autocomplete="off" autofocus aria-label="Rechercher dans toute l'application">
        </form>

        @if ($q === '')
          <div class="aide-bas"><i class="fa-solid fa-circle-info" style="color:#2B6CB0"></i> Saisissez un ou plusieurs mots : tous doivent se retrouver. La recherche couvre vos tâches, les agents, le courrier, les directions, les pages de l’application et l’aide, selon votre profil.</div>
        @elseif ($trop_court)
          <div class="aide-bas">Saisissez au moins deux caractères par mot.</div>
        @elseif (empty($rubriques))
          <div class="rg-bloc"><div class="rg-vide"><i class="fa-regular fa-face-frown" style="font-size:28px"></i><br>Aucun résultat pour « {{ $q }} ». Vérifiez l’orthographe ou essayez un seul mot.</div></div>
        @else
          @foreach ($rubriques as $r)
            <section class="rg-bloc rg-c-{{ $r['couleur'] }}">
              <h2><i class="fa-solid {{ $r['icone'] }}"></i>{{ $r['titre'] }} <span>{{ $r['nb'] }}</span></h2>
              @foreach ($r['items'] as $it)
                <a class="rg-lien" href="{{ $it['url'] }}"><b>{{ $it['titre'] }}</b><small>{{ $it['sous'] }}</small></a>
              @endforeach
              @if ($r['nb'] > count($r['items']))<p class="rg-pied" style="border:0">Et {{ $r['nb'] - count($r['items']) }} autre(s) : précisez votre recherche pour les voir.</p>@endif
            </section>
          @endforeach
        @endif
      </div>
    </div>
  </div>
@endsection
