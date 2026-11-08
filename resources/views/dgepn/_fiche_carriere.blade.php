{{-- Carrière de l'agent. Variables héritées de la fiche : $personnel, $estAdmin, $emploiTexte, $indice --}}
@php
  $fmt = fn ($d) => $d ? $d->format('d/m/Y') : null;
  $duree = function ($debut, $fin) {
      $d = $debut->diff($fin);
      $morceaux = array_filter([$d->y ? $d->y.' an'.($d->y > 1 ? 's' : '') : null, $d->m ? $d->m.' mois' : null]);

      return $morceaux ? implode(' et ', $morceaux) : ($d->d.' jour'.($d->d > 1 ? 's' : ''));
  };

  $finAnciennete = $personnel->date_deces ?: today();
  $anciennete = $personnel->date_premiere_prise_service && $personnel->date_premiere_prise_service->lte($finAnciennete)
      ? $duree($personnel->date_premiere_prise_service, $finAnciennete) : null;
  $retraite = $personnel->date_depart_retraite;
  $retraiteTexte = $retraite ? $fmt($retraite).($retraite->isFuture() ? ' · dans '.$duree(today(), $retraite) : ' · il y a '.$duree($retraite, today())) : null;

  $tuiles2 = [
      ['Grade', $personnel->grade?->code],
      ['Classe', $personnel->classeAgent?->libelle],
      ['Échelon', $personnel->echelon?->numero],
      ['Indice', $indice],
  ];
  $lignesCarriere = [
      ['Matricule', $personnel->matricule ?: null, 'fa-solid fa-hashtag', null],
      ["Type d'agent", $personnel->typeAgent?->libelle ?: null, 'fa-solid fa-id-badge', null],
      ['1ère prise de service', $fmt($personnel->date_premiere_prise_service), 'fa-solid fa-flag-checkered', null],
      ['Prise de service', $fmt($personnel->date_prise_service), 'fa-solid fa-calendar-check', null],
      ['Ancienneté', $anciennete, 'fa-solid fa-hourglass-half', null],
      ['Retraite', $retraiteTexte, 'fa-solid fa-umbrella-beach', null],
  ];
@endphp

<section class="fa3-carte fa3-carriere">
  <header class="fa3-carte__tete">
    <span class="fa3-carte__puce"><i class="fa-solid fa-briefcase"></i></span>
    <h3 class="fa3-carte__titre">Carrière</h3>
    @if ($estAdmin ?? false)
      <a class="fa3-crayon" href="#!" title="Modifier la carrière" onclick="car_ouvrir(); return false;"><i class="fa-solid fa-pen"></i></a>
    @endif
  </header>
  <div class="fa3-carte__corps">
    <div class="fa3-emploi">
      <i class="fa-solid fa-user-tie"></i>
      <div>
        <span class="fa3-emploi__lib">Emploi</span>
        <span class="fa3-emploi__val {{ $emploiTexte ? '' : 'fa3-emploi__val--vide' }}">{{ $emploiTexte ?: 'Non renseigné' }}</span>
      </div>
    </div>

    <div class="fa3-tuiles">
      @foreach ($tuiles2 as [$libT, $valT])
        <div class="fa3-tuile {{ $valT === null || $valT === '' ? 'fa3-tuile--vide' : '' }}">
          <span class="fa3-tuile__lib">{{ $libT }}</span>
          <span class="fa3-tuile__val">{{ $valT !== null && $valT !== '' ? $valT : '—' }}</span>
        </div>
      @endforeach
    </div>

    @foreach ($lignesCarriere as [$libI, $valI, $icoI, $lienI])
      <div class="fa3-ligne">
        <span class="fa3-ligne__ico"><i class="{{ $icoI }}"></i></span>
        <span class="fa3-ligne__lib">{{ $libI }}</span>
        @if ($valI) <span class="fa3-ligne__val">{{ $valI }}</span> @else <span class="fa3-ligne__val fa3-ligne__val--vide">Non renseigné</span> @endif
      </div>
    @endforeach

    @if ($personnel->date_deces)
      <div class="fa3-deces"><i class="fa-solid fa-ribbon"></i> Décédé(e) le {{ $fmt($personnel->date_deces) }}</div>
    @endif
  </div>
</section>
