<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionEnum;
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
    protected $casts = [
        'name' => PermissionEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeByName($query, string|PermissionEnum $name)
    {
        return $query->where('name', $name instanceof PermissionEnum ? $name->value : $name);
    }
}
