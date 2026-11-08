{{-- Fiche de transmission du courrier (PDF, dompdf) : reprend le modèle papier. --}}
@php
  $case = fn (bool $coche) => $coche ? '☒' : '☐';
  $cases = [];
  foreach ($groupes['postes'] as $p) { $cases[] = [$p->libelleActuel(), $imputee('poste', $p->id)]; }
  foreach ($groupes['directions'] as $d) { $cases[] = [$d->nom, $imputee('direction', $d->id)]; }
  foreach ($groupes['services_dg'] as $s) { $cases[] = [$s->nom, $imputee('service', $s->id)]; }
  $autres = $agents->pluck('destinataire_libelle')
      ->merge($courrier->imputations->flatMap->destinatairesAssocies->pluck('destinataire_libelle'))
      ->merge($courrier->diligences->whereIn('destinataire_type', ['sous_direction', 'service'])->filter(fn ($d) => ! collect($groupes['services_dg'])->contains('id', $d->destinataire_id) || $d->destinataire_type !== 'service')->pluck('destinataire_libelle'))
      ->unique()->implode(', ');
  $cases[] = ['AUTRE (PRÉCISER)'.($autres ? ' : '.$autres : ''), $autres !== ''];
  $lignesImputation = array_chunk($cases, 3);

  $idsInstructions = $derniere ? $derniere->instructions->pluck('id')->all() : [];
  $lignesInstructions = $instructions->chunk(3);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 28px 34px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; }
  table { width: 100%; border-collapse: collapse; }
  .entete td { text-align: center; vertical-align: top; font-size: 8.5px; font-weight: bold; line-height: 1.5; }
  .cadre td { border: 1.5px solid #222; padding: 5px 8px; }
  .bandeau th { background: #6b6b6b; color: #fff; text-align: center; padding: 5px; font-size: 9px; letter-spacing: .06em; border: 1.5px solid #222; }
  .etiquette { width: 28%; font-weight: bold; font-size: 8.5px; background: #f2f2f2; }
  .grille td { border: 1px solid #222; padding: 7px 8px; font-weight: bold; font-size: 8.5px; vertical-align: top; }
  .case { font-size: 12px; }
  .zone { border: 1.5px solid #222; border-top: 0; padding: 8px 10px; min-height: 40px; font-size: 9.5px; }
  .espace { height: 12px; }
  .pied { margin-top: 14px; font-size: 7.5px; color: #666; text-align: right; }
</style>
</head>
<body>

<table class="entete">
  <tr>
    <td style="width:55%;">{{ $ministere }}<br>- - - - - - - - - -<br>{{ $direction }}<br>- - - - - - - - - -</td>
    <td style="width:45%;">RÉPUBLIQUE DE CÔTE D'IVOIRE<br><span style="font-style:italic;font-weight:normal;">Union - Discipline - Travail</span><br>- - - - - - - - - -</td>
  </tr>
</table>

<div class="espace"></div>
<table class="cadre">
  <tr>
    <td style="width:62%;text-align:center;font-weight:bold;font-size:10px;" rowspan="2">FICHE DE TRANSMISSION DU COURRIER</td>
    <td style="font-weight:bold;">DATE : {{ $courrier->date_reception->format('d/m/Y') }}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;">N° : {{ $courrier->numero }}</td>
  </tr>
  <tr>
    <td colspan="2" style="font-weight:bold;">DU {{ optional($courrier->date_courrier)->format('d/m/Y') ?? '—' }}</td>
  </tr>
</table>

<div class="espace"></div>
<table class="cadre">
  <tr class="bandeau"><th colspan="2">INFORMATIONS COURRIER</th></tr>
  <tr><td class="etiquette">EXPÉDITEUR</td><td>{{ mb_strtoupper($courrier->expediteur) }}</td></tr>
  <tr><td class="etiquette">RÉFÉRENCE</td><td>{{ $courrier->reference_courrier ?: '—' }}</td></tr>
  <tr><td class="etiquette">OBJET</td><td style="height:34px;">{{ mb_strtoupper($courrier->objet) }}</td></tr>
  <tr><td class="etiquette">NATURE</td><td>{{ mb_strtoupper($courrier->type?->libelle ?? '—') }}</td></tr>
  <tr>
    <td class="etiquette">MODE DE TRANSMISSION</td>
    <td>
      <span class="case">{{ $case($courrier->mode_transmission === 'porteur_coursier') }}</span> Porteur/Coursier (remise en main propre)<br>
      <span class="case">{{ $case($courrier->mode_transmission === 'courrier_electronique') }}</span> Courrier électronique<br>
      <span class="case">{{ $case($courrier->mode_transmission === 'autre') }}</span> Autre (à préciser){{ $courrier->mode_transmission === 'autre' && $courrier->mode_transmission_precision ? ' : '.$courrier->mode_transmission_precision : '' }}
    </td>
  </tr>
  <tr><td class="etiquette">PIÈCES JOINTES</td><td>{{ str_pad((string) $courrier->nombre_pieces, 2, '0', STR_PAD_LEFT) }}</td></tr>
</table>

<div class="espace"></div>
<table class="cadre"><tr class="bandeau"><th>IMPUTATION DU COURRIER</th></tr></table>
<table class="grille">
  @foreach ($lignesImputation as $ligne)
    <tr>
      @foreach ($ligne as [$libelle, $coche]) <td style="width:33%;"><span class="case">{{ $case($coche) }}</span> {{ mb_strtoupper($libelle) }}</td> @endforeach
      @for ($i = count($ligne); $i < 3; $i++) <td style="width:33%;">&nbsp;</td> @endfor
    </tr>
  @endforeach
</table>

<div class="espace"></div>
<table class="cadre"><tr class="bandeau"><th>INSTRUCTIONS DE MONSIEUR LE DIRECTEUR GÉNÉRAL</th></tr></table>
<table class="grille">
  @foreach ($lignesInstructions as $ligne)
    <tr>
      @foreach ($ligne as $ins) <td style="width:33%;font-weight:normal;"><span class="case">{{ $case(in_array($ins->id, $idsInstructions, true)) }}</span> {{ $ins->libelle }}</td> @endforeach
      @for ($i = $ligne->count(); $i < 3; $i++) <td style="width:33%;">&nbsp;</td> @endfor
    </tr>
  @endforeach
  <tr>
    <td colspan="3" style="font-weight:bold;">
      Délais de traitement :
      @foreach ($delais as $dl)
        &nbsp;&nbsp;<span class="case">{{ $case($derniere && (int) $derniere->delai_id === $dl->id) }}</span> {{ mb_strtoupper($dl->libelle) }}@if ($derniere && (int) $derniere->delai_id === $dl->id && $dl->saisie_libre) ({{ $derniere->echeance->format('d/m/Y') }})@endif
      @endforeach
      @if ($derniere) &nbsp;&nbsp;— échéance : {{ $derniere->echeance->format('d/m/Y') }} @endif
    </td>
  </tr>
</table>

<div class="espace"></div>
<table class="cadre"><tr class="bandeau"><th>INSTRUCTIONS PARTICULIÈRES</th></tr></table>
<div class="zone">
  {{ $derniere?->instructions_particulieres }}
  @if ($derniere)<div style="margin-top:8px;text-align:right;font-size:8.5px;">{{ $derniere->auteur?->name }} — {{ $derniere->date_imputation->format('d/m/Y') }}</div>@endif
</div>

<div class="espace"></div>
<table class="cadre"><tr class="bandeau"><th>OBSERVATIONS</th></tr></table>
<div class="zone" style="min-height:50px;">{{ $courrier->observations }} @if ($derniere?->observations) {{ $derniere->observations }} @endif</div>

<p class="pied">Fiche éditée le {{ now()->format('d/m/Y à H:i') }} depuis e-Tâche</p>
</body>
</html>
