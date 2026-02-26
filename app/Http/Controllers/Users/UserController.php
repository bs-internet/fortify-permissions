<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserCreateRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Models\User;
use App\Services\Definitions\LanguageService;
use App\Services\Users\PermissionService;
use App\Services\Users\RoleService;
use App\Services\Users\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
        protected PermissionService $permissionService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        if (Gate::denies('viewAny', User::class)) {
            return back()->with('error', 'Kullanıcıları görüntüleme yetkiniz bulunmuyor.');
        }

        return Inertia::render('app/users/Users/Index', [
            'users' => $this->userService->getPaginated(
                filters: $request->only(['search', 'status']),
                perPage: 15
            ),
            'filters' => $request->only(['search', 'status']),
            'roles' => $this->roleService->getAll(),
            'permissions' => $this->permissionService->getAll(),
            'languages' => $this->languageService->getActiveLanguages(),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        if (Gate::denies('create', User::class)) {
            return back()->with('error', 'Kullanıcı oluşturma yetkiniz bulunmuyor.');
        }

        $this->userService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        if (Gate::denies('update', $user)) {
            return back()->with('error', 'Bu kullanıcıyı düzenleme yetkiniz bulunmuyor.');
        }

        $this->userService->update(
            $user,
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Gate::denies('delete', $user)) {
            return back()->with('error', 'Bu kullanıcıyı silme yetkiniz bulunmuyor.');
        }

        $this->userService->delete(
            $user,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Kullanıcı başarıyla silindi.');
    }
}

