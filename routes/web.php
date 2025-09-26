<?php

use App\Http\Controllers\AnnonceMarketplaceController;
use App\Http\Controllers\CommandeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticatedSessionController;
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

// Marketplace routes
Route::resource('annonces', AnnonceMarketplaceController::class);
Route::resource('commandes', CommandeController::class);
Route::get('mes-annonces', [AnnonceMarketplaceController::class, 'mesAnnonces'])->name('mes-annonces');
Route::get('mes-commandes', [CommandeController::class, 'mesCommandes'])->name('mes-commandes');
Route::get('commandes-recues', [CommandeController::class, 'commandesRecues'])->name('commandes-recues');
Route::patch('annonces/{annonce}/statut', [AnnonceMarketplaceController::class, 'updateStatut'])->name('annonces.statut');

// Authentication routes
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/* Backoffice Troc Routes */
Route::get('/troc', [PostDechetController::class, 'indexTroc'])->name('postdechets.troc');
Route::get('/postdechets/{post}/offres', [PostDechetController::class, 'showOffres'])->name('postdechets.offres');

// Backoffice OffreTroc routes
Route::prefix('offres-troc')->group(function () {
    Route::get('/', [OffreTrocController::class, 'index'])->name('offres-troc.index');
    Route::get('/create/{postId}', [OffreTrocController::class, 'create'])->name('offres-troc.create');
    Route::post('/{postId}', [OffreTrocController::class, 'store'])->name('offres-troc.store');
    Route::get('/{postId}', [OffreTrocController::class, 'show'])->name('offres-troc.show'); // Correction ici
    Route::patch('/{id}/statut', [OffreTrocController::class, 'updateStatut'])->name('offres-troc.update-statut');
    Route::get('/{id}/edit', [OffreTrocController::class, 'edit'])->name('offres-troc.edit');
    Route::put('/{id}', [OffreTrocController::class, 'update'])->name('offres-troc.update');
    Route::delete('/{id}', [OffreTrocController::class, 'destroy'])->name('offres-troc.destroy');
});

/* Frontoffice Troc Routes */
Route::get('/home/troc', [PostDechetController::class, 'indexTrocFront'])->name('postdechets.troc.front');
Route::get('/home/postdechets/{post}/offres', [PostDechetController::class, 'showOffresFront'])->name('postdechets.offres.front');

// Frontoffice OffreTroc routes
Route::prefix('home/offres-troc')->group(function () {
    Route::get('/', [OffreTrocController::class, 'indexFront'])->name('offres-troc.index.front');
    Route::get('/create/{postId}', [OffreTrocController::class, 'createFront'])->name('offres-troc.create.front');
    Route::post('/{postId}', [OffreTrocController::class, 'storeFront'])->name('offres-troc.store.front');
    Route::get('/{postId}', [OffreTrocController::class, 'showFront'])->name('offres-troc.show.front');
    Route::patch('/{id}/statut', [OffreTrocController::class, 'updateStatutFront'])->name('offres-troc.update-statut.front');
});