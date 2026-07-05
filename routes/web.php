<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class);

// Public listing browsing.
Route::get('/ogloszenia', [ListingController::class, 'index'])->name('listings.index');

// Guest-only auth screens.
Route::middleware('guest')->group(function () {
    Route::get('/rejestracja', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/rejestracja', [RegisteredUserController::class, 'store']);
    Route::get('/logowanie', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/logowanie', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/wyloguj', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

// Signed-in areas: the panel and creating or editing listings.
Route::middleware('auth')->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        Route::get('/', [DashboardController::class, 'overview'])->name('overview');
        Route::get('/ogloszenia', [DashboardController::class, 'listings'])->name('listings');
        Route::get('/wiadomosci', [DashboardController::class, 'messages'])->name('messages');
        Route::get('/konto', [DashboardController::class, 'account'])->name('account');
    });

    Route::get('/wystaw', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/ogloszenia', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/ogloszenia/{listing}/edytuj', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/ogloszenia/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::patch('/ogloszenia/{listing}/zakoncz', [ListingController::class, 'end'])->name('listings.end');
    Route::patch('/ogloszenia/{listing}/wznow', [ListingController::class, 'reactivate'])->name('listings.reactivate');
    Route::delete('/ogloszenia/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
});

// Public listing detail — kept last so it doesn't shadow /ogloszenia/... actions.
Route::get('/ogloszenia/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/design-cheatsheet', function () {
    return Inertia::render('DesignCheatsheet');
});
