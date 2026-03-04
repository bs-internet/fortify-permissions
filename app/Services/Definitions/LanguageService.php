<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\LanguageCreated;
use App\Events\LanguageDeleted;
use App\Events\LanguageUpdated;
use App\Models\Language;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class LanguageService
{
    private const CACHE_KEY_ACTIVE = 'languages_active';

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Language::class);

        return Language::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(config('otomasyon.pagination.per_page', 15));
    }

    /**
     * @return Collection<int, Language>
     */
    public function allActive(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_ACTIVE, function () {
            return Language::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Store a new language.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Language
    {
        Gate::authorize('create', Language::class);

        $language = Language::create($data);

        $this->clearCache();
        LanguageCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $language;
    }

    /**
     * Update an existing language.
     *
     * @param array<string, mixed> $data
     */
    public function update(Language $language, User $user, array $data, string $ipAddress, string $userAgent): Language
    {
        Gate::authorize('update', $language);

        $originalData = $language->only(array_keys($data));

        $language->fill($data);
        $language->save();

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            $this->clearCache();
            LanguageUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $language;
    }

    /**
     * Delete a language.
     */
    public function delete(Language $language, User $user, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $language);

        if (Language::query()->count() <= 1) {
            abort(403, 'Sistemde en az bir dil bulunmalıdır.');
        }

        if (User::where('language_id', $language->id)->exists()) {
            abort(403, 'Bu dil aktif olarak kullanıldığı için silinemez.');
        }

        $changes = [
            'deleted' => $language->only(['code', 'name']),
        ];

        $language->delete();

        $this->clearCache();
        LanguageDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the language caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}

