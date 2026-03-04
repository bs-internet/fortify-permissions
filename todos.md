# Sistem İnceleme Raporu ve Yapılacaklar (TODO)

> Tarih: 2026-03-04 | Kapsam: Güvenlik, Performans, Tutarlılık, Kod Kalitesi

---

## 🔴 KRİTİK (Hata / Güvenlik)

### 1. `CountryController` ve Diğer Tanımlama Controller'larında Yetkilendirme (Authz) Eksikliği
`index`, `store`, `update`, `destroy` metodlarında Controller seviyesinde veya Middleware seviyesinde explicit bir yetki kontrolü (`Gate::authorize`) veya Policy kontrolü görünmüyor. Her ne kadar Service katmanda kontroller olsa da, Controller seviyesinin de korunması veya `Authorize` middleware'i ile zırhlanması gerekir.
- [ ] Tüm Controller'ların giriş metodlarını kontrol et ve `Gate::authorize` veya `middleware('can:...')` ekle.

### 2. Hardcoded IP ve User-Agent Varsayılanları
`127.0.0.1` ve `unknown` değerleri birçok yerde (Controller ve Service) hardcoded olarak duruyor. `config('otomasyon.defaults.ip_address')` kullanımı artsa da, hala sızıntılar var.
- [ ] `UserController`, `CountryController`, `TaxController` vb. içerisindeki manuel stringleri temizle ve config'e bağla.

---

## 🟠 YÜKSEK ÖNCELİK (Performans / UX)

### 3. `Activity` Pruning Stratejisi
`Activity` modeli sadece silinmiş (soft-deleted) kayıtları temizliyor. Ancak çok fazla aktivite biriken bir sistemde "silinmemiş" (active) kayıtların da belli bir süre (örn: 1 yıl) sonra silinmesi veritabanı sağlığı için kritiktir.
- [ ] `Activity` modeli için aktif kayıtları da kapsayan bir pruning kuralı tanımla.

### 4. `Sonner` (Toast) Görünüm Doğrulaması
Pozisyon ve z-index iyileştirmesi yapıldı (`top-center`, `z-index: 9999`) ancak özellikle mobil cihazlarda ve dar ekranlarda bildirimlerin içeriği kesilip kesilmediği test edilmeli.
- [ ] Mobil uyumluluk testlerini gerçekleştir.

---

## 🟡 ORTA ÖNCELİK (Tutarsızlık / Temizlik)

### 5. Activity Sayfası Pagination Tutarsızlığı
Kayıtlı Kullanıcılar vb. sayfalarda Shadcn UI `Pagination` bileşeni kullanılırken, Etkinlik Kayıtları (`Activity.vue`) sayfasında ham HTML/CSS butonu (`v-html`) ve manuel döngü kullanılıyor.
- [ ] `resources/js/pages/app/settings/Activity.vue` -> Shadcn Pagination bileşenine dönüştür.

### 6. Controller'larda i18n Eksikliği
Başarı mesajları (örn: "Ülke başarıyla eklendi") doğrudan Controller'larda hardcoded string olarak duruyor. 
- [ ] Tüm Controller'lardaki `with('success', ...)` mesajlarını `__()` fonksiyonuna geçir.

---

## 🔵 DÜŞÜK ÖNCELİK (Kod Kalitesi)

### 7. Listener `ShouldQueue` Taraması
Bazı listener'lar `ShouldQueue` kullanırken bazılarında (örn: Şifre sıfırlama veya Hoşgeldin emaili gibi) bu durum senkron kalmış olabilir.
- [ ] `app/Listeners` altındaki tüm dosyaları kontrol et.
