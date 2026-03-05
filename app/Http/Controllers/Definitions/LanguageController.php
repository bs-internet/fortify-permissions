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
    public function __construct(
        protected LanguageService $languageService
    ) {}

    /**
     * Dil listeleme sayfası.
     */
    public function index(): Response
    {
        return Inertia::render('app/definitions/Language/Index', [
            'languages' => $this->languageService->all(),
            'defaultLanguageId' => settings('default_language'),
        ]);
    }

    /**
     * Yeni dil kaydetme işlemi.
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
     * Dil güncelleme işlemi.
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
     * Dil silme işlemi.
     */
    public function destroy(Language $language): RedirectResponse
    {
        $this->languageService->delete(
            $language,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Dil başarıyla silindi.');
    }
}
