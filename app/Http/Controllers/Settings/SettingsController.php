<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateGeneralSettingsRequest;
use App\Models\Setting;
use App\Services\Settings\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
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
     */
    public function __construct(
        private readonly SettingService $settingService
    ) {
    }

    /**
     * Display the general settings page.
     */
    public function index(): Response|RedirectResponse
    {
        if (Gate::denies('viewAny', Setting::class)) {
            return back()->with('error', 'Ayarları görüntüleme yetkiniz bulunmuyor.');
        }

        return Inertia::render('app/settings/GeneralSettings', [
            'settings' => [
                'site_name' => site_name(),
                'site_slogan' => site_slogan(),
                'email' => site_email(),
                'sender_name' => sender_name(),
                'logo_light' => logo('light'),
                'logo_dark' => logo('dark'),
                'favicon' => favicon(),
            ],
        ]);
    }

    /**
     * Update the general system settings.
     */
    public function update(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        if (Gate::denies('update', new Setting())) {
            return back()->with('error', 'Ayarları güncelleme yetkiniz bulunmuyor.');
        }

        $this->settingService->update(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Sistem ayarları başarıyla güncellendi.');
    }
}

