<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\LanguageCreateRequest;
use App\Http\Requests\Definitions\LanguageUpdateRequest;
use App\Models\Language;
use App\Services\Definitions\LanguageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * LanguageController constructor.
     */
    public function __construct(
        protected LanguageService $languageService
    ) {
    }

    /**
     * Display a listing of languages.
     */
    public function index(): Response|RedirectResponse
    {

        return Inertia::render('app/definitions/Language/Index', [
            'languages' => $this->languageService->all(),
        ]);
    }

    /**
     * Store a newly created language.
     */
    public function store(LanguageCreateRequest $request): RedirectResponse
    {

        $this->languageService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Dil başarıyla eklendi.');
    }

    /**
     * Update the specified language.
     */
    public function update(LanguageUpdateRequest $request, Language $language): RedirectResponse
    {

        $this->languageService->update(
            $language,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Dil başarıyla güncellendi.');
    }

    /**
     * Remove the specified language.
     */
    public function destroy(Language $language): RedirectResponse
    {

        $this->languageService->delete(
            $language,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Dil başarıyla silindi.');
    }
}

