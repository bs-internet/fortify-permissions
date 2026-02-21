<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\Common\ActivityService; // Service import edildi
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * ActivityController constructor.
     */
    public function __construct(
        protected ActivityService $activityService
    ) {}

    /**
     * Display a listing of the user activities.
     */
    public function index(Request $request): Response
    {
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
