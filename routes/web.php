<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\AuthenticatedSessionController;

 
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
// Login
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Register
Route::get('/register',  [AuthenticatedSessionController::class, 'register'])->name('register');
Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');