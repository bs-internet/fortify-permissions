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
|
| auth        -> kullanıcı giriş yapmış mı
| auth.session-> geçerli oturum mu
| verified    -> email doğrulanmış mı
| activeUser  -> enum durumuna göre sisteme erişebilir mi
|
*/

Route::middleware([
    'auth',
    'auth.session',
    'verified',
    'activeUser',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return Inertia::render('app/Dashboard');
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | App Routes with Write Access
    |--------------------------------------------------------------------------
    */
    Route::middleware('writeAcces')->group(function () {
        // Buraya yazma izni gerektiren rotalar gelecek
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::name('profile.')->prefix('profile')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Profile Information
        |--------------------------------------------------------------------------
        */
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::patch('/edit', 'update')->middleware('writeAcces')->name('update');
        });

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */
        Route::controller(PasswordController::class)->group(function () {
            Route::get('/password', 'edit')->name('password.edit');
            Route::put('/password', 'update')->middleware(['writeAcces', 'throttle:6,1'])->name('password.update');
        });

        /*
        |--------------------------------------------------------------------------
        | Two Factor
        |--------------------------------------------------------------------------
        */
        Route::controller(TwoFactorAuthenticationController::class)->group(function () {
            Route::get('/two-factor', 'show')->name('twofactor.show');
        });

        /*
        |--------------------------------------------------------------------------
        | Sessions
        |--------------------------------------------------------------------------
        */
        Route::controller(SessionController::class)->group(function () {
            Route::get('/sessions', 'index')->name('sessions.index');
            Route::delete('/sessions/{log}', 'destroy')->middleware('writeAcces')->name('sessions.destroy');
            Route::delete('/sessions', 'destroyOther')->middleware('writeAcces')->name('sessions.destroyOther');
        });

        /*
        |--------------------------------------------------------------------------
        | Appearance
        |--------------------------------------------------------------------------
        */
        Route::get('/appearance', function () {
            return Inertia::render('app/profile/Appearance');
        })->name('appearance.edit');
    });
});
