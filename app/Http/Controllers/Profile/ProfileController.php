<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Services\Profile\ProfileService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ProfileService $profileService Service for profile operations
     */
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    /**
     * Show the user's profile edit page.
     *
     * @param Request $request The incoming request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('app/profile/Profile', [
            'user' => $request->user(),
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile settings.
     *
     * @param ProfileUpdateRequest $request The validated profile update request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->profileService->update(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Profil bilgileriniz güncellendi.');
    }
}
