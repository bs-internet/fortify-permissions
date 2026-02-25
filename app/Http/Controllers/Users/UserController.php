<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/users/Users/Index');
    }
}
