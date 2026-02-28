<?php

declare(strict_types=1);

namespace App\Services\Users;

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
     * Clear the permission caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }
}

