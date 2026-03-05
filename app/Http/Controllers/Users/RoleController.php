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
    public function __construct(
        protected RoleService $roleService,
        protected PermissionService $permissionService
    ) {}

    /**
     * Rol Listeleme Sayfası
     */
    public function index(): Response
    {
        return Inertia::render('app/users/Role/Index', [
            'roles' => $this->roleService->all(),
        ]);
    }

    /**
     * Yeni Rol Oluşturma Sayfası
     */
    public function create(): Response
    {
        return Inertia::render('app/users/Role/Create', [
            'permissions' => Inertia::defer(fn () => $this->permissionService->groupedAll()),
        ]);
    }

    /**
     * Yeni Rolü Kaydetme İşlemi
     */
    public function store(RoleCreateRequest $request): RedirectResponse
    {
        $this->roleService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return redirect()->route('users.roles.index')->with('success', 'Rol başarıyla oluşturuldu.');
    }

    /**
     * Rol Düzenleme Sayfası
     */
    public function edit(Role $role): Response
    {
        $role->load('permissions:id');

        return Inertia::render('app/users/Role/Edit', [
            'role' => $role,
            'permissions' => Inertia::defer(fn () => $this->permissionService->groupedAll()),
        ]);
    }

    /**
     * Rol Güncelleme İşlemi
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

        return redirect()->route('users.roles.index')->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Rol Silme İşlemi
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->delete(
                $role,
                request()->user(),
                request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
                request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
            );

            return redirect()->route('users.roles.index')->with('success', 'Rol başarıyla silindi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
