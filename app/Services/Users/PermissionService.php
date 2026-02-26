<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\PermissionCreated;
use App\Events\PermissionDeleted;
use App\Events\PermissionUpdated;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    /**
     * Get all permissions.
     *
     * @return Collection<int, Permission>
     */
    public function getAll(): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a new permission.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Permission
    {
        $data['guard_name'] = 'web';

        $permission = Permission::create($data);

        PermissionCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $permission;
    }

    /**
     * Update an existing permission.
     *
     * @param array<string, mixed> $data
     */
    public function update(Permission $permission, User $user, array $data, string $ipAddress, string $userAgent): Permission
    {
        $originalData = $permission->only(array_keys($data));

        $permission->fill($data);
        $permission->save();

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] != $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            PermissionUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $permission;
    }

    /**
     * Delete a permission.
     */
    public function delete(Permission $permission, User $user, string $ipAddress, string $userAgent): void
    {
        $changes = [
            'deleted' => $permission->only(['name', 'label']),
        ];

        $permission->delete();

        PermissionDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }
}
