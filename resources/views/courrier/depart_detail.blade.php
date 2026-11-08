@extends('layout.master')
@section('title', 'Courrier départ '.$depart->numero)

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
@endsection

@section('main-content')
  @php $couleursParcours = ['depart' => '#2B6CB0', 'archivage' => '#7B6BE0', 'modification' => '#C77D0A']; @endphp
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--vert">
        <div>
          <p class="cr-hero__sur"><a href="{{ route('courrier.depart') }}" style="color:#fff;opacity:.85;text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Registre des courriers départ</a></p>
          <h1 class="cr-hero__titre" style="margin-bottom:10px;"><span class="cr-num cr-num--grand">{{ $depart->numero }}</span></h1>
          <p class="cr-hero__sous" style="margin-top:0;font-size:17px;opacity:1;font-weight:600;">{{ $depart->objet }}</p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
            <span class="cr-chip" style="--c:#fff;background:rgba(255,255,255,.18)">{{ $depart->libelleStatut() }}</span>
            @if ($depart->estConfidentiel()) <span class="cr-chip cr-chip--secret" style="--c:#fff;background:#A73B34"><i class="fa-solid fa-lock"></i> Confidentiel</span> @endif
          </div>
        </div>
        <div class="cr-hero__actions">
          @if ($peutModifier)<a class="cr-btn cr-btn--blanc" href="{{ route('courrier.depart.edit', $depart->id) }}"><i class="fa-solid fa-pen-to-square"></i> Modifier</a>@endif
          @if ($peutClasser)<button type="button" class="cr-btn cr-btn--blanc" onclick="cs_classer_ouvrir('{{ route('courrier.archives.depart', $depart->id) }}', 'Classer le départ {{ $depart->numero }}', @js($depart->numero_archives), @js(optional($depart->a_conserver_jusqu_au)->toDateString()))"><i class="fa-solid fa-box-archive"></i> {{ $depart->statut === 'archive' ? "Modifier l'archivage" : 'Classer' }}</button>@endif
          <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.depart.fiche', $depart->id) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Fiche</a>
          @if ($scan)<a class="cr-btn cr-btn--fantome" href="{{ route('courrier.depart.scan', $depart->id) }}?telecharger=1"><i class="fa-solid fa-download"></i> Scan</a>@endif
        </div>
      </div>

      @if ($depart->estConfidentiel())
        <div class="cr-alerte-conf"><i class="fa-solid fa-user-shield"></i><div><strong>Courrier confidentiel.</strong> Chaque consultation de ce courrier et de son scan est enregistrée dans le journal d'activité.</div></div>
      @endif

      <div class="grid grid-cols-12 card-gap cr-grille">
        <div class="col-span-4 md:col-span-12">
          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-circle-info"></i> Fiche du départ</h2></div>
            <div class="cr-carte__corps">
              <dl class="cr-fiche">
                <div class="cr-fiche__ligne"><dt>Destinataire</dt><dd>{{ $depart->destinataire }}@if ($depart->destinataire_precision)<br><span class="c-o-light">{{ $depart->destinataire_precision }}</span>@endif</dd></div>
                <div class="cr-fiche__ligne"><dt>Objet</dt><dd>{{ $depart->objet }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Date de départ</dt><dd>{{ $depart->date_depart->format('d/m/Y') }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Mode d'envoi</dt><dd>{{ $depart->libelleModeEnvoi() }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Pièces jointes</dt><dd>{{ $depart->nombre_pieces }}</dd></div>
                <div class="cr-fiche__ligne"><dt>Courrier d'origine</dt><dd>@if ($depart->courrier)<a href="{{ route('courrier.show', $depart->courrier->id) }}"><span class="cr-num">{{ $depart->courrier->numero }}</span></a>@else — @endif</dd></div>
                @if ($depart->reponse)<div class="cr-fiche__ligne"><dt>Réponse</dt><dd><a href="{{ route('courrier.diligence.show', $depart->reponse->diligence_id) }}#reponses">Réponse signée le {{ $depart->reponse->signe_le?->format('d/m/Y') }}</a></dd></div>@endif
                <div class="cr-fiche__ligne"><dt>Enregistré par</dt><dd>{{ $depart->enregistrePar?->name ?? '—' }}</dd></div>
                @if ($depart->observations)<div class="cr-fiche__ligne"><dt>Observations</dt><dd>{{ $depart->observations }}</dd></div>@endif
              </dl>
            </div>
          </div>

          @if ($depart->statut === 'archive')
            <div class="cs-sceau" style="margin-bottom:20px;">
              <i class="fa-solid fa-box-archive"></i>
              <div>
                <strong>Classé le {{ $depart->archive_le?->format('d/m/Y') }}</strong>
                <small>@if ($depart->numero_archives) N° d'archives <b>{{ $depart->numero_archives }}</b>@else <b style="color:#8a5605;">N° d'archives à renseigner</b>@endif @if ($depart->a_conserver_jusqu_au) · à conserver jusqu'au {{ $depart->a_conserver_jusqu_au->format('d/m/Y') }}@endif</small>
              </div>
            </div>
          @endif

          @if ($groupe->isNotEmpty())
            <div class="cr-carte">
              <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-users"></i> Même envoi, autres destinataires</h2></div>
              <div class="cr-carte__corps">
                @foreach ($groupe as $g)
                  <div class="cs-afaire__ligne" style="{{ $loop->first ? 'border-top:0;' : '' }}"><div><a href="{{ route('courrier.depart.show', $g->id) }}"><span class="cr-num">{{ $g->numero }}</span></a> <small>{{ $g->destinataire }}</small></div></div>
                @endforeach
              </div>
            </div>
          @endif
        </div>

        <div class="col-span-8 md:col-span-12">
          <div class="cr-visionneuse">
            <div class="cr-visionneuse__barre">
              <div class="cr-visionneuse__nom"><i class="fa-regular {{ $scan && $scan->extension === 'pdf' ? 'fa-file-pdf' : 'fa-file-image' }}"></i><span>{{ $scan ? $scan->nom_original : 'Scan du courrier expédié' }}</span></div>
              @if ($scan)
                <div class="cr-visionneuse__outils">
                  <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.depart.scan', $depart->id) }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Nouvel onglet</a>
                  <a class="cr-btn cr-btn--fantome" href="{{ route('courrier.depart.scan', $depart->id) }}?telecharger=1"><i class="fa-solid fa-download"></i> Télécharger</a>
                </div>
              @endif
            </div>
            <div class="cr-visionneuse__cadre">
              @if ($scan && $scan->extension === 'pdf')
                <iframe src="{{ route('courrier.depart.scan', $depart->id) }}#toolbar=1&navpanes=0&view=Fit" title="Scan du courrier {{ $depart->numero }}"></iframe>
              @elseif ($scan)
                <div class="cr-visionneuse__image"><img src="{{ route('courrier.depart.scan', $depart->id) }}" alt="Scan du courrier {{ $depart->numero }}"></div>
              @else
                <div class="cr-visionneuse__vide"><i class="fa-regular fa-file-lines"></i><strong>Aucun scan joint à ce courrier départ</strong>@if ($peutModifier)<a class="cr-btn cr-btn--primaire" href="{{ route('courrier.depart.edit', $depart->id) }}"><i class="fa-solid fa-cloud-arrow-up"></i> Ajouter le scan</a>@endif</div>
              @endif
            </div>
          </div>

          <div class="cr-carte">
            <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-route"></i> Parcours</h2></div>
            <div class="cr-carte__corps">
              <ul class="cr-parcours">
                @forelse ($parcours as $e)
                  <li class="cr-parcours__item" style="--c: {{ $couleursParcours[$e->evenement] ?? '#2B6CB0' }}"><span class="cr-parcours__texte">{{ $e->resume }}</span><span class="cr-parcours__meta">{{ $e->created_at->format('d/m/Y à H:i') }}@if ($e->acteur_nom) · {{ $e->acteur_nom }}@endif</span></li>
                @empty
                  <li class="cr-parcours__item"><span class="cr-parcours__texte">Enregistré le {{ $depart->created_at->format('d/m/Y') }}</span></li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div><br><br>
    @if ($peutClasser) @include('courrier._classer') @endif
  </div>
@endsection
