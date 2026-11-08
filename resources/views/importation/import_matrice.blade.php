@extends('layout.master')
@section('title', 'Import de la matrice')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/tache-list.css') }}"> {{-- classes tl-* (badges) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/import_matrice.css') }}">
@endsection

@section('main-content')
  <div class="page-body imp-page">
    <div class="container w-full">
      <div class="page-title">
        <div class="grid grid-cols-12 mx-2 items-center">
          <div class="col-span-6 sm:col-span-12">
            <h3>Importation de la matrice (feuille de route)</h3>
          </div>
          <div class="col-span-6 sm:col-span-12">
            <ol class="breadcrumb flex">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
              <li class="breadcrumb-item">Feuille de route</li>
              <li class="breadcrumb-item active">Import de la matrice</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="grid grid-cols-12 card-gap">

        {{-- ============ Bandeau ============ --}}
        <div class="col-span-12">
          <div class="imp-hero">
            <div class="imp-hero__icone"><i class="fa-solid fa-file-import"></i></div>
            <div class="imp-hero__corps">
              <h2 class="imp-hero__titre">Importer la matrice depuis Excel</h2>
              <p class="imp-hero__texte">Chargez axes, actions clés, actions mesurables et tâches en une seule opération, contrôlée ligne par ligne avant tout enregistrement.</p>
            </div>
            <ul class="imp-hero__garanties">
              <li><i class="fa-solid fa-shield-halved"></i> Tout ou rien</li>
              <li><i class="fa-solid fa-eye"></i> Prévisualisation</li>
              <li><i class="fa-solid fa-clone"></i> Sans doublon</li>
            </ul>
          </div>
        </div>

        {{-- ============ Étapes ============ --}}
        <div class="col-span-12">
          <div class="card imp-carte">
            <div class="imp-carte__corps" style="padding-top:18px; padding-bottom:18px;">
              <ol class="imp-stepper" id="imp_stepper" data-etape="1">
                <li class="imp-step">
                  <span class="imp-step__rond"><span class="imp-step__num">1</span><i class="fa-solid fa-check imp-step__ok"></i></span>
                  <span class="imp-step__libelle">Modèle</span><span class="imp-step__aide">Télécharger et remplir</span>
                </li>
                <li class="imp-step">
                  <span class="imp-step__rond"><span class="imp-step__num">2</span><i class="fa-solid fa-check imp-step__ok"></i></span>
                  <span class="imp-step__libelle">Fichier</span><span class="imp-step__aide">Choisir et analyser</span>
                </li>
                <li class="imp-step">
                  <span class="imp-step__rond"><span class="imp-step__num">3</span><i class="fa-solid fa-check imp-step__ok"></i></span>
                  <span class="imp-step__libelle">Vérification</span><span class="imp-step__aide">Contrôler puis importer</span>
                </li>
              </ol>
            </div>
          </div>
        </div>

        {{-- ============ 1. Modèle ============ --}}
        <div class="col-span-12">
          <div class="card imp-carte">
            <div class="imp-carte__tete">
              <span class="imp-num">1</span>
              <div>
                <h5 class="imp-carte__titre">Télécharger le modèle</h5>
                <p class="imp-carte__sous">Un classeur prêt à remplir, avec les valeurs de votre application</p>
              </div>
            </div>
            <div class="imp-carte__corps">
              <div class="imp-modele">
                <div class="imp-modele__visuel"><i class="fa-solid fa-file-excel"></i></div>
                <div class="imp-modele__corps">
                  <p class="imp-modele__texte">Une ligne = une tâche. Les valeurs d'axe, d'action clé et d'action mesurable peuvent être écrites une seule fois (cellules fusionnées ou vides en dessous) ou répétées sur chaque ligne. Les directions se citent par leur <b>code</b>, les agents par leur <b>matricule ou email</b>.</p>
                  <ul class="imp-onglets">
                    <li><i class="fa-solid fa-book-open"></i> Mode d'emploi</li>
                    <li><i class="fa-solid fa-table"></i> Matrice à remplir</li>
                    <li><i class="fa-solid fa-lightbulb"></i> Exemple</li>
                    <li><i class="fa-solid fa-list-ul"></i> Listes (codes, statuts, priorités)</li>
                  </ul>
                </div>
                <a class="btn btn-success text-white imp-btn-lg" href="{{ route('feuille_de_route.import_matrice.modele') }}"><i class="fa-solid fa-download"></i> Télécharger le modèle</a>
              </div>

              @if (count($directionsSansCode))
                <div class="imp-callout imp-callout--attention" role="alert">
                  <i class="fa-solid fa-triangle-exclamation"></i>
                  <div>
                    <b>Directions sans code.</b> Elles ne pourront pas être citées dans le fichier : <b>{{ implode(', ', $directionsSansCode) }}</b>.
                    Renseignez leur code dans la page Directions.
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- ============ 2. Fichier ============ --}}
        <div class="col-span-12">
          <div class="card imp-carte">
            <div class="imp-carte__tete">
              <span class="imp-num">2</span>
              <div>
                <h5 class="imp-carte__titre">Analyser votre fichier</h5>
                <p class="imp-carte__sous">L'analyse ne modifie rien : vous verrez chaque ligne avec ses erreurs éventuelles</p>
              </div>
            </div>
            <div class="imp-carte__corps">
              <div class="imp-formulaire">
                <div class="imp-champ">
                  <label class="imp-etiquette" for="exercice_id"><i class="fa-regular fa-calendar-days"></i> Exercice de destination</label>
                  <select class="form-select" id="exercice_id">
                    @foreach ($exercices as $exercice)
                      <option value="{{ $exercice->id }}" @selected($exercice->id == $exerciceDefautId)>{{ $exercice->libelle ?: $exercice->annee }}</option>
                    @endforeach
                  </select>
                  <p class="imp-aide">Les éléments seront créés dans cet exercice.</p>
                </div>
                <div class="imp-champ imp-champ--large">
                  <span class="imp-etiquette"><i class="fa-solid fa-file-excel"></i> Fichier Excel</span>
                  <label class="imp-depot" id="depot" for="fichier">
                    <input class="imp-depot__input" id="fichier" type="file" accept=".xlsx">
                    <span class="imp-depot__icone"><i class="fa-solid fa-cloud-arrow-up" id="depot_icone"></i></span>
                    <span class="imp-depot__titre" id="depot_titre">Glissez votre fichier ici ou <u>parcourez</u></span>
                    <span class="imp-depot__sous" id="depot_sous">Format .xlsx · 5 Mo maximum · onglet « Matrice »</span>
                  </label>
                </div>
              </div>
              <div class="imp-actions">
                <button class="btn btn-primary text-white imp-btn-lg" type="button" id="btn_analyser"><i class="fa-solid fa-magnifying-glass"></i> Analyser le fichier</button>
                <p class="imp-aide">L'import est « tout ou rien » : une seule erreur le bloque, rien n'est enregistré tant que le fichier n'est pas corrigé.</p>
              </div>
            </div>
          </div>
        </div>

        {{-- ============ Progression (analyse ou import en cours) ============ --}}
        <div class="col-span-12" id="zone_progression" hidden>
          <div class="card imp-carte">
            <div class="imp-carte__tete">
              <span class="imp-num"><i class="fa-solid fa-gear fa-spin"></i></span>
              <div>
                <h5 class="imp-carte__titre" id="prog_titre">Opération en cours</h5>
                <p class="imp-carte__sous">Ne fermez pas cette page pendant l'opération</p>
              </div>
            </div>
            <div class="imp-carte__corps">
              <div class="imp-barre" role="progressbar" id="prog_aria" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="imp-barre__rempli" id="prog_barre"></div>
                <div class="imp-barre__texte" id="prog_pourcent">0 %</div>
              </div>
              <p class="imp-operation" id="prog_operation" aria-live="polite">
                <b>En cours :</b> <span id="prog_libelle">Démarrage…</span><span class="imp-compteur" id="prog_compteur"></span>
              </p>
              <ul class="imp-etapes" id="prog_etapes"></ul>
            </div>
          </div>
        </div>

        {{-- ============ 3. Résultat de l'analyse ============ --}}
        <div class="col-span-12" id="zone_resultat" hidden>
          <div class="card imp-carte">
            <div class="imp-carte__tete">
              <span class="imp-num">3</span>
              <div>
                <h5 class="imp-carte__titre">Vérification du fichier</h5>
                <p class="imp-carte__sous">Contrôlez le détail ligne par ligne avant d'importer</p>
              </div>
              <button class="btn btn-success text-white imp-btn-lg" style="margin-left:auto;" type="button" id="btn_importer" disabled><i class="fa-solid fa-file-import"></i> Importer</button>
            </div>
            <div class="imp-carte__corps">
              <div id="verdict"></div>
              <div class="imp-kpis" id="resume"></div>

              <div class="imp-barre-outils">
                <div class="imp-seg" role="group" aria-label="Filtrer les lignes">
                  <button type="button" class="imp-seg__btn imp-actif" data-filtre="toutes"><i class="fa-solid fa-list"></i> Toutes <span class="imp-seg__nb" data-nb="toutes">0</span></button>
                  <button type="button" class="imp-seg__btn" data-filtre="erreur"><i class="fa-solid fa-circle-xmark"></i> Erreurs <span class="imp-seg__nb" data-nb="erreur">0</span></button>
                  <button type="button" class="imp-seg__btn" data-filtre="nouvelle"><i class="fa-solid fa-circle-plus"></i> Nouvelles <span class="imp-seg__nb" data-nb="nouvelle">0</span></button>
                  <button type="button" class="imp-seg__btn" data-filtre="existante"><i class="fa-solid fa-box-archive"></i> Existantes <span class="imp-seg__nb" data-nb="existante">0</span></button>
                </div>
                {{-- liste maintenue pour la logique de filtrage ; pilotée par les boutons ci-contre --}}
                <select id="filtre_lignes" class="imp-cache" tabindex="-1" aria-hidden="true">
                  <option value="toutes">Toutes les lignes</option>
                  <option value="erreur">Lignes en erreur</option>
                  <option value="nouvelle">Tâches nouvelles</option>
                  <option value="existante">Tâches déjà existantes</option>
                </select>
              </div>

              <div class="imp-table-cadre custom-scrollbar">
                <table class="imp-table">
                  <thead>
                    <tr>
                      <th>Ligne</th>
                      <th>État</th>
                      <th>Axe</th>
                      <th>Action clé</th>
                      <th>Action mesurable</th>
                      <th>Tâche</th>
                      <th>Responsable</th>
                      <th>Échéance</th>
                      <th>Messages</th>
                    </tr>
                  </thead>
                  <tbody id="corps_lignes"></tbody>
                </table>
              </div>
              <p class="imp-aide" id="note_troncature" hidden>Affichage limité : toutes les lignes en erreur sont montrées, les autres sont tronquées.</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    @include('importation.js_import_matrice')
  </div>
@endsection
