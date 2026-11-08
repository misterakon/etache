{{-- Réponses au courrier : projet, circuit de visa montant la hiérarchie, signature du DG, courrier départ. Variable : $diligence --}}
@php
  $user = auth()->user();
  $reponses = $diligence->reponses()->with(['visas.personnel', 'fichiers', 'auteur', 'depart'])->get();
  $peutRediger = \App\Support\DiligenceDroits::gererActions($diligence, $user);
  $peutCreerDepart = \App\Support\DepartDroits::peutEnregistrer($user) || $user->hasRole('ADMIN|DG');
  $rouvrirReponse = old('_form') === 'reponse' && $errors->any();
  $cleErreurs = ['visa', 'objet', 'commentaire', 'projet', 'signature'];
  $objetParDefaut = 'Réponse à votre courrier '.($diligence->courrier->reference_courrier ? 'réf. '.$diligence->courrier->reference_courrier : 'du '.$diligence->courrier->date_reception->format('d/m/Y')).' — '.\Illuminate\Support\Str::limit($diligence->courrier->objet, 120);
  $couleursEtape = ['en_attente' => '#2B6CB0', 'vise' => '#2E7D5B', 'refuse' => '#A73B34', 'annule' => '#8A94A6'];
@endphp

<section id="reponses" class="cr-carte cs-bloc">
  <div class="cr-carte__tete">
    <h2 class="cr-carte__titre"><i class="fa-solid fa-file-signature"></i> Réponses au courrier</h2>
    @if ($peutRediger)
      <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_reponse_nouvelle(@js($objetParDefaut))"><i class="fa-solid fa-plus"></i> Nouvelle réponse</button>
    @endif
  </div>

  @if (! $rouvrirReponse && $errors->hasAny($cleErreurs) && old('_form') !== 'action')
    <div class="cr-alerte-conf" style="margin:16px 22px 0;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
  @endif

  @forelse ($reponses as $r)
    @php
      $projet = $r->projet();
      $signee = $r->signee();
      $visas = $r->visasDuCycle();
      $courant = $r->visaCourant();
      $enAnnulation = $r->statut === 'en_visa' && $r->annulation_demandee_le !== null;
      $demandeur = $enAnnulation && $r->annulation_demandee_par ? \App\Models\User::find($r->annulation_demandee_par) : null;
      $decideur = $enAnnulation && $r->annulation_decideur_id ? \App\Models\Personnel::find($r->annulation_decideur_id) : null;
      $peutDecider = $enAnnulation && ($user->hasRole('ADMIN|DG') || ($user->personnel_id && (int) $user->personnel_id === (int) $r->annulation_decideur_id));
      $aDejaVise = $visas->where('statut', 'vise')->isNotEmpty();
      $monTour = ! $enAnnulation && $courant && ($user->hasRole('ADMIN') || (int) $courant->personnel_id === (int) $user->personnel_id);
      $anciens = $r->visas->where('cycle', '<', $r->cycle)->where('statut', 'refuse');
      $donneesReponse = ['id' => $r->id, 'objet' => $r->objet, 'commentaire' => $r->commentaire];
    @endphp
    <div class="cs-reponse">
      <div class="cs-reponse__tete">
        <div>
          <h3 class="cs-reponse__objet">{{ $r->objet }}</h3>
          <p class="cs-reponse__meta">Rédigée par {{ $r->auteur?->name ?? '—' }} · {{ $r->created_at->format('d/m/Y') }}@if ($r->soumis_le) · soumise le {{ $r->soumis_le->format('d/m/Y') }}@endif @if ($r->cycle > 1) · circuit n°{{ $r->cycle }}@endif</p>
          @if ($r->commentaire) <p class="cs-reponse__meta" style="color:var(--cr-texte);">{{ $r->commentaire }}</p> @endif
        </div>
        <span class="cr-chip" style="--c: {{ $r->couleurStatut() }}">{{ $r->libelleStatut() }}</span>
      </div>

      <div class="cs-fichiers">
        @if ($projet)
          <a class="cs-fichier" href="{{ route('courrier.reponse.fichier', [$r->id, 'projet']) }}" target="_blank" rel="noopener"><i class="{{ $projet->icone() }}"></i><span><small>Projet de réponse · v{{ $projet->version }}</small><strong>{{ $projet->nom_original }}</strong></span></a>
          <a class="cs-fichier" href="{{ route('courrier.reponse.fichier', [$r->id, 'projet']) }}?telecharger=1" title="Télécharger"><i class="fa-solid fa-download"></i><span><small>{{ $projet->tailleLisible() }}</small><strong>Télécharger</strong></span></a>
        @else
          <span class="cs-aide"><i class="fa-regular fa-file"></i> Aucun projet joint pour l'instant.</span>
        @endif
        @if ($signee)
          <a class="cs-fichier cs-fichier--signe" href="{{ route('courrier.reponse.fichier', [$r->id, 'signature']) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-circle-check"></i><span><small>Réponse signée</small><strong>{{ $signee->nom_original }}</strong></span></a>
        @endif
      </div>

      @if ($visas->isNotEmpty())
        <ol class="cs-circuit">
          @foreach ($visas as $v)
            @php $etat = ($courant && $courant->id === $v->id) ? 'cours' : $v->statut; @endphp
            <li class="cs-etape cs-etape--{{ $etat === 'en_attente' ? 'attente' : $etat }}" style="--c: {{ $etat === 'cours' ? '#2B6CB0' : ($couleursEtape[$v->statut] ?? '#5B6B84') }}">
              <span class="cs-etape__rond">@if ($v->statut === 'vise')<i class="fa-solid fa-check"></i>@elseif ($v->statut === 'refuse')<i class="fa-solid fa-xmark"></i>@elseif ($v->signataire)<i class="fa-solid fa-signature"></i>@else{{ $v->ordre }}@endif</span>
              <span class="cs-etape__nom">{{ $v->libelle }}</span>
              <span class="cs-etape__qui">{{ $v->personnel?->nomComplet() }}</span>
              <span class="cs-etape__etat">{{ $etat === 'cours' ? ($enAnnulation ? 'Suspendu' : ($v->signataire ? 'À signer' : 'À viser')) : ($v->statut === 'vise' && $v->signataire ? 'Signé' : $v->libelleStatut()) }}</span>
              @if ($v->decide_le) <span class="cs-etape__qui">{{ $v->decide_le->format('d/m/Y') }}</span> @endif
            </li>
          @endforeach
        </ol>
        @foreach ($visas->where('statut', 'refuse') as $v)
          <p class="cs-motif"><strong>Refusée par {{ $v->libelle }} :</strong> {{ $v->commentaire }}</p>
        @endforeach
      @endif

      @if ($enAnnulation)
        <div class="cs-attente-vous">
          <i class="fa-solid fa-hand"></i>
          <div style="flex:1;"><strong>Annulation demandée : le circuit de visa est suspendu.</strong> Demandée par {{ $demandeur?->name ?? '—' }} le {{ \Illuminate\Support\Carbon::parse($r->annulation_demandee_le)->format('d/m/Y à H:i') }} : « {{ $r->annulation_motif }} ». En attente de l'accord de {{ $decideur?->nomComplet() ?? 'la personne qui a visé en dernier' }}.</div>
          @if ($peutDecider)
            <div class="cs-reponse__actions" style="margin:0;">
              <form method="post" action="{{ route('courrier.reponse.annulation.accepter', $r->id) }}" onsubmit="return cs_confirmer(this, 'Accepter l\'annulation de cette réponse ?')">@csrf<button type="submit" class="cr-btn cr-btn--danger cr-btn--petit"><i class="fa-solid fa-check"></i> Accepter l'annulation</button></form>
              <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" onclick="cs_motif_ouvrir('{{ route('courrier.reponse.annulation.refuser', $r->id) }}', 'Refuser l\'annulation', 'Motif du refus (renvoyé au demandeur)', 'motif')"><i class="fa-solid fa-xmark"></i> Refuser</button>
            </div>
          @endif
        </div>
      @endif

      @if ($anciens->isNotEmpty())
        <details class="cs-historique"><summary>Refus précédents ({{ $anciens->count() }})</summary><ul>@foreach ($anciens as $v)<li>Circuit n°{{ $v->cycle }} · {{ $v->libelle }} : {{ $v->commentaire }}</li>@endforeach</ul></details>
      @endif

      @if ($r->statut === 'signee')
        <div class="cs-sceau">
          <i class="fa-solid fa-stamp"></i>
          <div><strong>Réponse signée le {{ $r->signe_le?->format('d/m/Y') }}</strong><small>Signature scannée déposée par {{ $r->signataire?->name ?? '—' }}.</small></div>
          <div style="margin-left:auto;">
            @if ($r->depart)
              <a class="cr-btn cr-btn--primaire cr-btn--petit" href="{{ route('courrier.depart.show', $r->depart->id) }}"><i class="fa-solid fa-paper-plane"></i> Départ {{ $r->depart->numero }}</a>
            @elseif ($peutCreerDepart)
              <a class="cr-btn cr-btn--primaire cr-btn--petit" href="{{ route('courrier.depart.create', ['reponse' => $r->id]) }}"><i class="fa-solid fa-paper-plane"></i> Enregistrer le courrier départ</a>
            @endif
          </div>
        </div>
      @endif

      @if ($monTour)
        <div class="cs-attente-vous">
          <i class="fa-solid fa-bell"></i>
          <div style="flex:1;"><strong>{{ $courant->signataire ? 'Cette réponse attend votre signature.' : 'Cette réponse attend votre visa.' }}</strong> Étape : {{ $courant->libelle }}.</div>
          <div class="cs-reponse__actions" style="margin:0;">
            @if ($courant->signataire)
              <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_signer({{ $courant->id }})"><i class="fa-solid fa-signature"></i> Déposer la réponse signée</button>
            @else
              <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_viser({{ $courant->id }})"><i class="fa-solid fa-check"></i> Viser</button>
            @endif
            <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="cs_motif_ouvrir('{{ route('courrier.reponse.refuser', $courant->id) }}', 'Refuser la réponse', 'Motif du refus (renvoyé au responsable)', 'commentaire')"><i class="fa-solid fa-xmark"></i> Refuser</button>
          </div>
        </div>
      @endif

      @if ($peutRediger && ! $enAnnulation && ($r->estModifiable() || $r->statut === 'en_visa'))
        <div class="cs-reponse__actions">
          @if ($r->estModifiable())
            <button type="button" class="cr-btn cr-btn--clair cr-btn--petit" data-reponse="{{ json_encode($donneesReponse) }}" onclick="cs_reponse_modifier(this)"><i class="fa-regular fa-pen-to-square"></i> {{ $r->statut === 'a_corriger' ? 'Corriger' : 'Modifier' }}</button>
            @if ($projet)
              <form method="post" action="{{ route('courrier.reponse.soumettre', $r->id) }}">@csrf<button type="submit" class="cr-btn cr-btn--primaire cr-btn--petit"><i class="fa-solid fa-paper-plane"></i> {{ $r->statut === 'a_corriger' ? 'Soumettre à nouveau au visa' : 'Soumettre au visa' }}</button></form>
            @endif
          @endif
          @if ($r->statut === 'en_visa')
            <button type="button" class="cr-btn cr-btn--danger cr-btn--petit" onclick="cs_motif_ouvrir('{{ route('courrier.reponse.annuler', $r->id) }}', 'Annuler la réponse', 'Motif de l\'annulation (obligatoire)', 'motif')"><i class="fa-regular fa-circle-xmark"></i> {{ $aDejaVise ? "Demander l'annulation" : 'Annuler la réponse' }}</button>
          @else
            <form method="post" action="{{ route('courrier.reponse.annuler', $r->id) }}" onsubmit="return cs_confirmer(this, 'Annuler cette réponse ?')">@csrf<button type="submit" class="cr-btn cr-btn--danger cr-btn--petit"><i class="fa-regular fa-circle-xmark"></i> Annuler la réponse</button></form>
          @endif
        </div>
      @endif
    </div>
  @empty
    <div class="cs-vide">
      <i class="fa-regular fa-envelope"></i>
      <strong>Aucune réponse pour le moment</strong>
      Rédigez un projet de réponse : il montera la hiérarchie pour visa, puis le Directeur Général le signera.
    </div>
  @endforelse
</section>

{{-- Fenêtre : rédiger / corriger une réponse --}}
@if ($peutRediger)
  <div class="modal fade panel-modal cs-modal" id="modal_cs_reponse" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirReponse ? 1 : 0 }}">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title" id="cs_reponse_titre"><i class="fa-solid fa-file-signature"></i> Réponse au courrier</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <form id="form_cs_reponse" method="post" action="{{ route('courrier.reponse.store', $diligence->id) }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_form" value="reponse">
          <input type="hidden" name="id_" value="{{ old('id_') }}">
          <div class="modal-body dark-modal">
            @if ($rouvrirReponse)
              <div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
            @endif
            <div class="cs-champ"><label for="cs_r_objet">Objet de la réponse <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="cs_r_objet" name="objet" type="text" maxlength="500" value="{{ old('objet') }}" required></div>
            <div class="cs-champ"><label for="cs_r_commentaire">Note pour les signataires <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="cs_r_commentaire" name="commentaire" rows="2" maxlength="2000">{{ old('commentaire') }}</textarea></div>
            <div class="cs-champ">
              <label for="cs_r_projet">Projet de réponse <span class="cs-opt" id="cs_r_projet_aide">(PDF, Word ou ODT)</span></label>
              <input class="form-control form-control-sm" id="cs_r_projet" name="projet" type="file" accept=".pdf,.doc,.docx,.odt">
              <small class="cs-aide">Un nouveau fichier remplace le précédent (les versions sont conservées).</small>
            </div>
            <label class="cr-interrupteur" style="margin-top:6px;">
              <input type="checkbox" name="soumettre" value="1" @checked(old('soumettre'))>
              <span class="cr-interrupteur__piste"></span>
              <span class="cr-interrupteur__texte"><strong>Soumettre aussitôt au circuit de visa</strong><small>Sinon, la réponse reste en brouillon.</small></span>
            </label>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Enregistrer</button></div>
        </form>
      </div>
    </div>
  </div>
@endif

{{-- Fenêtres : viser et signer --}}
<div class="modal fade panel-modal cs-modal" id="modal_cs_viser" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-check"></i> Viser la réponse</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form id="form_cs_viser" method="post" action="#">
        @csrf
        <div class="modal-body dark-modal">
          <div class="cs-champ"><label for="cs_v_commentaire">Observation <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="cs_v_commentaire" name="commentaire" rows="3" maxlength="2000"></textarea></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit">Apposer mon visa</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade panel-modal cs-modal" id="modal_cs_signer" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title"><i class="fa-solid fa-signature"></i> Déposer la réponse signée</h4><button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form id="form_cs_signer" method="post" action="#" enctype="multipart/form-data">
        @csrf
        <div class="modal-body dark-modal">
          <div class="cs-info"><i class="fa-solid fa-circle-info"></i><div>Déposez le scan de la réponse signée. La signature est tracée dans le journal d'activité{{ \App\Support\Reglages::get('courrier.depart_auto') ? ' et le courrier départ est enregistré automatiquement' : '' }}.</div></div>
          <div class="cs-champ"><label for="cs_s_fichier">Réponse signée (scan) <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="cs_s_fichier" name="signature" type="file" accept=".pdf,.jpg,.jpeg,.png" required><small class="cs-aide">PDF, JPG ou PNG.</small></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary text-white" type="submit"><i class="fa-solid fa-signature"></i> Signer</button></div>
      </form>
    </div>
  </div>
</div>
