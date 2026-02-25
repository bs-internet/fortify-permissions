<?php

namespace App\Http\Controllers\Definitions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('app/definitions/Language/Index');
    }
}
