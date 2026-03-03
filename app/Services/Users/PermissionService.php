<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Enums\CorePermission;
use App\Enums\DefinitionPermission;
use App\Events\PermissionUpdated;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    private const CACHE_KEY_ALL = 'permissions_all';

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Permission::class);
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(config('otomasyon.pagination.per_page'));
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
     * Get all permissions grouped by their module prefix.
     *
     * @return array<string, Collection<int, Permission>>
     */
    public function groupedAll(): array
    {
        Gate::authorize('viewAny', Permission::class);

        $moduleLabels = array_merge(
            CorePermission::moduleLabels(),
            DefinitionPermission::moduleLabels(),
        );

        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) use ($moduleLabels) {
                $name = $permission->name;
                $moduleKey = str_contains($name, '.')
                    ? explode('.', $name)[0]
                    : 'other';

                return $moduleLabels[$moduleKey] ?? ucfirst($moduleKey);
            })->toArray();
    }

    /**
     * Clear the permission caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

