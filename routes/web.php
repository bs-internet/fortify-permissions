<?php


use App\Http\Controllers\Definitions\{
    CountryController,
    CurrencyController,
    LanguageController,
    TaxController,
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
    Route::middleware('writeAccess')->group(function () {

        // Users
        Route::name('users.')->prefix('users')->group(function () {

            // Users
            Route::controller(UserController::class)->group(function () {
                Route::get('/users', 'index')->name('index');
                Route::get('/users/create', 'create')->name('create');
                Route::post('/users', 'store')->middleware('throttle:sensitiveActions')->name('store');
                Route::get('/users/{user}/edit', 'edit')->name('edit');
                Route::put('/users/{user}', 'update')->middleware('throttle:sensitiveActions')->name('update');
                Route::post('/users/{user}/verify-email', 'verifyEmail')->middleware('throttle:sensitiveActions')->name('verify-email');
                Route::delete('/users/{user}', 'destroy')->middleware('throttle:sensitiveActions')->name('destroy');
            });

            // Roles
            Route::controller(RoleController::class)->group(function () {
                Route::get('/roles', 'index')->name('roles.index');
                Route::get('/roles/create', 'create')->name('roles.create');
                Route::post('/roles', 'store')->middleware('throttle:sensitiveActions')->name('roles.store');
                Route::get('/roles/{role}/edit', 'edit')->name('roles.edit');
                Route::put('/roles/{role}', 'update')->middleware('throttle:sensitiveActions')->name('roles.update');
                Route::delete('/roles/{role}', 'destroy')->middleware('throttle:sensitiveActions')->name('roles.destroy');
            });

            // Permissions
            Route::controller(PermissionController::class)->group(function () {
                Route::get('/permissions', 'index')->name('permissions.index');
                Route::put('/permissions/{permission}', 'update')->middleware('throttle:sensitiveActions')->name('permissions.update');
            });
        });

        // Settings
        Route::name('settings.')->prefix('settings')->group(function () {

            // General Settings
            Route::controller(SettingsController::class)->group(function () {
                Route::get('/edit', 'index')->name('index');
                Route::post('/update', 'update')->middleware('throttle:sensitiveActions')->name('update');
            });

            // Definitions
            Route::name('definitions.')->prefix('definitions')->group(function () {

                // Units
                Route::controller(UnitController::class)->group(function () {
                    Route::get('/units', 'index')->name('units.index');
                    Route::post('/units', 'store')->middleware('throttle:sensitiveActions')->name('units.store');
                    Route::put('/units/{unit}', 'update')->middleware('throttle:sensitiveActions')->name('units.update');
                    Route::delete('/units/{unit}', 'destroy')->middleware('throttle:sensitiveActions')->name('units.destroy');
                });

                // Languages
                Route::controller(LanguageController::class)->group(function () {
                    Route::get('/languages', 'index')->name('languages.index');
                    Route::post('/languages', 'store')->middleware('throttle:sensitiveActions')->name('languages.store');
                    Route::put('/languages/{language}', 'update')->middleware('throttle:sensitiveActions')->name('languages.update');
                    Route::delete('/languages/{language}', 'destroy')->middleware('throttle:sensitiveActions')->name('languages.destroy');
                });

                // Currencies
                Route::controller(CurrencyController::class)->group(function () {
                    Route::get('/currencies', 'index')->name('currencies.index');
                    Route::post('/currencies', 'store')->middleware('throttle:sensitiveActions')->name('currencies.store');
                    Route::put('/currencies/{currency}', 'update')->middleware('throttle:sensitiveActions')->name('currencies.update');
                    Route::delete('/currencies/{currency}', 'destroy')->middleware('throttle:sensitiveActions')->name('currencies.destroy');
                });

                // Countries
                Route::controller(CountryController::class)->group(function () {
                    Route::get('/countries', 'index')->name('countries.index');
                    Route::post('/countries', 'store')->middleware('throttle:sensitiveActions')->name('countries.store');
                    Route::put('/countries/{country}', 'update')->middleware('throttle:sensitiveActions')->name('countries.update');
                    Route::delete('/countries/{country}', 'destroy')->middleware('throttle:sensitiveActions')->name('countries.destroy');
                });

                // Taxes
                Route::controller(TaxController::class)->group(function () {
                    Route::get('/taxes', 'index')->name('taxes.index');
                    Route::post('/taxes', 'store')->middleware('throttle:sensitiveActions')->name('taxes.store');
                    Route::put('/taxes/{tax}', 'update')->middleware('throttle:sensitiveActions')->name('taxes.update');
                    Route::delete('/taxes/{tax}', 'destroy')->middleware('throttle:sensitiveActions')->name('taxes.destroy');
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
            Route::patch('/edit', 'update')->middleware('writeAccess')->name('update');
        });

        // Password
        Route::controller(PasswordController::class)->group(function () {
            Route::get('/password', 'edit')->name('password.edit');
            Route::put('/password', 'update')->middleware(['writeAccess', 'throttle:6,1'])->name('password.update');
        });

        // Two Factor Authentication
        Route::controller(TwoFactorAuthenticationController::class)->group(function () {
            Route::get('/two-factor', 'show')->name('twofactor.show');
        });

        // Notifications
        Route::controller(NotificationController::class)->group(function () {
            Route::get('/notifications', 'index')->name('notifications.index');
            Route::get('/notifications/archived', 'archived')->name('notifications.archived');
            Route::post('/notifications/mark-as-read', 'markAsRead')->middleware('writeAccess')->name('notifications.markAsRead');
            Route::post('/notifications/mark-all-read', 'markAllAsRead')->middleware('writeAccess')->name('notifications.markAllAsRead');
            Route::post('/notifications/archive', 'archive')->middleware('writeAccess')->name('notifications.archive');
            Route::post('/notifications/archive-all-read', 'archiveAllRead')->middleware('writeAccess')->name('notifications.archiveAllRead');
        });

        // Sessions
        Route::controller(SessionController::class)->group(function () {
            Route::get('/sessions', 'index')->name('sessions.index');
            Route::delete('/sessions/{log}', 'destroy')->middleware('writeAccess')->name('sessions.destroy');
            Route::delete('/sessions', 'destroyOther')->middleware('writeAccess')->name('sessions.destroyOther');
        });

        // Appearance
        Route::get('/appearance', function () {
            return Inertia::render('app/profile/Appearance');
        })->name('appearance.edit');
    });
});
