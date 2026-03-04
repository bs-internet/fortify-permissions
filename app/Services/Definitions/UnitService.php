<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\UnitCreated;
use App\Events\UnitDeleted;
use App\Events\UnitUpdated;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class UnitService
{

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Unit::class);

        return Unit::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(config('otomasyon.pagination.per_page', 15));
    }

    /**
     * Store a new unit.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Unit
    {
        Gate::authorize('create', Unit::class);

        $unit = Unit::create($data);

        $this->clearCache();
        UnitCreated::dispatch($user, $data, $ipAddress, $userAgent);

        return $unit;
    }

    /**
     * Update an existing unit.
     *
     * @param array<string, mixed> $data
     */
    public function update(Unit $unit, User $user, array $data, string $ipAddress, string $userAgent): Unit
    {
        Gate::authorize('update', $unit);

        $originalData = $unit->only(array_keys($data));

        $unit->fill($data);
        $unit->save();

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
            UnitUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $unit;
    }

    /**
     * Delete a unit.
     */
    public function delete(Unit $unit, User $user, string $ipAddress, string $userAgent): void
    {
        Gate::authorize('delete', $unit);

        $changes = [
            'deleted' => $unit->only(['name', 'abbreviation']),
        ];

        $unit->delete();

        $this->clearCache();
        UnitDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }

    /**
     * Clear the unit caches.
     */
    private function clearCache(): void
    {
        // ...
    }
}

