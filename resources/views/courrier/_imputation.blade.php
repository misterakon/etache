{{-- Imputation et diligences d'un courrier. Variables : $courrier, $imputations, $peutImputer --}}
<div class="cr-carte">
  <div class="cr-carte__tete">
    <h2 class="cr-carte__titre"><i class="fa-solid fa-share-nodes"></i> Imputation et diligences</h2>
    @if ($peutImputer)
      <button type="button" class="cr-btn cr-btn--primaire cr-btn--petit" onclick="imp_ouvrir()"><i class="fa-solid fa-paper-plane"></i> {{ $imputations->isEmpty() ? 'Imputer' : 'Imputer à nouveau' }}</button>
    @endif
  </div>
  <div class="cr-carte__corps">
    @forelse ($imputations as $imputation)
      <div class="cr-imputation">
        <div class="cr-imputation__entete">
          <strong>Imputation du {{ $imputation->date_imputation->format('d/m/Y') }}</strong>
          <span class="c-o-light">par {{ $imputation->auteur?->name ?? '—' }} · délai {{ $imputation->delai_libelle ?? '—' }} · échéance {{ $imputation->echeance->format('d/m/Y') }}</span>
        </div>

        @if ($imputation->instructions->isNotEmpty())
          <div class="cr-puces">@foreach ($imputation->instructions as $ins) <span class="cr-instr-puce">{{ $ins->libelle }}</span> @endforeach</div>
        @endif
        @if ($imputation->instructions_particulieres)
          <p class="cr-citation">{{ $imputation->instructions_particulieres }}</p>
        @endif

        <div class="overflow-x-auto">
          <table class="cr-liste-param">
            <thead><tr><th>Diligence</th><th>Destinataire</th><th>Responsable</th><th>Rôle</th><th>Statut</th><th></th></tr></thead>
            <tbody>
              @foreach ($imputation->diligences as $dl)
                <tr>
                  <td><span class="cr-num">{{ $dl->code }}</span></td>
                  <td>{{ $dl->destinataire_libelle }}</td>
                  <td>@if ($dl->responsable) {{ $dl->responsable->nomComplet() }} @else <span class="cr-sans-resp"><i class="fa-solid fa-triangle-exclamation"></i> Aucun responsable en poste</span> @endif</td>
                  <td>{{ $dl->libelleRole() }}</td>
                  <td><span class="cr-chip" style="--c: {{ $dl->couleurStatut() }}">{{ $dl->libelleStatut() }}</span></td>
                  <td style="text-align:right;">@if (\App\Support\DiligenceDroits::voir($dl, auth()->user())) <a class="cr-btn cr-btn--clair cr-btn--petit" href="{{ route('courrier.diligence.show', $dl->id) }}">Ouvrir</a> @endif</td>
                </tr>
              @endforeach
              @foreach ($imputation->destinatairesAssocies as $associe)
                <tr>
                  <td><span class="c-o-light">—</span></td>
                  <td>{{ $associe->destinataire_libelle }}</td>
                  <td>{{ $associe->diligence?->destinataire_libelle ?? '—' }}</td>
                  <td>{{ $associe->role === 'avis' ? 'Avis / aide' : 'Copie / information' }}</td>
                  <td><span class="c-o-light">{{ $associe->role === 'avis' ? 'Associé à une diligence' : 'Lecture seule' }}</span></td>
                  <td></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @empty
      <div class="cr-vide" style="padding:28px 10px;">
        <i class="fa-solid fa-share-nodes"></i>
        <strong>Ce courrier n'a pas encore été imputé</strong>
        {{ $peutImputer ? "Utilisez « Imputer » pour désigner les destinataires, les instructions et le délai." : "L'imputation est faite par le Directeur Général." }}
      </div>
    @endforelse
  </div>
</div>
