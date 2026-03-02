<?php

namespace App\Enums;

enum CorePermission: string
{
    // User
    case USER_MANAGEMENT    = 'user.management';
    case USER_CREATE        = 'user.create';
    case USER_UPDATE        = 'user.update';
    case USER_DELETE        = 'user.delete';
    case USER_CHANGE_EMAIL  = 'user.change-email';
    case USER_VERIFY_EMAIL  = 'user.verify-email';
    case USER_CHANGE_STATUS = 'user.change-status';

    // Role
    case ROLE_MANAGEMENT = 'role.management';
    case ROLE_CREATE     = 'role.create';
    case ROLE_UPDATE     = 'role.update';
    case ROLE_DELETE     = 'role.delete';

    // Permission (Sadece Görüntüleme ve Bilgi Güncelleme)
    case PERMISSION_MANAGEMENT = 'permission.management';
    case PERMISSION_UPDATE     = 'permission.update';

    // Settings & Activity
    case SETTING_MANAGEMENT = 'setting.management';
    case SETTING_UPDATE     = 'setting.update';
    case ACTIVITY_VIEW      = 'activity.view';

    public function label(): string
    {
        return match($this) {
            self::USER_MANAGEMENT       => 'Kullanıcı Yönetimi',
            self::USER_CREATE           => 'Kullanıcı Oluşturma',
            self::USER_UPDATE           => 'Kullanıcı Düzenleme',
            self::USER_DELETE           => 'Kullanıcı Silme',
            self::USER_CHANGE_EMAIL     => 'E-posta Değiştirme',
            self::USER_VERIFY_EMAIL     => 'E-posta Doğrulama',
            self::USER_CHANGE_STATUS    => 'Durum Değiştirme',

            self::ROLE_MANAGEMENT       => 'Rol Yönetimi',
            self::ROLE_CREATE           => 'Rol Oluşturma',
            self::ROLE_UPDATE           => 'Rol Düzenleme',
            self::ROLE_DELETE           => 'Rol Silme',

            self::PERMISSION_MANAGEMENT => 'İzin Listeleme',
            self::PERMISSION_UPDATE     => 'İzin Bilgilerini Güncelleme',

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
            self::USER_CHANGE_EMAIL     => 'Kullanıcıların e-posta adreslerini değiştirme yetkisi.',
            self::USER_VERIFY_EMAIL     => 'Kullanıcıların e-posta adreslerini manuel olarak onaylama yetkisi.',
            self::USER_CHANGE_STATUS    => 'Kullanıcıların hesap durumlarını (aktif, pasif, taslak) değiştirme yetkisi.',

            self::ROLE_MANAGEMENT       => 'Kullanıcı rollerini listeleme ve yönetme yetkisi.',
            self::ROLE_CREATE           => 'Sisteme yeni yetki grupları (roller) ekleme yetkisi.',
            self::ROLE_UPDATE           => 'Rollerin sahip olduğu izinleri değiştirme yetkisi.',
            self::ROLE_DELETE           => 'Mevcut rollerini sistemden kaldırma yetkisi.',

            self::PERMISSION_MANAGEMENT => 'Sistemde tanımlı teknik izinleri görme yetkisi.',
            self::PERMISSION_UPDATE     => 'İzinlerin etiket ve açıklamalarını düzenleme yetkisi.',

            self::SETTING_MANAGEMENT    => 'Logo, başlık ve sistem genel ayarlarını görme yetkisi.',
            self::SETTING_UPDATE        => 'Sistem genel ayarlarını kalıcı olarak değiştirme yetkisi.',
            self::ACTIVITY_VIEW         => 'Sistem üzerindeki tüm işlem loglarını inceleme yetkisi.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function moduleLabels(): array
    {
        return [
            'user'       => 'Kullanıcı',
            'role'       => 'Rol ve Yetki',
            'permission' => 'İzin',
            'setting'    => 'Sistem Ayarları',
            'activity'   => 'Sistem Kayıtları',
        ];
    }
}
