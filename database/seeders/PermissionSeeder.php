<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'user.management',
                'label' => 'Kullanıcı Yönetimi',
                'description' => 'Kullanıcıları görme, oluşturma, düzenleme ve silme yetkisi.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'role.management',
                'label' => 'Rol Yönetimi',
                'description' => 'Rolleri görme, oluşturma, düzenleme ve silme yetkisi.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'permission.management',
                'label' => 'İzin Yönetimi',
                'description' => 'İzinleri görme, oluşturma, düzenleme ve silme yetkisi.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'definition.management',
                'label' => 'Tanımlama Yönetimi',
                'description' => 'Dil, para birimi ve birim gibi tanımlamaları yönetme yetkisi.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'setting.management',
                'label' => 'Ayarlar Yönetimi',
                'description' => 'Sistem ayarlarını yönetme yetkisi.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'activity.view',
                'label' => 'Aktivite Görüntüleme',
                'description' => 'Sistem loglarını ve aktivitelerini görme yetkisi.',
                'guard_name' => 'web',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
