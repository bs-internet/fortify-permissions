<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RoleUpdated;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoleService
{
    private const CACHE_KEY_SELECT = 'roles_select';

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Role::class);

        return Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'Super Admin')
            ->with('permissions:id,name,label')
            ->orderBy('name')
            ->paginate(config('otomasyon.pagination.per_page', 15));
    }

    /**
     * Tüm rolleri select/checkbox için döndür (Super Admin hariç, paginate yok).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Role>
     */
    public function allForSelect(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_SELECT, function () {
            return Role::query()
                ->where('guard_name', 'web')
                ->where('name', '!=', 'Super Admin')
                ->orderBy('name')
                ->get(['id', 'name', 'label', 'description']);
        });
    }

    /**
     * Store a new role.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Role
    {
        Gate::authorize('create', Role::class);

        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['guard_name'] = 'web';

        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['label']);
        }

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
        Gate::authorize('update', $role);

        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);
        unset($data['name']);

        $originalData = $role->only(['label', 'description']);

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
        Gate::authorize('delete', $role);

        if ($role->users()->count() > 0) {
            throw new AuthorizationException('Bu role atanmış kullanıcılar var, önce kullanıcıları kaldırın.');
        }

        if ($role->name === 'Super Admin' || $role->id === 1) {
            throw new AuthorizationException('Sistem yöneticisi rolü silinemez.');
        }

        $changes = [
            'deleted' => $role->only(['name', 'label']),
        ];

        $role->syncPermissions([]);
        $role->delete();

        $this->clearCache();
        RoleDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the role caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_SELECT);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
