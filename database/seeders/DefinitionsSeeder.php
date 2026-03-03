<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Setting;
use App\Enums\UnitType;
use Illuminate\Database\Seeder;

class DefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Languages ----
        $tr = Language::updateOrCreate(
            ['code' => 'tr'],
            [
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $en = Language::updateOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'native_name' => 'English',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // ---- Country: Türkiye ----
        $turkey = Country::updateOrCreate(
            ['code' => 'TR'],
            [
                'name' => 'Türkiye',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // ---- Currency: Türk Lirası ----
        $try = Currency::updateOrCreate(
            ['code' => 'TRY'],
            [
                'name' => 'Türk Lirası',
                'symbol' => '₺',
                'decimal_places' => 2,
                'thousand_separator' => '.',
                'decimal_separator' => ',',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // ---- Tax: KDV %20 ----
        $kdv = Tax::updateOrCreate(
            ['name' => 'KDV %20'],
            [
                'rate' => 20.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // ---- Country ↔ Tax ----
        $turkey->taxes()->syncWithoutDetaching([$kdv->id]);

        // ---- Unit: Adet ----
        $piece = Unit::updateOrCreate(
            ['name' => 'Adet'],
            [
                'abbreviation' => 'Ad',
                'type' => UnitType::Piece->value,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // ---- Default Settings ----
        Setting::updateOrCreate(
            ['key' => 'default_language_id'],
            ['value' => $tr->id]
        );

        Setting::updateOrCreate(
            ['key' => 'default_country_id'],
            ['value' => $turkey->id]
        );

        Setting::updateOrCreate(
            ['key' => 'default_currency_id'],
            ['value' => $try->id]
        );

        Setting::updateOrCreate(
            ['key' => 'default_tax_id'],
            ['value' => $kdv->id]
        );
    }
}
