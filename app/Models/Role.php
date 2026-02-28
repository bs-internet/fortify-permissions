<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
