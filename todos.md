# Kullanıcı Yönetimi - Yapılacaklar

## 1. Şifre Oluşturma (Kullanıcı Ekleme) ✅

**Mevcut durum:** Kullanıcı eklerken şifre + şifre onayı alanı var, `Password::min(8)` validasyonu uygulanıyor.

**Görüş:** Şifreyi admin'in belirlemesi yerine otomatik güçlü şifre üretilmesi daha doğru. Önerilen akış:
- Formdan şifre alanları kaldırılsın
- Backend'de `Str::password(16)` ile güçlü şifre üretilsin
- Kullanıcıya hoşgeldin maili ile "şifre belirleme" linki gönderilsin (Laravel'in password reset mekanizması kullanılabilir)
- Bu sayede admin hiçbir zaman kullanıcının şifresini bilmemiş olur

**Yapılanlar:**
- [x] `Create.vue`'dan şifre ve şifre onayı alanlarını kaldır
- [x] `UserCreateRequest`'ten şifre validasyonunu kaldır
- [x] `UserService::store()` içinde rastgele şifre üret
- [x] Hoşgeldin maili ile şifre belirleme linki gönder (madde 8 ile birlikte)

---

## 2. Şifre Alanını Düzenlemeden Kaldırma ✅

**Mevcut durum:** `Edit.vue`'da şifre alanı nullable olarak mevcut, boş bırakılırsa değişmiyor.

**Görüş:** Katılıyorum. Kullanıcı kendi şifresini profil sayfasından değiştirmeli, admin başkasının şifresini belirleyememeli. Güvenlik açısından doğru bir karar.

**Yapılanlar:**
- [x] `Edit.vue`'dan şifre ve şifre onayı alanlarını kaldır
- [x] `UserUpdateRequest`'ten şifre validasyonunu kaldır
- [x] `UserService::update()` içinden şifre güncelleme mantığını kaldır

---

## 3. E-posta Değiştirme Yetkisi ✅

**Mevcut durum:** `UserUpdateRequest`'te e-posta değiştirilebiliyor, herhangi bir ek yetki kontrolü yok.

**Görüş:** Katılıyorum. E-posta değişikliği hassas bir işlem. Yeni bir permission tanımlanmalı. E-posta değiştirildiğinde `email_verified_at` null yapılmalı.

**Yapılanlar:**
- [x] `PermissionEnum`'a `USER_CHANGE_EMAIL = 'user.change-email'` ekle
- [x] `UserService::update()` içinde e-posta değişikliğinde bu izni kontrol et
- [x] E-posta değiştirildiğinde `email_verified_at = null` yap
- [x] Frontend'de `can('user.change-email')` yoksa e-posta alanını `disabled` yap

---

## 4. E-posta Onaylama Yetkisi ✅

**Mevcut durum:** Admin panelinden e-posta onaylama mekanizması yok.

**Görüş:** Katılıyorum. Yetkili admin, bir kullanıcının e-posta adresini manuel olarak onaylayabilmeli.

**Yapılanlar:**
- [x] `PermissionEnum`'a `USER_VERIFY_EMAIL = 'user.verify-email'` ekle
- [x] `UserController`'a `verifyEmail(User)` metodu ekle
- [x] `UserService`'e `verifyEmail()` metodu ekle (`email_verified_at = now()`)
- [x] `Edit.vue`'da e-posta alanının yanına onay durumu göster (onaylı/onaysız badge)
- [x] Onaysızsa ve yetki varsa "Onayla" butonu göster
- [x] Kullanıcı listesinde onay durumunu göster (ikon)
- [x] `routes/web.php`'ye `verify-email` route ekle

---

## 5. UserStatus Enum Değerlendirmesi ✅

**Mevcut durum:** PASSIVE(0), ACTIVE(1), DRAFT(2). PASSIVE giriş yapamaz, DRAFT salt okunur, ACTIVE tam erişim.

**Görüş:** Mevcut 3 durum yeterli. Ek durum eklemek karmaşıklığı artırır ve şu an ihtiyaç görünmüyor.

**Yapılacak:** Şu an bir işlem gerekmez.

---

## 6. Status Yönetim Yetkisi ✅

**Mevcut durum:** Status değiştirmek için ayrı bir permission yok, `user.update` yetkisi yeterli sayılıyor.

**Görüş:** Katılıyorum. Status değişikliği kritik bir işlem, ayrı permission ile kontrol edilmeli.

**Yapılanlar:**
- [x] `PermissionEnum`'a `USER_CHANGE_STATUS = 'user.change-status'` ekle
- [x] `UserService::update()` içinde status değişikliğinde bu izni kontrol et
- [x] Frontend'de `can('user.change-status')` yoksa status alanını `disabled` yap

---

## 7. Kullanıcı Silme ✅

**Mevcut durum:** `user.delete` izni mevcut. `UserService::delete()` soft delete yapıyor. Admin ve id=1 koruması var.

**Yapılanlar:**
- [x] Silme işleminde kullanıcının aktif oturumlarını sonlandır (`sessions` tablosundan temizle)
- [x] Frontend'de silme öncesi onay dialogu mevcut

---

## 8. Hoşgeldin Maili ✅

**Mevcut durum:** `UserCreatedNotification` sadece veritabanı kanalında çalışıyor. Mail gönderimi yok.

**Görüş:** Kesinlikle katılıyorum. Ayrı bir `SendWelcomeEmail` listener oluşturuldu.

**Yapılanlar:**
- [x] `WelcomeUserMail` (Mailable) sınıfı oluştur
- [x] `mail.users.welcome` Markdown mail template'i oluştur
- [x] Ayrı `SendWelcomeEmail` listener oluştur (ShouldQueue, subscriber pattern)
- [x] Şifre belirleme linki için `Password::createToken()` kullan

---

## 9. Ek Maddeler ✅

### 9a. Kendi Hesabını Düzenleme Koruması ✅
- [x] `isSelf` computed ile kendi hesabını düzenlerken status, roller ve izinler alanları kilitlendi
- [x] Uyarı mesajı eklendi

### 9b. Kullanıcı Listesinde Rol Bazlı Filtreleme ✅
- [x] Rol filtresi eklendi (Select bileşeni + backend desteği)

### 9c. Son Giriş Bilgisi ✅
- [x] Kullanıcı listesinde ve düzenleme sayfasında `last_login_at` gösteriliyor

### 9d. Yeni İzinler İçin Seeder ✅
- [x] PermissionEnum'a 3 yeni değer eklendi (label + description ile)

---

## Çalıştırılması Gereken Komutlar

```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
npm run build
```

---
---

# Genel Sistem İyileştirme Önerileri

Tanımlamalar modülü (Currency, Language, Unit) hariç, sistemin geri kalanı için tespit edilen iyileştirme önerileri:

---

## A. Aktivite Logları

### A1. Pruning Süresi Tutarsızlığı (Bug)
**Dosya:** `app/Models/Activity.php`
**Sorun:** `prunable()` metodu 6 aylık süre tanımlarken, yorum satırı "12 aydan eski" diyor. Hangisinin doğru olduğuna karar verilip düzeltilmeli.

cevap: 6 ay.

### A2. Gelişmiş Filtreleme
**Dosya:** `resources/js/pages/app/settings/Activity.vue`
**Öneri:** Mevcut `type` filtresine ek olarak tarih aralığı filtresi ve kullanıcı filtresi eklenebilir. Büyük sistemlerde aktivite loglarında arama yapmak zorlaşıyor.

Cevap: olabilir.

---

## B. Rol/İzin Yönetimi

### B1. Rol Silme Güvenliği
**Dosya:** `app/Services/Users/RoleService.php`
**Öneri:** Rol silinmeden önce o role atanmış kullanıcı sayısı kontrol edilmeli. Eğer aktif kullanıcılar varsa uyarı verilmeli veya silme engellenmelidir. "Super Admin" rolünün silinmesi kesinlikle engellenmeli.

Cevap: Katılıyorum.

### B2. İzin Seçiminde Arama
**Dosya:** `resources/js/pages/app/users/Role/Create.vue`, `Edit.vue`
**Öneri:** Rol oluştururken/düzenlerken izin seçim alanına arama kutusu eklenebilir. İzin sayısı arttıkça modül bazlı gruplama yeterli olmayabilir, hızlı arama büyük kolaylık sağlar.

Cevap: Olabilir.

### B3. Rol Kullanım İstatistikleri
**Öneri:** Rol listesinde her rolün kaç kullanıcıya atandığı gösterilirse yönetim kolaylaşır.

Cevap: gerek yok. fazla iş.

---

## C. Bildirimler

### C1. Bildirim Tipi Görselleri
**Dosya:** `resources/js/pages/app/profile/Notifications.vue`
**Öneri:** Bildirimler düz liste halinde. Bildirim tipine göre ikon ve renk uygulanırsa (başarı=yeşil, uyarı=sarı, bilgi=mavi) kullanıcı deneyimi iyileşir.

Cevap: olabilir.

### C2. Okunmamış Bildirim Sayacı (Header'da)
**Öneri:** Okunmamış bildirim sayısı `HandleInertiaRequests` ile paylaşılıyor ama sidebar/header'da badge olarak gösterilmeli. Kullanıcı bildirim sayfasına gitmeden kaç okunmamış bildirimi olduğunu görebilmeli.

Cevap: olabilir.

---

## D. Profil Modülü

### D1. Profil Sayfasında Son Giriş Bilgisi
**Öneri:** Kullanıcının kendi profil sayfasında `last_login_at` bilgisi gösterilmiyor. Güvenlik farkındalığı açısından "Son giriş tarihiniz" bilgisi profilde de yer alabilir.

Cevap: evet

---

## E. Frontend Kalitesi

### E1. Tablo Yükleme İskeletleri (Skeleton)
**Öneri:** Sayfalanmış listeler (kullanıcılar, roller, izinler, aktiviteler) yüklenirken tablo boş görünüyor. Yükleme sırasında skeleton/pulse animasyonu eklenirse UX iyileşir.

Cevap: tamam

### E2. Form Hata Temizleme
**Öneri:** Form alanlarında hata gösterildikten sonra kullanıcı o alana yazmaya başlayınca hata mesajı otomatik temizlenmiyor. Inertia `useForm` ile `form.clearErrors('fieldName')` watch ile tetiklenebilir.

cevap: katılıyorum.

---

## F. Güvenlik

### F1. Super Admin Rolü UI Koruması
**Dosya:** `resources/js/pages/app/users/Role/Index.vue`
**Öneri:** Super Admin rolü listede normal roller gibi görünüyor. Düzenleme ve silme butonları gizlenebilir veya devre dışı bırakılabilir. Backend'de koruma olsa bile frontend'de de görsel olarak belirtilmeli.

cevap: Super Admin'e sadece Super Admin erişebilmeli. rol listesinde ve kullanıcı listesinde görünmüyor diye biliyorum.

Ek: Toast gösteriminde sorun var. Düzgün görünmüyor. Elden geçirilmeli. Shadcn2a ait sonner komponenti kullanılıyor sanırım.
