<?php

declare(strict_types=1);

namespace App\Http\Controllers\Definitions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Definitions\LanguageCreateRequest;
use App\Http\Requests\Definitions\LanguageUpdateRequest;
use App\Models\Language;
use App\Services\Definitions\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
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
        if (Gate::denies('viewAny', Language::class)) {
            return back()->with('error', 'Dilleri görüntüleme yetkiniz bulunmuyor.');
        }

        return Inertia::render('app/definitions/Language/Index', [
            'languages' => $this->languageService->getAll(),
        ]);
    }

    /**
     * Store a newly created language.
     */
    public function store(LanguageCreateRequest $request): RedirectResponse
    {
        if (Gate::denies('create', Language::class)) {
            return back()->with('error', 'Dil oluşturma yetkiniz bulunmuyor.');
        }

        $this->languageService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Dil başarıyla eklendi.');
    }

    /**
     * Update the specified language.
     */
    public function update(LanguageUpdateRequest $request, Language $language): RedirectResponse
    {
        if (Gate::denies('update', $language)) {
            return back()->with('error', 'Bu dili düzenleme yetkiniz bulunmuyor.');
        }

        $this->languageService->update(
            $language,
            $request->user(),
            $request->validated(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Dil başarıyla güncellendi.');
    }

    /**
     * Remove the specified language.
     */
    public function destroy(Language $language): RedirectResponse
    {
        if (Gate::denies('delete', $language)) {
            return back()->with('error', 'Bu dili silme yetkiniz bulunmuyor.');
        }

        $this->languageService->delete(
            $language,
            request()->user(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent() ?? 'unknown'
        );

        return back()->with('success', 'Dil başarıyla silindi.');
    }
}

