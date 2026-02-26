<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\Common\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * ActivityController constructor.
     */
    public function __construct(
        protected ActivityService $activityService
    ) {
    }

    /**
     * Display a listing of the user activities.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        if (Gate::denies('viewAny', Activity::class)) {
            return back()->with('error', 'Aktivite loglarını görüntüleme yetkiniz bulunmuyor.');
        }

        return Inertia::render('app/settings/Activity', [
            'activities' => $this->activityService->getPaginatedActivities(
                filters: $request->only(['type']),
                perPage: 25
            ),
            'filters' => $request->only(['type']),
            'availableTypes' => $this->activityService->getExistingTypes()
        ]);
    }
}

