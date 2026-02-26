<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\PermissionCreateRequest;
use App\Http\Requests\Users\PermissionUpdateRequest;
use App\Models\Permission;
use App\Services\Users\PermissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    /**
     * PermissionController constructor.
     */
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Display a listing of permissions.
     */
    public function index(): Response
    {
        return Inertia::render('app/users/Permissions/Index', [
            'permissions' => $this->permissionService->getAll(),
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(PermissionCreateRequest $request): RedirectResponse
    {
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
        $this->permissionService->delete(
            $permission,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Yetki başarıyla silindi.');
    }
}
