{{-- Modèle PDF commun des exports tabulaires.
     Variables : $titre, $infos, $entetes, $lignes, $numeriques, $mention, $cartes (option), $synthese (option) --}}
@php
  $cartes = $cartes ?? [];
  $synthese = $synthese ?? [];
  $numeriques = $numeriques ?? [];
  $courtes = $courtes ?? [];
  $theme = \App\Support\ExportTheme::class;
  $nbsp = "\u{00A0}";
  $gris = array_values(array_filter($infos, fn ($i) => str_starts_with($i, 'Édité') || str_starts_with($i, 'Exporté')));
  $orange = array_values(array_filter($infos, fn ($i) => ! (str_starts_with($i, 'Édité') || str_starts_with($i, 'Exporté'))));
  $formatMontant = fn ($v, $i) => ($numeriques[$i] ?? false) && is_numeric($v) && preg_match('/FCFA|montant|budget|brut|net|frais|total/i', (string) $entetes[$i])
      ? number_format((float) $v, 0, ',', $nbsp) : $v;
  $formatCarte = fn ($c) => (is_numeric($c['valeur'] ?? null) ? number_format((float) $c['valeur'], is_int($c['valeur']) ? 0 : 1, ',', $nbsp) : ($c['valeur'] ?? '')).(isset($c['unite']) ? $nbsp.$c['unite'] : '');
  $pied = trim(($mention ?: $theme::application()).' · '.$titre.' · '.now()->format('d/m/Y'));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>{{ $titre }}</title>
@include('exports._style')
</head>
<body>
@include('exports._cadre', ['pied' => $pied])

<h1 class="titre">{{ $titre }}</h1>
@foreach ($gris as $info)<p class="meta">{{ $info }}</p>@endforeach
@foreach ($orange as $info)<p class="filtre">{{ $info }}</p>@endforeach

@if (count($cartes))
  <table class="cartes"><tr>
    @foreach (array_slice($cartes, 0, 4) as $i => $carte)
      <td class="{{ $i === 0 ? 'premiere' : '' }} {{ $i === min(count($cartes), 4) - 1 ? 'derniere' : '' }}">
        <div class="carte"><div class="barre" style="background: {{ $theme::hex($theme::carte($i)) }};"></div>
          <div class="lib">{{ $carte['libelle'] ?? '' }}</div>
          <div class="val">{{ $formatCarte($carte) }}</div>
        </div>
      </td>
    @endforeach
  </tr></table>
@endif

@foreach (array_chunk($synthese, 2) as $paire)
  <table class="deux" style="margin-top: 14px;"><tr>
    @foreach ($paire as $k => $bloc)
      <td class="{{ $k === 0 ? 'g' : 'd' }}">
        <h2 class="section" style="margin-top: 0;">{{ $bloc['titre'] ?? '' }}</h2><div class="trait"></div>
        <table class="t">
          <thead><tr>
            @foreach (array_values($bloc['entetes'] ?? []) as $j => $e)<th class="{{ $j > 0 ? 'num' : '' }}">{{ $e }}</th>@endforeach
          </tr></thead>
          <tbody>
            @foreach ($bloc['lignes'] ?? [] as $n => $l)
              <tr class="{{ $n % 2 ? 'alt' : '' }}">
                @foreach (array_values($l) as $j => $v)<td class="{{ $j > 0 ? 'num' : 'fort' }}">{{ is_int($v) ? number_format($v, 0, ',', $nbsp) : $v }}</td>@endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </td>
    @endforeach
    @if (count($paire) < 2)<td></td>@endif
  </tr></table>
@endforeach

@if (count($cartes) || count($synthese))
  <div style="page-break-before: always;"></div>
  <h2 class="section" style="margin-top: 4px;">Détail ({{ count($lignes) }})</h2><div class="trait"></div>
@else
  <div style="height: 10px;"></div>
@endif

<table class="t">
  <thead><tr>
    @foreach ($entetes as $i => $e)<th class="{{ ($numeriques[$i] ?? false) ? 'num' : '' }}">{{ $e }}</th>@endforeach
  </tr></thead>
  <tbody>
    @if (count($lignes))
      @foreach ($lignes as $n => $ligne)
        <tr class="{{ $n % 2 ? 'alt' : '' }}">
          @foreach (array_values($ligne) as $i => $v)<td class="{{ ($numeriques[$i] ?? false) ? 'num' : '' }} {{ ($courtes[$i] ?? false) ? 'nw' : '' }}">{{ $formatMontant($v, $i) }}</td>@endforeach
        </tr>
      @endforeach
    @else
      <tr><td class="vide" colspan="{{ count($entetes) }}">Aucune donnée pour ces critères.</td></tr>
    @endif
  </tbody>
</table>
</body>
</html>
