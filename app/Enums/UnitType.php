<?php

declare(strict_types=1);

namespace App\Enums;

enum UnitType: string
{
    case Weight = 'weight';
    case Length = 'length';
    case Volume = 'volume';
    case Area = 'area';
    case Piece = 'piece';

    /**
     * Get human-readable label for the unit type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Weight => 'Ağırlık',
            self::Length => 'Uzunluk',
            self::Volume => 'Hacim',
            self::Area => 'Alan',
            self::Piece => 'Adet',
        };
    }

    /**
     * Get all available options for forms.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
