{{-- Menu "Télécharger" : conserve les filtres de l'écran (query string). Aucun JS : ce sont des liens. --}}
@php
  $exportsTaches = \App\Support\ExportTaches::TYPES;
  $lienExport = fn ($type, $format, array $plus = []) => route('feuille_de_route.export.taches', ['type' => $type, 'format' => $format])
      .'?'.http_build_query(array_merge(array_map(fn ($v) => $v ?? '', request()->except('vue')), $plus));
@endphp

<details class="vt-export">
  <summary class="btn btn-primary text-white"><i class="fa-solid fa-download"></i> Télécharger</summary>
  <div class="vt-export-menu card p-2">
    <p class="vt-export-note">Les filtres actifs sont appliqués.</p>
    @foreach ($exportsTaches as $type => $libelle)
      @if ($type === 'a_venir')
        @foreach (\App\Support\ExportTaches::joursAVenir() as $j)
          <div class="vt-export-ligne">
            <span>{{ $libelle }} ({{ $j }} j)</span>
            <a href="{{ $lienExport($type, 'xlsx', ['jours' => $j]) }}">Excel</a>
            <a href="{{ $lienExport($type, 'pdf', ['jours' => $j]) }}">PDF</a>
          </div>
        @endforeach
      @else
        <div class="vt-export-ligne">
          <span>{{ $libelle }}</span>
          <a href="{{ $lienExport($type, 'xlsx') }}">Excel</a>
          <a href="{{ $lienExport($type, 'pdf') }}">PDF</a>
        </div>
      @endif
    @endforeach
  </div>
</details>

@once
<style>
  .vt-export { position: relative; display: inline-block; }
  .vt-export > summary { list-style: none; cursor: pointer; }
  .vt-export > summary::-webkit-details-marker { display: none; }
  .vt-export-menu { position: absolute; right: 0; z-index: 50; min-width: 340px; margin-top: 4px; box-shadow: 0 6px 20px rgba(0,0,0,.15); }
  .vt-export-note { font-size: 11px; color: #777; margin: 0 0 6px; }
  .vt-export-ligne { display: flex; gap: 10px; align-items: center; padding: 4px 6px; border-bottom: 1px solid #eee; }
  .vt-export-ligne span { flex: 1; }
  .vt-export-ligne a { font-weight: 600; }
</style>
@endonce
