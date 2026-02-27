<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserCreateRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Models\User;
use App\Services\Definitions\LanguageService;
use App\Services\Users\PermissionService;
use App\Services\Users\RoleService;
use App\Services\Users\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
        protected PermissionService $permissionService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response|RedirectResponse
    {

        return Inertia::render('app/users/Users/Index', [
            'users' => $this->userService->paginate(
                filters: $request->only(['search', 'status'])
            ),
            'filters' => $request->only(['search', 'status']),
            'roles' => $this->roleService->all(),
            'permissions' => $this->permissionService->all(),
            'languages' => $this->languageService->allActive(),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {

        $this->userService->store(
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {

        $this->userService->update(
            $user,
            $request->user(),
            $request->validated(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {

        $this->userService->delete(
            $user,
            request()->user(),
            request()->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            request()->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        return back()->with('success', 'Kullanıcı başarıyla silindi.');
    }

    /**
     * Perform bulk action on selected users.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'string|exists:users,id',
        ]);

        $action = $validated['action'];
        $ids = $validated['ids'];

        $this->userService->bulkAction(
            $action,
            $ids,
            $request->user(),
            $request->ip() ?? config('otomasyon.defaults.ip_address', '127.0.0.1'),
            $request->userAgent() ?? config('otomasyon.defaults.user_agent', 'unknown')
        );

        $messages = [
            'delete' => 'Seçilen kullanıcılar başarıyla silindi.',
            'activate' => 'Seçilen kullanıcılar başarıyla aktifleştirildi.',
            'deactivate' => 'Seçilen kullanıcılar başarıyla pasifleştirildi.',
        ];

        return back()->with('success', $messages[$action]);
    }

    /**
     * Export users to excel.
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {

        $query = $this->userService->getForExport($request->only(['search', 'status']));

        $writer = \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload('kullanicilar.xlsx');

        foreach ($query->lazy() as $user) {
            /** @var User $user */
            $writer->addRow([
                'ID' => (string) $user->id,
                'Ad Soyad' => $user->name,
                'E-posta' => $user->email,
                'Ünvan' => $user->title ?? '-',
                'Durum' => $user->status->label(),
                'Roller' => $user->roles->pluck('label')->join(', '),
                'Doğrudan Yetkiler' => $user->permissions->pluck('label')->join(', '),
                'Dil' => $user->language ? $user->language->name : '-',
                'Oluşturulma Tarihi' => $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        return $writer->toBrowser();
    }
}

