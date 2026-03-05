<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'is_active',
        'sort_order',
    ];

    /**
     * Birimler arası dönüşüm ilişkisi (bu birimden diğerine).
     */
    public function conversions(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'unit_conversions', 'from_unit_id', 'to_unit_id')
            ->withPivot(['id', 'factor'])
            ->withTimestamps();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
