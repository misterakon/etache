@props(['tag' => null, 'titre', 'sous' => null, 'kpis' => []])

{{-- En-tête de page en dégradé. Style : public/assets/css/entete-page.css (même design que la page de détails d'une tâche).
     $kpis : liste de [libellé, valeur, sous-texte, icône Font Awesome, ton (retard|ok|ambre|violet|''), barre % ou null].
     Slots : « badges » (pastilles à gauche) et « actions » (boutons à droite). --}}
<section {{ $attributes->class(['ep-hero']) }}>
  @if ($tag || isset($badges) || isset($actions))
    <div class="ep-hero__ligne">
      <div class="ep-hero__badges">
        @if ($tag)<span class="ep-hero__code">{{ $tag }}</span>@endif
        @isset($badges){{ $badges }}@endisset
      </div>
      @isset($actions)<div class="ep-hero__actions">{{ $actions }}</div>@endisset
    </div>
  @endif

  <h1 class="ep-hero__titre">{{ $titre }}</h1>
  @if ($sous)<p class="ep-hero__sous">{{ $sous }}</p>@endif

  @if (count($kpis))
    <div class="ep-hero__kpis">
      @foreach ($kpis as [$libK, $valK, $sousK, $iconeK, $tonK, $barreK])
        <div class="ep-kpi {{ $tonK ? 'ep-kpi--'.$tonK : '' }}">
          <div class="ep-kpi__lib"><i class="{{ $iconeK }}"></i>{{ $libK }}</div>
          <strong class="ep-kpi__val">{{ $valK }}</strong>
          @if ($sousK)<span class="ep-kpi__sous">{{ $sousK }}</span>@endif
          @if ($barreK !== null)
            <div class="ep-kpi__barre" role="progressbar" aria-valuenow="{{ $barreK }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $barreK }}%"></i></div>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</section>
