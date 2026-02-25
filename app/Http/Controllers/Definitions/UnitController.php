<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Definitions\UnitService;
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
    public function index(Request $request): Response
    {
        return Inertia::render('app/definitions/Unit/Index');
    }
}
