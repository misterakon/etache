{{-- Carte « Profil RH » à inclure dans la fiche agent : @include('rh._fiche', ['personnel' => $personnel]) --}}
@php
  $moi = auth()->user();
  $autorise = $moi->hasRole('ADMIN|DG') || ((int) $moi->personnel_id === (int) $personnel->id);
@endphp
@if ($autorise)
  @php
    $chargeRh = \App\Support\ChargeTravail::pour($personnel);
    $completudeRh = \App\Support\CompletudeFiche::analyser($personnel->loadMissing('mandats'));
    $docsRh = \App\Models\DossierInfo::whereIn('piece_jointe_id', \App\Models\PieceJointe::where('attachable_type', $personnel->getMorphClass())->where('attachable_id', $personnel->id)->where('categorie', 'like', 'dossier\_%')->pluck('id'))->get();
    $docsAlerte = $docsRh->filter(fn ($d) => in_array($d->etat(), ['expire', 'bientot'], true))->count();
  @endphp
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
  <div class="cr-carte" style="margin-bottom:20px;">
    <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-gauge-high"></i> Activité et dossier RH</h2><a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('rh.agent', $personnel->id) }}">Profil RH complet</a></div>
    <div class="cr-carte__corps">
      @if ($chargeRh['surcharge'])<div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-triangle-exclamation"></i><div><strong>Sur-affectation :</strong> {{ $chargeRh['charge'] }} éléments ouverts (seuil {{ $chargeRh['seuil'] }}).</div></div>@endif
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
        <span class="cr-chip" style="--c:#2B6CB0">{{ $chargeRh['taches'] }} tâche(s) ouverte(s)</span>
        <span class="cr-chip" style="--c:{{ $chargeRh['retard'] ? '#A73B34' : '#8A94A6' }}">{{ $chargeRh['retard'] }} en retard</span>
        <span class="cr-chip" style="--c:#7B6BE0">{{ $chargeRh['diligences'] }} diligence(s)</span>
        <span class="cr-chip" style="--c:#0F8B8D">{{ $chargeRh['actions'] }} action(s)</span>
      </div>
      <div class="rh-pourcent" style="margin-bottom:12px;"><span class="rh-barre" style="--c: {{ $completudeRh['score'] >= 85 ? '#2E7D5B' : ($completudeRh['score'] >= 60 ? '#C77D0A' : '#A73B34') }}"><span style="width: {{ $completudeRh['score'] }}%"></span></span><strong>{{ $completudeRh['score'] }}%</strong></div>
      <p class="cs-aide" style="margin:0;">Fiche complète à {{ $completudeRh['score'] }} %@if ($completudeRh['essentiels']) · à compléter : {{ implode(', ', $completudeRh['essentiels']) }}@endif · {{ $personnel->competences()->count() }} compétence(s) · {{ $personnel->formations()->count() }} formation(s)@if ($docsAlerte) · <b style="color:#8a2c26;">{{ $docsAlerte }} document(s) à renouveler</b>@endif</p>
    </div>
  </div>
@endif
