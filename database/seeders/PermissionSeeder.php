<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CorePermission;
use App\Enums\DefinitionPermission;
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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $allCases = [...CorePermission::cases(), ...DefinitionPermission::cases()];

        foreach ($allCases as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission->value],
                [
                    'label'       => $permission->label(),
                    'description' => $permission->description(),
                    'guard_name'  => 'web',
                ]
            );
        }
    }
}
