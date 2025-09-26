<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\OffreTrocController;
use App\Http\Controllers\TransactionTrocController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('backoffice.pages.dashboard');
})->name('dashboard');

Route::resource('transactions-troc', TransactionTrocController::class);
Route::get('/home', function () {
    return view('frontoffice.pages.home');
})->name('home');

Route::resource('postdechets', PostDechetController::class);
Route::resource('propositions', PropositionController::class);

/* Troc *********** */
Route::get('/troc', [PostDechetController::class, 'indexTroc'])->name('postdechets.troc');

// Routes pour OffreTrocController (remplace PropositionTrocController)
Route::get('/offres-troc', [OffreTrocController::class, 'index'])->name('offres-troc.index');
Route::get('/offres-troc/create/{postId}', [OffreTrocController::class, 'create'])->name('offres-troc.create');
Route::post('/offres-troc/{postId}', [OffreTrocController::class, 'store'])->name('offres-troc.store');
Route::patch('/offres-troc/{id}/statut', [OffreTrocController::class, 'updateStatut'])->name('offres-troc.update-statut');
Route::resource('offres-troc', OffreTrocController::class)->except(['index', 'create', 'store']); // Évite doublons
// Nouvelle route pour voir les offres d'un post
Route::get('/postdechets/{post}/offres', [PostDechetController::class, 'showOffres'])->name('postdechets.offres');