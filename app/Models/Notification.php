<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class Notification extends BaseDatabaseNotification
{
    use HasUuids;

    protected $table = 'notifications';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }
}
