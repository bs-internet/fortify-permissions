<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\LanguageCreated;
use App\Events\LanguageDeleted;
use App\Events\LanguageUpdated;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class LanguageService
{
    /**
     * Get all languages ordered by sort_order.
     *
     * @return Collection<int, Language>
     */
    public function getAll(): Collection
    {
        return Language::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get only active languages (for dropdowns).
     *
     * @return Collection<int, Language>
     */
    public function getActiveLanguages(): Collection
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a new language.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Language
    {
        if (!empty($data['is_default'])) {
            Language::query()->where('is_default', true)->update(['is_default' => false]);
        }

        $language = Language::create($data);

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
        if (!empty($data['is_default'])) {
            Language::query()->where('is_default', true)->where('id', '!=', $language->id)->update(['is_default' => false]);
        }

        $originalData = $language->only(array_keys($data));

        $language->fill($data);
        $language->save();

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] != $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            LanguageUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $language;
    }

    /**
     * Delete a language.
     */
    public function delete(Language $language, User $user, string $ipAddress, string $userAgent): void
    {
        $changes = [
            'deleted' => $language->only(['code', 'name']),
        ];

        $language->delete();

        LanguageDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }
}
