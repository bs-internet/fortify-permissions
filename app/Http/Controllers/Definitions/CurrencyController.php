<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Definitions\CurrencyService;
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
    public function index(Request $request): Response
    {
        return Inertia::render('app/definitions/Currency/Index');
    }
}
