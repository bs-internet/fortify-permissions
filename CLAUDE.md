# Otomasyon Projesi

## Genel Kurallar

1. Asla test yazma.
2. Asla konsol komutu çalıştırma. `php`, `npm` vb. komutları söyle, ben çalıştırırım.
3. Türkçe iletişim kur. Değişken/metot isimleri İngilizce, kullanıcı mesajları ve açıklamalar Türkçe olsun.
4. Mevcut kod konvansiyonlarını takip et. Yeni dosya oluştururken komşu dosyaları kontrol et.
5. Mevcut dizin yapısını koru; onay almadan yeni kök dizin oluşturma.
6. Bağımlılıkları onay almadan değiştirme.
7. Açıklamalarda kısa ve öz ol, bariz detayları anlatma.
8. Markdown/dokümantasyon dosyası ancak açıkça istenirse oluştur.

---

## Proje Hakkında

Kullanıcı yönetimi, rol/izin sistemi, tanımlamalar ve sistem ayarlarını kapsayan bir otomasyon paneli. Tüm arayüz ve mesajlar Türkçe. Uygulama Laravel Herd ile sunuluyor.

**Teknoloji Yığını:**
- Backend: Laravel 12 + PHP 8.4 + Fortify (kimlik doğrulama) + Spatie Permission (yetkilendirme)
- Frontend: Vue 3 + Inertia.js v2 + Shadcn-Vue (Reka UI) + Tailwind CSS v4 + Lucide Icons
- Rota Yönetimi: Laravel Wayfinder (tip-güvenli TypeScript rota fonksiyonları)
- Veritabanı: Eloquent ORM, UUID birincil anahtar, soft delete
- UI Kütüphanesi: Shadcn-Vue (PrimeVue KULLANILMIYOR, Reka UI tabanlı headless bileşenler)
- Kod Formatlama: Laravel Pint
- Paket Yönetimi: Composer + NPM, Laravel Sail (Docker)

**Paket Sürümleri:**

| Paket | Sürüm |
|-------|-------|
| php | 8.4 |
| laravel/framework | v12 |
| inertiajs/inertia-laravel | v2 |
| @inertiajs/vue3 | v2 |
| vue | v3 |
| tailwindcss | v4 |
| laravel/fortify | v1 |
| laravel/wayfinder | v0 |
| laravel/pint | v1 |
| eslint | v9 |
| prettier | v3 |

---

## Mimari Yapı

### Backend Katmanları

**İstek akışı:** Route → Middleware → Controller → FormRequest (validasyon) → Service (iş mantığı) → Model → Event/Listener

- **Controller:** Sadece HTTP katmanını yönetir, iş mantığı Service sınıflarında.
- **Service:** Tüm CRUD, filtreleme, cache ve event dispatch işlemleri burada.
- **FormRequest:** Her form için ayrı request sınıfı, Türkçe hata mesajları ile. Inline validasyon kullanma.
- **Policy:** Her model için policy sınıfı, `Gate::before` ile Super Admin bypass.
- **Event/Listener:** Her önemli işlem (CRUD, profil, ayar) event fırlatır → Listener'lar aktivite loglar ve bildirim gönderir.

### Frontend Katmanları

- **Sayfa yapısı:** `AppLayout` → `ModuleLayout (partials/Layout.vue)` → Sayfa içeriği
- **Bileşen kütüphanesi:** `resources/js/components/ui/` altında Shadcn-Vue bileşenleri
- **Uygulama bileşenleri:** `resources/js/components/app/` altında AppSidebar, NavMain, Heading vb.
- **Composable'lar:** `usePermission`, `useAppearance`, `useCurrentUrl`, `useInitials`, `useTwoFactorAuth`
- **Form yönetimi:** Inertia `useForm()` ile, Wayfinder action URL'leri kullanılarak submit edilir
- **Bildirimler:** `vue-sonner` (toast) ile flash mesajlar AppLayout'ta izlenir

---

## Dizin Yapısı

### Backend
```
app/
├── Actions/Fortify/          # Fortify aksiyonları (şifre sıfırlama)
├── Console/Commands/          # Artisan komutları (arşivleme, temizlik)
├── Enums/                     # UserStatus, CorePermission, DefinitionPermission, UnitType
├── Events/                    # 22 event sınıfı (CRUD, profil, ayarlar)
├── Helpers/site_helpers.php   # Global yardımcı fonksiyonlar (settings, site_name, logo)
├── Http/
│   ├── Controllers/
│   │   ├── Definitions/       # Currency, Language, Unit controller'ları
│   │   ├── Profile/           # Profil, şifre, 2FA, bildirim, oturum
│   │   ├── Settings/          # Genel ayarlar, aktivite logları
│   │   └── Users/             # Kullanıcı, rol, izin yönetimi
│   ├── Middleware/             # HandleInertiaRequests, SetLocale, EnsureActiveUser, EnsureWriteAccess
│   └── Requests/              # Form request sınıfları (Definitions, Profile, Settings, Users)
├── Listeners/                 # 15+ listener (log, bildirim)
├── Mail/Profile/              # Şifre ve 2FA mail sınıfları
├── Models/                    # User, Role, Permission, Activity, Setting, Language, Currency, Unit, Notification, ArchivedNotification
├── Notifications/             # Veritabanı ve mail bildirimleri
├── Policies/                  # Her model için policy
├── Providers/                 # App, Fortify, Helper provider'ları
└── Services/                  # İş mantığı katmanı (Users, Definitions, Profile, Settings, Common)
```

### Frontend
```
resources/js/
├── actions/                   # Wayfinder üretimi (controller bazlı rota fonksiyonları)
├── components/
│   ├── app/                   # AppSidebar, NavMain, NavUser, Heading, Breadcrumbs, InputError vb.
│   │   └── common/            # Paylaşılan bileşenler (AlertError, AppLogo, UserInfo vb.)
│   └── ui/                    # Shadcn-Vue bileşenleri (40+ bileşen grubu)
├── composables/               # usePermission, useAppearance, useCurrentUrl, useInitials, useTwoFactorAuth
├── layouts/
│   ├── app/                   # AppSidebarLayout, AppHeaderLayout
│   └── auth/                  # AuthCardLayout, AuthSimpleLayout, AuthSplitLayout
├── lib/utils.ts               # cn() ve toUrl() yardımcı fonksiyonları
├── pages/
│   ├── app/
│   │   ├── Dashboard.vue
│   │   ├── definitions/       # Currency, Language, Unit Index sayfaları
│   │   ├── profile/           # Profil, Şifre, 2FA, Bildirimler, Oturumlar, Görünüm
│   │   ├── settings/          # Genel Ayarlar, Aktivite Logları
│   │   └── users/             # Users/Index, Role/Index-Create-Edit, Permissions/Index
│   └── auth/                  # Login, Register, ForgotPassword, ResetPassword, TwoFactorChallenge, VerifyEmail
├── routes/                    # Wayfinder üretimi (isimli rota fonksiyonları)
└── types/                     # TypeScript tip tanımları (auth, user, pagination, navigation, definitions, settings)
```

---

## Modeller ve İlişkiler

| Model | Özellikler |
|-------|-----------|
| **User** | UUID, SoftDeletes, HasRoles (Spatie), TwoFactorAuthenticatable, belongsTo(Language) |
| **Role** | UUID, Spatie Role genişletmesi, özel label/description alanları |
| **Permission** | UUID, Spatie Permission genişletmesi |
| **Activity** | UUID, SoftDeletes, Prunable (6 ay), belongsTo(User), log alanı (JSON) |
| **Setting** | UUID, key/value/type yapısı, getTypedValueAttribute ile tip dönüşümü |
| **Language** | UUID, code/name/native_name, hasMany(User), cache: rememberForever |
| **Currency** | UUID, code/name/symbol, decimal ayarları |
| **Unit** | UUID, name/abbreviation, type → UnitType enum cast |
| **Country** | UUID, code/name, is_active, belongsToMany(Tax) |
| **Tax** | UUID, name/rate, is_active, belongsToMany(Country), pivot: country_tax |
| **Notification** | UUID, polymorphic (notifiable), read_at |
| **ArchivedNotification** | UUID, SoftDeletes, Prunable (120 gün), polymorphic |

---

## Enum Tanımları

- **UserStatus:** PASSIVE(0) - giriş yapamaz, ACTIVE(1) - tam erişim, DRAFT(2) - salt okunur
- **CorePermission:** `user.*`, `role.*`, `permission.*`, `setting.*`, `activity.*`
- **DefinitionPermission:** `language.*`, `currency.*`, `unit.*`, `country.*`, `tax.*`
- **UnitType:** Weight, Length, Volume, Area, Piece

---

## Yetkilendirme Sistemi

- **Spatie Laravel Permission** ile rol ve izin yönetimi
- Her model için **Policy** sınıfı (`app/Policies/`)
- `Gate::before` ile **Super Admin** tüm izinleri bypass eder
- **EnsureActiveUser** middleware: PASSIVE kullanıcıları engeller
- **EnsureWriteAccess** middleware: DRAFT kullanıcıların POST/PUT/PATCH/DELETE işlemlerini engeller
- Frontend'de `usePermission()` composable ile `can()`, `canAny()`, `canAll()` kontrolleri

### İzin Modülleri (CorePermission)
| Modül | İzinler |
|-------|---------|
| Kullanıcı | user.management, user.create, user.update, user.delete, user.change-email, user.verify-email, user.change-status |
| Rol | role.management, role.create, role.update, role.delete |
| İzin | permission.management, permission.update |
| Ayarlar | setting.management, setting.update |
| Aktivite | activity.view |

### İzin Modülleri (DefinitionPermission)
| Modül | İzinler |
|-------|---------|
| Dil | language.management, language.create, language.update, language.delete |
| Para Birimi | currency.management, currency.create, currency.update, currency.delete |
| Birim | unit.management, unit.create, unit.update, unit.delete |
| Ülke | country.management, country.create, country.update, country.delete |
| Vergi | tax.management, tax.create, tax.update, tax.delete |

---

## PHP Kodlama Standartları

- Kontrol yapılarında tek satırlık gövdelerde bile süslü parantez kullan.
- PHP 8 constructor property promotion kullan: `public function __construct(public GitHub $github) { }`
- Boş parametresiz `__construct()` metodu oluşturma (private hariç).
- Metot ve fonksiyonlarda explicit return type declaration kullan.
- Uygun PHP type hint'leri kullan.
- PHPDoc blokları tercih et, kod içi yorum sadece karmaşık mantıklarda kullan.
- Array shape type tanımları ekle (uygun yerlerde).
- `env()` fonksiyonunu config dosyaları dışında kullanma, `config('app.name')` kullan.
- Zaman alan işlemler için `ShouldQueue` arayüzlü queued job'lar kullan.

---

## Laravel Konvansiyonları

### Veritabanı ve Eloquent
- Eloquent ilişki metotlarında return type hint kullan.
- Raw query veya manuel join yerine ilişki metotları tercih et.
- `DB::` yerine `Model::query()` kullan.
- N+1 sorgu sorununu eager loading ile önle.
- Cast'ler `casts()` metodu ile tanımlanır (mevcut model konvansiyonunu takip et).
- Migration'da kolon değişikliğinde tüm mevcut attribute'ları dahil et.

### Controller ve Validasyon
- Validasyon için her zaman FormRequest sınıfı oluştur, controller içinde inline validasyon yapma.
- Komşu FormRequest'leri kontrol ederek array mi string mi kural kullanıldığını belirle.

### Rota Yönetimi
- Sayfa linklerinde named route ve `route()` fonksiyonu tercih et.

### Laravel 12 Yapısı
- Middleware'ler `bootstrap/app.php` içinde `Application::configure()->withMiddleware()` ile tanımlanır.
- `bootstrap/providers.php` service provider listesini içerir.
- Konsol komutları `app/Console/Commands/` altında otomatik tanınır, manuel kayıt gerekmez.

---

## Frontend Konvansiyonları

### Sayfa Yapısı Şablonu
```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ModuleLayout from './partials/Layout.vue';
// Wayfinder action import'ları
// Props tanımı (defineProps)
// useForm, ref, computed, watch
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Sayfa Başlığı" />
        <ModuleLayout>
            <Heading title="..." description="..." />
            <!-- İçerik -->
        </ModuleLayout>
    </AppLayout>
</template>
```

### Form Yönetimi
- Inertia `useForm()` kullan, doğrudan `axios` veya `fetch` kullanma
- Submit URL'leri Wayfinder action'larından al: `form.post(store.url())`
- Hata gösterimi: `<InputError :message="form.errors.fieldName" />`
- İşlem durumu: `form.processing` ile buton disabled yap

### Bileşen Kuralları
- Yeni bileşen yazmadan önce `components/ui/` ve `components/app/` altında mevcut olanı kontrol et
- UI bileşenleri Shadcn-Vue'dan, uygulama bileşenleri `components/app/` altında
- Tüm sayfalar `AppLayout` wrapper'ı içinde olmalı
- Modüller kendi `partials/Layout.vue` dosyasını kullanır (sidebar navigasyon)
- Vue bileşenlerinde tek kök element kullan

### Navigasyon
- Sidebar navigasyonu `AppSidebar.vue` içinde tanımlı, izin bazlı filtreleme uygulanır
- Link'ler Wayfinder `routes/` fonksiyonlarıyla üretilir, `<Link>` bileşeni ile `prefetch` kullanılır

### Wayfinder
- Controller bazlı: `import { store } from '@/actions/.../Controller'` → `store.url()`
- İsimli rota: `import { index } from '@/routes/...'` → `index().url`
- Inertia form: `form.post(store.url())` veya `form.submit(store())`

### Inertia.js v2
- Deferred prop'larda skeleton/pulse animasyonlu boş durum göster
- Özellikler: deferred props, infinite scrolling, lazy loading on scroll, polling, prefetching

### Tailwind CSS v4
- Mevcut Tailwind konvansiyonlarını takip et, yeni pattern eklemeden önce proje pattern'lerini kontrol et

---

## Araç ve Servis Kullanımı

### Laravel Boost (MCP)
- `search-docs`: Laravel ekosistemi dokümantasyonu için öncelikli kullan. Sorguları basit ve geniş tut (ör: `['rate limiting', 'routing']`). Paket adı ekleme, otomatik algılanır.
- `tinker`: PHP çalıştırma ve Eloquent model sorgulama için kullan.
- `database-query`: Salt okunur veritabanı sorguları için kullan.
- `browser-logs`: Frontend/JS hata ayıklama için kullan (sadece güncel loglar).
- `get-absolute-url`: Kullanıcıya proje URL'i paylaşırken doğru scheme/domain/port için kullan.
- `list-artisan-commands`: Artisan komut parametrelerini kontrol etmek için kullan.

### Laravel Herd
- Uygulama `https?://otomasyon.test` adresinde sunulur. HTTP sunucusu için komut çalıştırma.

### Laravel Pint
- Değişiklikleri sonlandırmadan önce `vendor/bin/pint --dirty` çalıştır.

### Frontend Build
- UI değişikliği yansımıyorsa `npm run build`, `npm run dev` veya `composer run dev` çalıştırılması gerekebilir.

---

## Önemli Dosya Yolları

| Dosya | Açıklama |
|-------|----------|
| `bootstrap/app.php` | Middleware, exception, rota kayıtları |
| `bootstrap/providers.php` | Service provider listesi |
| `config/otomasyon.php` | Uygulama özel ayarlar (versiyon, pagination, cache) |
| `app/Enums/CorePermission.php` | Çekirdek sistem izinleri (user, role, permission, setting, activity) |
| `app/Enums/DefinitionPermission.php` | Tanımlama izinleri (language, currency, unit, country, tax) |
| `app/Enums/UserStatus.php` | Kullanıcı durumları |
| `app/Http/Middleware/HandleInertiaRequests.php` | Inertia ile paylaşılan veriler (auth, settings, flash) |
| `app/Helpers/site_helpers.php` | Global helper fonksiyonlar (settings, site_name, logo) |
| `resources/js/composables/usePermission.ts` | Frontend izin kontrolleri |
| `resources/js/components/app/AppSidebar.vue` | Sidebar navigasyon yapısı |
| `resources/css/app.css` | Tailwind v4 tema değişkenleri ve dark mode |
| `vite.config.ts` | Vite + Wayfinder + Tailwind yapılandırması |
