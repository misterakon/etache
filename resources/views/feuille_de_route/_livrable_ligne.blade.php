{{-- Une ligne de fichier. Variables : $p (PieceJointe), $tache, $peutDeposer, $contexte (libellé de sous-tâche ou null) --}}
@php $peutSupprimer = \App\Support\PieceJointeDroits::supprimer($p, $tache, auth()->user()); @endphp
<li class="td-lv__ligne">
  <span class="td-lv__icone"><i class="{{ $p->icone() }}"></i></span>

  <div class="td-lv__corps">
    <div class="td-lv__nom">
      @if ($p->estApercevable())
        <a href="{{ route('feuille_de_route.piece_jointe.apercu', $p->id) }}" target="_blank" rel="noopener">{{ $p->nom_original }}</a>
      @else
        <a href="{{ route('feuille_de_route.piece_jointe.telecharger', $p->id) }}">{{ $p->nom_original }}</a>
      @endif
      @if ($p->version > 1)<span class="td-lv__version">v{{ $p->version }}</span>@endif
    </div>
    <div class="td-st__chips">
      <span class="td-puce-m"><i class="fa-regular fa-hard-drive"></i> {{ $p->tailleLisible() }}</span>
      <span class="td-puce-m"><i class="fa-regular fa-calendar"></i> {{ $p->created_at->format('d/m/Y H:i') }}</span>
      @if ($p->deposePar)<span class="td-puce-m"><i class="fa-regular fa-user"></i> {{ $p->deposePar->name }}</span>@endif
      @if ($contexte)<span class="td-puce-m td-puce-m--violet"><i class="fa-solid fa-list-check"></i> {{ $contexte }}</span>@endif
    </div>
    @if ($p->version > 1)
      <details class="td-lv__anciennes">
        <summary>Versions précédentes</summary>
        @foreach ($p->versionsAnterieures() as $v)
          <div>
            <a href="{{ route('feuille_de_route.piece_jointe.telecharger', $v->id) }}">v{{ $v->version }} — {{ $v->nom_original }}</a>
            ({{ $v->tailleLisible() }}, {{ $v->created_at->format('d/m/Y') }}@if ($v->deposePar), {{ $v->deposePar->name }}@endif)
          </div>
        @endforeach
      </details>
    @endif
  </div>

  <div class="td-st__actions">
    <a class="td-ibtn td-ibtn--vert" href="{{ route('feuille_de_route.piece_jointe.telecharger', $p->id) }}" title="Télécharger"><i class="fa-solid fa-download"></i></a>
    @if ($peutDeposer)
      <button class="td-ibtn td-ibtn--bleu" type="button" title="Remplacer (l'ancienne version est conservée)"
              data-nom="{{ $p->nom_original }}" onclick="lv_replace_form({{ $p->id }}, '{{ $p->typeCourt() }}', {{ $p->attachable_id }}, this.dataset.nom)"><i class="fa-solid fa-arrows-rotate"></i></button>
    @endif
    @if ($peutSupprimer)
      <button class="td-ibtn td-ibtn--rouge" type="button" title="Supprimer" onclick="lv_delete({{ $p->id }})"><i class="fa-solid fa-trash-can"></i></button>
    @endif
  </div>
</li>
