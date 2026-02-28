<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Spatie'nin izin önbelleğini (cache) temizliyoruz.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Enum içindeki tüm durumları (cases) tek tek dönüyoruz.
        foreach (PermissionEnum::cases() as $permission) {
            Permission::updateOrCreate(
                // 'name' değerine göre ara (Sözlüğündeki teknik isim: user.management vb.)
                ['name' => $permission->value],
                // Bulamazsan oluştur, bulursan etiket ve açıklamaları güncelle
                [
                    'label'       => $permission->label(),
                    'description' => $permission->description(),
                    'guard_name'  => 'web',
                ]
            );
        }
    }
}
