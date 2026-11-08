{{-- Dossier de l'agent. Visible : ADMIN, DG et l'agent. Dépôt et suppression : ADMIN. Variables héritées : $personnel, $estAdmin --}}
@php
  $documents = \App\Models\PieceJointe::where('attachable_type', $personnel->getMorphClass())->where('attachable_id', $personnel->id)
      ->where('categorie', 'like', 'dossier_%')->orderBy('categorie')->orderByDesc('id')->get();
  $typesDoc = \App\Models\ListeValeur::deListe('type_document')->pluck('libelle', 'code');
@endphp

<section class="fa3-carte fa3-dossier">
  <header class="fa3-carte__tete">
    <span class="fa3-carte__puce"><i class="fa-solid fa-folder-open"></i></span>
    <h3 class="fa3-carte__titre">Dossier <small class="fa3-compteur">{{ $documents->count() }}</small></h3>
    @if ($peutAjouterDossier ?? false)
      <a class="fa3-crayon" href="#!" title="Ajouter un document" onclick="dos_ouvrir(); return false;"><i class="fa-solid fa-plus"></i></a>
    @endif
  </header>
  <div class="fa3-carte__corps">
    @forelse ($documents as $doc)
      @php $codeType = \Illuminate\Support\Str::after($doc->categorie, 'dossier_'); @endphp
      <div class="fa3-doc">
        <span class="fa3-doc__ico"><i class="{{ $doc->icone() }}"></i></span>
        <div class="fa3-doc__corps">
          <span class="fa3-doc__type">{{ $typesDoc[$codeType] ?? $codeType }}</span>
          <span class="fa3-doc__nom" title="{{ $doc->nom_original }}">{{ $doc->nom_original }}</span>
          <span class="fa3-doc__meta">{{ $doc->tailleLisible() }} · {{ $doc->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="fa3-doc__actions">
          @if ($doc->estApercevable())
            <a href="{{ route('dgepn.personnel.dossier.apercu', $doc->id) }}" target="_blank" rel="noopener" title="Voir"><i class="fa-regular fa-eye"></i></a>
          @endif
          <a href="{{ route('dgepn.personnel.dossier.telecharger', $doc->id) }}" title="Télécharger"><i class="fa-solid fa-download"></i></a>
          @if ($estAdmin ?? false)
            <a href="#!" class="fa3-doc__suppr" title="Retirer du dossier" onclick="dos_supprimer({{ $doc->id }}, @js($doc->nom_original)); return false;"><i class="fa-solid fa-trash-can"></i></a>
          @endif
        </div>
      </div>
    @empty
      <p class="fa3-vide">Aucun document dans le dossier.</p>
    @endforelse
    <p class="fa3-confidentiel"><i class="fa-solid fa-lock"></i> Dossier confidentiel : chaque consultation est enregistrée.</p>
  </div>
</section>
