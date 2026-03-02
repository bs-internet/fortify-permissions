<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'title' => $request->user()->title,
                    'email_verified_at' => $request->user()->email_verified_at,
                    'last_login_at' => $request->user()->last_login_at,
                    'created_at' => $request->user()->created_at,
                    'updated_at' => $request->user()->updated_at,
                    'can' => $request->user()->getAllPermissions()->pluck('name')->mapWithKeys(fn($permission) => [$permission => true]) ?? [],
                ] : null,
                'is_super_admin' => $request->user()?->hasRole('Super Admin') ?? false,
                'roles' => $request->user()?->getRoleNames() ?? [],
                'permissions' => $request->user()?->getAllPermissions()->pluck('name') ?? [],
            ],
            'settings' => [
                'site_name' => site_name(),
                'site_slogan' => site_slogan(),
                'email' => site_email(),
                'sender_name' => sender_name(),
                'logo_light' => logo('light'),
                'logo_dark' => logo('dark'),
                'favicon' => favicon(),
            ],
            'unreadNotificationCount' => fn() => $request->user()?->unreadNotifications()->count() ?? 0,
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
