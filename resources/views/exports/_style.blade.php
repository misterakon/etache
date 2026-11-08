{{-- Feuille de style commune des PDF (dompdf). Couleurs : config/export_theme.php --}}
@php
  $c = fn ($k) => \App\Support\ExportTheme::hex($k);
  $police = \App\Support\ExportTheme::policePdf();
  $hBandeau = \App\Support\ExportTheme::hauteurBandeau();
  $pAccent = \App\Support\ExportTheme::partAccent();
  $t = fn ($k) => \App\Support\ExportTheme::taille($k);
@endphp
<style>
  @page { margin: 34px 30px 46px 30px; }
  body { font-family: '{{ $police }}', 'DejaVu Sans', sans-serif; font-size: {{ $t('texte') }}; color: {{ $c('texte') }}; margin: 0; }
  table { border-collapse: collapse; }

  /* Bandeau fixe en haut de chaque page */
  .bandeau { position: fixed; top: -34px; left: -30px; right: -30px; height: {{ $hBandeau }}px; }
  .bandeau table { width: 100%; height: {{ $hBandeau }}px; }
  .bandeau td { height: {{ $hBandeau }}px; padding: 0; }
  .bandeau .b-accent { width: {{ $pAccent }}%; background: {{ $c('accent') }}; }
  .bandeau .b-primaire { background: {{ $c('primaire') }}; }

  /* Pied fixe */
  .pied { position: fixed; bottom: -36px; left: 0; right: 0; height: 20px; border-top: 1px solid {{ $c('ligne_bordure') }}; padding-top: 5px; font-size: {{ $t('pied') }}; color: {{ $c('texte_doux') }}; }

  /* Titre et informations */
  h1.titre { margin: 6px 0 4px; font-size: {{ $t('titre') }}; font-weight: bold; color: {{ $c('primaire') }}; }
  .meta { margin: 0 0 3px; font-size: {{ $t('infos') }}; color: {{ $c('texte_doux') }}; }
  .filtre { margin: 0 0 3px; font-size: {{ $t('infos') }}; font-weight: bold; color: {{ $c('accent_texte') }}; }

  /* Cartes de chiffres clés */
  table.cartes { width: 100%; margin: 16px 0 6px; }
  table.cartes td { width: 25%; padding: 0 6px; vertical-align: top; }
  table.cartes td.premiere { padding-left: 0; }
  table.cartes td.derniere { padding-right: 0; }
  .carte { position: relative; height: 52px; background: {{ $c('fond_carte') }}; border: 1px solid {{ $c('bordure_carte') }}; border-radius: 8px; padding: 9px 10px 0 18px; }
  .carte .barre { position: absolute; left: 9px; top: 10px; width: 3px; height: 32px; border-radius: 2px; }
  .carte .lib { font-size: {{ $t('carte_libelle') }}; color: {{ $c('texte_doux') }}; }
  .carte .val { margin-top: 6px; font-size: {{ $t('carte_valeur') }}; font-weight: bold; color: {{ $c('primaire') }}; }

  /* Titres de section */
  h2.section { margin: 20px 0 0; font-size: {{ $t('section') }}; font-weight: bold; color: {{ $c('primaire') }}; }
  .trait { width: 30px; height: 3px; margin: 3px 0 10px; background: {{ $c('accent') }}; border-radius: 2px; }

  /* Tableaux */
  table.t { width: 100%; }
  table.t th { padding: 8px 9px; background: {{ $c('primaire') }}; color: {{ $c('entete_texte') }}; font-size: {{ $t('entete') }}; font-weight: bold; text-align: left; }
  table.t td { padding: 7px 9px; border-bottom: 1px solid {{ $c('ligne_bordure') }}; font-size: {{ $t('tableau') }}; vertical-align: top; word-wrap: break-word; }
  table.t tr.alt td { background: {{ $c('ligne_alternee') }}; }
  table.t th.num, table.t td.num { text-align: right; }
  table.t td.nw { white-space: nowrap; }
  table.t td.fort { font-weight: bold; }
  table.t th.p { border-top-left-radius: 6px; border-top-right-radius: 6px; }
  table.t thead { display: table-header-group; }
  table.t tr { page-break-inside: avoid; }
  table.deux { width: 100%; }
  table.deux > tbody > tr > td { width: 50%; vertical-align: top; }
  table.deux td.g { padding-right: 14px; }
  table.deux td.d { padding-left: 14px; }
  .vide { padding: 18px; text-align: center; font-style: italic; color: {{ $c('texte_doux') }}; }
</style>
