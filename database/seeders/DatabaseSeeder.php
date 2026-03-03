<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Tax;
use App\Models\User;
use App\Services\Settings\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefinitionsSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $defaultLanguage = Language::where('code', 'tr')->first();
        $defaultCountry = Country::where('code', 'TR')->first();
        $defaultCurrency = Currency::where('code', 'TRY')->first();
        $defaultTax = Tax::where('name', 'KDV %20')->first();

        /** @var SettingService $settingService */
        $settingService = app(SettingService::class);
        $settingService->set('default_language', $defaultLanguage?->id, 'string');
        $settingService->set('default_country', $defaultCountry?->id, 'string');
        $settingService->set('default_currency', $defaultCurrency?->id, 'string');
        $settingService->set('default_tax', $defaultTax?->id, 'string');

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
                'language_id' => $defaultLanguage?->id,
                'title' => 'Süper Yönetici',
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Super Admin');

        $adminUser = User::firstOrCreate(
            ['email' => 'yonetici@admin.com'],
            [
                'name' => 'Yönetici',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
                'language_id' => $defaultLanguage?->id,
                'title' => 'Sistem Yöneticisi',
                'email_verified_at' => now(),
            ]
        );

        $adminUser->assignRole('Admin');
    }
}
