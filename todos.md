# Sistem İnceleme Bulguları (2026-03-05)

Tüm maddeler tamamlandı.

---

## Güvenlik

### Kritik
- [x] **Pagination v-html XSS Riski:** `Notifications.vue`, `NotificationsArchived.vue`, `Session.vue` dosyalarında pagination Shadcn-Vue Pagination bileşenine geçirildi.

### Orta
- [x] **PermissionUpdateRequest Hata Mesajı:** Yanlış `'name.unique'` key'i kaldırılıp doğru mesajlar eklendi.

### Düşük
- [x] **Content-Security-Policy Header:** `SetSecurityHeaders` middleware oluşturuldu (CSP, X-Content-Type-Options, X-Frame-Options, Referrer-Policy).

---

## Performans

### Orta
- [x] **UnitService Cache Eksik:** `Cache::rememberForever()` ile `allActive()` cache'lendi, `clearCache()` dolduruldu.
- [x] **Deferred Props:** `Inertia::defer()` ile permissions (User/Role create/edit) ve dropdown verileri (Settings) ertelendi. Frontend'de `<Deferred>` + `<Skeleton>` fallback eklendi.
- [x] **Notification `type` Index:** Yeni migration ile `type` kolonuna index eklendi.

### Düşük
- [x] **country_tax Pivot Index:** `tax_id` üzerine index eklendi.

---

## Tutarlılık

### Orta
- [x] **Event Dispatch Yöntemi:** `event()` helper kullanımları `::dispatch()` static metoduna dönüştürüldü.

### Düşük
- [x] **Listener Error Handling:** 5 notification listener'a `$tries`, `$backoff` ve `failed()` metodu eklendi.
- [x] **Wayfinder Rota Tutarlılığı:** Role/Create, Role/Edit ve GeneralSettings'te navigasyon `@/routes/`, mutation `@/actions/` pattern'ine uyumlu hale getirildi.
- [x] **GeneralSettings.vue Stil Tutarlılığı:** Card bileşenleri kaldırılıp create/edit sayfalarıyla aynı div+border pattern'ine geçirildi. Input/Select/Textarea'lara `shadow-none focus-visible:ring-1` eklendi.

---

## Kod Kalitesi

### Kritik
- [x] **Rol/İzin Seçici Bug:** `Users/Create.vue`'da `selectedPermissions[String(p.id)]` düzeltmesi yapıldı.

### Orta
- [x] **GeneralSettings.vue FileUploadField Refactor:** `FileUploadField.vue` bileşeni oluşturuldu, 3 tekrarlı upload alanı bu bileşenle değiştirildi.

### Düşük
- [x] **TypeScript any Tipi:** `Users/Index.vue`'da `(val: any)` → `(val: string)` düzeltildi.
- [x] **Service Transaction:** `UserService::store()` ve `update()` metodlarına `DB::transaction()` eklendi.

---

## Güçlü Yönler (Referans)
- Authorization sistemi mükemmel (Spatie + Policy + Gate::before, Service katmanında tutarlı)
- Service pattern düzgün uygulanmış, controller'lar temiz
- N+1 sorgu problemi yok (eager loading yapılmış)
- Cache stratejisi genel olarak iyi
- Rate limiting sensitive actions'da uygulanmış
- Event/Listener sistemi kapsamlı (28 event, 19 listener, queued)
- Mass assignment koruması (fillable) tüm modellerde var
- TypeScript ve Vue 3 best practices'e uygun
- Composable'lar modüler ve single responsibility
- UUID birincil anahtar tutarlı kullanılmış
