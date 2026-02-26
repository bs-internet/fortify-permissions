<?php


use App\Http\Controllers\Definitions\{
    CurrencyController,
    LanguageController,
    UnitController
};
use App\Http\Controllers\Settings\{
    ActivityController,
    SettingsController
};
use App\Http\Controllers\Profile\{
    NotificationController,
    PasswordController,
    ProfileController,
    SessionController,
    TwoFactorAuthenticationController
};
use App\Http\Controllers\Users\{
    PermissionController,
    RoleController,
    UserController
};
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

        // Users
        Route::name('users.')->prefix('users')->group(function () {

            // Users
            Route::controller(UserController::class)->group(function () {
                Route::get('/users', 'index')->name('index');
                Route::post('/users', 'store')->name('store');
                Route::put('/users/{user}', 'update')->name('update');
                Route::delete('/users/{user}', 'destroy')->name('destroy');
            });

            // Roles
            Route::controller(RoleController::class)->group(function () {
                Route::get('/roles', 'index')->name('roles.index');
                Route::post('/roles', 'store')->name('roles.store');
                Route::put('/roles/{role}', 'update')->name('roles.update');
                Route::delete('/roles/{role}', 'destroy')->name('roles.destroy');
            });

            // Permissions
            Route::controller(PermissionController::class)->group(function () {
                Route::get('/permissions', 'index')->name('permissions.index');
                Route::post('/permissions', 'store')->name('permissions.store');
                Route::put('/permissions/{permission}', 'update')->name('permissions.update');
                Route::delete('/permissions/{permission}', 'destroy')->name('permissions.destroy');
            });
        });

        // Settings
        Route::name('settings.')->prefix('settings')->group(function () {

            // General Settings
            Route::controller(SettingsController::class)->group(function () {
                Route::get('/edit', 'index')->name('index');
                Route::post('/update', 'update')->name('update');
            });

            // Definitions
            Route::name('definitions.')->prefix('definitions')->group(function () {

                // Units
                Route::controller(UnitController::class)->group(function () {
                    Route::get('/units', 'index')->name('units.index');
                    Route::post('/units', 'store')->name('units.store');
                    Route::put('/units/{unit}', 'update')->name('units.update');
                    Route::delete('/units/{unit}', 'destroy')->name('units.destroy');
                });

                // Languages
                Route::controller(LanguageController::class)->group(function () {
                    Route::get('/languages', 'index')->name('languages.index');
                    Route::post('/languages', 'store')->name('languages.store');
                    Route::put('/languages/{language}', 'update')->name('languages.update');
                    Route::delete('/languages/{language}', 'destroy')->name('languages.destroy');
                });

                // Currencies
                Route::controller(CurrencyController::class)->group(function () {
                    Route::get('/currencies', 'index')->name('currencies.index');
                    Route::post('/currencies', 'store')->name('currencies.store');
                    Route::put('/currencies/{currency}', 'update')->name('currencies.update');
                    Route::delete('/currencies/{currency}', 'destroy')->name('currencies.destroy');
                });
            });

            // Activities
            Route::controller(ActivityController::class)->group(function () {
                Route::get('/activities', 'index')->name('activities.index');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::name('profile.')->prefix('profile')->group(function () {

        // Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::patch('/edit', 'update')->middleware('writeAcces')->name('update');
        });

        // Password
        Route::controller(PasswordController::class)->group(function () {
            Route::get('/password', 'edit')->name('password.edit');
            Route::put('/password', 'update')->middleware(['writeAcces', 'throttle:6,1'])->name('password.update');
        });

        // Two Factor Authentication
        Route::controller(TwoFactorAuthenticationController::class)->group(function () {
            Route::get('/two-factor', 'show')->name('twofactor.show');
        });

        // Notifications
        Route::controller(NotificationController::class)->group(function () {
            Route::get('/notifications', 'index')->name('notifications.index');
            Route::get('/notifications/archived', 'archived')->name('notifications.archived');
            Route::post('/notifications/mark-as-read', 'markAsRead')->middleware('writeAcces')->name('notifications.markAsRead');
            Route::post('/notifications/mark-all-read', 'markAllAsRead')->middleware('writeAcces')->name('notifications.markAllAsRead');
            Route::post('/notifications/archive', 'archive')->middleware('writeAcces')->name('notifications.archive');
            Route::post('/notifications/archive-all-read', 'archiveAllRead')->middleware('writeAcces')->name('notifications.archiveAllRead');
        });

        // Sessions
        Route::controller(SessionController::class)->group(function () {
            Route::get('/sessions', 'index')->name('sessions.index');
            Route::delete('/sessions/{log}', 'destroy')->middleware('writeAcces')->name('sessions.destroy');
            Route::delete('/sessions', 'destroyOther')->middleware('writeAcces')->name('sessions.destroyOther');
        });

        // Appearance
        Route::get('/appearance', function () {
            return Inertia::render('app/profile/Appearance');
        })->name('appearance.edit');
    });
});
