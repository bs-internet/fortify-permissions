<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\CurrencyCreateRequest;
use App\Http\Requests\Definitions\CurrencyUpdateRequest;
use App\Models\Currency;
use App\Services\Definitions\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    /**
     * Para birimi listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Currency/Index', [
            'currencies' => $this->currencyService->all(),
            'defaultCurrencyId' => settings('default_currency'),
        ]);
    }

    /**
     * Yeni para birimi kaydetme işlemi.
     */
    public function store(CurrencyCreateRequest $request): RedirectResponse
    {
        $this->currencyService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Para birimi başarıyla eklendi.');
    }

    /**
     * Para birimi güncelleme işlemi.
     */
    public function update(CurrencyUpdateRequest $request, Currency $currency): RedirectResponse
    {
        $this->currencyService->update(
            $currency,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Para birimi başarıyla güncellendi.');
    }

    /**
     * Para birimi silme işlemi.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        $this->currencyService->delete(
            $currency,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Para birimi başarıyla silindi.');
    }
}
