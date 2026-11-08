{{-- Plan de traitement d'une diligence : actions à réaliser, validées par le supérieur hiérarchique. Variable : $diligence --}}
@php
  $user = auth()->user();
  $actions = $diligence->actions()->with(['createur'])->get();
  $av = $diligence->avancementActions();
  $peutGerer = \App\Support\DiligenceDroits::gererActions($diligence, $user);
  $peutValider = \App\Support\DiligenceDroits::peutValiderAction($diligence, $user);
  $enAttente = $actions->where('statut_validation', 'a_valider')->count();
  $validateur = \App\Support\DiligenceDroits::validateur($diligence);
  $rouvrirAction = old('_form') === 'action' && $errors->any();
  $cleErreurs = ['libelle', 'personnel_id', 'echeance', 'modalites', 'conditions', 'motif'];
@endphp

<section id="traitement" class="cr-carte cs-bloc">
  <div class="cr-carte__tete">
    <h2 class="cr-carte__titre"><i class="fa-solid fa-list-check"></i> Plan de traitement</h2>
    @if ($peutGerer)
      <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="cs_action_nouvelle()"><i class="fa-solid fa-plus"></i> Ajouter une action</button>
    @endif
  </div>

  <div class="cs-progression">
    <div class="cs-anneau" style="--p: {{ $av['pourcentage'] }};--c: {{ $av['pourcentage'] >= 100 ? '#2E7D5B' : '#2B6CB0' }}"><span>{{ $av['pourcentage'] }}%</span></div>
    <div class="cs-progression__texte">
      <strong>{{ $av['faites'] }} action(s) faite(s) sur {{ $av['total'] }}</strong>
      <small>
        Seules les actions validées comptent dans l'avancement.
        @if ($enAttente) {{ $enAttente }} en attente de validation par {{ $validateur?->nomComplet() ?? 'le supérieur hiérarchique' }}. @endif
      </small>
    </div>
  </div>

  @if (! $rouvrirAction && $errors->hasAny($cleErreurs))
    <div class="cr-alerte-conf" style="margin:16px 22px 0;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
  @endif

  @if ($actions->isEmpty())
    <div class="cs-vide">
      <i class="fa-regular fa-clipboard"></i>
      <strong>Aucune action définie</strong>
      Décomposez le travail en actions à réaliser, avec leurs modalités. Chaque action est validée par {{ $validateur?->nomComplet() ?? 'le supérieur hiérarchique' }}.
    </div>
  @else
    <ul class="cs-actions">
      @foreach ($actions as $a)
        @php
          $peutCocher = \App\Support\DiligenceDroits::peutCocherAction($a, $user);
          $enRetard = $a->echeance && ! $a->faite && $a->echeance->lt(today());
          $donnees = [
              'id' => $a->id, 'libelle' => $a->libelle, 'description' => $a->description,
              'echeance' => $a->echeance?->toDateString(), 'modalites' => $a->modalites, 'conditions' => $a->conditions,
          ];
        @endphp
        <li class="cs-action {{ $a->faite ? 'cs-action--faite' : '' }} {{ $a->statut_validation === 'a_valider' ? 'cs-action--attente' : '' }} {{ $a->statut_validation === 'refusee' ? 'cs-action--refusee' : '' }}">
          <form method="post" action="{{ route('courrier.diligence.action.cocher', $a->id) }}">
            @csrf
            <input type="hidden" name="faite" value="{{ $a->faite ? 0 : 1 }}">
            <button type="submit" class="cs-case {{ $a->faite ? 'cs-case--cochee' : '' }}" @disabled(! $peutCocher) title="{{ $peutCocher ? ($a->faite ? 'Remettre à faire' : 'Marquer comme faite') : ($a->estValidee() ? 'Réservé au responsable' : 'En attente de validation') }}"><i class="fa-solid fa-check"></i></button>
          </form>

          <div>
            <span class="cs-action__titre">{{ $a->libelle }}</span>
            @if ($a->description) <span class="cs-action__texte">{{ $a->description }}</span> @endif
            <div class="cs-action__ligne">
              @if ($a->echeance) <span class="cs-meta {{ $enRetard ? 'cs-meta--retard' : '' }}"><i class="fa-regular fa-calendar"></i> {{ $a->echeance->format('d/m/Y') }}@if ($enRetard) · en retard @endif</span> @endif
              @if ($a->faite && $a->faite_le) <span class="cs-meta"><i class="fa-solid fa-check"></i> Faite le {{ $a->faite_le->format('d/m/Y') }}</span> @endif
              <span class="cr-chip" style="--c: {{ $a->couleurValidation() }}">{{ $a->libelleValidation() }}</span>
            </div>
            @if ($a->modalites) <span class="cs-condition"><b>Modalités d'exécution</b>{{ $a->modalites }}</span> @endif
            @if ($a->conditions) <span class="cs-condition"><b>Conditions d'exécution</b>{{ $a->conditions }}</span> @endif
            @if ($a->statut_validation === 'refusee' && $a->motif_refus) <p class="cs-motif"><strong>Refusée :</strong> {{ $a->motif_refus }}</p> @endif
          </div>

          <div class="cs-outils">
            @if ($peutValider && $a->statut_validation === 'a_valider')
              <form method="post" action="{{ route('courrier.diligence.action.valider', $a->id) }}">@csrf<button type="submit" class="cs-outil cs-outil--ok" title="Valider"><i class="fa-solid fa-check"></i></button></form>
              <button type="button" class="cs-outil cs-outil--danger" title="Refuser" onclick="cs_motif_ouvrir('{{ route('courrier.diligence.action.rejeter', $a->id) }}', 'Refuser l\'action', 'Motif du refus', 'motif')"><i class="fa-solid fa-xmark"></i></button>
            @endif
            @if ($peutGerer)
              <button type="button" class="cs-outil cs-outil--ok" title="Modifier" data-action="{{ json_encode($donnees) }}" onclick="cs_action_modifier(this)"><i class="fa-regular fa-pen-to-square"></i></button>
              <form method="post" action="{{ route('courrier.diligence.action.delete', $a->id) }}" onsubmit="return cs_confirmer(this, 'Supprimer cette action ?')">@csrf @method('DELETE')<button type="submit" class="cs-outil cs-outil--danger" title="Supprimer"><i class="fa-regular fa-trash-can"></i></button></form>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  @endif

  <div class="cs-carte-pied">
    <span class="cs-aide"><i class="fa-solid fa-user-check"></i> Validateur des actions : <strong>{{ $validateur?->nomComplet() ?? '—' }}</strong> (supérieur hiérarchique direct du responsable).</span>
    @if ($diligence->estOuverte() && $av['total'] > 0 && $av['faites'] < $av['total'])
      <span class="cs-aide"><i class="fa-solid fa-circle-info"></i> Il reste {{ $av['total'] - $av['faites'] }} action(s) avant de marquer la diligence comme traitée.</span>
    @endif
  </div>
</section>

@if ($peutGerer)
  <div class="modal fade panel-modal cs-modal" id="modal_cs_action" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-ouvrir="{{ $rouvrirAction ? 1 : 0 }}">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="cs_action_titre"><i class="fa-solid fa-list-check"></i> Action du plan de traitement</h4>
          <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="form_cs_action" method="post" action="{{ route('courrier.diligence.action.store', $diligence->id) }}">
          @csrf
          <input type="hidden" name="_form" value="action">
          <input type="hidden" name="id_" value="{{ old('id_') }}">
          <div class="modal-body dark-modal">
            @if ($rouvrirAction)
              <div class="cr-alerte-conf" style="margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
            @endif
            <div class="cs-champ"><label for="cs_a_libelle">Action à mener <span class="cs-requis">*</span></label><input class="form-control form-control-sm" id="cs_a_libelle" name="libelle" type="text" maxlength="255" value="{{ old('libelle') }}" required></div>
            <div class="cs-champ"><label for="cs_a_description">Description <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="cs_a_description" name="description" rows="2" maxlength="2000">{{ old('description') }}</textarea></div>
            <input type="hidden" id="cs_a_agent" name="personnel_id" value="">
              <div class="cs-champ"><label for="cs_a_echeance">Échéance <span class="cs-opt">(max {{ $diligence->echeance->format('d/m/Y') }})</span></label><input class="form-control form-control-sm" id="cs_a_echeance" name="echeance" type="date" max="{{ $diligence->echeance->toDateString() }}" value="{{ old('echeance') }}"></div>
            <div class="cs-champ"><label for="cs_a_modalites">Modalités d'exécution <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="cs_a_modalites" name="modalites" rows="2" maxlength="2000">{{ old('modalites') }}</textarea></div>
            <div class="cs-champ"><label for="cs_a_conditions">Conditions d'exécution <span class="cs-opt">(facultatif)</span></label><textarea class="form-control form-control-sm" id="cs_a_conditions" name="conditions" rows="2" maxlength="2000">{{ old('conditions') }}</textarea></div>
            <div class="cs-info"><i class="fa-solid fa-circle-info"></i><div>L'action est soumise à la validation de <strong>{{ $validateur?->nomComplet() ?? 'votre supérieur hiérarchique' }}</strong>. Elle ne compte dans l'avancement qu'une fois validée.</div></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary text-white" type="button" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-primary text-white" type="submit" id="cs_action_btn">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
