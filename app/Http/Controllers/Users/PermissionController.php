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
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * İzin listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/users/Permissions/Index', [
            'permissions' => $this->permissionService->all(),
        ]);
    }

    /**
     * İzin güncelleme işlemi.
     */
    public function update(PermissionUpdateRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update(
            $permission,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Yetki başarıyla güncellendi.');
    }
}

