<?php

declare(strict_types=1);

namespace App\Enums;

enum DefinitionPermission: string
{
    // Language
    case LANGUAGE_MANAGEMENT = 'language.management';
    case LANGUAGE_CREATE     = 'language.create';
    case LANGUAGE_UPDATE     = 'language.update';
    case LANGUAGE_DELETE     = 'language.delete';

    // Currency
    case CURRENCY_MANAGEMENT = 'currency.management';
    case CURRENCY_CREATE     = 'currency.create';
    case CURRENCY_UPDATE     = 'currency.update';
    case CURRENCY_DELETE     = 'currency.delete';

    // Unit
    case UNIT_MANAGEMENT = 'unit.management';
    case UNIT_CREATE     = 'unit.create';
    case UNIT_UPDATE     = 'unit.update';
    case UNIT_DELETE     = 'unit.delete';

    // Country
    case COUNTRY_MANAGEMENT = 'country.management';
    case COUNTRY_CREATE     = 'country.create';
    case COUNTRY_UPDATE     = 'country.update';
    case COUNTRY_DELETE     = 'country.delete';

    // Tax
    case TAX_MANAGEMENT = 'tax.management';
    case TAX_CREATE     = 'tax.create';
    case TAX_UPDATE     = 'tax.update';
    case TAX_DELETE     = 'tax.delete';

    public function label(): string
    {
        return match($this) {
            self::LANGUAGE_MANAGEMENT => 'Dil Yönetimi',
            self::LANGUAGE_CREATE     => 'Dil Oluşturma',
            self::LANGUAGE_UPDATE     => 'Dil Düzenleme',
            self::LANGUAGE_DELETE     => 'Dil Silme',

            self::CURRENCY_MANAGEMENT => 'Para Birimi Yönetimi',
            self::CURRENCY_CREATE     => 'Para Birimi Oluşturma',
            self::CURRENCY_UPDATE     => 'Para Birimi Düzenleme',
            self::CURRENCY_DELETE     => 'Para Birimi Silme',

            self::UNIT_MANAGEMENT     => 'Ölçü Birimi Yönetimi',
            self::UNIT_CREATE         => 'Ölçü Birimi Oluşturma',
            self::UNIT_UPDATE         => 'Ölçü Birimi Düzenleme',
            self::UNIT_DELETE         => 'Ölçü Birimi Silme',

            self::COUNTRY_MANAGEMENT  => 'Ülke Yönetimi',
            self::COUNTRY_CREATE      => 'Ülke Oluşturma',
            self::COUNTRY_UPDATE      => 'Ülke Düzenleme',
            self::COUNTRY_DELETE      => 'Ülke Silme',

            self::TAX_MANAGEMENT      => 'Vergi Yönetimi',
            self::TAX_CREATE          => 'Vergi Oluşturma',
            self::TAX_UPDATE          => 'Vergi Düzenleme',
            self::TAX_DELETE          => 'Vergi Silme',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LANGUAGE_MANAGEMENT => 'Sistem dillerini ekleme, çıkarma ve varsayılan dili seçme yetkisi.',
            self::LANGUAGE_CREATE     => 'Yeni dil ekleme yetkisi.',
            self::LANGUAGE_UPDATE     => 'Dil bilgilerini düzenleme yetkisi.',
            self::LANGUAGE_DELETE     => 'Dil silme yetkisi.',

            self::CURRENCY_MANAGEMENT => 'Para birimlerini listeleme yetkisi.',
            self::CURRENCY_CREATE     => 'Yeni para birimi (TL, USD vb.) ekleme yetkisi.',
            self::CURRENCY_UPDATE     => 'Para birimi kurlarını veya isimlerini düzenleme yetkisi.',
            self::CURRENCY_DELETE     => 'Para birimlerini sistemden kaldırma yetkisi.',

            self::UNIT_MANAGEMENT     => 'Ağırlık ve uzunluk ölçü birimlerini listeleme yetkisi.',
            self::UNIT_CREATE         => 'Yeni ölçü birimi tanımlama yetkisi.',
            self::UNIT_UPDATE         => 'Ölçü birimi detaylarını değiştirme yetkisi.',
            self::UNIT_DELETE         => 'Ölçü birimlerini sistemden kaldırma yetkisi.',

            self::COUNTRY_MANAGEMENT  => 'Ülkeleri listeleme yetkisi.',
            self::COUNTRY_CREATE      => 'Yeni ülke tanımlama yetkisi.',
            self::COUNTRY_UPDATE      => 'Ülke bilgilerini düzenleme yetkisi.',
            self::COUNTRY_DELETE      => 'Ülkeleri sistemden kaldırma yetkisi.',

            self::TAX_MANAGEMENT      => 'Vergi oranlarını listeleme yetkisi.',
            self::TAX_CREATE          => 'Yeni vergi oranı tanımlama yetkisi.',
            self::TAX_UPDATE          => 'Vergi oranı bilgilerini düzenleme yetkisi.',
            self::TAX_DELETE          => 'Vergi oranlarını sistemden kaldırma yetkisi.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function moduleLabels(): array
    {
        return [
            'language' => 'Dil',
            'currency' => 'Para Birimi',
            'unit'     => 'Birim',
            'country'  => 'Ülke',
            'tax'      => 'Vergi',
        ];
    }
}
