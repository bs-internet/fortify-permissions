<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\CountryCreated;
use App\Events\CountryDeleted;
use App\Events\CountryUpdated;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class CountryService
{
    private const CACHE_KEY_ALL = 'countries_all';
    private const CACHE_KEY_ACTIVE = 'countries_active';

    /**
     * @return Collection<int, Country>
     */
    public function all(): Collection
    {
        Gate::authorize('viewAny', Country::class);

        return Cache::rememberForever(self::CACHE_KEY_ALL, function () {
            return Country::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * @return Collection<int, Country>
     */
    public function allActive(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_ACTIVE, function () {
            return Country::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Store a new country.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Country
    {
        Gate::authorize('create', Country::class);

        if (!empty($data['is_default'])) {
            Country::query()->where('is_default', true)->update(['is_default' => false]);
        }

        $country = Country::create($data);

        $this->clearCache();
        CountryCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $country;
    }

    /**
     * Update an existing country.
     *
     * @param array<string, mixed> $data
     */
    public function update(Country $country, User $user, array $data, string $ipAddress, string $userAgent): Country
    {
        Gate::authorize('update', $country);

        if (!empty($data['is_default'])) {
            Country::query()->where('is_default', true)->where('id', '!=', $country->id)->update(['is_default' => false]);
        }

        $originalData = $country->only(array_keys($data));

        $country->fill($data);
        $country->save();

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
            CountryUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $country;
    }

    /**
     * Delete a country.
     */
    public function delete(Country $country, User $user, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $country);

        $changes = [
            'deleted' => $country->only(['code', 'name']),
        ];

        $country->delete();

        $this->clearCache();
        CountryDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the country caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}
