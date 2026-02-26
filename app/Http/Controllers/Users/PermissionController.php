<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\PermissionCreateRequest;
use App\Http\Requests\Users\PermissionUpdateRequest;
use App\Models\Permission;
use App\Services\Users\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    /**
     * PermissionController constructor.
     */
    public function __construct(
        protected PermissionService $permissionService
    ) {
    }

    /**
     * Display a listing of permissions.
     */
    public function index(): Response|RedirectResponse
    {
        if (Gate::denies('viewAny', Permission::class)) {
            return back()->with('error', 'Yetkileri görüntüleme yetkiniz bulunmuyor.');
        }

        return Inertia::render('app/users/Permissions/Index', [
            'permissions' => $this->permissionService->getAll(),
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(PermissionCreateRequest $request): RedirectResponse
    {
        if (Gate::denies('create', Permission::class)) {
            return back()->with('error', 'Yetki oluşturma yetkiniz bulunmuyor.');
        }

        $this->permissionService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Yetki başarıyla eklendi.');
    }

    /**
     * Update the specified permission.
     */
    public function update(PermissionUpdateRequest $request, Permission $permission): RedirectResponse
    {
        if (Gate::denies('update', $permission)) {
            return back()->with('error', 'Bu yetkiyi düzenleme yetkiniz bulunmuyor.');
        }

        $this->permissionService->update(
            $permission,
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Yetki başarıyla güncellendi.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        if (Gate::denies('delete', $permission)) {
            return back()->with('error', 'Bu yetkiyi silme yetkiniz bulunmuyor.');
        }

        $this->permissionService->delete(
            $permission,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Yetki başarıyla silindi.');
    }
}

