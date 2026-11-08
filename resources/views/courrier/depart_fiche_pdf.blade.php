{{-- Fiche imprimable d'un courrier départ (dompdf) : reprend la ligne du registre papier. Couleurs : config/export_theme.php --}}
@php $c = fn ($k) => \App\Support\ExportTheme::hex($k); @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
@include('exports._style')
<style>
  .entete { width: 100%; margin-bottom: 4px; }
  .entete td { text-align: center; font-size: 8.5px; font-weight: bold; line-height: 1.5; color: {{ $c('texte_doux') }}; }
  .numero { margin: 0 0 14px; font-size: 24px; font-weight: bold; letter-spacing: .05em; color: {{ $c('accent_texte') }}; }
  table.fiche { width: 100%; }
  table.fiche td { padding: 9px 12px; border-bottom: 1px solid {{ $c('ligne_bordure') }}; vertical-align: top; font-size: 10.5px; }
  table.fiche td.e { width: 28%; background: {{ $c('fond_carte') }}; color: {{ $c('primaire') }}; font-weight: bold; font-size: 8.5px; text-transform: uppercase; letter-spacing: .05em; }
  .autres { margin-top: 14px; padding: 9px 12px; background: {{ $c('fond_carte') }}; border-left: 4px solid {{ $c('accent') }}; font-size: 9.5px; }
  table.signature { width: 100%; margin-top: 24px; }
  table.signature td { width: 48%; height: 84px; padding: 9px 11px; border: 1px solid {{ $c('bordure_carte') }}; border-radius: 8px; vertical-align: top; font-weight: bold; font-size: 9px; color: {{ $c('primaire') }}; }
  table.signature td.espace { width: 4%; border: 0; }
  .enregistre { margin-top: 14px; font-size: 8px; color: {{ $c('texte_doux') }}; text-align: right; }
</style>
</head>
<body>
@include('exports._cadre', ['pied' => (\App\Support\Reglages::get('exports.mention_pdf') ?: 'e-Tâche').' · Départ '.$depart->numero.' · '.now()->format('d/m/Y')])

<table class="entete"><tr><td>{{ $ministere }}<br>{{ $direction }}</td></tr></table>
<h1 class="titre">Courrier départ</h1>
<p class="meta">Édité le {{ now()->format('d/m/Y à H:i') }}</p>
<div class="trait" style="margin-bottom: 14px;"></div>
<div class="numero">{{ $depart->numero }}</div>

<table class="fiche">
  <tr><td class="e">Date de départ</td><td>{{ $depart->date_depart->format('d/m/Y') }}</td></tr>
  <tr><td class="e">Destinataire</td><td><strong>{{ $depart->destinataire }}</strong>@if ($depart->destinataire_precision)<br>{{ $depart->destinataire_precision }}@endif</td></tr>
  <tr><td class="e">Objet</td><td>{{ $depart->estConfidentiel() ? 'Courrier confidentiel' : $depart->objet }}</td></tr>
  <tr><td class="e">Courrier d'origine</td><td>{{ $depart->courrier?->numero ?? '—' }}</td></tr>
  <tr><td class="e">Mode d'envoi</td><td>{{ $depart->libelleModeEnvoi() }}</td></tr>
  <tr><td class="e">Nombre de pièces</td><td>{{ $depart->nombre_pieces }}</td></tr>
  <tr><td class="e">N° d'archives</td><td>{{ $depart->numero_archives ?: '' }}</td></tr>
  <tr><td class="e">À conserver jusqu'au</td><td>{{ $depart->a_conserver_jusqu_au?->format('d/m/Y') ?: '' }}</td></tr>
  <tr><td class="e">Observations</td><td>{{ $depart->observations }}</td></tr>
</table>
@if ($groupe->count() > 1)
  <div class="autres"><strong>Même envoi :</strong> {{ $groupe->map(fn ($g) => $g->numero.' ('.$g->destinataire.')')->implode(' · ') }}</div>
@endif

<table class="signature"><tr><td>Le service courrier</td><td class="espace"></td><td>Accusé de réception du destinataire (cachet, date, signature)</td></tr></table>
<div class="enregistre">Enregistré par {{ $depart->enregistrePar?->name ?? '—' }} le {{ $depart->created_at->format('d/m/Y') }}</div>
</body>
</html>
