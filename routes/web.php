<?php

use Illuminate\Support\Facades\Route;
 
Route::get('/', function () {
    return view('welcome');

});

Route::get('/dashboard', function () {
    return view('backoffice.pages.dashboard');
})->name('dashboard');

Route::get('/home', function () {
    return view('frontoffice.pages.home');
})->name('home');

