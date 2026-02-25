<?php

namespace App\Http\Controllers\Definitions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/definitions/Currency/Index');
    }
}
