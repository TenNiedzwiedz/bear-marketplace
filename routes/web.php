<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class);
Route::get('/ogloszenia', [ListingController::class, 'index'])->name('listings.index');
Route::get('/ogloszenia/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/design-cheatsheet', function () {
    return Inertia::render('DesignCheatsheet');
});
