{{-- Accusé de prise en charge d'une diligence (dompdf). Couleurs : config/export_theme.php --}}
@php $c = fn ($k) => \App\Support\ExportTheme::hex($k); $cc = $diligence->courrier; @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
@include('exports._style')
<style>
  .entete { width: 100%; margin-bottom: 4px; }
  .entete td { text-align: center; font-size: 8.5px; font-weight: bold; line-height: 1.5; color: {{ $c('texte_doux') }}; }
  .code { margin: 0 0 14px; font-size: 22px; font-weight: bold; letter-spacing: .05em; color: {{ $c('accent_texte') }}; }
  table.fiche { width: 100%; }
  table.fiche td { padding: 9px 12px; border-bottom: 1px solid {{ $c('ligne_bordure') }}; vertical-align: top; font-size: 10.5px; }
  table.fiche td.e { width: 28%; background: {{ $c('fond_carte') }}; color: {{ $c('primaire') }}; font-weight: bold; font-size: 8.5px; text-transform: uppercase; letter-spacing: .05em; }
  .texte { margin: 18px 0; padding: 12px 14px; background: {{ $c('fond_carte') }}; border-left: 4px solid {{ $c('accent') }}; font-size: 10.5px; line-height: 1.6; }
  table.signature { width: 100%; margin-top: 22px; }
  table.signature td { width: 48%; height: 80px; padding: 9px 11px; border: 1px solid {{ $c('bordure_carte') }}; border-radius: 8px; vertical-align: top; font-weight: bold; font-size: 9px; color: {{ $c('primaire') }}; }
  table.signature td.espace { width: 4%; border: 0; }
</style>
</head>
<body>
@include('exports._cadre', ['pied' => (\App\Support\Reglages::get('exports.mention_pdf') ?: 'e-Tâche').' · Accusé '.$diligence->code.' · '.now()->format('d/m/Y')])

<table class="entete"><tr><td>{{ $ministere }}<br>{{ $direction }}</td></tr></table>
<h1 class="titre">Accusé de prise en charge</h1>
<p class="meta">Édité le {{ now()->format('d/m/Y à H:i') }}</p>
<div class="trait" style="margin-bottom: 14px;"></div>
<div class="code">{{ $diligence->code }}</div>

<table class="fiche">
  <tr><td class="e">Courrier</td><td><strong>{{ $cc->numero }}</strong> · reçu le {{ $cc->date_reception->format('d/m/Y') }}</td></tr>
  <tr><td class="e">Expéditeur</td><td>{{ $cc->expediteur }}</td></tr>
  <tr><td class="e">Objet</td><td>{{ $cc->estConfidentiel() ? 'Courrier confidentiel' : $cc->objet }}</td></tr>
  <tr><td class="e">Imputé à</td><td>{{ $diligence->destinataire_libelle }}@if ($diligence->responsable) — {{ $diligence->responsable->nomComplet() }}@endif</td></tr>
  <tr><td class="e">Imputé par</td><td>{{ $diligence->imputation?->auteur?->name ?? '—' }} le {{ $diligence->imputation?->date_imputation?->format('d/m/Y') }}</td></tr>
  <tr><td class="e">Instructions du DG</td><td>{{ $diligence->imputation?->instructions->pluck('libelle')->implode(', ') ?: '—' }}</td></tr>
  <tr><td class="e">Échéance</td><td><strong style="color: {{ $c('accent_texte') }};">{{ $diligence->echeance->format('d/m/Y') }}</strong></td></tr>
</table>

<div class="texte">
  Il est accusé réception du courrier ci-dessus, <strong>pris en charge le {{ $diligence->pris_en_charge_le->format('d/m/Y à H:i') }}</strong>@if ($priseParNom) par {{ $priseParNom }}@endif.
  Le traitement sera conduit dans le délai fixé, soit au plus tard le <strong>{{ $diligence->echeance->format('d/m/Y') }}</strong>.
</div>

<table class="signature"><tr><td>Le responsable de la diligence</td><td class="espace"></td><td>Visa du service courrier / du secrétariat du DG</td></tr></table>
</body>
</html>
