{{-- Demandes de délai (report d'échéance) d'une diligence. Variable : $diligence --}}
@php
  $user = auth()->user();
  $delais = $diligence->delais()->with(['demandeur', 'decideur'])->get();
  $peutDemander = \App\Support\DiligenceDroits::peutDemanderDelai($diligence, $user);
  $peutDecider = \App\Support\DiligenceDroits::peutDeciderDelai($diligence, $user);
  $rouvrirDelai = old('_form') === 'delai' && $errors->any();
  $plafond = $diligence->diligenceParente?->echeance;
@endphp

<section id="delais" class="cr-carte cs-bloc">
  <div class="cr-carte__tete">
    <h2 class="cr-carte__titre"><i class="fa-solid fa-hourglass-half"></i> Délais</h2>
    @if ($peutDemander)
      <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="cs_modal('modal_cs_delai')"><i class="fa-solid fa-calendar-plus"></i> Demander un délai</button>
    @endif
  </div>

  @if (! $rouvrirDelai && $errors->hasAny(['decision', 'echeance_accordee', 'motif_decision', 'echeance_souhaitee', 'motif']) && old('_form') !== 'action')
    <div class="cr-alerte-conf" style="margin:16px 22px 0;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
  @endif

  @if ($delais->isEmpty())
    <div class="cs-vide"><i class="fa-regular fa-calendar-check"></i><strong>Aucune demande de délai</strong>Échéance actuelle : {{ $diligence->echeance->format('d/m/Y') }}.</div>
  @else
    <ul class="cs-delais">
      @foreach ($delais as $dl)
        <li class="cs-delai">
          <div class="cs-delai__dates">
            <span class="cs-date"><small>Échéance</small><strong>{{ $dl->echeance_actuelle->format('d/m/Y') }}</strong></span>
            <i class="fa-solid fa-arrow-right-long cs-fleche"></i>
            @if ($dl->statut === 'accorde')
              <span class="cs-date cs-date--nouvelle"><small>Accordée</small><strong>{{ $dl->echeance_accordee->format('d/m/Y') }}</strong></span>
            @else
              <span class="cs-date"><small>Souhaitée</small><strong>{{ $dl->echeance_souhaitee->format('d/m/Y') }}</strong></span>
            @endif
            <span class="cr-chip" style="--c: {{ $dl->couleurStatut() }}">{{ $dl->libelleStatut() }}</span>
          </div>
          <p class="cs-delai__motif">{{ $dl->motif }}</p>
          <small class="c-o-light">Demandé par {{ $dl->demandeur?->name ?? '—' }} le {{ $dl->created_at->format('d/m/Y à H:i') }}</small>
          @if ($dl->statut !== 'en_attente')
            <small class="c-o-light" style="display:block;">{{ $dl->statut === 'accorde' ? 'Accordé' : 'Refusé' }} par {{ $dl->decideur?->name ?? '—' }} le {{ $dl->decide_le?->format('d/m/Y à H:i') }}@if ($dl->motif_decision) · {{ $dl->motif_decision }}@endif</small>
          @elseif ($peutDecider)
            <div class="cs-decision">
              <form method="post" action="{{ route('courrier.diligence.delai.decider', $dl->id) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                @csrf
                <input type="hidden" name="decision" value="accorder">
                <label class="cs-aide" for="cs_acc_{{ $dl->id }}">Nouvelle échéance</label>
                <input class="form-control form-control-sm" style="width:auto;" id="cs_acc_{{ $dl->id }}" type="date" name="echeance_accordee" min="{{ today()->toDateString() }}" @if ($plafond && ! $user->hasRole('ADMIN|DG')) max="{{ $plafond->toDateString() }}" @endif value="{{ $dl->echeance_souhaitee->toDateString() }}">
                <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-check"></i> Accorder</button>
              </form>
              <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="cs_motif_ouvrir('{{ route('courrier.diligence.delai.decider', $dl->id) }}', 'Refuser le délai', 'Motif du refus', 'motif_decision', 'decision', 'refuser')"><i class="fa-solid fa-xmark"></i> Refuser</button>
            </div>
          @else
            <small class="cs-aide" style="display:block;margin-top:8px;"><i class="fa-regular fa-clock"></i> En attente de décision de celui qui a imputé le courrier.</small>
          @endif
        </li>
      @endforeach
    </ul>
  @endif
</section>

@if ($peutDemander)
  <div class="modal fade panel-modal cs-modal" id="modal_cs_delai" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirDelai ? 1 : 0 }}">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Demander un délai supplémentaire</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <form method="post" action="{{ route('courrier.diligence.delai.demander', $diligence->id) }}">
          @csrf
          <input type="hidden" name="_form" value="delai">
          <div class="modal-body dark-modal">
            @if ($rouvrirDelai)
              <div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
            @endif
            <div class="cs-champ">
              <label for="cs_d_date">Nouvelle échéance souhaitée <span class="cs-requis">*</span></label>
              <input class="form-control form-control-sm" id="cs_d_date" type="date" name="echeance_souhaitee" min="{{ $diligence->echeance->copy()->addDay()->toDateString() }}" @if ($plafond) max="{{ $plafond->toDateString() }}" @endif value="{{ old('echeance_souhaitee') }}" required>
              <small class="cs-aide">Échéance actuelle : {{ $diligence->echeance->format('d/m/Y') }}@if ($plafond) · plafond : {{ $plafond->format('d/m/Y') }} (diligence parente)@endif</small>
            </div>
            <div class="cs-champ"><label for="cs_d_motif">Motif <span class="cs-requis">*</span></label><textarea class="form-control form-control-sm" id="cs_d_motif" name="motif" rows="4" maxlength="2000" required>{{ old('motif') }}</textarea></div>
            <div class="cs-info"><i class="fa-solid fa-circle-info"></i><div>La demande est adressée à celui qui a imputé le courrier{{ $plafond ? ' et au responsable de la diligence parente' : '' }}. Vous serez informé de sa décision.</div></div>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Envoyer la demande</button></div>
        </form>
      </div>
    </div>
  </div>
@endif
