<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users with relationships.
     *
     * @param array<string, mixed> $filters
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles:id,name,label', 'permissions:id,name,label', 'language:id,name,code'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Store a new user.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $authUser, array $data, string $ipAddress, string $userAgent): User
    {
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
        $changes = [
            'deleted' => $user->only(['name', 'email']),
        ];

        $user->syncRoles([]);
        $user->syncPermissions([]);
        $user->delete();

        UserDeleted::dispatch($authUser, $changes, $ipAddress, $userAgent);
    }
}
