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
    public function __construct(
        protected PasswordService $passwordService
    ) {}

    /**
     * Şifre düzenleme sayfası.
     */
    public function edit(): Response
    {
        return Inertia::render('app/profile/Password');
    }

    /**
     * Şifre güncelleme işlemi.
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $this->passwordService->update(
            $request->user(),
            $request->validated('password'),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Şifreniz güncellendi.');
    }
}
