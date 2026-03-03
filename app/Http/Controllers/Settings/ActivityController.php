<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\Common\ActivityService;
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
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Activity::class);

        $filterKeys = ['type', 'user_id', 'date_from', 'date_to'];

        return Inertia::render('app/settings/Activity', [
            'activities' => $this->activityService->getPaginatedActivities(
                filters: $request->only($filterKeys),
                perPage: 25
            ),
            'filters' => $request->only($filterKeys),
            'availableTypes' => $this->activityService->getExistingTypes(),
            'availableUsers' => $this->activityService->getUsersWithActivities(),
        ]);
    }
}

