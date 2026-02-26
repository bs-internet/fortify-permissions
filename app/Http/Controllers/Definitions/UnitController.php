<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Enums\UnitType;
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
    /**
     * UnitController constructor.
     */
    public function __construct(
        protected UnitService $unitService
    ) {}

    /**
     * Display a listing of units.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Unit/Index', [
            'units' => $this->unitService->getAll(),
            'unitTypes' => UnitType::options(),
        ]);
    }

    /**
     * Store a newly created unit.
     */
    public function store(UnitCreateRequest $request): RedirectResponse
    {
        $this->unitService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Birim başarıyla eklendi.');
    }

    /**
     * Update the specified unit.
     */
    public function update(UnitUpdateRequest $request, Unit $unit): RedirectResponse
    {
        $this->unitService->update(
            $unit,
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Birim başarıyla güncellendi.');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        $this->unitService->delete(
            $unit,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Birim başarıyla silindi.');
    }
}
