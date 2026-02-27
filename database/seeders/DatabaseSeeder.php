<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\User;
use App\Enums\UserStatus;
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
            LanguageSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $defaultLanguage = Language::where('is_default', true)->first();

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
    }
}
