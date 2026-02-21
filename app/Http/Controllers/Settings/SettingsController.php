<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateGeneralSettingsRequest;
use App\Services\Settings\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for managing system-wide settings.
 *
 * This controller handles displaying and updating general system
 * configurations like branding assets and communication details.
 */
class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SettingService $settingService Service for setting operations
     */
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    /**
     * Display the general settings page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('app/settings/Setting', [
            'settings' => $this->settingService->all(),
        ]);
    }

    /**
     * Update the general system settings.
     *
     * @param UpdateGeneralSettingsRequest $request The validated update request
     * @return RedirectResponse
     */
    public function update(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        // Tüm mantık (güncelleme, dosya yönetimi ve event tetikleme) Service içinde
        $this->settingService->update(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Sistem ayarları başarıyla güncellendi.');
    }
}
