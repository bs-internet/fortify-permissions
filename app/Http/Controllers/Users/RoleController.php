<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RoleCreateRequest;
use App\Http\Requests\Users\RoleUpdateRequest;
use App\Models\Role;
use App\Services\Users\PermissionService;
use App\Services\Users\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */
    public function __construct(
        protected RoleService $roleService,
        protected PermissionService $permissionService
    ) {
    }

    /**
     * Display a listing of roles.
     */
    public function index(): Response|RedirectResponse
    {

        return Inertia::render('app/users/Role/Index', [
            'roles' => $this->roleService->all(),
            'permissions' => $this->permissionService->groupedAll(),
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleCreateRequest $request): RedirectResponse
    {

        $this->roleService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Rol başarıyla eklendi.');
    }

    /**
     * Update the specified role.
     */
    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {

        $this->roleService->update(
            $role,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {

        $this->roleService->delete(
            $role,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Rol başarıyla silindi.');
    }
}

