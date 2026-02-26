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

- [ ] **Toplu işlemler** — Kullanıcı listesine checkbox ve toplu aksiyon ekle
- [ ] **Export özelliği** — Listeler için CSV/Excel export
- [ ] **Bildirim tercihleri** — Profil sayfasına bildirim tercih paneli ekle
- [ ] **İki Faktörlü Doğrulama tamamla** — Enable/disable, kurtarma kodları aksiyonları
- [ ] **Global hata yakalama** — `app.ts` içinde Inertia global hata listener'ı ekle
- [ ] **EmptyState bileşeni** — Tutarlı boş durum gösterimi için reusable bileşen
- [ ] **Service katmanında yetkilendirme** — İş kurallarını service'lere taşı
- [ ] **Metot isimlendirme tutarlılığı** — Service'lerde convention belirle
- [ ] **DTO kullanımı** — Notification/Activity dönüşümlerini DTO sınıflarına taşı

## ⚪ Düşük

- [ ] **`writeAcces` typo düzelt** — `writeAccess` olarak güncelle
- [ ] **Sabit değerleri çıkar** — Hardcoded değerleri config'e taşı
- [ ] **Gereksiz import'ları temizle** — `vendor/bin/pint --dirty` çalıştır
