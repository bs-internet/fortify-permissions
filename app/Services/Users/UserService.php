<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Enums\PermissionEnum;
use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\Language;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('otomasyon.pagination.per_page', 15);
        Gate::authorize('viewAny', User::class);

        return $this->getFilteredQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get unpaginated builder for export.
     *
     * @param array<string, mixed> $filters
     * @return Builder<User>
     */
    public function getFilteredQuery(array $filters = []): Builder
    {
        Gate::authorize('viewAny', User::class);

        /** @var Builder $query */
        $query = User::query()
            ->with(['roles:id,name,label', 'permissions:id,name,label', 'language:id,name,code'])
            ->whereDoesntHave('roles', fn (Builder $q) => $q->where('name', 'Super Admin'));

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

        $query->when($filters['role'] ?? null, function (Builder $query, $roleId) {
            $query->whereHas('roles', fn (Builder $q) => $q->where('id', $roleId));
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
        unset($data['roles'], $data['permissions']);

        if (empty($data['language_id'])) {
            $data['language_id'] = Language::query()->where('is_default', true)->value('id');
        }

        $data['password'] = Str::password(16);

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
        unset($data['roles'], $data['permissions']);

        if (isset($data['email']) && $data['email'] !== $user->email) {
            if (!$authUser->can(PermissionEnum::USER_CHANGE_EMAIL->value)) {
                $data['email'] = $user->email;
            } else {
                $data['email_verified_at'] = null;
            }
        }

        if (isset($data['status']) && (int) $data['status'] !== $user->status->value) {
            if (!$authUser->can(PermissionEnum::USER_CHANGE_STATUS->value)) {
                $data['status'] = $user->status->value;
            }
        }

        if (empty($data['language_id'])) {
            $data['language_id'] = Language::query()->where('is_default', true)->value('id');
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
     * Manually verify a user's email address.
     */
    public function verifyEmail(User $user, User $authUser): void
    {
        Gate::authorize('update', $user);

        if (!$authUser->can(PermissionEnum::USER_VERIFY_EMAIL->value)) {
            throw new AuthorizationException('E-posta doğrulama yetkiniz yok.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();
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

        DB::table('sessions')->where('user_id', $user->id)->delete();

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
