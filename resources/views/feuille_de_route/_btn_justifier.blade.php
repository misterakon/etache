{{-- Bouton "Justifier / Demander un report". Variables : $t (tache), $justif (JustificationRetard::contexte), $classe, $libelle et $style (optionnels) --}}
@if (in_array($t->id, $justif['demandables']) && \App\Support\JustificationRetard::tacheOuverte($t) && ! $justif['enAttente']->has($t->id))
  <button class="{{ $classe }}" type="button" onclick="justifier_tache(this)" data-bs-toggle="tooltip" data-bs-placement="top" data-tooltip="Justifier / Demander un report" style="{{ $style ?? '' }}"
          data-url="{{ route('feuille_de_route.tache.justification.store', $t->id) }}"
          data-titre="{{ $t->code }} — {{ $t->libelle }}"
          data-echeance="{{ optional($t->date_fin)->format('Y-m-d') }}"><i class="fa-solid fa-calendar-plus"></i>{{ $libelle ?? '' }}</button>
@endif
