<?php

use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FiliereController as AdminFiliereController;
use App\Http\Controllers\Admin\GalleryCategoryController as AdminGalleryCategoryController;
use App\Http\Controllers\Admin\GalleryPhotoController as AdminGalleryPhotoController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Pages publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/presentation', [PresentationController::class, 'index'])->name('presentation');
Route::get('/filieres', [FiliereController::class, 'index'])->name('filieres');
Route::get('/actualites', [NewsController::class, 'index'])->name('news.index');
Route::get('/actualites/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/galerie', [GalleryController::class, 'index'])->name('gallery');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::redirect('/dashboard', '/admin')->middleware('auth')->name('dashboard');

// Profil (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Back-office
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pages/{page:slug}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page:slug}', [AdminPageController::class, 'update'])->name('pages.update');

    Route::resource('actualites', AdminNewsController::class)->except(['show']);
    Route::resource('filieres', AdminFiliereController::class)->except(['show']);
    Route::resource('categories-galerie', AdminGalleryCategoryController::class)->except(['show']);
    Route::resource('photos-galerie', AdminGalleryPhotoController::class)->except(['show', 'edit', 'update']);

    Route::get('/parametres', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/parametres', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::get('/messages', [AdminContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('messages.destroy');
});

require __DIR__.'/auth.php';
