<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CorePermission;
use App\Enums\DefinitionPermission;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'guard_name',
        'label',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopeByName($query, string|CorePermission|DefinitionPermission $name)
    {
        $value = match (true) {
            $name instanceof CorePermission,
            $name instanceof DefinitionPermission => $name->value,
            default => $name,
        };

        return $query->where('name', $value);
    }
}
