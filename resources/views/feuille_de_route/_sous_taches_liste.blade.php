{{-- Liste des sous-tâches (rechargée par AJAX après chaque action). Variables : $tache, $droits. Style : tache-details.css (td-st-*) --}}
@php
  $sousTaches = $tache->sousTaches()->with('cocheePar', 'piecesJointes', 'agent')->get();
  $peutDeposerFichier = \App\Support\PieceJointeDroits::deposer($tache, auth()->user())['ok'];
  $valides = $sousTaches->where('statut_validation', 'validee');
  $total = $valides->count();
  $cochees = $valides->where('cochee', true)->count();
  $enAttente = $sousTaches->where('statut_validation', 'a_valider')->count();
  $peutValider = \App\Support\SousTacheDroits::peutValider($tache, auth()->user());
  $pct = \App\Support\AvancementTache::pourcentage($total, $cochees);
  $calcule = (bool) $tache->exerciceTache()?->avancementParSousTaches();
  $tonBarre = $pct >= 100 ? 'terminee' : ($pct > 0 ? 'encours' : 'neutre');
@endphp

@unless ($calcule)
  <div class="td-info"><i class="fa-solid fa-circle-info"></i> Mode manuel : les sous-tâches ne modifient pas l'avancement de la tâche.</div>
@endunless
@if ($droits['raison'])
  <div class="td-info td-info--verrou"><i class="fa-solid fa-lock"></i> {{ $droits['raison'] }}</div>
@endif

<div class="td-st-resume tl-{{ $tonBarre }}">
  <div class="td-st-resume__chiffre">
    <strong>{{ $cochees }} sur {{ $total }} faite(s)</strong>
    <span>
      @if ($enAttente) {{ $enAttente }} en attente de validation @endif
      @if ($calcule) @if ($enAttente) · @endif Statut : {{ $tache->statutExecution?->libelle ?? '—' }} @endif
    </span>
  </div>
  <div class="td-st-resume__barre" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $pct }}%"></i></div>
  @if ($calcule)<div class="td-st-resume__pct">{{ $pct }} %</div>@endif
</div>

<ul class="td-st">
  @forelse ($sousTaches as $s)
    @php $peutCocher = \App\Support\SousTacheDroits::peutCocher($s, auth()->user()); @endphp
    <li class="td-st__ligne {{ $s->cochee ? 'td-st__ligne--fait' : '' }} {{ $s->statut_validation === 'refusee' ? 'td-st__ligne--refus' : '' }}">
      <input type="checkbox" class="td-st__case" @checked($s->cochee) @disabled(! $peutCocher) aria-label="Marquer la sous-tâche comme faite"
             onchange="st_cocher({{ $s->id }}, this.checked, this)">

      <div class="td-st__corps">
        <div class="td-st__libelle">{{ $s->libelle }}</div>

        <div class="td-st__chips">
          @if ($s->echeance)<span class="td-puce-m"><i class="fa-regular fa-calendar"></i> Échéance {{ $s->echeance->format('d/m/Y') }}</span>@endif
          @if ($s->cochee && $s->cochee_le)
            <span class="td-puce-m td-puce-m--vert"><i class="fa-solid fa-check"></i> Faite le {{ $s->cochee_le->format('d/m/Y H:i') }}@if ($s->cocheePar) par {{ $s->cocheePar->name }}@endif</span>
          @endif
          @if ($s->statut_validation === 'a_valider')
            <span class="td-puce-m td-puce-m--ambre"><i class="fa-solid fa-hourglass-half"></i> En attente de validation du supérieur</span>
          @elseif ($s->statut_validation === 'refusee')
            <span class="td-puce-m td-puce-m--rouge"><i class="fa-solid fa-ban"></i> Refusée : {{ $s->motif_refus }}</span>
          @endif
        </div>

        @if ($s->description) <p class="td-st__texte">{{ $s->description }}</p> @endif
        @if ($s->modalites) <p class="td-st__texte"><strong>Modalités :</strong> {{ $s->modalites }}</p> @endif
        @if ($s->conditions) <p class="td-st__texte"><strong>Conditions :</strong> {{ $s->conditions }}</p> @endif

        @if ($s->piecesJointes->isNotEmpty())
          <div class="td-st__fichiers">
            @foreach ($s->piecesJointes as $f)
              <a class="td-fichier" href="{{ route('feuille_de_route.piece_jointe.telecharger', $f->id) }}"><i class="fa-solid fa-paperclip"></i> {{ $f->nom_original }}</a>
            @endforeach
          </div>
        @endif
      </div>

      @if ($droits['gerer'] || $peutDeposerFichier || ($peutValider && $s->statut_validation === 'a_valider'))
        <div class="td-st__actions">
          @if ($peutValider && $s->statut_validation === 'a_valider')
            <button class="td-ibtn td-ibtn--vert" type="button" title="Valider" onclick="st_valider({{ $s->id }})"><i class="fa-solid fa-check"></i></button>
            <button class="td-ibtn td-ibtn--ambre" type="button" title="Refuser" onclick="st_refuser({{ $s->id }})"><i class="fa-solid fa-xmark"></i></button>
          @endif
          @if ($peutDeposerFichier)
            <button class="td-ibtn td-ibtn--gris" type="button" title="Joindre un fichier" onclick="lv_add_form('sous_tache', {{ $s->id }})"><i class="fa-solid fa-paperclip"></i></button>
          @endif
          @if ($droits['gerer'])
            <button class="td-ibtn td-ibtn--bleu" type="button" title="Modifier" data-id="{{ $s->id }}" data-libelle="{{ $s->libelle }}" data-description="{{ $s->description }}" data-echeance="{{ $s->echeance?->format('Y-m-d') }}" data-modalites="{{ $s->modalites }}" data-conditions="{{ $s->conditions }}" onclick="st_edit(this)"><i class="fa-regular fa-pen-to-square"></i></button>
            <button class="td-ibtn td-ibtn--rouge" type="button" title="Supprimer" onclick="st_delete({{ $s->id }}, {{ $s->cochee ? 'true' : 'false' }})"><i class="fa-solid fa-trash-can"></i></button>
          @endif
        </div>
      @endif
    </li>
  @empty
    <li class="td-vide-grand">
      <span><i class="fa-solid fa-list-check"></i></span>
      <strong>Aucune sous-tâche pour le moment</strong>
      <p>Découpez la tâche en étapes : cochez-les au fur et à mesure, l'avancement suit.</p>
    </li>
  @endforelse
</ul>
