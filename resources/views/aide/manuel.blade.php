@extends('layout.master')
@section('title', 'Manuel complet')

@section('main-content')
  <div class="page-body">
    <div class="container aide-page aide">
      <section class="aide-hero">
        <h1>Manuel d’utilisation d’e-Tâche</h1>
        <p>Tous les chapitres de votre profil sur une seule page. Pour l’enregistrer en PDF : imprimer, puis choisir « Enregistrer au format PDF ».</p>
        <div class="aide-hero__actions"><button type="button" class="aide-btn aide-btn--plein" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimer / PDF</button><a class="aide-btn" href="{{ route('aide.index') }}"><i class="fa-solid fa-arrow-left"></i> Retour à l’aide</a></div>
      </section>
      @foreach ($chapitres as $c)
        <section class="aide-manuel-chap">
          <div class="aide-entete-chap aide-c-{{ $c['couleur'] }}"><span class="aide-resultat__ico"><i class="{{ $c['icone'] }}"></i></span><div><h1>{{ $loop->iteration }}. {{ $c['titre'] }}</h1><p>{{ $c['resume'] }}</p></div></div>
          @if ($c['intro_html'])<div class="aide-section"><div class="aide-texte">{!! $c['intro_html'] !!}</div></div>@endif
          @foreach ($c['sections'] as $s)
            <div class="aide-section" style="margin-top:12px"><h2>{{ $s['titre'] }}</h2><div class="aide-texte">{!! $s['html'] !!}</div></div>
          @endforeach
        </section>
      @endforeach
    </div>
  </div>
@endsection
