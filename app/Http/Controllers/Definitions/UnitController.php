<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\UnitCreateRequest;
use App\Http\Requests\Definitions\UnitUpdateRequest;
use App\Models\Unit;
use App\Services\Definitions\UnitService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    public function __construct(
        protected UnitService $unitService
    ) {}

    /**
     * Birim listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Unit/Index', [
            'units' => $this->unitService->all(),
        ]);
    }

    /**
     * Yeni birim kaydetme işlemi.
     */
    public function store(UnitCreateRequest $request): RedirectResponse
    {
        $this->unitService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Birim başarıyla eklendi.');
    }

    /**
     * Birim güncelleme işlemi.
     */
    public function update(UnitUpdateRequest $request, Unit $unit): RedirectResponse
    {
        $this->unitService->update(
            $unit,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Birim başarıyla güncellendi.');
    }

    /**
     * Birim silme işlemi.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        $this->unitService->delete(
            $unit,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Birim başarıyla silindi.');
    }
}
