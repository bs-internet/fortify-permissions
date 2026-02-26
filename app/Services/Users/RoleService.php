<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RoleUpdated;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class RoleService
{
    private const CACHE_KEY_ALL = 'roles_all';

    /**
     * Get all roles with their permissions.
     *
     * @return Collection<int, Role>
     */
    public function getAll(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_ALL, function () {
            return Role::query()
                ->where('guard_name', 'web')
                ->with('permissions:id,name,label')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Store a new role.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Role
    {
        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['guard_name'] = 'web';

        $role = Role::create($data);

        if (!empty($permissionIds)) {
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
        }

        $this->clearCache();
        RoleCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $role;
    }

    /**
     * Update an existing role.
     *
     * @param array<string, mixed> $data
     */
    public function update(Role $role, User $user, array $data, string $ipAddress, string $userAgent): Role
    {
        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);

        $originalData = $role->only(array_keys($data));

        $role->fill($data);
        $role->save();

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            $this->clearCache();
            RoleUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $role;
    }

    /**
     * Delete a role.
     */
    public function delete(Role $role, User $user, string $ipAddress, string $userAgent): void
    {
        $changes = [
            'deleted' => $role->only(['name', 'label']),
        ];

        $role->delete();

        $this->clearCache();
        RoleDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the role caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }
}

