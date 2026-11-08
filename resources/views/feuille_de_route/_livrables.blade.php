{{-- Onglet "Livrables" du détail d'une tâche. Variable : $tache. Style : public/assets/css/tache-details.css (td-panel, td-lv-*) --}}
@php
  $droitDepot = \App\Support\PieceJointeDroits::deposer($tache, auth()->user());
  $peutDeposer = $droitDepot['ok'];
  $sousTaches = $tache->sousTaches()->get()->keyBy('id');
  $pieces = $tache->piecesJointes()->with('deposePar')->get();
  $piecesSt = \App\Models\PieceJointe::where('attachable_type', (new \App\Models\SousTache)->getMorphClass())
      ->whereIn('attachable_id', $sousTaches->keys())->where('actuelle', true)->with('deposePar')->orderByDesc('created_at')->get();
@endphp

<div class="td-panel">
  <div class="td-panel__tete">
    <div class="td-card__tete">
      <span class="td-card__ico td-card__ico--ambre"><i class="fa-solid fa-folder-open"></i></span>
      <div><h5>Livrables</h5><p>Preuves de réalisation déposées sur la tâche et ses sous-tâches</p></div>
    </div>
    <div class="td-panel__actions">
      @if ($pieces->isNotEmpty() || $piecesSt->isNotEmpty())
        <a class="td-btn td-btn--vert" href="{{ route('feuille_de_route.piece_jointe.zip', $tache->id) }}"><i class="fa-solid fa-file-zipper"></i> Tout télécharger</a>
      @endif
      @if ($peutDeposer)
        <button class="td-btn td-btn--primary" type="button" onclick="lv_add_form('tache', {{ $tache->id }})"><i class="fa-solid fa-plus"></i> Déposer un fichier</button>
      @endif
    </div>
  </div>

  <div class="td-lv-attendu">
    <span class="td-lv-attendu__ico"><i class="fa-solid fa-box-open"></i></span>
    <div>
      <small>Livrable attendu</small>
      <strong>{{ $tache->livrable ?: 'Non précisé' }}</strong>
    </div>
  </div>

  @unless ($peutDeposer)
    <div class="td-info td-info--verrou"><i class="fa-solid fa-lock"></i> {{ $droitDepot['raison'] }}</div>
  @endunless

  <ul class="td-lv">
    @forelse ($pieces as $p)
      @include('feuille_de_route._livrable_ligne', ['p' => $p, 'tache' => $tache, 'peutDeposer' => $peutDeposer, 'contexte' => null])
    @empty
      <li class="td-vide-grand">
        <span><i class="fa-regular fa-folder-open"></i></span>
        <strong>Aucun fichier déposé sur la tâche</strong>
        <p>Déposez ici le rapport, le compte rendu ou toute preuve de réalisation.</p>
      </li>
    @endforelse
  </ul>

  @if ($piecesSt->isNotEmpty())
    <h6 class="td-lv-sous">Fichiers des sous-tâches <span class="td-puce-m">{{ $piecesSt->count() }}</span></h6>
    <ul class="td-lv">
      @foreach ($piecesSt as $p)
        @include('feuille_de_route._livrable_ligne', ['p' => $p, 'tache' => $tache, 'peutDeposer' => $peutDeposer, 'contexte' => $sousTaches[$p->attachable_id]->libelle ?? null])
      @endforeach
    </ul>
  @endif
</div>

@include('feuille_de_route.form_livrable')
@include('feuille_de_route.js_livrable')
