# Users Modülü Yeniden Yapılandırma

## Backend Değişiklikleri

- [ ] **Route güncellemeleri** — `routes/web.php`'ye `create` ve `edit` route'ları ekle, `export` route'unu kaldır.
- [ ] **Controller güncellemeleri** — `UserController`'a `create()` ve `edit()` metotları ekle, `export()` kaldır, `index()`'ten gereksiz veriyi temizle.
- [ ] **Service/Backend dil desteği** — `UserService::store/update` içinde `language_id` boşsa default dil ata.

## Frontend — Yeni Sayfalar

- [ ] **Users/Create.vue oluştur** — Role/Create.vue ile aynı tasarım stili. Alanlar: Ad, E-posta, Ünvan, Durum, Şifre, Şifre Tekrar, Dil (koşullu), Roller (checkbox), Doğrudan Yetkiler (grouped checkbox).
- [ ] **Users/Edit.vue oluştur** — Role/Edit.vue ile aynı tasarım stili. Mevcut kullanıcı verileri dolu gelecek. Sil butonu yok.

## Frontend — Index.vue Sadeleştirme

- [ ] **Tablo sadeleştir** — Doğrudan Yetkiler, Dil sütunlarını kaldır. Durum'u Ad'ın önüne al. Sil butonunu kaldır. Düzenle butonu edit sayfasına link olacak.
- [ ] **Dialog/modal CRUD kaldır** — Create/Edit dialog kodunu ve ilgili state'leri kaldır.
- [ ] **Dışa Aktar kaldır** — Export butonu ve ilgili kodu kaldır.

## Koşullu Dil Alanı

- [ ] **Dil alanı koşullu gösterim** — Create/Edit sayfalarında `languages.length > 1` ise Select göster, değilse gizle.
