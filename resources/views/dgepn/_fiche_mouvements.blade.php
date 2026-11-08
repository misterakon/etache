{{-- Mouvements de poste de l'agent. Variables : $personnel, $estAdmin --}}
@php $mouvements = \App\Support\AffectationPoste::mouvements($personnel); @endphp
<section class="fa3-carte fa3-mouvements">
  <header class="fa3-carte__tete">
    <span class="fa3-carte__puce"><i class="fa-solid fa-right-left"></i></span>
    <h3 class="fa3-carte__titre">Mouvements de poste</h3>
    @if ($estAdmin ?? false)
      <a class="fa3-bouton" href="#!" onclick="aff_ouvrir({{ $personnel->id }}, @js($personnel->nomComplet()), {{ $personnel->fonction_id ?? 'null' }}, {{ $personnel->service_id ?? 'null' }}); return false;"><i class="fa-solid fa-user-tie"></i> Gérer les postes</a>
    @endif
  </header>
  <div class="fa3-carte__corps fa3-mouvements__corps">
    <div class="fa3-mouvements__col">
      <h4 class="fa3-sous-titre">Postes à titulaire unique</h4>
      <div class="overflow-x-auto">
        <table class="fa3-table">
          <thead><tr><th>Poste</th><th>Type</th><th>Période</th><th>État</th><th>Motif de fin</th></tr></thead>
          <tbody>
            @forelse ($mouvements['mandats'] as $m)
              @php
                $enCours = ! $m->date_fin || $m->date_fin->gte(today());
                $neJamaisPrisEffet = $m->date_fin && $m->date_fin->lt($m->date_debut);
              @endphp
              <tr>
                <td><b>{{ $m->libellePoste() }}</b>@if ($m->reference_acte)<small class="fa3-meta"> · acte {{ $m->reference_acte }}</small>@endif</td>
                <td>{{ $m->type === \App\Models\MandatResponsable::INTERIM ? 'Intérim' : 'Titulaire' }}</td>
                <td>@if ($neJamaisPrisEffet) {{ $m->date_debut->format('d/m/Y') }} <small class="fa3-meta">(terminé avant prise d'effet)</small> @else {{ $m->date_debut->format('d/m/Y') }} → {{ $m->date_fin ? $m->date_fin->format('d/m/Y') : '…' }} @endif</td>
                <td>@if ($enCours) <span class="tl-badge tl-terminee">En poste</span> @else <span class="tl-badge tl-neutre">Terminé</span> @endif</td>
                <td>{{ $m->motif_fin ?: '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="fa3-vide">Aucun poste à titulaire unique occupé jusqu'ici.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="fa3-mouvements__col">
      <h4 class="fa3-sous-titre">Historique des affectations</h4>
      <ul class="fa3-historique">
        @forelse ($mouvements['historique'] as $h)
          <li>
            <span class="tl-badge {{ $h->evenement === 'fin_affectation' ? 'tl-retard' : 'tl-encours' }}">{{ $h->evenement === 'fin_affectation' ? 'Fin' : 'Affectation' }}</span>
            <span>{{ $h->resume }} <small class="fa3-meta">· {{ $h->created_at->format('d/m/Y H:i') }}@if ($h->acteur_nom) · {{ $h->acteur_nom }}@endif</small></span>
          </li>
        @empty
          <li class="fa3-vide">Aucune affectation enregistrée.</li>
        @endforelse
      </ul>
    </div>
  </div>
</section>
