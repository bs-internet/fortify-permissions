<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'label' => 'Süper Yönetici',
                'description' => 'Sistemdeki tüm yetkilere sahip en üst düzey yönetici.',
                'guard_name' => 'web',
            ]
        );

        $admin = Role::updateOrCreate(
            ['name' => 'Admin'],
            [
                'label' => 'Yönetici',
                'description' => 'Kullanıcı ve rol yönetimi dahil genel sistem yönetim yetkilerine sahip.',
                'guard_name' => 'web',
            ]
        );

        $admin->syncPermissions(Permission::all());
    }
}
