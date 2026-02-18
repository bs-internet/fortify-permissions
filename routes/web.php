<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Profile\PasswordController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\TwoFactorAuthenticationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ana sayfa direkt login'e yönlendir
Route::redirect('/', '/login');



/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'auth.session', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('app/Dashboard');
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::redirect('/profile', '/profile/edit');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile/edit', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Password
    Route::get('/profile/password', [PasswordController::class, 'edit'])
        ->name('user-password.edit');

    Route::put('/profile/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    // Appearance
    Route::get('/profile/appearance', function () {
        return Inertia::render('app/profile/Appearance');
    })->name('appearance.edit');

    // Two Factor
    Route::get('/profile/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
});
