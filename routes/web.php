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
// <CHANGE> Ajouter les nouvelles routes resource
Route::resource('annonces', AnnonceMarketplaceController::class);
Route::resource('commandes', CommandeController::class);

// <CHANGE> Ajouter les routes personnalisées
Route::get('mes-annonces', [AnnonceMarketplaceController::class, 'mesAnnonces'])->name('mes-annonces');
Route::get('mes-commandes', [CommandeController::class, 'mesCommandes'])->name('mes-commandes');
Route::get('commandes-recues', [CommandeController::class, 'commandesRecues'])->name('commandes-recues');
Route::patch('annonces/{annonce}/statut', [AnnonceMarketplaceController::class, 'updateStatut'])->name('annonces.statut');
// Login
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Register
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

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