<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Definitions\LanguageService;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * LanguageController constructor.
     */
    public function __construct(
        protected LanguageService $languageService
    ) {}

    /**
     * Display a listing of languages.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/definitions/Language/Index');
    }
}
