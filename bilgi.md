# Sistem Önerileri ve İyileştirmeler

Mevcut implementasyon (Faz 1-6) tamamlandıktan sonra tespit edilen öneriler.

---

## 1. Güvenlik

### Kritik

- **Authorization Policy eksik**: Route'lar middleware ile korunuyor ancak fine-grained yetkilendirme yok. Örneğin bir kullanıcı, başka bir kullanıcının rolünü değiştirebilir mi? `UserPolicy`, `RolePolicy`, `PermissionPolicy` oluşturulmalı ve controller'larda `$this->authorize()` kullanılmalı.

- **Rate limiting eksik**: Sadece şifre değiştirmede `throttle:6,1` var. Kullanıcı oluşturma, rol/yetki işlemleri gibi hassas endpoint'lere de rate limiting eklenmeli.

- **Soft delete cascade**: Kullanıcı soft-delete edildiğinde ilişkili roller ve yetkiler otomatik temizlenmiyor. `UserService::delete()` içinde `$user->syncRoles([])` ve `$user->syncPermissions([])` çağrılabilir.

### Orta

- **Loose equality**: Service'lerdeki değişiklik takibinde `!=` (loose) yerine `!==` (strict) kullanılmalı. Tip farklılıkları yanlış pozitif sonuçlara yol açabilir.

- **IP/User-Agent doğrulama**: Loglanan IP ve User-Agent değerleri sanitize edilmiyor. Çok uzun User-Agent stringleri veritabanı sorunlarına neden olabilir.

---

## 2. Veritabanı

### Eksik İndeksler

```php
// users tablosu
$table->index('status');       // Filtreleme performansı
$table->index('language_id');  // Foreign key performansı

// notifications tablosu
$table->index('read_at');      // Okunmamış bildirim sorguları
```

### Cache Önerileri

- **Tanımlama tabloları** (languages, currencies, units) nadiren değişir. Service'lerde `Cache::remember()` ile cache'lenebilir. Oluştur/güncelle/sil işlemlerinden sonra cache temizlenmeli.

- Spatie Permission cache'i zaten otomatik yönetiliyor (config/permission.php). Ekstra bir şey gerekmez.

---

## 3. Frontend

### Toast/Flash Mesaj Sistemi

En önemli eksik. Controller'lar `back()->with('success', '...')` döndürüyor ama Vue tarafında bunu yakalayıp gösteren bir mekanizma yok. Sonner (toast) bileşeni zaten kurulu.

**Çözüm**: `HandleInertiaRequests` middleware'inde flash mesajı paylaş:

```php
'flash' => [
    'success' => fn () => $request->session()->get('success'),
    'error' => fn () => $request->session()->get('error'),
],
```

Layout'ta Sonner ile göster:

```vue
import { toast } from '@/components/ui/sonner';
const page = usePage();
watch(() => page.props.flash, (flash) => {
    if (flash.success) toast.success(flash.success);
    if (flash.error) toast.error(flash.error);
}, { deep: true, immediate: true });
```

### Global Hata Yakalama

Inertia request'leri başarısız olduğunda kullanıcıya bilgi verilmiyor. `app.ts` içinde global hata listener'ı eklenebilir.

### Boş Durum Tutarlılığı

Bazı sayfalarda "Henüz kayıt eklenmemiş" yazısı var, bazılarında ikon ile desteklenmiş boş durum. Tüm sayfalar tutarlı olmalı. Reusable bir `EmptyState` bileşeni düşünülebilir.

---

## 4. Mimari

### Service Katmanı

- **Yetkilendirme service'lerde olmalı**: Şu an tüm yetkilendirme middleware'e bırakılmış. Ama "admin kendi rolünü silebilir mi?" gibi iş kuralları service katmanında olmalı.

- **Metot isimlendirme tutarlılığı**: Bazı service'lerde `getAll()`, bazılarında `getPaginated()`, `getActiveLanguages()`. Tutarlı bir convention belirlenebilir.

### DTO (Data Transfer Object) Kullanımı

Notification ve Activity verileri `.through()` closure'larında dönüştürülüyor. Bu dönüşümler DTO sınıflarına taşınabilir, böylece farklı yerlerde tekrar kullanılabilir.

---

## 5. Üretim Hazırlığı

### Toplu İşlemler

Kullanıcı listesinde toplu seçim ve toplu işlem (durum değiştirme, silme) özelliği beklenir. Tablo satırlarına checkbox eklenip "Seçilenleri Sil" / "Seçilenleri Pasif Yap" gibi aksiyonlar eklenebilir.

### Export

Kullanıcı, rol, dil vb. listeleri CSV/Excel olarak dışa aktarma. `maatwebsite/excel` veya basit CSV oluşturma ile sağlanabilir.

### Bildirim Tercihleri

Kullanıcılar hangi bildirimleri alacaklarını seçemiyor. Profil sayfasında bir bildirim tercih paneli eklenebilir.

### İki Faktörlü Doğrulama Tamamlama

`TwoFactorAuthenticationController` sadece `show()` metodu var. Enable/disable, kurtarma kodları gösterme gibi aksiyonlar da eklenmeli.

---

## 6. Kod Kalitesi

### Typo Düzeltmesi

- `writeAcces` middleware alias'ı → `writeAccess` olmalı. `bootstrap/app.php` ve `routes/web.php`'deki tüm referanslar güncellenmeli.

### Sabit Değerler

```php
// Şu an hardcoded olan değerler:
// - Pagination: 15, 25
// - Cache TTL: 3600, 86400
// - Upload directory: 'settings'

// Bunlar sabit olarak tanımlanabilir veya config'e taşınabilir.
```

### Gereksiz Import Uyarıları

IDE bazı service dosyalarında "unnecessary use directive" uyarısı veriyor. `vendor/bin/pint --dirty` çalıştırılarak temizlenebilir.

---

## Öncelik Sırası

| Öncelik | Konu | Açıklama |
|---------|------|----------|
| 🔴 Kritik | Toast/Flash mesaj sistemi | Kullanıcı hiçbir geri bildirim alamıyor |
| 🔴 Kritik | Authorization Policies | Yetki kontrolü eksik |
| 🟡 Yüksek | Eksik veritabanı indeksleri | Performans |
| 🟡 Yüksek | Strict equality (`!==`) | Veri bütünlüğü |
| 🟢 Orta | Toplu işlemler | Kullanılabilirlik |
| 🟢 Orta | Export özelliği | Kullanılabilirlik |
| ⚪ Düşük | writeAcces typo | Kozmetik |
| ⚪ Düşük | Sabit değer çıkarma | Bakım kolaylığı |
