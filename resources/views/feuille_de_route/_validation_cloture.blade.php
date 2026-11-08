{{-- Validation de la clôture d'une tâche : bandeau sur la fiche. Variable : $tache --}}
@php
  $etatCloture = $tache->cloture_validation;
  $user = auth()->user();
@endphp
@if (in_array($etatCloture, ['a_valider', 'refusee'], true))
  @php
    $valideurs = \App\Support\SousTacheDroits::validateurs($tache);
    $peutValider = \App\Support\ClotureTache::peutValider($tache, $user);
    $estAffecte = $user?->personnel_id && $tache->affectations()->where('personnel_id', $user->personnel_id)->exists();
    $decideur = $tache->cloture_validee_par ? \App\Models\User::find($tache->cloture_validee_par) : null;
    $soumisPar = $tache->cloture_soumise_par ? \App\Models\User::find($tache->cloture_soumise_par) : null;
  @endphp
  <div class="container" id="validation">
    @if ($errors->has('cloture'))
      <div class="alert alert-danger">{{ $errors->first('cloture') }}</div>
    @endif
    <div class="ct-banniere {{ $etatCloture === 'a_valider' ? 'ct-banniere--attente' : 'ct-banniere--refus' }}">
      <div class="ct-icone"><i class="fa-solid {{ $etatCloture === 'a_valider' ? 'fa-hourglass-half' : 'fa-rotate-left' }}"></i></div>
      <div class="ct-texte">
        @if ($etatCloture === 'a_valider')
          <strong>Clôture en attente de validation</strong>
          Soumise le {{ $tache->cloture_soumise_le?->format('d/m/Y à H:i') }}@if ($soumisPar) par {{ $soumisPar->name }}@endif. Valideur : {{ $valideurs->map(fn ($p) => $p->nomComplet())->implode(', ') ?: 'le DG' }}.
          La tâche reste « En cours » ({{ (int) $tache->avancement_pourcentage }} %) et n'est pas comptée comme terminée tant que la clôture n'est pas validée.
        @else
          <strong>Clôture refusée</strong>
          Refusée le {{ $tache->cloture_validee_le?->format('d/m/Y') }}@if ($decideur) par {{ $decideur->name }}@endif : « {{ $tache->cloture_motif_refus }} ». Corrigez, puis soumettez de nouveau.
        @endif
      </div>
      <div class="ct-actions">
        @if ($etatCloture === 'a_valider' && $peutValider)
          <button type="button" class="ct-bouton ct-bouton--ok" onclick="ct_valider('{{ route('feuille_de_route.tache.cloture.valider', $tache->id) }}')"><i class="fa-solid fa-check"></i> Valider</button>
          <button type="button" class="ct-bouton ct-bouton--ko" onclick="ct_refuser('{{ route('feuille_de_route.tache.cloture.refuser', $tache->id) }}')"><i class="fa-solid fa-xmark"></i> Refuser</button>
        @elseif ($etatCloture === 'refusee' && ($estAffecte || $user?->hasRole('ADMIN|DG')))
          <button type="button" class="ct-bouton ct-bouton--neutre" onclick="ct_soumettre('{{ route('feuille_de_route.tache.cloture.soumettre', $tache->id) }}')"><i class="fa-solid fa-paper-plane"></i> Soumettre de nouveau</button>
        @endif
      </div>
    </div>
  </div>
  @include('feuille_de_route._validation_cloture_assets')
@endif
