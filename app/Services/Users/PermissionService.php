<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\PermissionCreated;
use App\Events\PermissionDeleted;
use App\Events\PermissionUpdated;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PermissionService
{
    private const CACHE_KEY_ALL = 'permissions_all';

    /**
     * @return Collection<int, Permission>
     */
    public function all(): Collection
    {
        Gate::authorize('viewAny', Permission::class);

        return Cache::rememberForever(self::CACHE_KEY_ALL, function () {
            return Permission::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Store a new permission.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Permission
    {
        Gate::authorize('create', Permission::class);

        $data['guard_name'] = 'web';

        $permission = Permission::create($data);

        $this->clearCache();
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
        Gate::authorize('update', $permission);

        $originalData = $permission->only(array_keys($data));

        $permission->fill($data);
        $permission->save();

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
            PermissionUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $permission;
    }

    /**
     * Delete a permission.
     */
    public function delete(Permission $permission, User $user, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $permission);

        $changes = [
            'deleted' => $permission->only(['name', 'label']),
        ];

        $permission->delete();

        $this->clearCache();
        PermissionDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the permission caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }
}

