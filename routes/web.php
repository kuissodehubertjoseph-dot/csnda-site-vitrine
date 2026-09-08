<?php

use App\Http\Controllers\AccesController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\EtablissementAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && session('etablissement_id')) {
        return redirect()->route('eleves.index');
    }

    return view('accueil', ['ecoles' => config('cortex.ecoles')]);
})->name('accueil');

Route::middleware('guest')->group(function () {
    Route::get('/ecoles/{etablissement:slug}/connexion', [EtablissementAuthController::class, 'create'])
        ->name('ecoles.connexion');
    Route::post('/ecoles/{etablissement:slug}/connexion', [EtablissementAuthController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('eleves.index');
    })->name('dashboard');

    Route::get('/eleves/import', [StudentImportController::class, 'create'])->name('eleves.import.form');
    Route::post('/eleves/import/apercu', [StudentImportController::class, 'apercu'])->name('eleves.import.apercu');
    Route::post('/eleves/import', [StudentImportController::class, 'store'])->name('eleves.import.store');

    Route::delete('/eleves-tout', [StudentController::class, 'destroyTout'])->name('eleves.destroyTout');

    Route::resource('eleves', StudentController::class)->parameters(['eleves' => 'student']);
    Route::patch('/eleves/{student}/statut', [StudentController::class, 'toggleStatut'])->name('eleves.statut');
    Route::patch('/eleves/{student}/classe', [StudentController::class, 'deplacerClasse'])->name('eleves.deplacer');

    Route::get('/eleves/{student}/carte/recto', [CardController::class, 'recto'])->name('cartes.recto');
    Route::get('/eleves/{student}/carte/verso', [CardController::class, 'verso'])->name('cartes.verso');
    Route::get('/eleves/{student}/carte', [CardController::class, 'telecharger'])->name('cartes.telecharger');

    Route::get('/cartes/lot', [CardController::class, 'formulaireLot'])->name('cartes.lot.form');
    Route::post('/cartes/lot', [CardController::class, 'genererLot'])->name('cartes.lot.generer');
    Route::get('/cartes/lot/{lot}/{face}/{paquet?}', [CardController::class, 'telechargerLot'])->name('cartes.lot.telecharger');

    Route::get('/acces', [AccesController::class, 'index'])->name('acces.index');
    Route::post('/acces', [AccesController::class, 'store'])->name('acces.store');
    Route::patch('/acces/{utilisateur}', [AccesController::class, 'update'])->name('acces.update');
    Route::delete('/acces/{utilisateur}', [AccesController::class, 'destroy'])->name('acces.destroy');

    Route::get('/parametres', [SettingsController::class, 'edit'])->name('parametres.edit');
    Route::post('/parametres/signature', [SettingsController::class, 'updateSignature'])->name('parametres.signature');
    Route::post('/parametres/cachet', [SettingsController::class, 'updateCachet'])->name('parametres.cachet');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
