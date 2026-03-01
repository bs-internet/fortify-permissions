# Rol & İzin Sayfaları - Yapılacaklar

## Fonksiyonel Hatalar

- [ ] **Tümünü Seç / Tümünü Kaldır butonları** — Role Create ve Edit sayfalarında modül card'larındaki "Tümünü Seç" / "Tümünü Kaldır" butonlarına tıklandığında ilgili modüldeki tüm permission checkbox'ları seçilmeli veya kaldırılmalı.
  - Dosyalar: `resources/js/pages/app/users/Role/Create.vue`, `Edit.vue`
  - `toggleModule()` fonksiyonu ve Checkbox `@update:checked` event handler'ı gözden geçirilecek

- [ ] **Edit sayfasında izinler seçili gelmiyor** — Role düzenleme sayfası açıldığında, role'e tanımlı permission'lar checkbox'larda seçili olarak görünmüyor.
  - Dosyalar: `app/Http/Controllers/Users/RoleController.php` (edit metodu), `resources/js/pages/app/users/Role/Edit.vue`
  - Controller'da `$role->load('permissions:id')` ile yükleme ve frontend'de `String(p.id)` dönüşümü arasındaki tip uyumsuzluğu kontrol edilecek

- [ ] **Tekil seçimde sayaç güncellenmiyor** — Permission'ları tek tek seçince üstteki "X Yetki Seçildi" / "X Aktif Yetki" sayacı güncellenmiyor, ama Tümünü Seç/Kaldır ile güncelleniyor.
  - Dosyalar: `resources/js/pages/app/users/Role/Create.vue`, `Edit.vue`
  - `togglePermission()` fonksiyonunda Inertia useForm reactivity sorunu, `@update:checked` handler'ı düzeltilecek

## Yetki Kontrolleri

- [ ] **Role Index.vue yetki kontrolleri** — "Yeni Rol Ekle" butonu `role.create`, "Düzenle" butonu `role.update` yetkisi ile koşullu gösterilecek.
  - Dosya: `resources/js/pages/app/users/Role/Index.vue`
  - `usePermission` composable import edilip `can()` ile `v-if` kontrolü eklenecek

- [ ] **Role Edit.vue yetki kontrolü** — "Rolü Sil" butonu `role.delete` yetkisi ile koşullu gösterilecek.
  - Dosya: `resources/js/pages/app/users/Role/Edit.vue`
  - `usePermission` composable import edilip `can()` ile `v-if` kontrolü eklenecek

- [ ] **Permission Index.vue yetki kontrolü** — "Düzenle" butonu `permission.update` yetkisi ile koşullu gösterilecek.
  - Dosya: `resources/js/pages/app/users/Permissions/Index.vue`
  - `usePermission` composable import edilip `can()` ile `v-if` kontrolü eklenecek

## Tasarım

- [ ] **Role sayfaları tasarım standardizasyonu** — Index, Create ve Edit sayfalarındaki card ve tabloları daha köşeli (`rounded-lg` → `rounded-md`), gölgesiz (`shadow-none`), belirgin border'lı (`border-border`) ve tutarlı hale getir.
  - Dosyalar: `resources/js/pages/app/users/Role/Index.vue`, `Create.vue`, `Edit.vue`
  - Tüm card'larda: `rounded-md border border-border bg-card shadow-none`
  - Modül card header: `bg-muted/40 px-4 py-2.5 border-b border-border`
  - Font boyutları tutarlı hale getirilecek
