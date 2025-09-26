<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Front\PostDechetFrontController;

// Accueil
Route::get('/', fn () => view('welcome'));
Route::get('/home', fn () => view('frontoffice.pages.home'))->name('home');

// Backoffice (existant)
Route::get('/dashboard', fn () => view('backoffice.pages.dashboard'))->name('dashboard');
Route::resource('postdechets', PostDechetController::class);
Route::resource('propositions', PropositionController::class);

// Auth
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Liens placeholder
Route::view('/troc', 'frontoffice.pages.stub')->name('troc.index');
Route::view('/transformations', 'frontoffice.pages.stub')->name('transformations.index');
Route::view('/donations', 'frontoffice.pages.stub')->name('donations.index');
Route::view('/marketplace', 'frontoffice.pages.stub')->name('marketplace.index');

// ---------------------
// Frontoffice Waste Posts
// ---------------------
Route::prefix('waste-posts')->name('front.waste-posts.')->group(function () {
    // liste (public)
    Route::get('/', [PostDechetFrontController::class, 'index'])->name('index');

    // --- IMPORTANT : les routes "create/edit" AVANT la route paramétrée ---
    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostDechetFrontController::class, 'create'])->name('create');
        Route::post('/',      [PostDechetFrontController::class, 'store'])->name('store');

        Route::get('/{postDechet}/edit', [PostDechetFrontController::class, 'edit'])->name('edit');
        Route::put('/{postDechet}',      [PostDechetFrontController::class, 'update'])->name('update');
        Route::delete('/{postDechet}',   [PostDechetFrontController::class, 'destroy'])->name('destroy');
    });

    // détail (public) – on contraint le paramètre pour éviter de “manger” /create
    Route::get('/{postDechet}', [PostDechetFrontController::class, 'show'])
        ->whereNumber('postDechet')   // si tu utilises l'id numérique
        ->name('show');
});
