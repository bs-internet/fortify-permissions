<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateGeneralSettingsRequest;
use App\Services\Definitions\CountryService;
use App\Services\Definitions\CurrencyService;
use App\Services\Definitions\LanguageService;
use App\Services\Definitions\TaxService;
use App\Services\Settings\SettingService;
use Illuminate\Http\RedirectResponse;
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
        private readonly SettingService $settingService,
        private readonly LanguageService $languageService,
        private readonly CurrencyService $currencyService,
        private readonly CountryService $countryService,
        private readonly TaxService $taxService,
    ) {
    }

    /**
     * Display the general settings page.
     */
    public function index(): Response|RedirectResponse
    {

        return Inertia::render('app/settings/GeneralSettings', [
            'settings' => [
                'site_name' => site_name(),
                'site_slogan' => site_slogan(),
                'email' => site_email(),
                'sender_name' => sender_name(),
                'logo_light' => logo('light'),
                'logo_dark' => logo('dark'),
                'favicon' => favicon(),
                'default_language' => settings('default_language'),
                'default_currency' => settings('default_currency'),
                'default_country' => settings('default_country'),
                'default_tax' => settings('default_tax'),
            ],
            'languages' => $this->languageService->allActive()->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'code' => $l->code]),
            'currencies' => $this->currencyService->allActive()->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'code' => $c->code, 'symbol' => $c->symbol]),
            'countries' => $this->countryService->allActive()->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'code' => $c->code]),
            'taxes' => $this->taxService->allActive()->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'rate' => $t->rate]),
        ]);
    }

    /**
     * Update the general system settings.
     */
    public function update(UpdateGeneralSettingsRequest $request): RedirectResponse
    {

        $this->settingService->update(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Sistem ayarları başarıyla güncellendi.');
    }
}
