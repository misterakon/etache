@php $s = $d['synthese']; @endphp
<div class="ck-grille ck-grille--2-1">
  <section class="ck-carte ck-synthese" id="ck_synthese" data-ia="{{ $d['iaReglee'] ? 1 : 0 }}">
    <header class="ck-carte__tete">
      <h2><i class="fa-solid fa-feather-pointed"></i> Synthèse rédigée <span id="ck_syn_chip">@if ($s)<span class="ck-puce" style="--c: {{ $s->couleurSource() }}">{{ $s->libelleSource() }}</span>@endif</span></h2>
      <span class="ck-actions-tete">
        <button type="button" class="ck-btn ck-btn--primaire" id="ck_syn_generer"><i class="fa-solid fa-wand-magic-sparkles"></i> <span>{{ $s ? 'Régénérer' : 'Générer la synthèse' }}</span></button>
        <button type="button" class="ck-btn" id="ck_syn_copier" @if (! $s) hidden @endif><i class="fa-regular fa-copy"></i> Copier</button>
        @if ($peutEditer)<button type="button" class="ck-btn" id="ck_syn_corriger" data-url="{{ $s ? route('pilotage.synthese.editer', $s->id) : '' }}" @if (! $s) hidden @endif><i class="fa-regular fa-pen-to-square"></i> Corriger</button>@endif
      </span>
    </header>
    <div class="ck-synthese__texte" id="ck_syn_texte" @if (! $s) hidden @endif>@if ($s){!! \App\Support\RedacteurSynthese::versHtml($s->contenu) !!}@endif</div>
    <div class="ck-vide" id="ck_syn_vide" @if ($s) hidden @endif>
      <i class="fa-regular fa-file-lines"></i><strong>Aucune synthèse pour ce périmètre et cette période</strong>
      <span>Générez une note claire à partir des chiffres : situation, points d'attention et priorités.</span>
    </div>
    @if ($peutEditer)
      <form id="ck_syn_edition" class="ck-edition" hidden>
        <textarea id="ck_syn_contenu" rows="16" aria-label="Texte de la synthèse">{{ $s?->contenu }}</textarea>
        <div class="ck-edition__actions"><button type="submit" class="ck-btn ck-btn--primaire"><i class="fa-solid fa-check"></i> Enregistrer</button><button type="button" class="ck-btn" id="ck_syn_annuler">Annuler</button><small>Les titres commencent par « ## », les puces par « - ». L'original reste conservé dans l'historique.</small></div>
      </form>
    @endif
    <p class="ck-alerte" id="ck_syn_alerte" hidden></p>
    <footer class="ck-synthese__pied" id="ck_syn_pied" @if (! $s) hidden @endif>
      <span id="ck_syn_pied_g">@if ($s)Générée le {{ $s->created_at->format('d/m/Y à H:i') }}{{ $s->auteur ? ' par '.$s->auteur->name : ' automatiquement' }}@endif</span>
      <span><i class="fa-solid fa-shield-halved"></i> Seuls les totaux sont utilisés : aucun nom, courrier ni document n'est transmis.</span>
    </footer>
  </section>

  <section class="ck-carte">
    <header class="ck-carte__tete"><h2><i class="fa-solid fa-clock-rotate-left"></i> Dernières synthèses</h2></header>
    @forelse ($d['historique'] as $h)
      <a class="ck-histo" href="{{ route('pilotage.index', ['exercice_id' => $h->exercice_id, 'type' => $h->periode_type, 'valeur' => $h->periode_valeur, 'portee' => $h->portee_type === 'global' ? 'global' : $h->portee_type.':'.$h->portee_id, 'synthese_id' => $h->id]) }}">
        <span class="ck-puce" style="--c: {{ $h->couleurSource() }}">{{ $h->libelleSource() }}</span>
        <span>{{ $h->created_at->format('d/m/Y H:i') }}<small>{{ $h->auteur?->name ?? 'Automatique' }}</small></span>
      </a>
    @empty
      <div class="ck-vide"><i class="fa-regular fa-folder-open"></i><strong>Aucun historique</strong></div>
    @endforelse
    @if ($peutEditer)<p class="ck-info"><i class="fa-solid fa-pen"></i> Pour corriger un texte à la main, ouvrez-le depuis l'historique.</p>@endif
  </section>
</div>
