<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // User
    case USER_MANAGEMENT = 'user.management';
    case USER_CREATE     = 'user.create';
    case USER_UPDATE     = 'user.update';
    case USER_DELETE     = 'user.delete';

    // Role
    case ROLE_MANAGEMENT = 'role.management';
    case ROLE_CREATE     = 'role.create';
    case ROLE_UPDATE     = 'role.update';
    case ROLE_DELETE     = 'role.delete';

    // Permission (Sadece Görüntüleme ve Bilgi Güncelleme)
    case PERMISSION_MANAGEMENT = 'permission.management';
    case PERMISSION_UPDATE     = 'permission.update';

    // Core Definitions
    //Language
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
    case UNIT_MANAGEMENT     = 'unit.management';
    case UNIT_CREATE         = 'unit.create';
    case UNIT_UPDATE         = 'unit.update';
    case UNIT_DELETE         = 'unit.delete';

    // Settings & Activity
    case SETTING_MANAGEMENT  = 'setting.management';
    case SETTING_UPDATE      = 'setting.update';
    case ACTIVITY_VIEW       = 'activity.view';

    public function label(): string
    {
        return match($this) {
            self::USER_MANAGEMENT       => 'Kullanıcı Yönetimi',
            self::USER_CREATE           => 'Kullanıcı Oluşturma',
            self::USER_UPDATE           => 'Kullanıcı Düzenleme',
            self::USER_DELETE           => 'Kullanıcı Silme',

            self::ROLE_MANAGEMENT       => 'Rol Yönetimi',
            self::ROLE_CREATE           => 'Rol Oluşturma',
            self::ROLE_UPDATE           => 'Rol Düzenleme',
            self::ROLE_DELETE           => 'Rol Silme',

            self::PERMISSION_MANAGEMENT => 'İzin Listeleme',
            self::PERMISSION_UPDATE     => 'İzin Bilgilerini Güncelleme',

            self::LANGUAGE_MANAGEMENT   => 'Dil Yönetimi',
            self::LANGUAGE_CREATE       => 'Dil Oluşturma',
            self::LANGUAGE_UPDATE       => 'Dil Düzenleme',
            self::LANGUAGE_DELETE       => 'Dil Silme',

            self::CURRENCY_MANAGEMENT   => 'Para Birimi Yönetimi',
            self::CURRENCY_CREATE       => 'Para Birimi Oluşturma',
            self::CURRENCY_UPDATE       => 'Para Birimi Düzenleme',
            self::CURRENCY_DELETE       => 'Para Birimi Silme',

            self::UNIT_MANAGEMENT       => 'Ölçü Birimi Yönetimi',
            self::UNIT_CREATE           => 'Ölçü Birimi Oluşturma',
            self::UNIT_UPDATE           => 'Ölçü Birimi Düzenleme',
            self::UNIT_DELETE           => 'Ölçü Birimi Silme',

            self::SETTING_MANAGEMENT    => 'Sistem Ayarları',
            self::SETTING_UPDATE        => 'Sistem Ayarlarını Güncelleme',
            self::ACTIVITY_VIEW         => 'Aktivite Kayıtlarını Görüntüleme',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::USER_MANAGEMENT       => 'Sistemdeki kullanıcıları listeleme yetkisi.',
            self::USER_CREATE           => 'Yeni kullanıcı hesabı açma yetkisi.',
            self::USER_UPDATE           => 'Mevcut kullanıcı bilgilerini değiştirme yetkisi.',
            self::USER_DELETE           => 'Kullanıcı hesaplarını sistemden kaldırma yetkisi.',

            self::ROLE_MANAGEMENT       => 'Kullanıcı rollerini listeleme ve yönetme yetkisi.',
            self::ROLE_CREATE           => 'Sisteme yeni yetki grupları (roller) ekleme yetkisi.',
            self::ROLE_UPDATE           => 'Rollerin sahip olduğu izinleri değiştirme yetkisi.',
            self::ROLE_DELETE           => 'Mevcut rollerini sistemden kaldırma yetkisi.',

            self::PERMISSION_MANAGEMENT => 'Sistemde tanımlı teknik izinleri görme yetkisi.',
            self::PERMISSION_UPDATE     => 'İzinlerin etiket ve açıklamalarını düzenleme yetkisi.',

            self::LANGUAGE_MANAGEMENT   => 'Sistem dillerini ekleme, çıkarma ve varsayılan dili seçme yetkisi.',
            self::LANGUAGE_CREATE       => 'Yeni dil ekleme yetkisi.',
            self::LANGUAGE_UPDATE       => 'Dil bilgilerini düzenleme yetkisi.',
            self::LANGUAGE_DELETE       => 'Dil silme yetkisi.',

            self::CURRENCY_MANAGEMENT   => 'Para birimlerini listeleme yetkisi.',
            self::CURRENCY_CREATE       => 'Yeni para birimi (TL, USD vb.) ekleme yetkisi.',
            self::CURRENCY_UPDATE       => 'Para birimi kurlarını veya isimlerini düzenleme yetkisi.',
            self::CURRENCY_DELETE       => 'Para birimlerini sistemden kaldırma yetkisi.',

            self::UNIT_MANAGEMENT       => 'Ağırlık ve uzunluk ölçü birimlerini listeleme yetkisi.',
            self::UNIT_CREATE           => 'Yeni ölçü birimi tanımlama yetkisi.',
            self::UNIT_UPDATE           => 'Ölçü birimi detaylarını değiştirme yetkisi.',
            self::UNIT_DELETE           => 'Ölçü birimlerini sistemden kaldırma yetkisi.',

            self::SETTING_MANAGEMENT    => 'Logo, başlık ve sistem genel ayarlarını görme yetkisi.',
            self::SETTING_UPDATE        => 'Sistem genel ayarlarını kalıcı olarak değiştirme yetkisi.',
            self::ACTIVITY_VIEW         => 'Sistem üzerindeki tüm işlem loglarını inceleme yetkisi.',
        };
    }

    public static function getModuleLabel(string $module): string
    {
        return match ($module) {
            'user'       => 'Kullanıcı',
            'role'       => 'Rol ve Yetki',
            'permission' => 'İzin',
            'unit'       => 'Birim',
            'language'   => 'Dil',
            'currency'   => 'Para Birimi',
            'setting'    => 'Sistem Ayarları',
            'activity'   => 'Sistem Kayıtları',
            default      => ucfirst($module),
        };
    }
}
