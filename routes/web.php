<?php

use Illuminate\Support\Facades\Route;

/** Backoffice contrôleurs existants */
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;

/** Auth */
use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;

/** Front waste-posts */
use App\Http\Controllers\Front\PostDechetFrontController;

/** Marketplace */
use App\Http\Controllers\AnnonceMarketplaceController;
use App\Http\Controllers\CommandeController;

/** Troc */
use App\Http\Controllers\OffreTrocController;
use App\Http\Controllers\TransactionTrocController;

/** Donations */
use App\Http\Controllers\DonationController;

use App\Http\Controllers\Front\PropositionFrontController;


/* =========================
 |  Pages simples
 * ========================= */
Route::view('/home', 'frontoffice.pages.home')->name('home');
Route::get('/', fn () => redirect()->route('home'));


/* =========================
 |  Backoffice commun
 * ========================= */
Route::get('/dashboard', fn () => view('backoffice.pages.dashboard'))->name('dashboard');

Route::resource('postdechets', PostDechetController::class);
Route::resource('propositions', PropositionController::class);

/* =========================
 |  Authentification
 * ========================= */
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/* =========================
 |  Marketplace
 * ========================= */
Route::resource('annonces', AnnonceMarketplaceController::class);
Route::resource('commandes', CommandeController::class);
Route::get('mes-annonces', [AnnonceMarketplaceController::class, 'mesAnnonces'])->name('mes-annonces');
Route::get('mes-commandes', [CommandeController::class, 'mesCommandes'])->name('mes-commandes');
Route::get('commandes-recues', [CommandeController::class, 'commandesRecues'])->name('commandes-recues');
Route::patch('annonces/{annonce}/statut', [AnnonceMarketplaceController::class, 'updateStatut'])->name('annonces.statut');
Route::get('/marketplace', function () {
    return view('frontoffice.pages.marketplace.marketplace');
})->name('marketplace');
Route::get('/commandes-page', function () {
    return view('frontoffice.pages.commandes.commandes');
})->name('commandes.page');

Route::get('/api/mes-post-dechets', [AnnonceMarketplaceController::class, 'getUserPostDechets'])->name('api.mes-post-dechets');

/* =========================
 |  Troc – Backoffice
 * ========================= */
Route::resource('transactions-troc', TransactionTrocController::class);
Route::get('/troc', [PostDechetController::class, 'indexTroc'])->name('postdechets.troc');
Route::get('/postdechets/{post}/offres', [PostDechetController::class, 'showOffres'])->name('postdechets.offres');

/* OffreTroc Backoffice */
Route::prefix('offres-troc')->group(function () {
    Route::get('/', [OffreTrocController::class, 'index'])->name('offres-troc.index');
    Route::middleware('auth')->group(function () {
        Route::get('/create/{postId}', [OffreTrocController::class, 'create'])->name('offres-troc.create');
        Route::post('/{postId}', [OffreTrocController::class, 'store'])->name('offres-troc.store');
        Route::get('/{postId}', [OffreTrocController::class, 'show'])->name('offres-troc.show');
        Route::patch('/{id}/statut', [OffreTrocController::class, 'updateStatut'])->name('offres-troc.update-statut');
        Route::get('/{id}/edit', [OffreTrocController::class, 'edit'])->name('offres-troc.edit');
        Route::put('/{id}', [OffreTrocController::class, 'update'])->name('offres-troc.update');
        Route::delete('/{id}', [OffreTrocController::class, 'destroy'])->name('offres-troc.destroy');
        Route::delete('/{postId}/{id}/photos/{index}/destroy', [OffreTrocController::class, 'destroyPhoto'])->name('offres-troc.photo-destroy');
    });
});

/* =========================
 |  Troc – Frontoffice
 * ========================= */
Route::get('/home/troc', [PostDechetController::class, 'indexTrocFront'])->name('postdechets.troc.front');
Route::get('/home/postdechets/{post}/offres', [PostDechetController::class, 'showOffresFront'])->name('postdechets.offres.front');
Route::get('/home/offres-troc/thankyou', function() {
    return view('frontoffice.pages.offres-troc.thankyou');
})->name('offres-troc.thankyou');

/* OffreTroc Frontoffice */
Route::prefix('home/offres-troc')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', [OffreTrocController::class, 'indexFront'])->name('postdechets.troc-index.front');
        Route::get('/create/{postId}', [OffreTrocController::class, 'createFront'])->name('offres-troc.create.front');
        Route::post('/{postId}', [OffreTrocController::class, 'storeFront'])->name('offres-troc.storeFront');
        Route::get('/{postId}', [OffreTrocController::class, 'showFront'])->name('offres-troc.show.front');
        Route::patch('/{id}/statut', [OffreTrocController::class, 'updateStatutFront'])->name('offres-troc.update-statut.front');
        Route::get('/{id}/edit', [OffreTrocController::class, 'editFront'])->name('offres-troc.edit.front');
        Route::put('/{id}', [OffreTrocController::class, 'updateFront'])->name('offres-troc.update.front');
        Route::delete('/{id}', [OffreTrocController::class, 'destroyFront'])->name('offres-troc.destroy.front');
    });
});

/* =========================
 |  Frontoffice Waste Posts (CRUD)
 * ========================= */
Route::prefix('waste-posts')->name('front.waste-posts.')->group(function () {
    // Public
    Route::get('/', [PostDechetFrontController::class, 'index'])->name('index');

    // Protégé
    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostDechetFrontController::class, 'create'])->name('create');
        Route::post('/',      [PostDechetFrontController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [OffreTrocController::class, 'editFront'])->name('offres-troc.edit.front');
        Route::put('/{id}', [OffreTrocController::class, 'updateFront'])->name('offres-troc.update.front');

        Route::get('/{postDechet}/edit', [PostDechetFrontController::class, 'edit'])->name('edit');
        Route::put('/{postDechet}',      [PostDechetFrontController::class, 'update'])->name('update');
        Route::delete('/{postDechet}',   [PostDechetFrontController::class, 'destroy'])->name('destroy');
    });

    // Show public (contrainte pour ne pas “manger” /create)
    Route::get('/{postDechet}', [PostDechetFrontController::class, 'show'])
        ->whereNumber('postDechet')
        ->name('show');
});


/* =========================
 |  Donation
 * ========================= */
Route::middleware('auth')->group(function () {
    Route::resource('donations', DonationController::class);
    Route::get('mes-donations', [DonationController::class, 'mesDonations'])->name('mes-donations');
    Route::get('donations/{donation}/requests', [DonationController::class, 'showRequests'])->name('donations.showRequests');
    Route::post('donations/{donation}/request', [DonationController::class, 'requestDonation'])->name('donations.request');
    Route::post('donation-requests/{donationRequest}/accept', [DonationController::class, 'acceptRequest'])->name('donation-requests.accept');
    Route::post('donation-requests/{donationRequest}/reject', [DonationController::class, 'rejectRequest'])->name('donation-requests.reject');
    Route::get('my-requests', [DonationController::class, 'myRequests'])->name('donate.myRequests');
});

// Frontoffice donation routes
Route::get('/donate', [DonationController::class, 'frontLanding'])->name('donate.donationpage');
Route::get('/donate/create', [DonationController::class, 'frontCreate'])->name('donate.create');
Route::get('/donate/thankyou', [DonationController::class, 'frontThankyou'])->name('donate.thankyou');
Route::post('/donate/{donation}/take', [DonationController::class, 'takeDonation'])->name('donate.take');

/* =========================
 |  Propositions – Frontoffice
 * ========================= */
Route::prefix('mes-propositions')
    ->name('front.propositions.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/',                    [PropositionFrontController::class, 'index'])->name('index');
        Route::get('/create/{postDechet}', [PropositionFrontController::class, 'create'])->name('create');
        Route::post('/{postDechet}',       [PropositionFrontController::class, 'store'])->name('store');
        Route::get('/{proposition}/edit',  [PropositionFrontController::class, 'edit'])->name('edit');
        Route::put('/{proposition}',       [PropositionFrontController::class, 'update'])->name('update');
        Route::delete('/{proposition}',    [PropositionFrontController::class, 'destroy'])->name('destroy');
         Route::get('/recues', [\App\Http\Controllers\Front\PropositionFrontController::class, 'received'])
             ->name('received');
    });
    /* =========================
 |  Transactions Troc – Frontoffice
 * ========================= */
Route::prefix('home/transactions-troc')
    ->name('transactions-troc.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [TransactionTrocController::class, 'indexFront'])->name('index.front');
        Route::get('/{id}', [TransactionTrocController::class, 'showFront'])->name('show.front');
        Route::get('/{id}/edit', [TransactionTrocController::class, 'editFront'])->name('edit.front');
        Route::put('/{id}', [TransactionTrocController::class, 'updateFront'])->name('update.front');
    });
    Route::post('/favorites/toggle/{post}', [PostDechetController::class, 'toggleFavorite'])->name('favorites.toggle');
    // Backoffice Routes transaction troc
Route::prefix('transactions-troc')->group(function () {
    // List all transactions (Backoffice)
    Route::get('/', [TransactionTrocController::class, 'index'])->name('transactions-troc.index');
    
    // Show transaction details (Backoffice)
    Route::get('/{id}', [TransactionTrocController::class, 'show'])->name('transactions-troc.show');
    
    // Update transaction (Backoffice, assuming it’s used)
    Route::middleware('auth')->group(function () {
        Route::put('/{id}', [TransactionTrocController::class, 'update'])->name('transactions-troc.update');
    });
});