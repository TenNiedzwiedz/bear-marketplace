<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class);

// Public listing browsing.
Route::get('/ogloszenia', [ListingController::class, 'index'])->name('listings.index');

// Public seller profile with their active listings.
Route::get('/sprzedawca/{user}', [SellerController::class, 'show'])->name('sellers.show');

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

    // Any signed-in user can report a listing or an account.
    Route::post('/zglos', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/wystaw', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/ogloszenia', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/ogloszenia/{listing}/edytuj', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/ogloszenia/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::patch('/ogloszenia/{listing}/zakoncz', [ListingController::class, 'end'])->name('listings.end');
    Route::patch('/ogloszenia/{listing}/wznow', [ListingController::class, 'reactivate'])->name('listings.reactivate');
    Route::delete('/ogloszenia/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
});

// Moderation panel — staff only.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'overview'])->name('overview');
    Route::get('/zgloszenia', [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/ogloszenia', [AdminDashboardController::class, 'listings'])->name('listings');
    Route::get('/uzytkownicy', [AdminDashboardController::class, 'users'])->name('users');

    // Listing moderation.
    Route::patch('/ogloszenia/{listing}/ukryj', [ModerationController::class, 'hideListing'])->name('listings.hide');
    Route::patch('/ogloszenia/{listing}/przywroc', [ModerationController::class, 'restoreListing'])->name('listings.restore');
    Route::delete('/ogloszenia/{listing}', [ModerationController::class, 'deleteListing'])->name('listings.destroy');

    // Account moderation.
    Route::patch('/uzytkownicy/{user}/zablokuj', [ModerationController::class, 'blockUser'])->name('users.block');
    Route::patch('/uzytkownicy/{user}/odblokuj', [ModerationController::class, 'unblockUser'])->name('users.unblock');

    // Report queue.
    Route::patch('/zgloszenia/{report}/rozpatrz', [ModerationController::class, 'resolveReport'])->name('reports.resolve');
    Route::patch('/zgloszenia/{report}/odrzuc', [ModerationController::class, 'dismissReport'])->name('reports.dismiss');
});

// Public listing detail — kept last so it doesn't shadow /ogloszenia/... actions.
Route::get('/ogloszenia/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::get('/design-cheatsheet', function () {
    return Inertia::render('DesignCheatsheet');
});
