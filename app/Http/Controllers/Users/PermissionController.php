<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Users\PermissionService;
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
    public function index(Request $request): Response
    {
        return Inertia::render('app/users/Permissions/Index');
    }
}
