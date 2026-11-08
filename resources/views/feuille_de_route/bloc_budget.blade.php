                  {{-- Budget : répartition par statut (anneau) --}}
                  @php
                    // Mêmes listes que les onglets : 2 = En cours, 3 = En attente, 4 = Terminées ; $retards = tâches en retard
                    $budgetDe = fn ($liste) => (float) $liste->sum('budget_estime');
                    $bTermine = $budgetDe($onglets[4][2]);
                    $bEncours = $budgetDe($onglets[2][2]);
                    $bAttente = $budgetDe($onglets[3][2]);
                    // Reste du budget : tâches annulées ou sans statut
                    $bAutres = max(0, (float) $budgetTotal - ($bTermine + $bEncours + $bAttente));
                    $bAutres = $bAutres > 0.5 ? $bAutres : 0;
                    $bRetard = $budgetDe($retards);
                    $pctRealise = $budgetTotal > 0 ? (int) round($bTermine / $budgetTotal * 100) : 0;
                    $pctRetard = $budgetTotal > 0 ? (int) round($bRetard / $budgetTotal * 100) : 0;

                    // Montant abrégé pour la légende : 425 k, 1,25 M, 2,3 Md (le montant exact est dans l'infobulle)
                    $court = function ($v) {
                        $f = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ',');
                        $v = (float) $v;

                        return match (true) {
                            $v >= 999500000 => $f($v / 1e9).' Md',
                            $v >= 999500 => $f($v / 1e6).' M',
                            $v >= 10000 => $f($v / 1e3).' k',
                            default => number_format($v, 0, ',', ' '),
                        };
                    };

                    // [libellé, couleur, montant, aide affichée au survol]
                    $segments = [
                        ['Terminées', 'terminee', $bTermine, ''],
                        ['En cours', 'encours', $bEncours, ''],
                        ['En attente', 'attente', $bAttente, 'Non démarrées, bloquées ou suspendues'],
                    ];
                    if ($bAutres > 0) { $segments[] = ['Autres', 'neutre', $bAutres, 'Tâches annulées ou sans statut']; }
                    $totalSegments = array_sum(array_column($segments, 2));
                    $arcs = [];
                    $debutArc = 0.0;
                    foreach ($segments as [$libelleS, $tonS, $montantS, $aideS]) {
                        $partS = $totalSegments > 0 ? $montantS / $totalSegments * 100 : 0;
                        if ($partS > 0) { $arcs[] = [$tonS, $partS, $debutArc]; }
                        $debutArc += $partS;
                    }
                    $ecartArc = count($arcs) > 1 ? 0.8 : 0; // petit espace entre les arcs
                  @endphp
                  <div class="tl-ensemble__budget">
                    <div class="main-card-box tl-stat tl-budget h-full">
                      <div class="p-5 main-cost-performance">
                        <div class="common-space">
                          <h5>Budget des tâches</h5>
                          <div class="tl-bubble"><i class="fa-solid fa-coins"></i></div>
                        </div>
                        <div class="common-space p-t-10 tl-budget-ligne">
                          <div class="tl-budget-gauche">
                            <div class="tl-budget-repartition">
                              <div class="tl-anneau" role="img" aria-label="Budget par statut : {{ $pctRealise }} % réalisé">
                                <svg viewBox="0 0 42 42">
                                  <g transform="rotate(-90 21 21)">
                                    <circle class="tl-anneau__fond" cx="21" cy="21" r="15.9155" fill="none" stroke-width="5"/>
                                    @foreach ($arcs as [$tonA, $partA, $debutA])
                                      @php $longueurA = max($partA - $ecartArc, 0.1); @endphp
                                      <circle class="tl-anneau__seg tl-anneau__seg--{{ $tonA }}" cx="21" cy="21" r="15.9155" fill="none" stroke-width="5" pathLength="100" stroke-dasharray="{{ round($longueurA, 2) }} {{ round(100 - $longueurA, 2) }}" stroke-dashoffset="{{ round(-$debutA, 2) }}"/>
                                    @endforeach
                                  </g>
                                </svg>
                                <div class="tl-anneau__centre"><strong>{{ $pctRealise }}%</strong><small>réalisé</small></div>
                              </div>
                              <ul class="tl-budget-legende">
                                @foreach ($segments as [$libelleL, $tonL, $montantL, $aideL])
                                  <li title="{{ $aideL ? $aideL.' : ' : '' }}{{ $fcfa($montantL) }}"><span><span class="tl-dot tl-{{ $tonL }}"></span>{{ $libelleL }}</span><b>{{ $court($montantL) }}</b></li>
                                @endforeach
                              </ul>
                            </div>
                            @if ($budgetTotal > 0)
                              <p class="tl-budget-retard {{ $bRetard > 0 ? 'tl-budget-retard--oui' : 'tl-budget-retard--non' }}">
                                @if ($bRetard > 0)
                                  <i class="fa-solid fa-triangle-exclamation"></i> Dont en retard : <b>{{ $court($bRetard) }}</b> ({{ $pctRetard }} %)
                                @else
                                  <i class="fa-solid fa-circle-check"></i> Aucun budget en retard
                                @endif
                              </p>
                            @endif
                          </div>
                          <div class="cost-right-data">
                            <ul>
                              <li>
                                <h6>{{ $fcfa($budgetTotal) }}</h6><span>Budget total</span>
                              </li>
                              <li>
                                <h6>{{ $fcfa($budgetTermine) }}</h6><span>Tâches terminées</span>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
