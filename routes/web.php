<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Profile\PasswordController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SessionController;
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
    | Settings
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::name('profile.')->prefix('profile')->group(function () {

        // Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::patch('/edit', 'update')->name('update');
        });

        // Password
        Route::controller(PasswordController::class)->group(function () {
            Route::get('/password', 'edit')->name('password.edit');
            Route::put('/password', 'update')
                ->middleware('throttle:6,1')
                ->name('password.update');
        });

        // Two Factor
        Route::controller(TwoFactorAuthenticationController::class)->group(function () {
            Route::get('/two-factor', 'show')->name('twofactor.show');
        });

        // Sessions
        Route::controller(SessionController::class)->group(function () {
            Route::get('/sessions', 'index')->name('sessions.index');
            Route::delete('/sessions/{log}', 'destroy')->name('sessions.destroy');
            Route::delete('/sessions', 'destroyOther')->name('sessions.destroyOther');
        });

        // Appearance
        Route::get('/appearance', function () {
            return Inertia::render('app/profile/Appearance');
        })->name('appearance.edit');

    });
});
