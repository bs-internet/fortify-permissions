<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\PasswordUpdateRequest;
use App\Services\Profile\PasswordService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param PasswordService $passwordService Service for password operations
     */
    public function __construct(
        private readonly PasswordService $passwordService
    ) {}

    /**
     * Show the user's password edit page.
     *
     * @return Response
     */
    public function edit(): Response
    {
        return Inertia::render('app/profile/Password');
    }

    /**
     * Update the user's password.
     *
     * @param PasswordUpdateRequest $request The validated password update request
     * @return RedirectResponse
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $this->passwordService->update(
            $request->user(),
            $request->validated('password'),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Şifreniz güncellendi.');
    }
}
