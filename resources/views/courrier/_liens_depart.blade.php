{{-- Fiche d'un courrier arrivée : réponses, courriers départ liés et archivage. Variable : $courrier --}}
@include('courrier._select_recherche')
@php
  $user = auth()->user();
  $departs = $courrier->departs()->get();
  $reponses = $courrier->reponses()->with('diligence')->where('statut', '!=', 'annulee')->get();
  $peutDepart = \App\Support\DepartDroits::peutEnregistrer($user) || $user->hasRole('ADMIN|DG');
  $peutClasser = \App\Support\DepartDroits::peutClasser($user) && in_array($courrier->statut, ['solde', 'archive'], true);
@endphp

@if ($departs->isNotEmpty() || $reponses->isNotEmpty() || $peutDepart || $courrier->statut === 'archive' || $peutClasser)
  <div class="cr-carte">
    <div class="cr-carte__tete">
      <h2 class="cr-carte__titre"><i class="fa-solid fa-paper-plane"></i> Réponses, départ et archivage</h2>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        @if ($peutDepart)<a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.depart.create', ['courrier' => $courrier->id]) }}"><i class="fa-solid fa-plus"></i> Courrier départ</a>@endif
      </div>
    </div>
    <div class="cr-carte__corps">
      @foreach ($reponses as $r)
        <div class="cs-afaire__ligne" style="border-top:0;">
          <div><b>{{ \Illuminate\Support\Str::limit($r->objet, 70) }}</b><small>Réponse · diligence {{ $r->diligence->code }}</small></div>
          <span class="cr-chip" style="--c: {{ $r->couleurStatut() }}">{{ $r->libelleStatut() }}</span>
        </div>
      @endforeach
      @foreach ($departs as $dp)
        <div class="cs-afaire__ligne">
          <div><a href="{{ route('courrier.depart.show', $dp->id) }}"><span class="cr-num">{{ $dp->numero }}</span></a> <small>{{ $dp->destinataire }} · {{ $dp->date_depart->format('d/m/Y') }}</small></div>
          <span class="cr-chip" style="--c: {{ $dp->couleurStatut() }}">{{ $dp->libelleStatut() }}</span>
        </div>
      @endforeach
      @if ($courrier->statut === 'archive')
        <div class="cs-sceau" style="margin-top:12px;">
          <i class="fa-solid fa-box-archive"></i>
          <div>
            <strong>Courrier classé@if ($courrier->archive_le) le {{ $courrier->archive_le->format('d/m/Y') }}@endif</strong>
            <small>@if ($courrier->numero_archives) N° d'archives <b>{{ $courrier->numero_archives }}</b>@else <b style="color:#8a5605;">N° d'archives à renseigner</b>@endif @if ($courrier->a_conserver_jusqu_au) · à conserver jusqu'au {{ $courrier->a_conserver_jusqu_au->format('d/m/Y') }}@endif</small>
          </div>
        </div>
      @endif
      @if ($peutClasser)
        <div style="margin-top:14px;">
          <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_classer_ouvrir('{{ route('courrier.archives.courrier', $courrier->id) }}', 'Classer le courrier {{ $courrier->numero }}', @js($courrier->numero_archives), @js(optional($courrier->a_conserver_jusqu_au)->toDateString()))"><i class="fa-solid fa-box-archive"></i> {{ $courrier->statut === 'archive' ? "Modifier l'archivage" : 'Classer ce courrier' }}</button>
        </div>
      @endif
      @if ($departs->isEmpty() && $reponses->isEmpty() && $courrier->statut !== 'archive' && ! $peutClasser)
        <p class="cs-aide">Aucune réponse ni courrier départ pour l'instant.</p>
      @endif
    </div>
  </div>
  @if ($peutClasser) @include('courrier._classer') @endif
@endif
