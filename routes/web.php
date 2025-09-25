<?php

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
