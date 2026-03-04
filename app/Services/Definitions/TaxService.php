<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\TaxCreated;
use App\Events\TaxDeleted;
use App\Events\TaxUpdated;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class TaxService
{
    private const CACHE_KEY_ACTIVE = 'taxes_active';

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Tax::class);

        return Tax::query()
            ->with('countries')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(config('otomasyon.pagination.per_page', 15));
    }

    /**
     * @return Collection<int, Tax>
     */
    public function allActive(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY_ACTIVE, function () {
            return Tax::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'rate']);
        });
    }

    /**
     * Store a new tax.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Tax
    {
        Gate::authorize('create', Tax::class);

        $countryIds = $data['countries'] ?? [];
        unset($data['countries']);

        $tax = Tax::create($data);
        $tax->countries()->sync($countryIds);

        $this->clearCache();
        TaxCreated::dispatch($user, array_merge($data, ['countries' => $countryIds]), $ipAddress, $userAgent);

        return $tax;
    }

    /**
     * Update an existing tax.
     *
     * @param array<string, mixed> $data
     */
    public function update(Tax $tax, User $user, array $data, string $ipAddress, string $userAgent): Tax
    {
        Gate::authorize('update', $tax);

        $countryIds = $data['countries'] ?? [];
        unset($data['countries']);

        $originalData = $tax->only(array_keys($data));
        $originalCountryIds = $tax->countries->pluck('id')->toArray();

        $tax->fill($data);
        $tax->save();
        $tax->countries()->sync($countryIds);

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        sort($originalCountryIds);
        $sortedNewIds = $countryIds;
        sort($sortedNewIds);

        if ($originalCountryIds !== $sortedNewIds) {
            $changes['countries'] = [
                'old' => $originalCountryIds,
                'new' => $countryIds,
            ];
        }

        if (!empty($changes)) {
            $this->clearCache();
            TaxUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $tax;
    }

    /**
     * Delete a tax.
     */
    public function delete(Tax $tax, User $user, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $tax);

        $changes = [
            'deleted' => $tax->only(['name', 'rate']),
        ];

        $tax->delete();

        $this->clearCache();
        TaxDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the tax caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}
