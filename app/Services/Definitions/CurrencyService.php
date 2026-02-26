<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\CurrencyCreated;
use App\Events\CurrencyDeleted;
use App\Events\CurrencyUpdated;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CurrencyService
{
    /**
     * Get all currencies ordered by sort_order.
     *
     * @return Collection<int, Currency>
     */
    public function getAll(): Collection
    {
        return Currency::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a new currency.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Currency
    {
        if (!empty($data['is_default'])) {
            Currency::query()->where('is_default', true)->update(['is_default' => false]);
        }

        $currency = Currency::create($data);

        CurrencyCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $currency;
    }

    /**
     * Update an existing currency.
     *
     * @param array<string, mixed> $data
     */
    public function update(Currency $currency, User $user, array $data, string $ipAddress, string $userAgent): Currency
    {
        if (!empty($data['is_default'])) {
            Currency::query()->where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
        }

        $originalData = $currency->only(array_keys($data));

        $currency->fill($data);
        $currency->save();

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
            CurrencyUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $currency;
    }

    /**
     * Delete a currency.
     */
    public function delete(Currency $currency, User $user, string $ipAddress, string $userAgent): void
    {
        $changes = [
            'deleted' => $currency->only(['code', 'name']),
        ];

        $currency->delete();

        CurrencyDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }
}
