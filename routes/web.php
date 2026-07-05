<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class);

Route::prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [DashboardController::class, 'overview'])->name('overview');
    Route::get('/ogloszenia', [DashboardController::class, 'listings'])->name('listings');
    Route::get('/wiadomosci', [DashboardController::class, 'messages'])->name('messages');
    Route::get('/konto', [DashboardController::class, 'account'])->name('account');
});
Route::get('/wystaw', [ListingController::class, 'create'])->name('listings.create');

Route::get('/ogloszenia', [ListingController::class, 'index'])->name('listings.index');
Route::post('/ogloszenia', [ListingController::class, 'store'])->name('listings.store');
Route::get('/ogloszenia/{listing}/edytuj', [ListingController::class, 'edit'])->name('listings.edit');
Route::put('/ogloszenia/{listing}', [ListingController::class, 'update'])->name('listings.update');
Route::get('/ogloszenia/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/design-cheatsheet', function () {
    return Inertia::render('DesignCheatsheet');
});
