@extends('layout.master')
@section('title', 'Diligence '.$diligence->code)

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  @php
    $c = $diligence->courrier; $imp = $diligence->imputation;
    // Courrier confidentiel : le contenu (expéditeur, objet, référence) n'est visible que des personnes désignées, de l'ADMIN et du DG
    $masque = $c->estConfidentiel() && ! $peutVoirCourrier;
  @endphp
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--violet">
        <div>
          <p class="cr-hero__sur"><a href="{{ route('courrier.mes') }}" style="color:#fff;opacity:.85;text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Mes courriers à traiter</a></p>
          <h1 class="cr-hero__titre" style="margin-bottom:10px;"><span class="cr-num cr-num--grand">{{ $diligence->code }}</span></h1>
          <p class="cr-hero__sous" style="margin-top:0;font-size:17px;opacity:1;font-weight:600;">{{ $c->numero }} — {{ $masque ? 'Courrier confidentiel : contenu réservé' : $c->objet }}</p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
            <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $diligence->libelleStatut() }}</span>
            <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $diligence->libelleRole() }}</span>
            <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">Échéance {{ $diligence->echeance->format('d/m/Y') }}</span>
          </div>
        </div>
        <div class="cr-hero__actions">
          @if ($diligence->pris_en_charge_le)
            <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.diligence.accuse', $diligence->id) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Accusé de prise en charge</a>
          @endif
          @if ($peutReimputer)
            <button type="button" class="cr-btn cr-btn--fantome" onclick="imp_ouvrir()"><i class="fa-solid fa-share"></i> Réimputer</button>
          @endif
          @if ($peutAgir && $diligence->statut === 'a_prendre_en_charge')
            <form method="post" action="{{ route('courrier.diligence.prendre', $diligence->id) }}">@csrf<button class="cr-btn cr-btn--blanc" type="submit"><i class="fa-solid fa-hand-holding"></i> Prendre en charge</button></form>
          @endif
          @if ($peutAgir)
            <button type="button" class="cr-btn cr-btn--fantome" onclick="dl_ouvrir('solder', {{ $diligence->id }})"><i class="fa-solid fa-circle-check"></i> Marquer comme traitée</button>
          @endif
          @if ($peutRenvoyer)
            <button type="button" class="cr-btn cr-btn--fantome" onclick="dl_ouvrir('renvoyer', {{ $diligence->id }})"><i class="fa-solid fa-rotate-left"></i> {{ $diligence->diligenceParente ? 'Renvoyer au supérieur' : ($diligence->imputation?->auteur?->hasRole('DG') ? 'Renvoyer au DG' : "Renvoyer à l'imputeur") }}</button>
          @endif
          @if ($peutAnnuler)
            <button type="button" class="cr-btn cr-btn--fantome" onclick="dl_ouvrir('annuler', {{ $diligence->id }})"><i class="fa-solid fa-ban"></i> Annuler</button>
          @endif
        </div>
      </div>

      <div class="cr-detail" style="grid-template-columns: minmax(0,1fr) minmax(0,1fr);">
        <div class="cr-detail__colonne">
          <div class="cr-carte">
            <div class="cr-carte__tete">
              <h2 class="cr-carte__titre"><i class="fa-solid fa-envelope-open-text"></i> Le courrier</h2>
              @if ($peutVoirCourrier) <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.show', $c->id) }}">Ouvrir le courrier et son scan</a> @endif
            </div>
            <div class="cr-carte__corps">
              <dl class="cr-fiche">
                <div class="cr-fiche__ligne"><dt>Numéro</dt><dd><span class="cr-num">{{ $c->numero }}</span> @if ($c->estConfidentiel()) <span class="cr-chip cr-chip--secret" style="--c:#6B1F1A"><i class="fa-solid fa-lock"></i> Confidentiel</span> @endif</dd></div>
                @if ($masque)
                  <div class="cr-fiche__ligne"><dt>Contenu</dt><dd><i class="fa-solid fa-lock"></i> Réservé aux personnes désignées par l'imputation, au DG et à l'administrateur.</dd></div>
                @else
                  <div class="cr-fiche__ligne"><dt>Expéditeur</dt><dd>{{ $c->expediteur }}</dd></div>
                  <div class="cr-fiche__ligne"><dt>Objet</dt><dd>{{ $c->objet }}</dd></div>
                @endif
                <div class="cr-fiche__ligne"><dt>Type</dt><dd>{{ $c->type?->libelle ?? '—' }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Reçu le</dt><dd>{{ $c->date_reception->format('d/m/Y') }}</dd></div>
                @unless ($masque)<div class="cr-fiche__ligne"><dt>Référence</dt><dd>{{ $c->reference_courrier ?: '—' }}</dd></div>@endunless
              </dl>
            </div>
          </div>
        </div>

        <div class="cr-detail__colonne">
          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-clipboard-list"></i> Consignes</h2></div>
            <div class="cr-carte__corps">
              <dl class="cr-fiche">
                <div class="cr-fiche__ligne"><dt>Destinataire</dt><dd>{{ $diligence->destinataire_libelle }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Responsable</dt><dd>{{ $diligence->responsable?->nomComplet() ?? '— aucun responsable en poste —' }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Imputé par</dt><dd>{{ $imp->auteur?->name ?? '—' }} le {{ $imp->date_imputation->format('d/m/Y') }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Délai</dt><dd>{{ $imp->delai_libelle ?? '—' }} · échéance {{ $diligence->echeance->format('d/m/Y') }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Instructions</dt><dd>@forelse ($imp->instructions as $ins) <span class="cr-instr-puce">{{ $ins->libelle }}</span> @empty — @endforelse</dd></div>
                @if ($imp->instructions_particulieres) <div class="cr-fiche__ligne"><dt>Particulières</dt><dd>{{ $imp->instructions_particulieres }}</dd></div> @endif
              </dl>
            </div>
          </div>

          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-route"></i> Avancement</h2></div>
            <div class="cr-carte__corps">
              <ul class="cr-parcours">
                <li class="cr-parcours__item" style="--c:#7B6BE0"><span class="cr-parcours__texte">Imputée</span><span class="cr-parcours__meta">{{ $imp->date_imputation->format('d/m/Y') }}</span></li>
                @if ($diligence->pris_en_charge_le) <li class="cr-parcours__item" style="--c:#2B6CB0"><span class="cr-parcours__texte">Prise en charge</span><span class="cr-parcours__meta">{{ $diligence->pris_en_charge_le->format('d/m/Y à H:i') }}</span></li> @endif
                @if ($diligence->solde_le) <li class="cr-parcours__item" style="--c:#2E7D5B"><span class="cr-parcours__texte">Traitée</span><span class="cr-parcours__meta">{{ $diligence->solde_le->format('d/m/Y à H:i') }}@if ($diligence->commentaire_solde) · {{ $diligence->commentaire_solde }}@endif</span></li> @endif
                @if ($diligence->annule_le) <li class="cr-parcours__item" style="--c:#A73B34"><span class="cr-parcours__texte">Annulée</span><span class="cr-parcours__meta">{{ $diligence->annule_le->format('d/m/Y à H:i') }} · {{ $diligence->motif_annulation }}</span></li> @endif
              </ul>
              @if ($diligence->statut === 'reimputee')
                <p class="c-o-light" style="margin:14px 0 0;font-size:13px;"><i class="fa-solid fa-share"></i> Cette diligence a été réimputée ; son suivi continue dans les diligences liées.</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

      {{-- Traitement, délais et réponses (étape 3) --}}
      @php
        $nbActionsAttente = $diligence->actions()->where('statut_validation', 'a_valider')->count();
        $nbDelaisAttente = $diligence->delais()->where('statut', 'en_attente')->count();
        $nbReponsesActives = $diligence->reponses()->whereIn('statut', ['brouillon', 'en_visa', 'a_corriger'])->count();
      @endphp
      <nav class="cs-nav" style="margin-top:22px;" aria-label="Sections de la diligence">
        <a href="#traitement"><i class="fa-solid fa-list-check"></i> Traitement @if ($nbActionsAttente) <span class="cs-compte" title="Actions à valider">{{ $nbActionsAttente }}</span> @endif</a>
        <a href="#delais"><i class="fa-solid fa-hourglass-half"></i> Délais @if ($nbDelaisAttente) <span class="cs-compte" title="Demandes en attente">{{ $nbDelaisAttente }}</span> @endif</a>
        <a href="#reponses"><i class="fa-solid fa-file-signature"></i> Réponses @if ($nbReponsesActives) <span class="cs-compte cs-compte--danger" title="Réponses en cours">{{ $nbReponsesActives }}</span> @endif</a>
      </nav>
      @include('courrier._traitement', ['diligence' => $diligence])
      @include('courrier._delais', ['diligence' => $diligence])
      @include('courrier._reponses', ['diligence' => $diligence])
      @include('courrier.js_traitement')

      @if ($diligence->diligenceParente || $diligence->diligencesEnfants->isNotEmpty())
        <div class="cr-carte" style="margin-top:18px;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-code-branch"></i> Chaîne de réimputation</h2></div>
          <div class="cr-carte__corps">
            @if ($diligence->diligenceParente)
              <p>Diligence parente : <a href="{{ route('courrier.diligence.show', $diligence->diligenceParente->id) }}">{{ $diligence->diligenceParente->code }} · {{ $diligence->diligenceParente->destinataire_libelle }}</a></p>
            @endif
            @if ($diligence->diligencesEnfants->isNotEmpty())
              <p>Transmise à :</p>
              <ul>@foreach ($diligence->diligencesEnfants as $enfant)<li><a href="{{ route('courrier.diligence.show', $enfant->id) }}">{{ $enfant->code }} · {{ $enfant->destinataire_libelle }}</a> — {{ $enfant->libelleStatut() }}</li>@endforeach</ul>
            @endif
          </div>
        </div>
      @endif

      @if ($diligence->avisAssocies->isNotEmpty())
        <div class="cr-carte" style="margin-top:18px;">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-comments"></i> Avis et aide associés</h2></div>
          <div class="cr-carte__corps">
            @foreach ($diligence->avisAssocies as $destinataireAvis)
              <div style="padding:12px 0;border-bottom:1px solid #e5e7eb;">
                <strong>{{ $destinataireAvis->destinataire_libelle }}</strong>
                @forelse ($destinataireAvis->avis as $avis)
                  <p style="margin:8px 0 4px;">{{ $avis->commentaire }}</p>
                  <small class="c-o-light">{{ $avis->auteur?->name ?? 'Utilisateur' }} · {{ $avis->created_at->format('d/m/Y à H:i') }}</small>
                @empty
                  <p class="c-o-light" style="margin:8px 0;">Aucun avis transmis pour l'instant.</p>
                @endforelse
                @if ($destinatairesAvisPourMoi->contains('id', $destinataireAvis->id) && $diligence->estOuverte())
                  <form method="post" action="{{ route('courrier.diligence.avis', $diligence->id) }}" style="margin-top:10px;">
                    @csrf
                    <input type="hidden" name="_form" value="avis">
                    <input type="hidden" name="destinataire_id" value="{{ $destinataireAvis->id }}">
                    <label class="cr-etiquette" for="avis_{{ $destinataireAvis->id }}">Déposer un avis ou une aide</label>
                    <textarea class="form-control form-control-sm" id="avis_{{ $destinataireAvis->id }}" name="commentaire" rows="3" maxlength="2000" required>{{ old('destinataire_id') == $destinataireAvis->id ? old('commentaire') : '' }}</textarea>
                    @if (old('destinataire_id') == $destinataireAvis->id) @error('commentaire') <p class="cr-erreur">{{ $message }}</p> @enderror @endif
                    <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit" style="margin-top:8px;">Transmettre l'avis</button>
                  </form>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

    {{-- Fenêtre « Solder » / « Annuler » --}}
    <div class="modal fade panel-modal" id="modal_action_diligence" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ old('_form') === 'diligence_action' && $errors->has('commentaire') ? 1 : 0 }}">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header"><h4 class="modal-title" id="dl_titre">Marquer comme traitée</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
          <form id="form_action_diligence" method="post" action="#">
            @csrf
            <input type="hidden" name="_form" value="diligence_action">
            <div class="modal-body dark-modal">
              <div class="cr-champ">
                <label class="cr-etiquette" for="dl_commentaire" id="dl_etiquette">Commentaire</label>
                <textarea class="form-control form-control-sm" id="dl_commentaire" name="commentaire" rows="4" maxlength="2000">{{ old('commentaire') }}</textarea>
                @error('commentaire') <p class="cr-erreur">{{ $message }}</p> @enderror
              </div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Fermer</button><button class="btn btn-primary text-white" type="submit" id="dl_btn">Valider</button></div>
          </form>
        </div>
      </div>
    </div>

    @if ($peutReimputer)
      @include('courrier.form_imputation', ['courrier' => $c, 'diligenceParente' => $diligence, 'groupes' => $groupes, 'instructions' => $instructions, 'delais' => $delais])
      @include('courrier.js_imputation')
    @endif

    <script type="text/javascript">
      /* Solder / annuler une diligence (formulaire classique ; JS natif pour les liaisons) */
      const DL_URL = { solder: "{{ route('courrier.diligence.solder', ':id') }}", annuler: "{{ route('courrier.diligence.annuler', ':id') }}", renvoyer: "{{ route('courrier.diligence.renvoyer', ':id') }}" };

      function dl_ouvrir(action, id) {
        const modal = document.getElementById('modal_action_diligence');
        if (modal.parentElement !== document.body) { document.body.appendChild(modal); }
        const form = document.getElementById('form_action_diligence');
        form.action = DL_URL[action].replace(':id', id);
        const T = { solder: ['Marquer comme traitée', 'Commentaire (facultatif)', 'Marquer comme traitée'], annuler: ['Annuler la diligence', "Motif de l'annulation (obligatoire)", 'Annuler la diligence'], renvoyer: ['Renvoyer le dossier', 'Motif du renvoi (obligatoire)', 'Renvoyer'] }[action];
        document.getElementById('dl_titre').textContent = T[0];
        document.getElementById('dl_etiquette').textContent = T[1];
        document.getElementById('dl_commentaire').required = action !== 'solder';
        document.getElementById('dl_btn').textContent = T[2];
        $('#modal_action_diligence').modal('show');
      }

      window.addEventListener('load', function () {
        const modal = document.getElementById('modal_action_diligence');
        if (modal && modal.dataset.ouvrir === '1') { dl_ouvrir('annuler', {{ $diligence->id }}); }
      });
    </script>
  </div>
@endsection
