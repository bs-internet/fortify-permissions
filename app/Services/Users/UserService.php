<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class UserService
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('otomasyon.pagination.per_page', 15);
        Gate::authorize('viewAny', User::class);

        return $this->getForExport($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get unpaginated builder for export.
     *
     * @param array<string, mixed> $filters
     * @return Builder<User>
     */
    public function getForExport(array $filters = []): Builder
    {
        Gate::authorize('viewAny', User::class);

        /** @var Builder $query */
        $query = User::query()
            ->with(['roles:id,name,label', 'permissions:id,name,label', 'language:id,name,code']);

        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        });

        $query->when(isset($filters['status']) && $filters['status'] !== '', function (Builder $query) use ($filters) {
            $query->where('status', $filters['status']);
        });

        return $query->orderBy('name');
    }

    /**
     * Store a new user.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $authUser, array $data, string $ipAddress, string $userAgent): User
    {
        Gate::authorize('create', User::class);

        $roleIds = $data['roles'] ?? [];
        $permissionIds = $data['permissions'] ?? [];
        unset($data['roles'], $data['permissions'], $data['password_confirmation']);

        $user = User::create($data);

        if (!empty($roleIds)) {
            $roles = Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
        }

        if (!empty($permissionIds)) {
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $user->syncPermissions($permissions);
        }

        UserCreated::dispatch($authUser, ['created_user' => $user->email], $ipAddress, $userAgent);

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param array<string, mixed> $data
     */
    public function update(User $user, User $authUser, array $data, string $ipAddress, string $userAgent): User
    {
        Gate::authorize('update', $user);

        $roleIds = $data['roles'] ?? [];
        $permissionIds = $data['permissions'] ?? [];
        unset($data['roles'], $data['permissions'], $data['password_confirmation']);

        // Skip empty password
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $originalData = $user->only(array_keys($data));

        $user->fill($data);
        $user->save();

        $roles = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $user->syncPermissions($permissions);

        $changes = [];
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $changes['password'] = ['old' => '***', 'new' => '***'];
                continue;
            }
            if (array_key_exists($key, $originalData) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            UserUpdated::dispatch($authUser, $changes, $ipAddress, $userAgent);
        }

        return $user;
    }

    /**
     * Delete (soft) a user.
     */
    public function delete(User $user, User $authUser, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $user);

        if ($user->id === 1 || $user->hasRole('Admin')) {
            throw new AuthorizationException('Sistem yöneticisi silinemez.');
        }

        $changes = [
            'deleted' => $user->only(['name', 'email']),
        ];

        $user->syncRoles([]);
        $user->syncPermissions([]);
        $user->delete();

        UserDeleted::dispatch($authUser, $changes, $ipAddress, $userAgent);
    }

    /**
     * Perform bulk action on multiple users.
     */
    public function bulkAction(string $action, array $ids, User $authUser, string $ipAddress, string $userAgent): void
    {
        $users = User::whereIn('id', $ids)->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            /** @var User $user */
            switch ($action) {
                case 'delete':
                    $this->delete($user, $authUser, $ipAddress, $userAgent);
                    break;
                case 'activate':
                    $this->update($user, $authUser, ['status' => 1], $ipAddress, $userAgent);
                    break;
                case 'deactivate':
                    $this->update($user, $authUser, ['status' => 2], $ipAddress, $userAgent);
                    break;
            }
        }
    }
}
