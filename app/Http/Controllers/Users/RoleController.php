<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Users\RoleService;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */
    public function __construct(
        protected RoleService $roleService
    ) {}

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/users/Role/Index');
    }
}
