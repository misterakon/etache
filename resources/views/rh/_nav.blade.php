{{-- Navigation du pilotage du personnel --}}
@php
  $liens = [
      ['rh.tableau_de_bord', 'fa-chart-pie', 'Tableau de bord', 'ADMIN|DG'],
      ['rh.qualite', 'fa-clipboard-check', 'Qualité des fiches', 'ADMIN|DG'],
      ['rh.doublons', 'fa-clone', 'Doublons', 'ADMIN|DG'],
      ['rh.comptes', 'fa-user-lock', 'Comptes', 'ADMIN|DG'],
      ['rh.import', 'fa-file-excel', 'Import / export', 'ADMIN|DG'],
      ['rh.organigramme', 'fa-sitemap', 'Organigramme', 'ADMIN|DG|RD'],
      ['rh.charge', 'fa-gauge-high', 'Charge de travail', 'ADMIN|DG'],
      ['rh.dossiers', 'fa-folder-open', 'Dossiers', 'ADMIN|DG'],
      ['rh.archives', 'fa-box-archive', 'Archives', 'ADMIN|DG'],
  ];
@endphp
<nav class="rh-nav" aria-label="Pilotage du personnel">
  @foreach ($liens as [$route, $icone, $libelle, $roles])
    @if (auth()->user()->hasRole($roles))
      <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'rh-nav--actif' : '' }}"><i class="fa-solid {{ $icone }}"></i> {{ $libelle }}</a>
    @endif
  @endforeach
</nav>
