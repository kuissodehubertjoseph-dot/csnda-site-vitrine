<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'eleves.index' : 'login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('eleves.index');
    })->name('dashboard');

    Route::get('/eleves/import', [StudentImportController::class, 'create'])->name('eleves.import.form');
    Route::post('/eleves/import/apercu', [StudentImportController::class, 'apercu'])->name('eleves.import.apercu');
    Route::post('/eleves/import', [StudentImportController::class, 'store'])->name('eleves.import.store');

    Route::resource('eleves', StudentController::class)->parameters(['eleves' => 'student']);
    Route::patch('/eleves/{student}/statut', [StudentController::class, 'toggleStatut'])->name('eleves.statut');

    Route::get('/eleves/{student}/carte/recto', [CardController::class, 'recto'])->name('cartes.recto');
    Route::get('/eleves/{student}/carte/verso', [CardController::class, 'verso'])->name('cartes.verso');
    Route::get('/eleves/{student}/carte', [CardController::class, 'telecharger'])->name('cartes.telecharger');

    Route::get('/cartes/lot', [CardController::class, 'formulaireLot'])->name('cartes.lot.form');
    Route::post('/cartes/lot', [CardController::class, 'genererLot'])->name('cartes.lot.generer');
    Route::get('/cartes/lot/{lot}/{type}', [CardController::class, 'telechargerLot'])->name('cartes.lot.telecharger');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
