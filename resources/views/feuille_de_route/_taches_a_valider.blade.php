{{-- Mes tâches : clôtures de tâches en attente de ma validation (réglage « Clôture des tâches »). --}}
@php
  $userCt = auth()->user();
  $aValiderCt = \App\Models\Tache::exerciceOuvert()->where('cloture_validation', 'a_valider')->with('statutExecution')->orderBy('cloture_soumise_le')->get()
      ->filter(fn ($t) => \App\Support\ClotureTache::peutValider($t, $userCt))->values();
@endphp
@if ($aValiderCt->isNotEmpty())
  @include('feuille_de_route._validation_cloture_assets')
  <div class="ct-banniere ct-banniere--attente" style="align-items:flex-start;">
    <div class="ct-icone"><i class="fa-solid fa-clipboard-check"></i></div>
    <div class="ct-texte">
      <strong>Clôtures de tâches à valider ({{ $aValiderCt->count() }})</strong>
      <div class="ct-liste">
        @foreach ($aValiderCt as $t)
          <div class="ct-ligne">
            <div><b>{{ $t->code }} — {{ \Illuminate\Support\Str::limit($t->libelle, 90) }}</b><small>Soumise le {{ $t->cloture_soumise_le?->format('d/m/Y') }}@if ($t->date_fin) · échéance {{ $t->date_fin->format('d/m/Y') }}@endif</small></div>
            <div class="ct-actions">
              <a class="ct-bouton ct-bouton--neutre" href="{{ route('feuille_de_route.tache.details', $t->id) }}#validation" style="color:#fff;text-decoration:none;">Examiner</a>
              <button type="button" class="ct-bouton ct-bouton--ok" onclick="ct_valider('{{ route('feuille_de_route.tache.cloture.valider', $t->id) }}')"><i class="fa-solid fa-check"></i> Valider</button>
              <button type="button" class="ct-bouton ct-bouton--ko" onclick="ct_refuser('{{ route('feuille_de_route.tache.cloture.refuser', $t->id) }}')"><i class="fa-solid fa-xmark"></i> Refuser</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif
