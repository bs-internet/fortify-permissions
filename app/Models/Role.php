<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
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
}
