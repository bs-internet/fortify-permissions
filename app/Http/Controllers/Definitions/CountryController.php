<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\CountryCreateRequest;
use App\Http\Requests\Definitions\CountryUpdateRequest;
use App\Models\Country;
use App\Services\Definitions\CountryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService
    ) {}

    /**
     * Ülke listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Country/Index', [
            'countries' => $this->countryService->all(),
            'defaultCountryId' => settings('default_country'),
        ]);
    }

    /**
     * Yeni ülke kaydetme işlemi.
     */
    public function store(CountryCreateRequest $request): RedirectResponse
    {
        $this->countryService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Ülke başarıyla eklendi.');
    }

    /**
     * Ülke güncelleme işlemi.
     */
    public function update(CountryUpdateRequest $request, Country $country): RedirectResponse
    {
        $this->countryService->update(
            $country,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Ülke başarıyla güncellendi.');
    }

    /**
     * Ülke silme işlemi.
     */
    public function destroy(Country $country): RedirectResponse
    {
        $this->countryService->delete(
            $country,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Ülke başarıyla silindi.');
    }
}
