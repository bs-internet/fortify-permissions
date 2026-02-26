# Yol Haritası: Kullanıcı Yönetimi, Yetkilendirme ve Tanımlamalar

## Bağlam

Sistem scaffolding aşamasında: Controller, Service, Event, Listener, Notification, FormRequest dosyaları oluşturulmuş ama içleri boş. Migration'lar henüz yok (languages, currencies, units tabloları). Rotalar sadece GET index içeriyor. Vue sayfaları iskelet halinde.

Bu plan tüm CRUD işlemlerini, validation kurallarını, service katmanını, frontend sayfaları ve yetkilendirmeyi hayata geçirmeyi amaçlıyor.

## Kararlar

- **Para Birimi**: `akaunting/laravel-money` paketi kullanılacak (formatlama ve dönüştürme için)
- **Birim Tipleri**: 5 tip yeterli (Ağırlık, Uzunluk, Hacim, Alan, Adet)
- **Rol Atama**: Çoklu rol desteği (bir kullanıcıya birden fazla rol atanabilir, multi-select)
- **Doğrudan Yetki Atama**: Kullanıcıya rol üzerinden yetki vermenin yanında, doğrudan (direkt) permission da atanabilir. Spatie `givePermissionTo()` / `syncPermissions()` ile desteklenir.
- **Label/Description Kullanımı**: Role ve Permission modellerindeki `label` ve `description` alanları UI'da aktif kullanılacak. `name` teknik isim (slug), `label` kullanıcıya gösterilen isim, `description` açıklama.

---

## Faz 1: Veritabanı Temeli

### 1.1 Dosya Adı Düzeltmesi
- [ ] `app/Models/Curency.php` → `app/Models/Currency.php` olarak yeniden adlandır (sınıf adı zaten doğru)

### 1.2 UnitType Enum Oluşturma
- [ ] `app/Enums/UnitType.php` oluştur
  - Değerler: Weight (Ağırlık), Length (Uzunluk), Volume (Hacim), Area (Alan), Piece (Adet)
  - `label()` ve `options()` metodları (UserStatus pattern'i gibi)

### 1.3 Migration'lar

- [ ] **a) `create_languages_table`**

| Kolon | Tip | Not |
|-------|-----|-----|
| id | uuid PK | |
| code | string(10), unique | tr, en, de |
| name | string | Türkçe, English |
| native_name | string | Türkçe, English |
| is_default | boolean, default false | |
| is_active | boolean, default true | |
| sort_order | smallint, default 0 | |
| timestamps | | |

- [ ] **b) `create_currencies_table`**

| Kolon | Tip | Not |
|-------|-----|-----|
| id | uuid PK | |
| code | string(10), unique | TRY, USD, EUR |
| name | string | Türk Lirası |
| symbol | string(10) | ₺, $, € |
| decimal_places | tinyint, default 2 | |
| thousand_separator | string(5), default '.' | |
| decimal_separator | string(5), default ',' | |
| is_default | boolean, default false | |
| is_active | boolean, default true | |
| sort_order | smallint, default 0 | |
| timestamps | | |

- [ ] **c) `create_units_table`**

| Kolon | Tip | Not |
|-------|-----|-----|
| id | uuid PK | |
| name | string | Kilogram, Metre |
| abbreviation | string(20) | kg, m |
| type | string(50) | UnitType enum |
| is_active | boolean, default true | |
| sort_order | smallint, default 0 | |
| timestamps | | |

- [ ] **d) `add_language_id_to_users_table`**

| Kolon | Tip | Not |
|-------|-----|-----|
| language_id | uuid, nullable | FK → languages.id, nullOnDelete |

### 1.4 Model Güncellemeleri
- [ ] **Language**: fillable, casts, `users(): HasMany` ilişkisi
- [ ] **Currency**: fillable, casts (dosya adı düzeltildikten sonra)
- [ ] **Unit**: fillable, casts (`type` → UnitType enum cast)
- [ ] **User**: `language_id` fillable'a ekle, `language(): BelongsTo` ilişkisi ekle
- [ ] **Role**: `fillable` = ['name', 'guard_name', 'label', 'description']
- [ ] **Permission**: `fillable` = ['name', 'guard_name', 'label', 'description']

---

## Faz 2: Tanımlama Modülleri (Language, Currency, Unit)

Her modül için aynı pattern uygulanacak:

### 2.1 Dil (Language) Modülü

- [ ] **Form Requests**:
  - `LanguageCreateRequest`: code (required, unique), name (required), native_name (required), is_default, is_active, sort_order
  - `LanguageUpdateRequest`: Aynı kurallar, unique ignore ile

- [ ] **Service** (`LanguageService`):
  - `getAll()`: Tüm dilleri sort_order'a göre getir
  - `store()`: Oluştur, is_default ise diğerlerin default'unu kaldır, event dispatch
  - `update()`: Güncelle, değişiklikleri takip et, event dispatch
  - `delete()`: Sil, event dispatch
  - `getActiveLanguages()`: Sadece aktif dilleri getir (kullanıcı formu için)

- [ ] **Controller** (`LanguageController`):
  - `index()`: Inertia render + languages listesi
  - `store()`: LanguageCreateRequest ile validate, service'e delege et
  - `update()`: LanguageUpdateRequest ile validate, service'e delege et
  - `destroy()`: Sil

- [ ] **Rotalar** (mevcut definitions grubuna eklenir):
  ```
  POST   /settings/definitions/languages          → store
  PUT    /settings/definitions/languages/{language} → update
  DELETE /settings/definitions/languages/{language} → destroy
  ```

- [ ] **Vue Sayfası** (`Language/Index.vue`):
  - Tablo: code, name, native_name, is_default (Badge), is_active (Badge), sort_order, İşlemler
  - Ekle/Düzenle: Dialog içinde form (useForm)
  - Sil: AlertDialog onay
  - shadcn-vue: Table, Dialog, Button, Input, Switch, Badge

### 2.2 Para Birimi (Currency) Modülü

- [ ] Aynı pattern. Ek alanlar: symbol, decimal_places, thousand_separator, decimal_separator
- [ ] **`akaunting/laravel-money` entegrasyonu**:
  - `config/money.php` içerisinde desteklenen para birimleri yapılandırılır
  - Currency model tanımlama tablosu olarak kalır (hangi para birimleri aktif, default hangisi vb.)
  - İleride fiyat/tutar gösterimlerinde `money()` helper ve Blade directive kullanılabilir

### 2.3 Birim (Unit) Modülü

- [ ] Aynı pattern. Ek: type alanı için UnitType enum select dropdown'u
- [ ] Controller `index()` metodunda `UnitType::options()` da gönderilecek

---

## Faz 3: Yetki ve Rol Modülleri

### 3.1 Yetki (Permission) Modülü

- [ ] **Form Requests**:
  - `PermissionCreateRequest`: name (required, unique, slug format), label (required, kullanıcıya gösterilen isim), description (nullable, açıklama)
  - `PermissionUpdateRequest`: Aynı, unique ignore ile

- [ ] **Service** (`PermissionService`):
  - `getAll()`: Tüm yetkileri listele (id, name, label, description)
  - `store()`: Oluştur (guard_name: 'web' default), event dispatch
  - `update()`: Güncelle, event dispatch
  - `delete()`: Sil, event dispatch

- [ ] **Controller** ve **Rotalar**: Standart CRUD pattern
  ```
  POST   /users/permissions          → store
  PUT    /users/permissions/{permission} → update
  DELETE /users/permissions/{permission} → destroy
  ```

- [ ] **Vue Sayfası**: Tablo (name, label, description kolonları) + Dialog (name=teknik isim, label=görünen isim, description=açıklama)

### 3.2 Rol (Role) Modülü

- [ ] **Form Requests**:
  - `RoleCreateRequest`: name (required, unique, slug format), label (required, görünen isim), description (nullable), permissions[] (array, exists:permissions,id)
  - `RoleUpdateRequest`: Aynı, unique ignore ile

- [ ] **Service** (`RoleService`):
  - `getAll()`: with('permissions:id,name,label') ile
  - `store()`: Rol oluştur + `syncPermissions()` ile yetki ata, event dispatch
  - `update()`: Güncelle + yetkileri senkronize et, event dispatch
  - `delete()`: Sil, event dispatch

- [ ] **Controller**: `index()` hem rolleri hem tüm yetkileri gönderir (yetki atama UI için)
  ```
  POST   /users/roles          → store
  PUT    /users/roles/{role}   → update
  DELETE /users/roles/{role}   → destroy
  ```

- [ ] **Vue Sayfası**: Tablo (name, label, description, atanmış yetkiler badge listesi) + Dialog (name, label, description + Checkbox listesi ile yetki atama, her checkbox'ta permission.label gösterilir)

---

## Faz 4: Kullanıcı Modülü

### 4.1 Form Requests

- [ ] **UserCreateRequest**:
  - name (required), email (required, unique), title (nullable), password (required, min:8, confirmed), status (required, enum), language_id (nullable, exists), roles[] (nullable, array, her eleman exists:roles,id), permissions[] (nullable, array, her eleman exists:permissions,id)

- [ ] **UserUpdateRequest**:
  - Aynı, ama password nullable (boş bırakıldığında değişmez), email unique ignore

### 4.2 UserService

- [ ] Implementasyon:
  - `getPaginated()`: with(['roles:id,name,label', 'permissions:id,name,label', 'language']), filtreleme (search, status), pagination
  - `store()`: Kullanıcı oluştur, çoklu rol ata (syncRoles), doğrudan yetki ata (syncPermissions), event dispatch
  - `update()`: Güncelle, boş şifre atla, rolleri senkronize et (syncRoles), yetkileri senkronize et (syncPermissions), event dispatch
  - `delete()`: Soft delete, event dispatch

### 4.3 UserController

- [ ] Implementasyon:
  - `index()`: users (paginated), filters, roles (id,name,label), permissions (id,name,label), languages (aktif), statuses (UserStatus::options())
  - `store()`, `update()`, `destroy()`: Standart pattern

### 4.4 Rotalar
- [ ] Ekle:
  ```
  POST   /users/users          → store
  PUT    /users/users/{user}   → update
  DELETE /users/users/{user}   → destroy
  ```

### 4.5 Vue Sayfası (`Users/Index.vue`)

- [ ] En karmaşık sayfa:
  - Arama çubuğu (debounced Inertia reload)
  - Durum filtresi (Select)
  - Tablo: Ad, Email, Ünvan, Durum (Badge), Roller (Badge), Doğrudan Yetkiler (Badge), Dil, Tarih, İşlemler
  - Ekle Dialog: name, email, title, password, password_confirmation, status (Select), language (Select), roles (Multi-select checkbox, label gösterilir), permissions (Multi-select checkbox, label gösterilir - doğrudan yetki atama)
  - Düzenle Dialog: Aynı ama password opsiyonel
  - Sil: AlertDialog
  - Not: Kullanıcı detayında "rol üzerinden gelen yetkiler" ve "doğrudan atanan yetkiler" ayrı gösterilebilir

### 4.6 Notification İçerikleri

- [ ] `UserCreatedNotification::toArray()`: başlık, mesaj, tarih
- [ ] `UserUpdatedNotification::toArray()`: başlık, mesaj, tarih

---

## Faz 5: Frontend Yetkilendirme

### 5.1 HandleInertiaRequests Güncellemesi
- [ ] `auth.user` içerisine `roles` ve `permissions` ekle (getRoleNames, getAllPermissions)

### 5.2 usePermission Composable
- [ ] `resources/js/composables/usePermission.ts` oluştur
  - `hasRole()`, `hasPermission()`, `hasAnyRole()`, `hasAnyPermission()`
  - Vue sayfalarında buton/alan gizleme için kullanılacak

### 5.3 TypeScript Tip Tanımları
- [ ] `resources/js/types/definitions.ts`: Language, Currency, Unit tipleri
- [ ] `resources/js/types/users.ts`: Role, Permission, UserListItem tipleri
- [ ] `resources/js/types/auth.ts`: User tipine roles/permissions ekle, id'yi string yap (UUID)

---

## Faz 6: Wayfinder ve Build

- [ ] Çalıştır:
  ```
  php artisan wayfinder:generate
  npm run build
  ```

---

## Önemli Notlar

1. **Event/Listener kaydı gerekmez**: `bootstrap/app.php` otomatik keşfediyor
2. **Şifre hashleme otomatik**: User model'de `'password' => 'hashed'` cast var
3. **Soft delete zaten var**: User model'de SoftDeletes trait mevcut
4. **Guard name**: Rol/yetki oluştururken default 'web' kullanılacak
5. **is_default yönetimi**: Bir kayıt default yapıldığında diğer default'lar temizlenecek
6. **Mevcut Listener'lar tamam**: LogLanguageActivity, LogCurrencyActivity vb. zaten dolu

---

## Değiştirilecek/Oluşturulacak Dosyalar

### Yeni Dosyalar
- `app/Enums/UnitType.php`
- `database/migrations/` (4 adet)
- `resources/js/types/definitions.ts`
- `resources/js/types/users.ts`
- `resources/js/composables/usePermission.ts`

### Düzeltilecek Dosyalar
- `app/Models/Curency.php` → `Currency.php` (rename)
- `app/Models/Language.php`, `Currency.php`, `Unit.php` (fillable, casts, relationships)
- `app/Models/User.php` (language_id, language relationship)
- `app/Models/Role.php`, `Permission.php` (fillable)
- `app/Http/Controllers/Users/UserController.php` (full CRUD)
- `app/Http/Controllers/Users/RoleController.php` (full CRUD)
- `app/Http/Controllers/Users/PermissionController.php` (full CRUD)
- `app/Http/Controllers/Definitions/LanguageController.php` (full CRUD)
- `app/Http/Controllers/Definitions/CurrencyController.php` (full CRUD)
- `app/Http/Controllers/Definitions/UnitController.php` (full CRUD)
- `app/Services/Users/UserService.php` (full implementation)
- `app/Services/Users/RoleService.php` (full implementation)
- `app/Services/Users/PermissionService.php` (full implementation)
- `app/Services/Definitions/LanguageService.php` (full implementation)
- `app/Services/Definitions/CurrencyService.php` (full implementation)
- `app/Services/Definitions/UnitService.php` (full implementation)
- `app/Http/Requests/Users/*` (validation rules)
- `app/Http/Requests/Definitions/*` (validation rules)
- `app/Notifications/Users/*` (toArray içerikleri)
- `app/Http/Middleware/HandleInertiaRequests.php` (roles/permissions share)
- `routes/web.php` (CRUD rotalar)
- `resources/js/Pages/app/users/Users/Index.vue` (full page)
- `resources/js/Pages/app/users/Role/Index.vue` (full page)
- `resources/js/Pages/app/users/Permissions/Index.vue` (full page)
- `resources/js/Pages/app/definitions/Language/Index.vue` (full page)
- `resources/js/Pages/app/definitions/Currency/Index.vue` (full page)
- `resources/js/Pages/app/definitions/Unit/Index.vue` (full page)
- `resources/js/types/auth.ts` (User tipi güncelleme)

---

## Doğrulama

- [ ] Her faz sonunda `php artisan migrate` (Faz 1)
- [ ] Her controller sonunda ilgili sayfayı tarayıcıda kontrol et
- [ ] CRUD işlemlerini test et: oluştur, güncelle, sil
- [ ] Activity log'da kayıtların görüntülenip görüntülenmediğini kontrol et
- [ ] Kullanıcı oluştururken dil seçimi çalıştığını doğrula
- [ ] Rol ataması ve yetki senkronizasyonunu doğrula
