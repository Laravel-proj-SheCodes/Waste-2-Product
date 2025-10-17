<?php
use Illuminate\Support\Facades\Route;

/** Backoffice contrôleurs existants */
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\UserController;


/** Auth */
use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

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

use App\Http\Controllers\EcoBotGroqController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/* =========================
 |  Pages simples
 * ========================= */
Route::view('/home', 'frontoffice.pages.home')->name('home')->middleware(['auth', 'verified']);
Route::get('/', fn () => redirect()->route('home'));


/* =========================
 |  Backoffice commun
 * ========================= */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('backoffice.pages.dashboard'))->name('dashboard');

    Route::resource('postdechets', PostDechetController::class);
    Route::resource('propositions', PropositionController::class);
    Route::resource('users', UserController::class)->only(['index', 'show']);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');

    // Proposals (transformator)
    //Route::resource('proposition-transformations', PropositionTransformationController::class);

    // Processes
    //Route::resource('processus-transformations', ProcessusTransformationController::class);

    // Products
    //Route::resource('produit-transformes', ProduitTransformeController::class);
});

/* =========================
 |  Authentification
 * ========================= */
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Routes pour la vérification d'email
Route::get('/email/verify', function () {
    return view('frontoffice.authentication.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Lien de vérification renvoyé !');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Marquer une notification comme lue + rediriger
// routes/web.php
Route::get('/notifications/read/{id}', function ($id) {
    $n = Auth::user()->notifications()->findOrFail($id);
    $n->markAsRead();

    $url = $n->data['url'] ?? null;
    if ($url) return redirect($url);

    $propId = $n->data['proposition_id'] ?? null;
    return redirect()->route('front.propositions.received', ['highlight' => $propId]);
})->middleware('auth')->name('notifications.read');

 

// (Optionnel) Route de debug pour vérifier que la cloche fonctionne
Route::get('/debug-notif', function () {
    $u = Auth::user(); abort_unless($u, 403);
    $u->notify(new \App\Notifications\ProposalReceived(
        postId: 999,
        propositionId: 999,
        senderName: 'DEBUG',
        postTitle: 'Test'
    ));
    return 'OK';
})->middleware('auth');
/* =========================
 |  Marketplace
 * ========================= */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('annonces', AnnonceMarketplaceController::class);
    Route::resource('commandes', CommandeController::class);
    Route::get('mes-annonces', [AnnonceMarketplaceController::class, 'mesAnnonces'])->name('mes-annonces');
    Route::get('mes-commandes', [CommandeController::class, 'mesCommandes'])->name('mes-commandes');
    Route::get('commandes-recues', [CommandeController::class, 'commandesRecues'])->name('commandes-recues');
    Route::patch('annonces/{annonce}/statut', [AnnonceMarketplaceController::class, 'updateStatut'])->name('annonces.statut');
    Route::get('annonces/{annonce}/commandes', [AnnonceMarketplaceController::class, 'showCommandes'])
            ->name('annonces.commandes');
});



Route::get('/marketplace', function () {
    return view('frontoffice.pages.marketplace.marketplace');
})->name('marketplace');
Route::get('/commandes-page', function () {
    return view('frontoffice.pages.commandes.commandes');
})->name('commandes.page');

Route::get('/api/mes-post-dechets', [AnnonceMarketplaceController::class, 'getUserPostDechets'])->name('api.mes-post-dechets');
//2fa 
Route::middleware(['auth', 'verified'])->group(function () {
    // 2FA Settings
    Route::get('/profile/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/profile/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/profile/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/profile/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// 2FA Verification during login (after auth but before verified)
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerify'])->name('two-factor.verify-show');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verifyLogin'])->name('two-factor.verify-login');
});

/* =========================
 |  Troc – Backoffice
 * ========================= */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transactions-troc', TransactionTrocController::class);
    Route::get('/troc', [PostDechetController::class, 'indexTroc'])->name('postdechets.troc');
    Route::get('/postdechets/{post}/offres', [PostDechetController::class, 'showOffres'])->name('postdechets.offres');

    /* OffreTroc Backoffice */
    Route::prefix('offres-troc')->group(function () {
        Route::get('/', [OffreTrocController::class, 'index'])->name('offres-troc.index');
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
Route::prefix('home/offres-troc')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [OffreTrocController::class, 'indexFront'])->name('postdechets.troc-index.front');
    Route::get('/create/{postId}', [OffreTrocController::class, 'createFront'])->name('offres-troc.create.front');
    Route::post('/{postId}', [OffreTrocController::class, 'storeFront'])->name('offres-troc.storeFront');
    Route::get('/{postId}', [OffreTrocController::class, 'showFront'])->name('offres-troc.show.front');
    Route::patch('/{id}/statut', [OffreTrocController::class, 'updateStatutFront'])->name('offres-troc.update-statut.front');
    Route::get('/{id}/edit', [OffreTrocController::class, 'editFront'])->name('offres-troc.edit.front');
    Route::put('/{id}', [OffreTrocController::class, 'updateFront'])->name('offres-troc.update.front');
    Route::delete('/{id}', [OffreTrocController::class, 'destroyFront'])->name('offres-troc.destroy.front');
});

/* =========================
 |  Frontoffice Waste Posts (CRUD)
 * ========================= */
Route::prefix('waste-posts')->name('front.waste-posts.')->group(function () {
    // Public
    Route::get('/', [PostDechetFrontController::class, 'index'])->name('index');

    // Protégé
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [PostDechetFrontController::class, 'create'])->name('create');
        Route::post('/',      [PostDechetFrontController::class, 'store'])->name('store');

        Route::get('/{postDechet}/edit', [PostDechetFrontController::class, 'edit'])->name('edit');
        Route::put('/{postDechet}',      [PostDechetFrontController::class, 'update'])->name('update');
        Route::delete('/{postDechet}',   [PostDechetFrontController::class, 'destroy'])->name('destroy');
    });
    Route::get('/{postDechet}', [PostDechetFrontController::class, 'show'])
        ->whereNumber('postDechet')
        ->name('show');
});

/* =========================
 |  Donation
 * ========================= */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('donations', DonationController::class);
    Route::get('mes-donations', [DonationController::class, 'myDonationsFront'])->name('mes-donations');
    Route::get('donations/{donation}/requests', [DonationController::class, 'showRequests'])->name('donations.showRequests');
    Route::post('donations/{donation}/request', [DonationController::class, 'requestDonation'])->name('donations.request');
    Route::post('donation-requests/{donationRequest}/accept', [DonationController::class, 'acceptRequest'])->name('donation-requests.accept');
    Route::post('donation-requests/{donationRequest}/reject', [DonationController::class, 'rejectRequest'])->name('donation-requests.reject');
    Route::get('my-requests', [DonationController::class, 'myRequests'])->name('donate.myRequests');
});

// Frontoffice donation routes
 // Frontoffice routes
    Route::get('/donate', [DonationController::class, 'frontLanding'])->name('donate.donationpage');
    Route::get('/donate/create', [DonationController::class, 'frontCreate'])->name('donate.create');
    Route::get('/donate/thankyou', [DonationController::class, 'frontThankyou'])->name('donate.thankyou');
    Route::post('/donate/{donation}/take', [DonationController::class, 'takeDonation'])->name('donate.take');
    Route::get('/donate/{donation}', [DonationController::class, 'show'])->name('donate.show');
    Route::get('/donate/{donation}/edit', [DonationController::class, 'edit'])->name('donate.edit');
    Route::get('/donate/{donation}/requests', [DonationController::class, 'showRequests'])->name('donate.showRequests');

    
/* =========================
 |  Propositions – Frontoffice
 * ========================= */
Route::prefix('mes-propositions')
    ->name('front.propositions.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/',                      [\App\Http\Controllers\Front\PropositionFrontController::class, 'index'])->name('index');
        Route::get('/create/{postDechet}',   [\App\Http\Controllers\Front\PropositionFrontController::class, 'create'])->name('create');
        Route::post('/{postDechet}',         [\App\Http\Controllers\Front\PropositionFrontController::class, 'store'])->name('store');
        Route::get('/{proposition}/edit',    [\App\Http\Controllers\Front\PropositionFrontController::class, 'edit'])->name('edit');
        Route::put('/{proposition}',         [\App\Http\Controllers\Front\PropositionFrontController::class, 'update'])->name('update');
        Route::delete('/{proposition}',      [\App\Http\Controllers\Front\PropositionFrontController::class, 'destroy'])->name('destroy');

        Route::get('/recues', [PropositionFrontController::class, 'received'])->name('received');
        Route::post('/{proposition}/accept', [\App\Http\Controllers\Front\PropositionFrontController::class, 'accept'])->name('accept');
        Route::post('/{proposition}/reject', [\App\Http\Controllers\Front\PropositionFrontController::class, 'reject'])->name('reject');
    });

/* =========================
 |  Transactions Troc – Frontoffice
 * ========================= */

Route::post('/analyze-image', [PostDechetFrontController::class, 'analyze'])->name('front.waste-posts.analyze');

Route::prefix('home/transactions-troc')
    ->name('transactions-troc.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', [TransactionTrocController::class, 'indexFront'])->name('index.front');
        Route::get('/{id}', [TransactionTrocController::class, 'showFront'])->name('show.front');
        Route::get('/{id}/edit', [TransactionTrocController::class, 'editFront'])->name('edit.front');
        Route::put('/{id}', [TransactionTrocController::class, 'updateFront'])->name('update.front');
    });
Route::post('/favorites/toggle/{post}', [PostDechetController::class, 'toggleFavorite'])->name('favorites.toggle');

/* =========================
 |  API pour transformation processus
 * ========================= */

 // Proposals (transformator)
Route::resource('proposition-transformations', PropositionTransformationController::class);

// Processes
Route::resource('processus-transformations', ProcessusTransformationController::class);

// Products
Route::resource('produit-transformes', ProduitTransformeController::class);


Route::post('/eco-bot', [EcoBotGroqController::class, 'chat'])->name('eco-bot.chat');


/** Mot de passe oublié / Réinitialisation */
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

// Formulaire "mot de passe oublié"
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Envoi de l'email de reset
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Formulaire de reset avec token
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Soumission du nouveau mot de passe
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
});



Route::get('/test-token', function () {
    return env('HUGGINGFACE') ? 'Token OK' : 'Token vide';
});
Route::get('/test-path', function () {
    return base_path('.env');
});

//Route::post('/waste-posts/visual-search', [PostDechetFrontController::class, 'analyze'])->name('front.waste-posts.visual-search');

Route::post('/waste-posts/{post}/estimate', [PostDechetFrontController::class, 'analyze'])->name('front.waste-posts.estimate');
Route::post('/waste-posts/{postDechet}/estimate-ai', [PostDechetFrontController::class, 'estimateAI'])->name('front.waste-posts.estimateAI');
Route::post('/waste-posts/visual-search', [PostDechetFrontController::class, 'visualSearch'])->name('front.waste-posts.visual-search');

