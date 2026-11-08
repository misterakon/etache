<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AideController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ClotureExerciceController;
use App\Http\Controllers\ClotureTacheController;
use App\Http\Controllers\CockpitController;
use App\Http\Controllers\CourrierController;
use App\Http\Controllers\DepartController;
use App\Http\Controllers\DgepnController;
use App\Http\Controllers\DiligenceActionController;
use App\Http\Controllers\DiligenceController;
use App\Http\Controllers\DiligenceDelaiController;
use App\Http\Controllers\DossierPersonnelController;
use App\Http\Controllers\ExportTacheController;
use App\Http\Controllers\FeuilleRouteControlleur;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\ImputationController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\JustificationController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MotDePasseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParametrageCourrierController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\PersonnelCarriereController;
use App\Http\Controllers\PieceJointeController;
use App\Http\Controllers\PilotageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\ReconductionController;
use App\Http\Controllers\ReglageController;
use App\Http\Controllers\ReponseCourrierController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\RhAgentController;
use App\Http\Controllers\RhController;
use App\Http\Controllers\RhImportController;
use App\Http\Controllers\RhPilotageController;
use App\Http\Controllers\SousTacheController;
use App\Http\Controllers\TachePonctuelleController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\VerrouillageController;
use App\Http\Middleware\ChangerMotDePasseProvisoire;
use App\Http\Middleware\ExigerProfil;
use App\Http\Middleware\VerrouillageInactivite;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

/* ---------- Écran de verrouillage (accessible même session verrouillée) ---------- */
Route::middleware('auth')->group(function () {
    Route::get('/verrouille', [VerrouillageController::class, 'show'])->name('verrouillage.show');
    Route::post('/verrouille', [VerrouillageController::class, 'unlock'])->middleware('throttle:deverrouillage')->name('verrouillage.unlock');
    Route::post('/verrouiller', [VerrouillageController::class, 'lock'])->name('verrouillage.lock');
});

Route::get('/dashboard', [MainController::class, 'dashboard'])->middleware(['auth', VerrouillageInactivite::class, ChangerMotDePasseProvisoire::class])->name('dashboard');

Route::middleware(['auth', VerrouillageInactivite::class, ChangerMotDePasseProvisoire::class])->group(function () {

    Route::post('/session/actif', [VerrouillageController::class, 'ping'])->name('session.ping');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{id}/ouvrir', [NotificationController::class, 'ouvrir'])->name('ouvrir');
        Route::post('/tout-lire', [NotificationController::class, 'toutLire'])->name('tout_lire');
    });

    Route::prefix('parametre')->name('parametre.')->group(function () {

        // --- Exercice ---
        Route::get('/exercice', [ParametreController::class, 'exercices'])->middleware('can:exercice.voir')->name('exercice');
        Route::post('/exercice', [ParametreController::class, 'storeExercice'])->name('exercice.store');
        Route::get('/exercice/{id}', [ParametreController::class, 'getExercice'])->middleware('can:exercice.voir')->name('exercice.get');
        Route::delete('/exercice/{id}', [ParametreController::class, 'deleteExercice'])->name('exercice.delete');

        Route::get('/exercice/{id}/apercu-mode', [ParametreController::class, 'apercuModeExercice'])->middleware('can:exercice.voir')->name('exercice.apercu_mode');

        // --- Type agent ---
        Route::get('/type-agent', [ParametreController::class, 'typeAgents'])->middleware('can:type_agent.voir')->name('type_agent');
        Route::post('/type-agent', [ParametreController::class, 'storeTypeAgent'])->name('type_agent.store');
        Route::get('/type-agent/{id}', [ParametreController::class, 'getTypeAgent'])->middleware('can:type_agent.voir')->name('type_agent.get');
        Route::delete('/type-agent/{id}', [ParametreController::class, 'deleteTypeAgent'])->name('type_agent.delete');

        // --- Famille d'emploi ---
        Route::get('/famille-emploi', [ParametreController::class, 'familleEmplois'])->middleware('can:famille_emploi.voir')->name('famille_emploi');
        Route::post('/famille-emploi', [ParametreController::class, 'storeFamilleEmploi'])->name('famille_emploi.store');
        Route::get('/famille-emploi/{id}', [ParametreController::class, 'getFamilleEmploi'])->middleware('can:famille_emploi.voir')->name('famille_emploi.get');
        Route::delete('/famille-emploi/{id}', [ParametreController::class, 'deleteFamilleEmploi'])->name('famille_emploi.delete');

        // --- Emploi ---
        Route::get('/emploi', [ParametreController::class, 'emplois'])->middleware('can:emploi.voir')->name('emploi');
        Route::post('/emploi', [ParametreController::class, 'storeEmploi'])->name('emploi.store');
        Route::get('/emploi/{id}', [ParametreController::class, 'getEmploi'])->middleware('can:emploi.voir')->name('emploi.get');
        Route::delete('/emploi/{id}', [ParametreController::class, 'deleteEmploi'])->name('emploi.delete');

        // --- Profil d'acces ---
        Route::get('/profil-acces', [ParametreController::class, 'profilsAcces'])->middleware('can:profils_acces.voir')->name('profil_acces');
        Route::post('/profil-acces', [ParametreController::class, 'storeProfilAcces'])->name('profil_acces.store');
        Route::get('/profil-acces/{id}', [ParametreController::class, 'getProfilAcces'])->middleware('can:profils_acces.voir')->name('profil_acces.get');
        Route::delete('/profil-acces/{id}', [ParametreController::class, 'deleteProfilAcces'])->name('profil_acces.delete');

        // --- Role d'affectation ---
        Route::get('/role-affectation', [ParametreController::class, 'rolesAffectation'])->middleware('can:role_affectation.voir')->name('role_affectation');
        Route::post('/role-affectation', [ParametreController::class, 'storeRoleAffectation'])->name('role_affectation.store');
        Route::get('/role-affectation/{id}', [ParametreController::class, 'getRoleAffectation'])->middleware('can:role_affectation.voir')->name('role_affectation.get');
        Route::delete('/role-affectation/{id}', [ParametreController::class, 'deleteRoleAffectation'])->name('role_affectation.delete');

        // --- Priorite ---
        Route::get('/priorite', [ParametreController::class, 'priorites'])->middleware('can:priorite.voir')->name('priorite');
        Route::post('/priorite', [ParametreController::class, 'storePriorite'])->name('priorite.store');
        Route::get('/priorite/{id}', [ParametreController::class, 'getPriorite'])->middleware('can:priorite.voir')->name('priorite.get');
        Route::delete('/priorite/{id}', [ParametreController::class, 'deletePriorite'])->name('priorite.delete');

        // --- Statut d'execution ---
        Route::get('/statut-execution', [ParametreController::class, 'statutsExecution'])->middleware('can:statut_execution.voir')->name('statut_execution');
        Route::post('/statut-execution', [ParametreController::class, 'storeStatutExecution'])->name('statut_execution.store');
        Route::get('/statut-execution/{id}', [ParametreController::class, 'getStatutExecution'])->middleware('can:statut_execution.voir')->name('statut_execution.get');
        Route::delete('/statut-execution/{id}', [ParametreController::class, 'deleteStatutExecution'])->name('statut_execution.delete');

        // --- Situation temporelle ---
        Route::get('/situation-temporelle', [ParametreController::class, 'situationsTemporelles'])->middleware('can:situation_temporelle.voir')->name('situation_temporelle');
        Route::post('/situation-temporelle', [ParametreController::class, 'storeSituationTemporelle'])->name('situation_temporelle.store');
        Route::get('/situation-temporelle/{id}', [ParametreController::class, 'getSituationTemporelle'])->middleware('can:situation_temporelle.voir')->name('situation_temporelle.get');
        Route::delete('/situation-temporelle/{id}', [ParametreController::class, 'deleteSituationTemporelle'])->name('situation_temporelle.delete');

        // --- Canal de notification ---
        Route::get('/canal-notification', [ParametreController::class, 'canauxNotification'])->middleware('can:canal_notification.voir')->name('canal_notification');
        Route::post('/canal-notification', [ParametreController::class, 'storeCanalNotification'])->name('canal_notification.store');
        Route::get('/canal-notification/{id}', [ParametreController::class, 'getCanalNotification'])->middleware('can:canal_notification.voir')->name('canal_notification.get');
        Route::delete('/canal-notification/{id}', [ParametreController::class, 'deleteCanalNotification'])->name('canal_notification.delete');

        // --- Regle d'alerte (relation many-to-many avec les canaux) ---
        Route::get('/regle-alerte', [ParametreController::class, 'reglesAlerte'])->middleware('can:regle_alerte.voir')->name('regle_alerte');
        Route::post('/regle-alerte', [ParametreController::class, 'storeRegleAlerte'])->name('regle_alerte.store');
        Route::get('/regle-alerte/{id}', [ParametreController::class, 'getRegleAlerte'])->middleware('can:regle_alerte.voir')->name('regle_alerte.get');
        Route::delete('/regle-alerte/{id}', [ParametreController::class, 'deleteRegleAlerte'])->name('regle_alerte.delete');

        // Configuration (pas encore converti aux droits précis : réservé à ADMIN, DG)
        Route::middleware(ExigerProfil::class.':ADMIN,DG')->group(function () {
            Route::get('/exercice/{id}/cloture', [ClotureExerciceController::class, 'index'])->name('exercice.cloture');
            Route::post('/exercice/{id}/cloture', [ClotureExerciceController::class, 'cloturer'])->name('exercice.cloturer');
            Route::post('/exercice/{id}/reouverture', [ClotureExerciceController::class, 'rouvrir'])->name('exercice.rouvrir');
            Route::get('/exercice/{id}/archive', [ClotureExerciceController::class, 'archive'])->name('exercice.archive');
            Route::get('/reconduction', [ReconductionController::class, 'index'])->name('reconduction');
            Route::post('/reconduction', [ReconductionController::class, 'executer'])->name('reconduction.executer');
            Route::post('/reconduction/structure', [ReconductionController::class, 'executerStructure'])->name('reconduction.structure');
        });

    });

    Route::prefix('dgepn')->name('dgepn.')->group(function () {

        // --- Direction --- (la lecture était déjà ouverte à tout utilisateur connecté avant cette conversion : elle le reste)
        Route::get('/direction', [DgepnController::class, 'directions'])->name('direction');
        Route::post('/direction', [DgepnController::class, 'storeDirection'])->name('direction.store');
        Route::get('/direction/{id}', [DgepnController::class, 'getDirection'])->name('direction.get');
        Route::delete('/direction/{id}', [DgepnController::class, 'deleteDirection'])->name('direction.delete');

        // --- Sous-direction --- (lecture ouverte à tous, comme avant)
        Route::get('/sous-direction', [DgepnController::class, 'sousDirections'])->name('sous_direction');
        Route::post('/sous-direction', [DgepnController::class, 'storeSousDirection'])->name('sous_direction.store');
        Route::get('/sous-direction/{id}', [DgepnController::class, 'getSousDirection'])->name('sous_direction.get');
        Route::delete('/sous-direction/{id}', [DgepnController::class, 'deleteSousDirection'])->name('sous_direction.delete');

        // --- Service --- (lecture ouverte à tous, comme avant)
        Route::get('/service', [DgepnController::class, 'services'])->name('service');
        Route::post('/service', [DgepnController::class, 'storeService'])->name('service.store');
        Route::get('/service/{id}', [DgepnController::class, 'getService'])->name('service.get');
        Route::delete('/service/{id}', [DgepnController::class, 'deleteService'])->name('service.delete');

        // --- Responsables (hierarchie de la DG aux services, historique des nominations) ---
        Route::get('/responsable', [ResponsableController::class, 'index'])->middleware('can:responsable.voir')->name('responsable');
        Route::get('/responsable/historique', [ResponsableController::class, 'historique'])->middleware('can:responsable.voir')->name('responsable.historique');
        Route::post('/responsable', [ResponsableController::class, 'store'])->middleware(ExigerProfil::class.':ADMIN')->name('responsable.store');
        Route::post('/responsable/{id}/fin', [ResponsableController::class, 'fin'])->middleware(ExigerProfil::class.':ADMIN')->name('responsable.fin');
        Route::delete('/responsable/{id}', [ResponsableController::class, 'destroy'])->middleware(ExigerProfil::class.':ADMIN')->name('responsable.delete');

        // --- Personnel ---
        Route::get('/personnel', [DgepnController::class, 'personnels'])->middleware('can:personnel.voir')->name('personnel');
        Route::post('/personnel', [DgepnController::class, 'storePersonnel'])->name('personnel.store');
        Route::get('/personnel/{id}', [DgepnController::class, 'getPersonnel'])->name('personnel.get');
        Route::delete('/personnel/{id}', [DgepnController::class, 'deletePersonnel'])->name('personnel.delete');
        Route::get('/personnel/{id}/fiche', [DgepnController::class, 'fichePersonnel'])->name('personnel.fiche');
        Route::post('/personnel/{id}/photo', [DgepnController::class, 'updatePhotoPersonnel'])->name('personnel.photo');

        Route::post('/personnel/affectation', [AffectationController::class, 'store'])->middleware(ExigerProfil::class.':ADMIN')->name('personnel.affectation');
        Route::post('/personnel/affectation/fin', [AffectationController::class, 'fin'])->middleware(ExigerProfil::class.':ADMIN')->name('personnel.affectation.fin');
        Route::get('/fonction', [FonctionController::class, 'index'])->name('fonction');
        Route::post('/fonction', [FonctionController::class, 'store'])->name('fonction.store');
        Route::delete('/fonction/{id}', [FonctionController::class, 'delete'])->name('fonction.delete');

        Route::post('/personnel/{id}/carriere', [PersonnelCarriereController::class, 'update'])->whereNumber('id')->name('personnel.carriere');
        Route::post('/personnel/{id}/dossier', [DossierPersonnelController::class, 'store'])->whereNumber('id')->name('personnel.dossier.store');
        Route::get('/personnel/dossier/{id}/apercu', [DossierPersonnelController::class, 'apercu'])->whereNumber('id')->name('personnel.dossier.apercu');
        Route::get('/personnel/dossier/{id}/telecharger', [DossierPersonnelController::class, 'telecharger'])->whereNumber('id')->name('personnel.dossier.telecharger');
        Route::delete('/personnel/dossier/{id}', [DossierPersonnelController::class, 'destroy'])->whereNumber('id')->name('personnel.dossier.delete');
    });

    Route::prefix('utilisateur')->name('utilisateur.')->group(function () {
        Route::get('/compte', [UtilisateurController::class, 'comptes'])->middleware('can:comptes_utilisateurs.voir')->name('compte');
        Route::post('/compte', [UtilisateurController::class, 'storeCompte'])->name('compte.store');
        Route::get('/compte/{id}', [UtilisateurController::class, 'getCompte'])->middleware('can:comptes_utilisateurs.voir')->name('compte.get');
        Route::delete('/compte/{id}', [UtilisateurController::class, 'deleteCompte'])->name('compte.delete');
    });

    Route::prefix('feuille-de-route')->name('feuille_de_route.')->group(function () {

        Route::get('/matrice/{exercice}', [FeuilleRouteControlleur::class, 'matrice'])->name('matrice');
        Route::get('/export/taches/{type}/{format}', [ExportTacheController::class, 'telecharger'])->whereIn('format', ['xlsx', 'pdf'])->middleware('throttle:20,1')->name('export.taches');

        Route::post('/piece-jointe', [PieceJointeController::class, 'store'])->name('piece_jointe.store');
        Route::get('/piece-jointe/{id}/telecharger', [PieceJointeController::class, 'telecharger'])->name('piece_jointe.telecharger');
        Route::get('/piece-jointe/{id}/apercu', [PieceJointeController::class, 'apercu'])->name('piece_jointe.apercu');
        Route::delete('/piece-jointe/{id}', [PieceJointeController::class, 'destroy'])->name('piece_jointe.delete');
        Route::get('/tache/{id}/livrables-zip', [PieceJointeController::class, 'zip'])->name('piece_jointe.zip');

        // --- Axe ---
        Route::get('/axe', [FeuilleRouteControlleur::class, 'axes'])->name('axe');
        Route::post('/axe', [FeuilleRouteControlleur::class, 'storeAxe'])->name('axe.store');
        Route::get('/axe/{id}', [FeuilleRouteControlleur::class, 'getAxe'])->name('axe.get');
        Route::delete('/axe/{id}', [FeuilleRouteControlleur::class, 'deleteAxe'])->name('axe.delete');

        // --- Action cle ---
        Route::get('/action-cle', [FeuilleRouteControlleur::class, 'actionsCles'])->name('action_cle');
        Route::post('/action-cle', [FeuilleRouteControlleur::class, 'storeActionCle'])->name('action_cle.store');
        Route::get('/action-cle/{id}', [FeuilleRouteControlleur::class, 'getActionCle'])->name('action_cle.get');
        Route::delete('/action-cle/{id}', [FeuilleRouteControlleur::class, 'deleteActionCle'])->name('action_cle.delete');

        // --- Action mesurable ---
        Route::get('/action-mesurable', [FeuilleRouteControlleur::class, 'actionsMesurables'])->name('action_mesurable');
        Route::post('/action-mesurable', [FeuilleRouteControlleur::class, 'storeActionMesurable'])->name('action_mesurable.store');
        Route::get('/action-mesurable/{id}', [FeuilleRouteControlleur::class, 'getActionMesurable'])->name('action_mesurable.get');
        Route::delete('/action-mesurable/{id}', [FeuilleRouteControlleur::class, 'deleteActionMesurable'])->name('action_mesurable.delete');

        // --- Tache ---
        Route::get('/tache', [FeuilleRouteControlleur::class, 'taches'])->name('tache');
        Route::post('/tache', [FeuilleRouteControlleur::class, 'storeTache'])->name('tache.store');
        Route::get('/tache/{id}', [FeuilleRouteControlleur::class, 'getTache'])->name('tache.get');
        Route::delete('/tache/{id}', [FeuilleRouteControlleur::class, 'deleteTache'])->name('tache.delete');
        Route::get('/taches/mes-taches', [FeuilleRouteControlleur::class, 'mesTaches'])->name('tache_mes_taches');
        Route::get('/tache/{id}/details', [FeuilleRouteControlleur::class, 'detailsTache'])->name('tache.details');
        Route::post('/tache/{id}/mise-a-jour', [FeuilleRouteControlleur::class, 'majRapideTache'])->name('tache.maj_rapide');

        Route::post('/tache/{id}/cloture/valider', [ClotureTacheController::class, 'valider'])->whereNumber('id')->name('tache.cloture.valider');
        Route::post('/tache/{id}/cloture/refuser', [ClotureTacheController::class, 'refuser'])->whereNumber('id')->name('tache.cloture.refuser');
        Route::post('/tache/{id}/cloture/soumettre', [ClotureTacheController::class, 'soumettre'])->whereNumber('id')->name('tache.cloture.soumettre');

        Route::post('/tache/{id}/justification', [JustificationController::class, 'store'])->name('tache.justification.store');
        Route::post('/justification/{id}/decision', [JustificationController::class, 'decision'])->name('justification.decision');
        Route::post('/justification/{id}/annuler', [JustificationController::class, 'annuler'])->name('justification.annuler');

        Route::get('/taches/matrice', [FeuilleRouteControlleur::class, 'tachesMatrice'])->name('tache.matrice');
        Route::get('/taches/calendrier', [FeuilleRouteControlleur::class, 'tachesCalendrier'])->name('tache.calendrier');
        Route::get('/taches/calendrier/evenements', [FeuilleRouteControlleur::class, 'tachesCalendrierEvenements'])->name('tache.calendrier.evenements');
        Route::get('/taches/gantt', [FeuilleRouteControlleur::class, 'tachesGantt'])->name('tache.gantt');

        // sous tache
        Route::post('/sous-tache', [SousTacheController::class, 'store'])->name('sous_tache.store');
        Route::get('/sous-tache/{id}', [SousTacheController::class, 'get'])->name('sous_tache.get');
        Route::post('/sous-tache/{id}/cocher', [SousTacheController::class, 'cocher'])->name('sous_tache.cocher');
        Route::post('/sous-tache/{id}/valider', [SousTacheController::class, 'valider'])->name('sous_tache.valider');
        Route::post('/sous-tache/{id}/refuser', [SousTacheController::class, 'rejeter'])->name('sous_tache.refuser');
        Route::delete('/sous-tache/{id}', [SousTacheController::class, 'destroy'])->name('sous_tache.delete');

        // pour la justification d'un retard
        Route::get('/justification', [JustificationController::class, 'index'])->middleware('can:justification.voir')->name('justification.index');

        // --- Charge de travail (vue direction generale : qui travaille sur quoi) ---
        Route::get('/charge-de-travail', [FeuilleRouteControlleur::class, 'chargeDeTravail'])->name('charge_travail');

        // --- Vue d'ensemble (lecture seule, toute la hierarchie sur un ecran) ---
        Route::get('/vue-ensemble', [FeuilleRouteControlleur::class, 'vueEnsemble'])->name('vue_ensemble');

        // --- Alerte (lecture seule, journal des alertes envoyees) ---
        Route::get('/alerte', [FeuilleRouteControlleur::class, 'alertes'])->middleware('can:alerte.voir')->name('alerte');
    });

    Route::prefix('courrier')->name('courrier.')->group(function () {
        Route::get('/arrivee', [CourrierController::class, 'index'])->name('arrivee');
        Route::get('/arrivee/export', [CourrierController::class, 'export'])->middleware('throttle:20,1')->name('export');
        Route::get('/doublon', [CourrierController::class, 'doublon'])->name('doublon');
        Route::post('/', [CourrierController::class, 'store'])->name('store');

        Route::get('/parametrage', [ParametrageCourrierController::class, 'index'])->name('parametrage');
        Route::post('/parametrage/type', [ParametrageCourrierController::class, 'storeType'])->name('parametrage.type.store');
        Route::delete('/parametrage/type/{id}', [ParametrageCourrierController::class, 'deleteType'])->name('parametrage.type.delete');
        Route::post('/parametrage/urgence', [ParametrageCourrierController::class, 'storeUrgence'])->name('parametrage.urgence.store');
        Route::delete('/parametrage/urgence/{id}', [ParametrageCourrierController::class, 'deleteUrgence'])->name('parametrage.urgence.delete');

        Route::get('/diligences', [DiligenceController::class, 'index'])->name('diligences');
        Route::get('/mes-courriers', [DiligenceController::class, 'mes'])->name('mes');
        Route::get('/diligence/{id}', [DiligenceController::class, 'show'])->whereNumber('id')->name('diligence.show');
        Route::post('/diligence/{id}/prendre', [DiligenceController::class, 'prendre'])->whereNumber('id')->name('diligence.prendre');
        Route::post('/diligence/{id}/solder', [DiligenceController::class, 'solder'])->whereNumber('id')->name('diligence.solder');
        Route::post('/diligence/{id}/annuler', [DiligenceController::class, 'annuler'])->whereNumber('id')->name('diligence.annuler');
        Route::post('/diligence/{id}/reimputer', [ImputationController::class, 'reimputer'])->whereNumber('id')->name('diligence.reimputer');
        Route::post('/diligence/{id}/avis', [DiligenceController::class, 'avis'])->whereNumber('id')->name('diligence.avis');
        Route::post('/diligence/{id}/renvoyer', [DiligenceController::class, 'renvoyer'])->whereNumber('id')->name('diligence.renvoyer');
        Route::get('/diligence/{id}/accuse', [DiligenceController::class, 'accuse'])->whereNumber('id')->name('diligence.accuse');
        Route::get('/diligences/export', [DiligenceController::class, 'export'])->middleware('throttle:20,1')->name('diligences.export');

        // Plan de traitement, délais, réponses (étape 3)
        Route::post('/diligence/{id}/action', [DiligenceActionController::class, 'store'])->whereNumber('id')->name('diligence.action.store');
        Route::post('/diligence/action/{id}/cocher', [DiligenceActionController::class, 'cocher'])->whereNumber('id')->name('diligence.action.cocher');
        Route::post('/diligence/action/{id}/valider', [DiligenceActionController::class, 'valider'])->whereNumber('id')->name('diligence.action.valider');
        Route::post('/diligence/action/{id}/rejeter', [DiligenceActionController::class, 'rejeter'])->whereNumber('id')->name('diligence.action.rejeter');
        Route::delete('/diligence/action/{id}', [DiligenceActionController::class, 'destroy'])->whereNumber('id')->name('diligence.action.delete');
        Route::post('/diligence/{id}/delai', [DiligenceDelaiController::class, 'demander'])->whereNumber('id')->name('diligence.delai.demander');
        Route::post('/diligence/delai/{id}/decider', [DiligenceDelaiController::class, 'decider'])->whereNumber('id')->name('diligence.delai.decider');
        Route::post('/diligence/{id}/reponse', [ReponseCourrierController::class, 'store'])->whereNumber('id')->name('reponse.store');
        Route::post('/reponse/{id}/soumettre', [ReponseCourrierController::class, 'soumettre'])->whereNumber('id')->name('reponse.soumettre');
        Route::post('/reponse/{id}/annuler', [ReponseCourrierController::class, 'annuler'])->whereNumber('id')->name('reponse.annuler');
        Route::post('/reponse/visa/{id}/viser', [ReponseCourrierController::class, 'viser'])->whereNumber('id')->name('reponse.viser');
        Route::post('/reponse/visa/{id}/refuser', [ReponseCourrierController::class, 'refuser'])->whereNumber('id')->name('reponse.refuser');
        Route::post('/reponse/visa/{id}/signer', [ReponseCourrierController::class, 'signer'])->whereNumber('id')->name('reponse.signer');
        Route::get('/reponse/{id}/fichier/{categorie}', [ReponseCourrierController::class, 'fichier'])->whereNumber('id')->whereIn('categorie', ['projet', 'signature'])->name('reponse.fichier');

        // Registre départ et archives (étape 4)
        Route::get('/depart', [DepartController::class, 'index'])->name('depart');
        Route::get('/depart/nouveau', [DepartController::class, 'create'])->name('depart.create');
        Route::get('/depart/export', [DepartController::class, 'export'])->middleware('throttle:20,1')->name('depart.export');
        Route::post('/depart', [DepartController::class, 'store'])->name('depart.store');
        Route::get('/depart/{id}', [DepartController::class, 'show'])->whereNumber('id')->name('depart.show');
        Route::get('/depart/{id}/modifier', [DepartController::class, 'edit'])->whereNumber('id')->name('depart.edit');
        Route::get('/depart/{id}/fiche', [DepartController::class, 'fiche'])->whereNumber('id')->name('depart.fiche');
        Route::get('/depart/{id}/scan', [DepartController::class, 'scan'])->whereNumber('id')->name('depart.scan');
        Route::get('/archives', [ArchiveController::class, 'index'])->name('archives');
        Route::post('/archives/courrier/{id}', [ArchiveController::class, 'classerCourrier'])->whereNumber('id')->name('archives.courrier');
        Route::post('/archives/depart/{id}', [ArchiveController::class, 'classerDepart'])->whereNumber('id')->name('archives.depart');

        Route::post('/parametrage/instruction', [ParametrageCourrierController::class, 'storeInstruction'])->name('parametrage.instruction.store');
        Route::delete('/parametrage/instruction/{id}', [ParametrageCourrierController::class, 'deleteInstruction'])->name('parametrage.instruction.delete');
        Route::post('/parametrage/delai', [ParametrageCourrierController::class, 'storeDelai'])->name('parametrage.delai.store');
        Route::delete('/parametrage/delai/{id}', [ParametrageCourrierController::class, 'deleteDelai'])->name('parametrage.delai.delete');
        Route::post('/parametrage/poste', [ParametrageCourrierController::class, 'storePoste'])->name('parametrage.poste.store');
        Route::delete('/parametrage/poste/{id}', [ParametrageCourrierController::class, 'deletePoste'])->name('parametrage.poste.delete');

        // Doivent rester APRÈS les routes fixes ci-dessus
        Route::get('/{id}', [CourrierController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/scan', [CourrierController::class, 'scanApercu'])->whereNumber('id')->name('scan.apercu');
        Route::get('/{id}/scan/telecharger', [CourrierController::class, 'scanTelecharger'])->whereNumber('id')->name('scan.telecharger');
        Route::post('/{id}/imputer', [ImputationController::class, 'store'])->whereNumber('id')->name('imputer');
        Route::get('/{id}/fiche', [CourrierController::class, 'fiche'])->whereNumber('id')->name('fiche');
    });

    // --- Pilotage et analyses (F8 / F12) : ADMIN, DG ; RD pour ses directions ---
    Route::prefix('pilotage')->name('pilotage.')->group(function () {
        Route::get('/', [PilotageController::class, 'index'])->middleware('can:pilotage.voir')->name('index');
        Route::post('/synthese', [PilotageController::class, 'synthetiser'])->name('synthese');
        Route::post('/synthese/{id}', [PilotageController::class, 'editer'])->whereNumber('id')->name('synthese.editer');
        Route::get('/historique', [PilotageController::class, 'historique'])->middleware('can:pilotage.voir')->name('historique');
        Route::get('/export/excel', [PilotageController::class, 'exportExcel'])->middleware(['can:pilotage.exporter', 'throttle:20,1'])->name('export.excel');
        Route::get('/export/pdf', [PilotageController::class, 'exportPdf'])->middleware(['can:pilotage.exporter', 'throttle:20,1'])->name('export.pdf');
    });

    // --- Tâches ponctuelles : travail hors feuille de route, visible de la chaîne hiérarchique (tous profils connectés) ---
    Route::prefix('feuille-de-route/ponctuelles')->name('ponctuelle.')->group(function () {
        Route::get('/', [TachePonctuelleController::class, 'index'])->name('index');
        Route::get('/donnees', [TachePonctuelleController::class, 'donnees'])->name('donnees');
        Route::post('/', [TachePonctuelleController::class, 'store'])->name('store');
        Route::get('/{id}', [TachePonctuelleController::class, 'show'])->whereNumber('id')->name('show');
        Route::post('/{id}', [TachePonctuelleController::class, 'update'])->whereNumber('id')->name('update');
        Route::post('/{id}/decision', [TachePonctuelleController::class, 'decider'])->whereNumber('id')->name('decision');
        Route::delete('/{id}', [TachePonctuelleController::class, 'destroy'])->whereNumber('id')->name('destroy');
        Route::post('/{id}/livrables', [TachePonctuelleController::class, 'livrableStore'])->whereNumber('id')->name('livrable.store');
        Route::get('/{id}/livrables/{pieceId}', [TachePonctuelleController::class, 'livrableTelecharger'])->whereNumber(['id', 'pieceId'])->name('livrable.telecharger');
        Route::delete('/{id}/livrables/{pieceId}', [TachePonctuelleController::class, 'livrableSupprimer'])->whereNumber(['id', 'pieceId'])->name('livrable.supprimer');
    });

    // --- Aide : manuel, panneau « ? », visite guidée ; recherche dans toute l'application ---
    Route::prefix('aide')->group(function () {
        Route::get('/', [AideController::class, 'index'])->name('aide.index');
        Route::get('/recherche', [AideController::class, 'chercher'])->middleware('throttle:120,1')->name('aide.recherche');
        Route::get('/contexte', [AideController::class, 'contexte'])->middleware('throttle:120,1')->name('aide.contexte');
        Route::get('/manuel', [AideController::class, 'manuel'])->name('aide.manuel');
        Route::post('/visite-terminee', [AideController::class, 'visiteTerminee'])->name('aide.visite.terminee');
        Route::get('/{slug}', [AideController::class, 'chapitre'])->where('slug', '[a-z0-9-]+')->name('aide.chapitre');
    });
    Route::get('/recherche', [RechercheController::class, 'index'])->middleware('throttle:60,1')->name('recherche');
    Route::get('/recherche/suggestions', [RechercheController::class, 'suggestions'])->middleware('throttle:120,1')->name('recherche.suggestions');

    // --- Pilotage (cockpit) : page unique à onglets chargés en AJAX (remplacera Suivi de l'exécution + Pilotage et analyses) ---
    Route::prefix('cockpit')->name('cockpit.')->middleware('can:cockpit.voir')->group(function () {
        Route::get('/', [CockpitController::class, 'index'])->name('index');
        Route::get('/onglet/{onglet}', [CockpitController::class, 'onglet'])->name('onglet');
    });

    // --- Personnel : qualité des fiches, comptes, import, pilotage, profil RH (F10) ---
    Route::prefix('rh')->name('rh.')->group(function () {
        Route::get('/qualite', [RhController::class, 'qualite'])->middleware('can:rh_qualite.voir')->name('qualite');
        Route::get('/qualite/export', [RhController::class, 'qualiteExport'])->middleware('can:rh_qualite.exporter')->name('qualite.export');
        Route::get('/doublons', [RhController::class, 'doublons'])->middleware('can:rh_doublons.voir')->name('doublons');
        Route::get('/archives', [RhController::class, 'archives'])->middleware('can:rh_archives.voir')->name('archives');
        Route::post('/agent/{id}/archiver', [RhController::class, 'archiver'])->whereNumber('id')->name('archiver');
        Route::post('/archives/{id}/restaurer', [RhController::class, 'restaurer'])->whereNumber('id')->name('restaurer');
        Route::get('/comptes', [RhController::class, 'comptes'])->middleware('can:rh_comptes.voir')->name('comptes');
        Route::post('/comptes', [RhController::class, 'comptesCreer'])->name('comptes.creer');
        Route::get('/comptes/telecharger', [RhController::class, 'comptesTelecharger'])->name('comptes.telecharger');
        Route::post('/comptes/desactiver', [RhController::class, 'comptesDesactiver'])->name('comptes.desactiver');
        Route::get('/import', [RhImportController::class, 'index'])->middleware('can:rh_import.voir')->name('import');
        Route::get('/import/modele', [RhImportController::class, 'modele'])->middleware('can:rh_import.voir')->name('import.modele');
        Route::get('/import/export', [RhImportController::class, 'export'])->middleware('can:rh_import.voir')->name('import.export');
        Route::post('/import/apercu', [RhImportController::class, 'apercu'])->name('import.apercu');
        Route::post('/import/appliquer', [RhImportController::class, 'appliquer'])->name('import.appliquer');
        Route::get('/tableau-de-bord', [RhPilotageController::class, 'tableauDeBord'])->middleware('can:rh_tableau_de_bord.voir')->name('tableau_de_bord');
        Route::get('/charge', [RhPilotageController::class, 'charge'])->middleware('can:rh_charge.voir')->name('charge');
        Route::get('/dossiers', [RhPilotageController::class, 'dossiers'])->middleware('can:rh_dossiers.voir')->name('dossiers');
        Route::post('/dossiers/{pieceId}', [RhPilotageController::class, 'datesDossier'])->whereNumber('pieceId')->name('dossiers.dates');
        Route::get('/organigramme', [RhPilotageController::class, 'organigramme'])->middleware('can:rh_organigramme.voir')->name('organigramme');

        // Profil RH : l'agent concerné, l'ADMIN et le DG (contrôlé dans le contrôleur)
        Route::get('/agent/{id}', [RhAgentController::class, 'show'])->whereNumber('id')->name('agent');
        Route::post('/agent/{id}/competence', [RhAgentController::class, 'storeCompetence'])->whereNumber('id')->name('competence.store');
        Route::delete('/competence/{id}', [RhAgentController::class, 'deleteCompetence'])->whereNumber('id')->name('competence.delete');
        Route::post('/agent/{id}/formation', [RhAgentController::class, 'storeFormation'])->whereNumber('id')->name('formation.store');
        Route::delete('/formation/{id}', [RhAgentController::class, 'deleteFormation'])->whereNumber('id')->name('formation.delete');

        Route::get('/mot-de-passe', [MotDePasseController::class, 'show'])->name('mot_de_passe');
        Route::post('/mot-de-passe', [MotDePasseController::class, 'store'])->name('mot_de_passe.store');
    });

    Route::get('/journal-activite', [JournalController::class, 'index'])->name('journal.index');
    Route::get('/reglages', [ReglageController::class, 'index'])->name('reglages.index');
    Route::post('/reglages', [ReglageController::class, 'store'])->name('reglages.store');
    Route::post('/reglages/retablir', [ReglageController::class, 'retablir'])->name('reglages.retablir');

});

require __DIR__.'/import_matrice.php';
require __DIR__.'/auth.php';
