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
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
        protected PermissionService $permissionService,
        protected LanguageService $languageService
    ) {}

    /**
     * Kullanıcı listeleme sayfası.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/users/Users/Index', [
            'users' => $this->userService->paginate(
                filters: $request->only(['search', 'status', 'role'])
            ),
            'filters' => $request->only(['search', 'status', 'role']),
            'statuses' => UserStatus::options(),
            'roles' => $this->roleService->allForSelect(),
        ]);
    }

    /**
     * Yeni kullanıcı oluşturma sayfası.
     */
    public function create(): Response
    {
        return Inertia::render('app/users/Users/Create', [
            'roles' => $this->roleService->allForSelect(),
            'permissions' => Inertia::defer(fn () => $this->permissionService->groupedAll()),
            'languages' => $this->languageService->allActive(),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Yeni kullanıcıyı kaydet.
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        $this->userService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Kullanıcı düzenleme sayfası.
     */
    public function edit(User $user): Response
    {
        $user->load(['roles:id,name,label', 'permissions:id,name,label', 'language:id,name,code']);

        return Inertia::render('app/users/Users/Edit', [
            'user' => $user,
            'roles' => $this->roleService->allForSelect(),
            'permissions' => Inertia::defer(fn () => $this->permissionService->groupedAll()),
            'languages' => $this->languageService->allActive(),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Kullanıcıyı güncelle.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->userService->update(
            $user,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Kullanıcının e-posta adresini manuel olarak doğrula.
     */
    public function verifyEmail(Request $request, User $user): RedirectResponse
    {
        $this->userService->verifyEmail($user, $request->user());

        return back()->with('success', 'Kullanıcının e-posta adresi doğrulandı.');
    }

    /**
     * Kullanıcıyı sil (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete(
            $user,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Kullanıcı başarıyla silindi.');
    }

}

