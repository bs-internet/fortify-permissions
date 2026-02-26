<?php

declare(strict_types=1);

namespace App\Services\Definitions;

use App\Events\UnitCreated;
use App\Events\UnitDeleted;
use App\Events\UnitUpdated;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UnitService
{
    /**
     * Get all units ordered by sort_order.
     *
     * @return Collection<int, Unit>
     */
    public function getAll(): Collection
    {
        return Unit::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a new unit.
     *
     * @param array<string, mixed> $data
     */
    public function store(User $user, array $data, string $ipAddress, string $userAgent): Unit
    {
        $unit = Unit::create($data);

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
        $originalData = $unit->only(array_keys($data));

        $unit->fill($data);
        $unit->save();

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
            UnitUpdated::dispatch($user, $changes, $ipAddress, $userAgent);
        }

        return $unit;
    }

    /**
     * Delete a unit.
     */
    public function delete(Unit $unit, User $user, string $ipAddress, string $userAgent): void
    {
        $changes = [
            'deleted' => $unit->only(['name', 'abbreviation']),
        ];

        $unit->delete();

        UnitDeleted::dispatch($user, $changes, $ipAddress, $userAgent);
    }
}
