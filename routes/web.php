<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostDechetController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\PropositionTransformationController;
use App\Http\Controllers\ProduitTransformeController;
use App\Http\Controllers\ProcessusTransformationController;

 
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

// Proposals (transformator)
Route::resource('proposition-transformations', PropositionTransformationController::class);

// Processes
Route::resource('processus-transformations', ProcessusTransformationController::class);

// Products
Route::resource('produit-transformes', ProduitTransformeController::class);