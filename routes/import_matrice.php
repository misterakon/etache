<?php

use App\Http\Controllers\ImportMatriceController;
use Illuminate\Support\Facades\Route;

// Import de la matrice depuis Excel (ADMIN, DG). Chargé depuis routes/web.php : require __DIR__.'/import_matrice.php';
Route::middleware('auth')
    ->prefix('feuille-de-route/import-matrice')
    ->name('feuille_de_route.import_matrice.')
    ->group(function () {
        Route::get('/', [ImportMatriceController::class, 'index'])->name('index');
        Route::get('/modele', [ImportMatriceController::class, 'modele'])->name('modele');
        Route::post('/previsualiser', [ImportMatriceController::class, 'previsualiser'])->name('previsualiser');
        Route::get('/progression/{id}', [ImportMatriceController::class, 'progression'])->where('id', '[A-Za-z0-9]{20,40}')->name('progression');
        Route::post('/importer', [ImportMatriceController::class, 'importer'])->name('importer');
    });
