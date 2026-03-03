# Sistem İnceleme Raporu ve Yapılacaklar

> Tarih: 2026-03-03 | Kapsam: Backend, Frontend, Altyapı, Güvenlik, Veritabanı

---

## KRİTİK (Hata/Güvenlik)

### 1. Settings key tutarsızlığı
`DefinitionsSeeder` `default_language_id` key'i kullanırken, `DatabaseSeeder` + `SettingsController` + `UpdateGeneralSettingsRequest` `default_language` kullanıyor. Varsayılan dil/para birimi/ülke/vergi değerleri hiçbir zaman doğru okunmuyor.
- [ ] `database/seeders/DefinitionsSeeder.php` — key'leri `_id` suffix'siz yap
- [ ] `database/seeders/DatabaseSeeder.php` — tutarlılığı doğrula

### 3. `UserService::delete()` UUID ile `=== 1` karşılaştırması
UUID birincil anahtar kullanıldığı halde `$user->id === 1` kontrolü yapılıyor. Bu kontrol hiçbir zaman true olmaz, Super Admin silme koruması devre dışı.
- [ ] `app/Services/Users/UserService.php:186` — UUID veya rol bazlı kontrol yap

### 6. `PermissionService::clearCache()` Spatie cache temizlemiyor
İzin güncellendiğinde Spatie'nin `PermissionRegistrar::forgetCachedPermissions()` çağrılmıyor. İzin değişiklikleri anlık efekt göstermez.
- [ ] `app/Services/Users/PermissionService.php:96-99` — `app(PermissionRegistrar::class)->forgetCachedPermissions()` ekle

### 7. `ActivityService` sort_field whitelist kontrolü yok
`$sortField` kullanıcı girdisinden geliyor ve hiçbir whitelist kontrolü yok. Potansiyel SQL injection riski.
- [ ] `app/Services/Common/ActivityService.php:94-98` — İzin verilen alan listesi tanımla

### 8. `handleFilePreview` bug — önizleme çalışmıyor
Template'de ref unwrap edilip değer geçiriliyor, ref'in kendisi değil. `preview.value = ...` çalışmaz, logo/favicon önizlemesi hiç güncellenmez.
- [ ] `resources/js/pages/app/settings/GeneralSettings.vue:100,120,140` — Ref geçirme mantığını düzelt

---

## YÜKSEK ÖNCELİK

### 9. `router.on` listener temizlenmemesi — bellek sızıntısı
`router.on('start')` ve `router.on('finish')` listener'ları `onUnmounted`'da temizlenmiyor. Component kaldırılsa bile listener çalışmaya devam eder.
- [ ] `resources/js/pages/app/users/Users/Index.vue:174-175`
- [ ] `resources/js/pages/app/users/Role/Index.vue:68-69`
- [ ] `resources/js/pages/app/users/Permissions/Index.vue:95-96`
- [ ] `resources/js/pages/app/settings/Activity.vue:93-94`

### 10. Modül layout sidebar'larında izin kontrolü yok
`sidebarNavItems` tanımlarında `show` alanı kullanılmıyor. İzni olmayan kullanıcıya da navigasyon linkleri görünüyor (URL'e giderse 403 alır ama link hala görünür).
- [ ] `resources/js/pages/app/users/partials/Layout.vue`
- [ ] `resources/js/pages/app/definitions/partials/Layout.vue`
- [ ] `resources/js/pages/app/settings/partials/Layout.vue`
- [ ] `resources/js/pages/app/profile/partials/Layout.vue`

### 11. `archiveAllRead` race condition + N+1 INSERT
Her bildirim için ayrı `ArchivedNotification::create()` çağrısı yapılıyor. Transaction kullanılmıyor, eşzamanlı isteklerde duplicate kayıt riski var.
- [ ] `app/Services/Common/NotificationService.php:147-166` — `DB::transaction` + toplu `insert()` kullan

### 12. Hardcoded URL kullanımı
Wayfinder action/route fonksiyonları yerine string URL yazılmış.
- [ ] `resources/js/pages/app/profile/Session.vue:45,63,73`
- [ ] `resources/js/pages/app/profile/Notifications.vue:65`

### 13. `ArchiveOldNotifications` açıklama-kod uyuşmazlığı
Komut açıklaması "30 günden eski" diyor, kod `subDays(60)` ile 60 gün kullanıyor.
- [ ] `app/Console/Commands/ArchiveOldNotifications.php:25,34` — Açıklama veya kodu hizala

---

## ORTA ÖNCELİK (Tutarsızlık/Kod Kalitesi)

### 14. `destroy()` metodlarında hardcoded IP/user-agent
`config('otomasyon.defaults.ip_address')` yerine `'127.0.0.1'` ve `'unknown'` hardcoded yazılmış.
- [ ] `app/Http/Controllers/Users/RoleController.php:99`
- [ ] `app/Http/Controllers/Definitions/LanguageController.php:79`
- [ ] `app/Http/Controllers/Definitions/CurrencyController.php:79`
- [ ] `app/Http/Controllers/Definitions/UnitController.php:79`
- [ ] `app/Http/Controllers/Definitions/CountryController.php:75`
- [ ] `app/Http/Controllers/Definitions/TaxController.php:75`

### 15. `UserController::bulkAction` inline validasyon
Proje kuralına göre FormRequest kullanılmalı, inline `$request->validate()` uygunsuz.
- [ ] `app/Http/Controllers/Users/UserController.php:139-143` — `UserBulkActionRequest` oluştur

### 16. `bulkAction` hardcoded status değerleri
`status => 1` ve `status => 2` yerine `UserStatus` enum kullanılmalı.
- [ ] `app/Services/Users/UserService.php:221-225`

### 17. Service'lerde tekrar eden `changes` hesaplama kodu
Her service'in `update()` metodunda aynı değişiklik takip döngüsü tekrarlanıyor. Trait veya base service'e taşınmalı.
- [ ] Tüm service `update()` metodları — `TracksChanges` trait oluştur

### 18. `SettingsController::index` cache bypass
`Language::query()`, `Currency::query()` gibi doğrudan DB sorguları yapılıyor. Mevcut service `allActive()` metodları kullanılmıyor.
- [ ] `app/Http/Controllers/Settings/SettingsController.php:53-56` — Service metodlarını kullan

### 19. `RoleService` ve `PermissionService` cache tanımlı ama kullanılmıyor
`CACHE_KEY_ALL` sabiti tanımlı, `clearCache()` bu key'i temizliyor ama `all()` metodlarında cache kullanılmıyor.
- [ ] `app/Services/Users/RoleService.php` — `all()` ve `allForSelect()` cache ekle
- [ ] `app/Services/Users/PermissionService.php` — `groupedAll()` cache ekle

### 20. Log listener'ları `ShouldQueue` kullanmıyor
12 aktivite log listener'ı senkron çalışıyor. Yüksek trafikte her işlem request süresini uzatır.
- [ ] `app/Listeners/Log*.php` (12 dosya) — `ShouldQueue` implement et

### 21. `SoftDeleteOldActivities` + `model:prune` zaman çakışması
İkisi de 6 ay süresi kullanıyor. Bir kayıt soft delete edildiği gün hemen hard delete edilebilir. Aralarında buffer süresi olmalı (örn: soft delete 6 ay, hard delete 12 ay).
- [ ] `app/Console/Commands/SoftDeleteOldActivities.php`
- [ ] `app/Models/Activity.php` — prunable süresini uzat

### 22. `SessionController` service katmanı yok
DB sorguları ve `resolveLocation()` yardımcı metodu doğrudan controller'da. Return type hint'ler eksik.
- [ ] `app/Http/Controllers/Profile/SessionController.php` — `SessionService` oluştur

### 24. Pagination tutarsızlığı
Activity, Notifications, NotificationsArchived, Session sayfaları `v-html` ile ham pagination butonları kullanırken diğer sayfalar Shadcn Pagination bileşeni kullanıyor.
- [ ] 4 sayfa — Shadcn Pagination bileşenine geçir

### 26. Native `confirm()` kullanımı
Diğer tüm silme onayları `AlertDialog` kullanırken burada native browser dialog kullanılıyor.
- [ ] `resources/js/pages/app/users/Users/Index.vue:129` — `AlertDialog` kullan

### 27. `Activity.vue` tipsiz veri
`data: any[]` ve `links: any[]` olarak tanımlanmış, TypeScript tip güvenliği yok.
- [ ] `resources/js/pages/app/settings/Activity.vue:34-35` — Doğru tipleri tanımla

### 28. `ActivityController` tutarsız yetkilendirme
`Gate::denies` + manuel `back()` kullanıyor, diğer controller'lar `Gate::authorize` (exception fırlatır) pattern'ini kullanıyor. Ayrıca service'de de aynı kontrol var (çift kontrol).
- [ ] `app/Http/Controllers/Settings/ActivityController.php:31-33` — `Gate::authorize` pattern'ine geçir

---

## DÜŞÜK ÖNCELİK (Temizlik/Stil)

### 33. `activities.type` kolonu index eksik
`type` kolonu filtreleme ve distinct sorgularda kullanılıyor ama index yok.
- [ ] Migration oluştur — `activities.type` index ekle

### 35. Erişilebilirlik eksiklikleri
- [ ] Role/Create, Role/Edit, Users/Create, Users/Edit — div click handler'lara `role="button"` + keyboard support ekle
- [ ] Modül layout nav elementleri — `aria-label` ekle

### 36. `ArchivedNotification::prunable()` sadece `deleted_at`'e bakıyor
Arşivlenmiş ama silinmemiş kayıtlar hiç temizlenmez. `archived_at`'e göre de pruning yapılmalı.
- [ ] `app/Models/ArchivedNotification.php:48-52`

### 37. `LanguageService::delete()` ilişki kontrolü eksik
Dil silinmeden önce o dile bağlı kullanıcı olup olmadığı kontrol edilmiyor.
- [ ] `app/Services/Definitions/LanguageService.php:106` — `language->users()->exists()` kontrolü ekle

### 38. `HelperServiceProvider` aşırı karmaşık cache mekanizması
Tek bir `site_helpers.php` dosyası için MD5 hash bazlı cache mekanizması overengineering. Basit `require_once` yeterli.
- [ ] `app/Providers/HelperServiceProvider.php` — Sadeleştir

### 39. `settings()` helper her çağrıda container resolve
`app(SettingService::class)` her çağrıda yeniden resolve ediliyor. `HandleInertiaRequests` içinde 8 kez çağrılıyor.
- [ ] `app/Helpers/site_helpers.php` — Singleton binding veya static cache kullan

### 40. `HandleInertiaRequests` her request'te `Storage::exists()` çağırıyor
Logo/favicon için her request'te disk I/O yapılıyor, cache'lenmemiş.
- [ ] `app/Http/Middleware/HandleInertiaRequests.php` — Dosya varlık kontrolünü cache'le
