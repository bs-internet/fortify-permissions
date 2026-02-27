# Yapılacaklar Listesi

`bilgi.md` dosyasından çıkarılan iyileştirmeler, öncelik sırasına göre.

---

## 🔴 Kritik

- [x] **Toast/Flash mesaj sistemi** — `HandleInertiaRequests` middleware'ine flash mesaj paylaşımı ekle, Layout'ta Sonner ile göster
- [x] **Authorization Policies** — `UserPolicy`, `RolePolicy`, `PermissionPolicy` oluştur, controller'larda `$this->authorize()` kullan
- [x] **Rate limiting genişlet** — Kullanıcı oluşturma, rol/yetki endpoint'lerine rate limiting ekle
- [x] **Soft delete cascade** — `UserService::delete()` içinde `syncRoles([])` ve `syncPermissions([])` çağır

## 🟡 Yüksek

- [x] **Eksik veritabanı indeksleri** — `users.status`, `users.language_id`, `notifications.read_at` indeksleri ekle
- [x] **Strict equality (`!==`)** — Service'lerdeki `!=` kullanımlarını `!==` ile değiştir
- [x] **IP/User-Agent sanitizasyonu** — Loglanan değerleri doğrula ve sınırla
- [x] **Cache ekle** — Tanımlama tabloları (languages, currencies, units) için `Cache::remember()` uygula

## 🟢 Orta

- [x] **Toplu işlemler** — Kullanıcı listesine checkbox ve toplu aksiyon ekle
- [x] **Export özelliği** — Listeler için CSV/Excel export
- [~] ~~**Bildirim tercihleri** — (Gerek görülmediği için iptal edildi)~~
- [x] **Sidebar Yetki Kontrolü** — Sidebar menülerini policy kurallarına göre dinamik hale getir
- [x] **İki Faktörlü Doğrulama tamamla** — Enable/disable, kurtarma kodları aksiyonları
- [x] **Global hata yakalama** — `app.ts` içinde Inertia global hata listener'ı ekle
- [x] **EmptyState bileşeni** — Tutarlı boş durum gösterimi için reusable bileşen
- [x] **Service katmanında yetkilendirme** — İş kurallarını service'lere taşı
- [x] **Metot isimlendirme tutarlılığı** — Service'lerde convention belirlendi ve uygulandı
- [~] ~~**DTO kullanımı** — (Resource kullanımı yeterli görüldüğünden iptal edildi)~~

## ⚪ Düşük

- [x] **`writeAcces` typo düzelt** — `writeAccess` olarak güncellendi
- [x] **Sabit değerleri çıkar** — `config/otomasyon.php` dosyasına taşındı
- [x] **Gereksiz import'ları temizle** — Kod temizliği yapıldı
