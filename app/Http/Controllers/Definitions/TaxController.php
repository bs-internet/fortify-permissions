<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\TaxCreateRequest;
use App\Http\Requests\Definitions\TaxUpdateRequest;
use App\Models\Tax;
use App\Services\Definitions\CountryService;
use App\Services\Definitions\TaxService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaxController extends Controller
{
    public function __construct(
        protected TaxService $taxService,
        protected CountryService $countryService
    ) {}

    /**
     * Vergi listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Tax/Index', [
            'taxes' => $this->taxService->all(),
            'countries' => $this->countryService->allActive(),
        ]);
    }

    /**
     * Yeni vergi kaydetme işlemi.
     */
    public function store(TaxCreateRequest $request): RedirectResponse
    {
        $this->taxService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Vergi oranı başarıyla eklendi.');
    }

    /**
     * Vergi güncelleme işlemi.
     */
    public function update(TaxUpdateRequest $request, Tax $tax): RedirectResponse
    {
        $this->taxService->update(
            $tax,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Vergi oranı başarıyla güncellendi.');
    }

    /**
     * Vergi silme işlemi.
     */
    public function destroy(Tax $tax): RedirectResponse
    {
        $this->taxService->delete(
            $tax,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Vergi oranı başarıyla silindi.');
    }
}
