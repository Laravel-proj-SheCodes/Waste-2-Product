<?php

use App\Http\Controllers\AnnonceMarketplaceController;
use App\Http\Controllers\CommandeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;

 
Route::get('/', function () {
    return view('welcome');

});

Route::get('/dashboard', function () {
    return view('backoffice.pages.dashboard');
})->name('dashboard');

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