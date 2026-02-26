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
    /**
     * CurrencyController constructor.
     */
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    /**
     * Display a listing of currencies.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Currency/Index', [
            'currencies' => $this->currencyService->getAll(),
        ]);
    }

    /**
     * Store a newly created currency.
     */
    public function store(CurrencyCreateRequest $request): RedirectResponse
    {
        $this->currencyService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Para birimi başarıyla eklendi.');
    }

    /**
     * Update the specified currency.
     */
    public function update(CurrencyUpdateRequest $request, Currency $currency): RedirectResponse
    {
        $this->currencyService->update(
            $currency,
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Para birimi başarıyla güncellendi.');
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        $this->currencyService->delete(
            $currency,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Para birimi başarıyla silindi.');
    }
}
