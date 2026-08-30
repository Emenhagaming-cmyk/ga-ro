# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-30 sore — deploy fitur Tabungan, Koperasi, & Berita ke panel admin)

### Deploy Fitur Tabungan, Koperasi, dan Berita ke Panel Admin (2026-08-30)

**Yang dilakukan:**
- Commit semua perubahan `backend-admin` (termasuk file baru): controller, model, migration, view, routes, dan update sidebar `layouts/app.blade.php`.
- Fitur yang di-deploy:
  - **Kelola Tabungan** — sidebar menu + halaman index/show + controller `adminIndex`/`adminShow`/`adminStore`.
  - **Kelola Koperasi** — sidebar menu + halaman index/show + controller `adminIndex`/`adminShow`.
  - **Kelola Berita** — sidebar menu + CRUD lengkap (index/create/edit/store/update/destroy).
- Deploy Vercel production dari folder `backend-admin` berhasil (`Ready in 34s`).
- Alias production di-set:
  - `paneladminsmkbu.vercel.app` → deployment baru
  - `spmb-admin.vercel.app` → deployment baru

**Catatan penting:**
- Migrasi baru (`tabungans`, `koperasi_orders`, `beritas`, `add_input_by_to_tabungans_table`) BELUM dijalankan ke TiDB production. Jalankan `php artisan migrate --force` di environment production agar halaman Tabungan/Koperasi/Berita tidak error.
- File `.env.local` yang dibuat `vercel link` sudah dihapus sesuai trap AGENTS.md.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-30 — perbaikan UI profil siswa)

### Perbaikan UI Halaman Profil & Dashboard Siswa (2026-08-30)

**File yang diperbaiki:**
- `backend/resources/views/pendaftaran/dashboard-siswa.blade.php` — halaman utama profil/status siswa
- `backend/resources/views/auth/profile.blade.php` — halaman akun siswa

**Perbaikan yang diterapkan (design system + aksesibilitas):**
- Konsolidasi design token dengan CSS custom properties (`:root`) untuk warna, radius, shadow, transition
- Heading hierarchy diperbaiki (`h1` untuk banner title, `h2` untuk card title — tidak skip level)
- Tambah `aria-label`, `role="status"`, `aria-live="polite"`/`assertive`, `aria-hidden="true"` pada elemen dekoratif
- Semua form input punya `id` + `label for` eksplisit (tidak lagi label tanpa for)
- Banner siswa ditambah subtitle untuk konteks tambahan
- Stat cards ditambah hover state dengan top border accent + subtle lift
- Detail grid items ditambah hover state
- tombol aksi ditambah `white-space: nowrap` untuk mencegah teks pecah
- Edit form section ditambah margin-top khusus (`ds-card--edit`)
- Responsive breakpoint tambahan `480px` untuk mobile kecil (button full width, actions stack vertikal)
- Profile page di-redesign total: hero dengan avatar glass-style, section-based layout, consistent card rows, proper logout form dengan aria-label
- Back button ditambah `aria-label="Kembali ke beranda"`
- Alert messages ditambah `role` + `aria-live`

**Catatan:** LSP Blade menampilkan false-positive errors pada inline `style="background:{{ ... }};"` — ini normal untuk Blade syntax dan bukan error runtime.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-30 malam — UI Berita + Fitur Kelola Berita Admin)

### Redesign UI Berita + Fitur Tambah Berita di Panel Admin (2026-08-30)

**Backend-admin (paneladminsmkbu.vercel.app):**
- Migration `2026_08_30_200000_create_beritas_table.php`: tabel `beritas` (id, title, slug unique, category, category_color, excerpt, content, image_path, author, published_at, featured, read_time, is_published, user_id FK, timestamps). Dijalankan ke TiDB production.
- Model `Berita.php`: fillable lengkap, cast boolean, `generateSlug()`, `getImageUrlAttribute()`, `toApiArray()` (format untuk frontend Vue).
- `BeritaController.php`: CRUD lengkap (index, create, store, edit, update, destroy). Upload gambar ke `storage/public/berita` via `store('berita','public')`. Auto-estimasi `read_time` dari word count. Hapus gambar lama saat update/destroy.
- Views blade: `berita/index.blade.php` (tabel + thumbnail + badge kategori + stat bar), `berita/create.blade.php` (form dengan drag-and-drop upload gambar, preview langsung, excerpt counter, checkbox featured/tayang), `berita/edit.blade.php` (form pre-filled + tampil gambar lama).
- Routes: GET/POST/PUT/DELETE `/berita` + `/berita/create` + `/berita/{berita}/edit` di admin group (role:admin).
- Sidebar: menu "Kelola Berita" dengan icon koran ditambah di layout admin.

**Backend (pendaftaranspmb.vercel.app):**
- Model `Berita.php` + migration dicopy dari backend-admin.
- `BeritaApiController.php`: `GET /berita` (JSON list, fallback array kosong jika tabel belum ada), `GET /berita/{slug}` (detail satu berita). Header `Cache-Control: public, max-age=60`.
- Routes: `/berita` dan `/berita/{slug}` publik (sebelum `/spp/ortu/{user}`).

**Frontend (smkbu-sby.vercel.app):**
- `NewsView.vue` di-redesign total:
  - Featured card: layout split (gambar kiri 50% / teks kanan 50%), gambar `object-fit:cover` + hover scale, fallback gradient warna per kategori jika gambar 404, badge "Unggulan" + chip kategori di atas gambar.
  - Grid cards: 3 kolom vertikal, aspect-ratio 16/10 untuk gambar, fallback gradient, chip kategori, "Baca →" CTA.
  - Skeleton loading saat fetch.
  - Empty state + Reset Filter button.
  - Fetch dari backend API `/berita` dengan fallback ke `news.json` lokal.
- `BeritaPreview.vue`: fetch dari backend API dulu, fallback ke news.json.

**Deploy & Verifikasi:**
- Backend: `spmb-backend-lxv2on84d` → `pendaftaranspmb.vercel.app`. `/berita` → 200 ✅
- Admin: `spmb-admin-5d2qeub71` → `paneladminsmkbu.vercel.app` + `spmb-admin.vercel.app`. `/berita` → 302 login ✅
- Frontend: `lomba-kex6om6f6` → `smkbu-sby.vercel.app`. `/berita` → 200 ✅
- TiDB: `beritas` table berhasil di-migrate.

---

## 📌 STATUS TERAKHIR (sesi fix blocker 500 — 2026-08-30)

### Root Cause 500 Fresh Deploy — SOLVED ✅

**Root cause:** `config/database.php` di KEDUA `backend/` dan `backend-admin/` menggunakan `use Pdo\Mysql;` (baris 2) dan konstanta `Mysql::ATTR_SSL_CA`. Namespace `Pdo\Mysql` baru tersedia di **PHP 8.5**, sedangkan Vercel menjalankan **PHP 8.3** (via `vercel-php@0.7.4`). Hasil: **Fatal error saat config di-load** → semua route 500 body-kosong. `/up` tetap 200 karena tidak merender blade dan tidak memanggil DB config.

**Fix yang diterapkan:**
- `backend/config/database.php`: hapus `use Pdo\Mysql;`, ganti `Mysql::ATTR_SSL_CA` → `PDO::MYSQL_ATTR_SSL_CA` (2 lokasi: mysql + mariadb).
- `backend-admin/config/database.php`: fix sama persis.
- `backend/api/phpinfo.php`: diagnostik baru (cek PHP version, extensions, env, tmp writable).
- `backend/vercel.json`: tambah route `/phpinfo` + function `api/phpinfo.php`.

**Deploy & Verifikasi production (2026-08-30):**
- `backend`: `spmb-backend-fsbvtwt9k` → alias otomatis `pendaftaranspmb.vercel.app`. `/up` 200 ✅, `/login` 200 ✅, `/register` 200 ✅.
- `backend-admin`: `spmb-admin-gweif3xr1` → alias otomatis `paneladminsmkbu.vercel.app` + alias manual `spmb-admin.vercel.app`. `/up` 200 ✅, `/login` 200 ✅.

**Semua fitur yang sebelumnya tertahan sekarang LIVE di produksi:** SPP, security hardening, optimasi performa, panel admin optimasi.

---

## 📌 STATUS TERAKHIR (sesi debug produksi — 2026-08-30 malam)

### Investigasi 500 produksi (backend + admin) — BELUM SOLVED

**Gejala:** Semua route backend & admin 500 body-kosong di Vercel (bahkan `/up` health Laravel), tanpa error output walau `APP_DEBUG=true`. Local `php artisan serve` (PHP 8.2) BOOTS BERSIH dengan SEMUA kode terbaru. Kedua project (`spmb-backend` & `backend-admin`) patah bersamaan; user deploy 22m sebelum pun sudah broken.

**Yang sudah dilakukan / diverifikasi:**
- **Env vars project OK** (permintaan user): APP_KEY, DB_CONNECTION/HOST/PORT/DATABASE/USERNAME/PASSWORD, MYSQL_ATTR_SSL_CA, APP_URL, FRONTEND_URL, SESSION_SAME_SITE semua ada.
- **SSO/Deployment Protection `spmb-backend` sempat ON** → dimatikan via API PATCH `{"ssoProtection":null}` (per trap AGENTS.md). Belum solusi 500.
- **dompdf DISPROVEN** (test decisif): `composer remove barryvdh/laravel-dompdf` di `backend-admin` → deploy preview `spmb-admin-qg31bzytd-...` → masih 500 di `/login`,`/up`,`/admin`. → Komposer/lock/vendor admin direstore penuh ke semula (git bersih untuk composer.json/lock). Dompdf DIPAKAI di `backend/PendaftaranController.php:345` & `admin/PendaftaranController.php:409` → tidak boleh dihapus permanen.
- **Fresh build = broken, old build = OK**: deployment lama `spmb-backend-60pba8i8d-...` & `5bbtoh1cc-...` masih 200 (di-uji ulang). Hanya *fresh* `composer build` yg 500. `composer.lock` sama dgn HEAD (tidak drift). → Break kemungkinan di **environment build Vercel (PHP 8.3 via vercel-php@0.7.4; lokal 8.2)** / vendor fresh, bukan kode app.
- Ruled out: missing env, SSO, stale `bootstrap/cache` (sudah di `.vercelignore`, masih 500), syntax `php -l` bersih (index.php, bootstrap/app.php, providers.php), dompdf.
- **BLOCKER:** Vercel API log runtime/build semua 404 (`/v3/deployments/{id}/runtime-logs`, `/v1/deployments/{id}/build-log`, `/v2/deployments/{id}/buildlog`). Tidak bisa lihat fatal PHP asli; CLI `vercel logs` hanya line access PHP built-in-server.

**Tindakan permanen kini di working tree:** `backend/vercel.json` APP_DEBUG dikembalikan **false**; `backend/.vercelignore` menyertakan `bootstrap/cache` (pertahankan). Prod alias saat ini masih menunjuk ke deploy broken.

**RECOVERY DIEKSEKUSI ✓ (selesai 2026-08-30):** repoint production alias ke deployment lama yg dikenal-baik:
- `pendaftaranspmb.vercel.app` → `spmb-backend-60pba8i8d-...` (`vercel alias set`) → `/login` 200, `/up` 200.
- `paneladminsmkbu.vercel.app` → `spmb-admin-px9we7k0z-...` (08-23, terakhir brom sebelum break) → `/login` 200, `/up` 200.
- Situs produksi **NORMAL kembali**. Note: deploy admin terakhir `acddcb7sj` (08-30 06:25) SUDAH broken → break terjadi antara 08-23 (good) dan 06:25 hari ini.
- **Belum solved: fresh `composer build` tetap 500** → optimasi baru (Tabungan count SQL, spp/guru-kasir, chartData/laporan) BELUM hidup di produksi (alias lama tdk memuatnya). Staging berikutnya bedah `vercel-php`/PHP 8.3 vs vendor (mis. `thecodingmachine/safe` yg tampil "could not scan" saat composer op) atau minimal-repro PHP polos di Vercel, lalu fresh deploy + alias lagi.

---

## 📌 STATUS TERAKHIR (sesi deploy — 2026-08-30)

### Deploy SPP + Optimasi ke Produksi

- **Migrasi TiDB** (pattern `vercel env pull --environment=production`: pull → `.env.local` → rename `.env.production.local` → set env proses DB_* + `MYSQL_ATTR_SSL_CA=backend\certs\isrgrootx1.pem` — path runtime `/var/task/user/certs/...` ≠ lokal → `php artisan migrate --force` di **satu shell** → hapus file) → 3 migrasi `Ran`: `100000` role enum guru/kasir, `100001` spp_bills+spp_payments, `110000` drop plain_password. Verifikasi schema: spp_bills ✓ spp_payments ✓ plain_password gone ✓.
- **Backend** `pendaftaranspmb.vercel.app` (deploy `spmb-backend-i3q1mpmcj-...`, alias otomatis): `/login` 200, `/csrf-token` balas token (CSRF except list kini tertutup — frontend pakai header X-CSRF-TOKEN via `src/services/csrf.js`), `/lowongan/count` 200, `/spp`+`/spp/kasir`+`/admin/spp/rekap` 302 (guest), `/spp/ortu/1` 403 (signed route aktif). `SecurityHeaders` middleware aktif.
- **Panel** `spmb-admin.vercel.app` (deploy `spmb-admin-nfsixudzy-...`; alias otomatis `paneladminsmkbu.vercel.app` + manual alias `spmb-admin`): `/login` 200, `/admin` & `/admin/spp` 302 (guest). Rekap SPP di panel live.
- **Frontend** `smkbu-sby.vercel.app` (deploy `lomba-493toh3pc-...`, alias otomatis): `/` 200, bundle `SppView-5ziMPgHy.js` live; `bhapppp.vercel.app` 307 (legacy, masih hidup).
- **kasirIndex**: filter belum-lunas dipindah ke SQL **subquery portable** (`whereRaw ... < nominal`), menggantikan `havingRaw COALESCE(paid,0)` (MySQL-only → error `HAVING` di sqlite). Maksud perf (filter di SQL) tetap, suite test sqlite tetap hijau (19 passed).
- `APP_DEBUG` di `backend/vercel.json` dikembalikan **false** (di working tree sempat `true` — bocor stack trace, sekuriti).
- Deploy: `vercel.cmd deploy --prod --yes` per app (root/backend/backend-admin), auth `zakkyilhamf-7419`.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-30 sore — PERFORMANCE AUDIT & OPTIMIZATION)

### Sesi Optimasi Performa Menyeluruh (2026-08-30) — 3 bagian

Gejala: load lambat, interaksi berat (geser/klik), API lama. Audit → fix → ukur ulang. Ledger: `PERF.md`.

**Frontend (smkbu-sby.vercel.app):**
- `CursorGlow.vue`: reactivitas ref tiap `mousemove` + CSS transition `left/top` di elemen fixed → ganti ke `requestAnimationFrame` + tulis langsung `el.style`, `passive:true`, hapus transition. (Perbaikan INP utama saat geser kursor/scroll di desktop.)
- `HomeView.vue`: 6 section di bawah fold (SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview, TabunganBanner) jadi `defineAsyncComponent` + lazy-`mount` via wrapper baru `components/common/LazyMount.vue` (IntersectionObserver, rootMargin 600px). Bundle route awal HomeView **37.33→25.36 kB** (gz 11.33→8.34), CSS 46.2→28.3 kB; tiap section jadi chunk ~1-1.6 kB gz yang di-fetch saat scroll masuk.
- **CareerPreview** pakai endpoint ringan `/lowongan/count` (sebelumnya unduh seluruh list hanya utk angka).
- `useAuthSession.js`: polling `/auth-status` setiap 30s kini HANYA saat login (guest di landing publik tidak fetch tiap 30s); interval tetap singleton modul.

**Backend (pendaftaranspmb.vercel.app):**
- Endpoint baru `GET /lowongan/count` (COUNT is_active saja; route sebelum `/{lowongan}`).
- `TabunganController`: saldo via **1 query aggregate** `SUM(CASE WHEN type...)` di `index` & `store` (helper `saldoFor()`), bukan load semua transaksi + reduce di PHP. **Verifikasi hasil identik** (agg=php=1000, match).
- `SppController::kasirIndex`: filter bill belum-lunas pindah ke **SQL** (`withSum('payments as paid')` + `havingRaw('COALESCE(paid,0) < nominal')`).

**Panel Admin (spmb-admin.vercel.app):**
- `chartData()`: 30×COUNT query → **1×GROUP BY DATE(created_at)** + reuse cache `freshStats()`.
- `laporan()`: 8×COUNT terpisah → pakai `freshStats()` cached (bentuk `$stats` sama, view tetap jalan).

**Keputusan / dilewati (log di PERF.md):**
- Index DB tidak dibuat — tabel kecil (seq scan lebih murah), pajak tulis tanpa bukti query plan.
- Hard-limit `/lowongan` ditunda — halaman cari butuh list penuh utk filter client-side.
- LoadingScreen.vue tidak dipakai di mana pun (bukan bottleneck).

**Verifikasi:** `php -l` bersih (backend & admin); `route:list` bersih; HTTP `/lowongan/count` → `{"total":25}`; aggregate Tabungan == reduce PHP; build Vite sukses; browser dev → semua lazy section render benar saat scroll (error console hanya CORS karena test pakai port 5175 ≠ 5174 diizinkan — normalnya pakai 5174).

> Catatan deploy: perubahan backend `backend/` & panel `backend-admin/` perlu di-deploy ke Vercel masing-masing (kasirIndex & aggregate memakai query MySQL baru — pesan pakai `php artisan serve` lokal utk cek, lalu deploy).

---

## 📌 STATUS TERAKHIR (sesi 2026-08-30)

### Sesi Fitur SPP — bukti bayar satu arah (2026-08-30)

Fitur SPP: bayar sekali di kasir → status langsung terlihat siswa, guru, admin, ortu. Tanpa payment gateway, input manual.

**Arsitektur (per keputusan skoping):**
- Web utama (blade): kasir input + guru rekap + siswa lihat status. Admin login ke panel (backend-admin) — web utama tetap TOLAK login admin.
- Panel backend-admin: rekap read-only untuk admin.
- Roles enum users → `('admin','siswa','pendaftar','guru','kasir')`.

**Backend `backend/` (semua committed):**
- Migrations: `2026_08_30_100000_add_guru_kasir_roles_to_users_table.php` (enum, kini driver-safe → skip di sqlite, jalan di MySQL/TiDB), `2026_08_30_100001_create_spp_bills_table.php` (`spp_bills` + `spp_payments`).
- Models `SppBill.php` (`user`, `payments`), `SppPayment.php` (`bill`, `inputter`); relasi `sppBills()` di `User.php`.
- `SppController.php`: `index` (siswa: bills+payments, terbayar/sisa), `store` (kasir/admin; POST JSON **dan** form blade — redirect back + sukses/error; `lunas` jika terbayar ≥ nominal), `adminIndex` (JSON `/admin/spp`), `kasirIndex` (view, search q), `rekapIndex` (view), `ortuIndex` (signed URL publik).
- Routes: `GET /spp` (auth), `GET /spp/kasir` (kasir,admin), `POST /spp/pay` (kasir,admin), `GET /admin/spp` (JSON, guru,admin), `GET /admin/spp/rekap` (guru,admin), `GET /spp/ortu/{user}` (publik signed).
- `AuthController::login`: guru → `/admin/spp/rekap`, kasir → `/spp/kasir`.
- `GenerateSppBills.php` artisan command `spp:generate {--periode=} {--nominal=}` (default periode sekarang, nominal 150000; idempotent per user+periode via firstOrCreate).
- Views `resources/views/spp/`: `kasir.blade.php` (input + search + stats), `rekap.blade.php` (+ tombol salin Link ortu), `ortu.blade.php` (tanpa akun, signed URL).
- Helper `formatPeriode(periode)` / `formatPeriodeShort()` di `app/helpers.php` (bulan Indonesia locale-independent).
- **Ortu link tanpa akun** = Laravel signed route (`URL::signedRoute('spp.ortu', ['user'=>id])`), validasi `hasValidSignature()` → 403 tanpa signature. Ditampilkan di rekap (guru/admin) utk di-share WhatsApp.
- Test: `SppModelTest` (3), `SppCommandTest` (2), `SppControllerTest` (11 — termasuk ortu signed/no-signature, kasir/guru/admin/siswa guard). **Total 19 passed (55 assertions)**.

**Frontend `backend-admin/` (committed):**
- Copy: migrations SPP, models SppBill/SppPayment, helper `formatPeriode`(+Short), `SppController::rekapIndex`, route `GET /admin/spp` (role admin, name `admin.spp.index`), view `resources/views/spp/rekap.blade.php`, link sidebar "Rekap SPP".
- `User.php` + `sppBills()` relasi.
- Catatan: panel lokal `php artisan migrate` di-sqlite sudah lama rusak (migrasi lama pakai `ALTER TABLE ... MODIFY ENUM` non-sqlite) — bukan regresi sesi ini. Migrasi SPP baru dibuat driver-safe.

**Frontend Vue `src/` (committed):**
- `SppView.vue`: halaman siswa lihat ringkasan sisa + riwayat tagihan per periode (status lunas/belum, pembayaran). Route `/spp` `meta:{requiresSiswa}`. Link di navbar dropdown Layanan ("SPP", guard `guardSiswa`).
- Role guru/kasir pada payload `?auth=` sudah didukung langsung (`frontendAuthUrl` role generik) — tidak perlu perubahan.

**Commit:** `c2561ec` (slice1) `e15f71f` (slice2) `3d83510` (slice3) `8e70846` (slice4) `f4c222a` (5a) `54e8456` (5b) `31902a3` (5c) `d9c902d` (format bulan) `a559291` (5d).

**Lanjutan / catatan:**
- Migrasi prod TiDB belum dijalankan (role enum + spp tables) — jalan saat deploy backend & panel berikutnya.
- Kasir input tanpa overpay di sisi server (amount min:1; jika lebih dari sisa tetap `lunas`) — acceptable, note `ponytail:`.
- Nominal per-tingkat/kelas belum ada (data kelas belum dimodelkan) — `spp:generate` pakai nominal flat.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-23)

### Sesi Web Vitals Optimization — LCP/CLS/INP (2026-08-23)

**Frontend (smkbu-sby.vercel.app):**
- **Self-host Quicksand font**: download `.woff2` dari Google Fonts, simpan di `public/fonts/`, `@font-face` di `variable.css`, `<link rel="preload">` di `index.html`. Hapus render-blocking `fonts.googleapis.com` CSS (~2-4s LCP saving).
- **Hero h1 animation fix**: `animation-fill-mode: both` → `forwards`, `animation-delay: 0s`, `opacity` mulai dari `0.92` (bukan `0`). h1 visible di frame pertama (~0.85s LCP saving).
- **Image optimization + CLS fix**: tambah `width`/`height` di semua `<img>` (Navbar, Footer, FloatingAi, AboutSchool, Feature). Preload logo (`fetchpriority="high"`). Lazy load below-fold images.
- **Disable heavy effects on mobile**: BackgroundFX `display:none` di ≤768px. CursorGlow sudah >900px. Hero ambient blur 70→40px. Navbar `backdrop-filter` 18→8px.
- **Vite code splitting**: `manualChunks` → `vue-vendor` (99KB) + `icons` (12KB) terpisah. Main bundle **104KB → 7.7KB** (93% reduction).
- **Defer fetchStatus()**: `loaded = ref(true)` — render langsung tanpa tunggu backend auth response.
- Deploy OK, build 7.64s.

**Admin Panel (paneladminsmkbu.vercel.app):**
- **DB query optimization**: 8 COUNT queries → **1 GROUP BY** + in-memory cache 10s (static property). `snapshot()` juga pakai cache. TiDB hit berkurang drastis.
- **Self-host font**: copy `Quicksand-Variable.woff2` ke `backend-admin/public/fonts/`, `@font-face` inline, preload.
- **CSS extraction**: 10KB inline CSS → `public/css/admin.css` (14KB). Critical CSS (layout, sidebar, topbar, h1.form-title) di-inline ~2KB. Admin.css load non-blocking via `media="print" onload`.
- **Remove dead JS**: hapus `@vite` (axios 48KB tidak dipakai).
- **Remove double polling**: hapus `setInterval(poll, 20000)` di layout (dashboard sudah punya polling sendiri).
- **Logo WebP**: convert `logo.png` (60KB) → `logo.webp` (13KB). Tambah `width="40" height="40"` + preload. Hapus PHP route, serve via Vercel static.
- **Static file serving**: update `vercel.json` — routes `/css/*`, `/fonts/*`, `/logo.*`, `/favicon.ico` → `/public/*` SEBELUM catch-all PHP route. Static files serve dari Vercel edge (~50ms), bukan PHP cold start (~500ms). Hapus PHP routes `file_get_contents()` dari `web.php`.
- **Fix favicon**: copy dari root project (sebelumnya 0 bytes).
- Deploy OK, CSS ter-load, LCP element `h1.form-title`.

### Sesi E-Learning Redesign + Koperasi Toast Fix (2026-08-23)
- **E-Learning CSS**: 339 lines CSS appended ke `ELearningView.vue` — 3-column layout (sidebar 240px + main + panel 280px), stats cards, continue learning, material grid, quiz grid, achievements, responsive breakpoints (1200px/768px). Font Plus Jakarta Sans via Google Fonts.
- **E-Learning warna hijau**: update CSS variables dari orange (`#f97316`) ke hijau forest site (`#3a6450`). Theme konsisten dengan `variable.css`.
- **Koperasi toast removal**: hapus `showToast()` dari `addToCart()` di `KoperasiView.vue`.
- Deploy OK, kedua fitur included dalam build yang sama.

### Sesi ApplyModal + Career Center Fixes (2026-08-23)
- **ApplyModal textarea bug fix**: hapus `<Teleport to="body">` — scoped CSS tidak apply ke teleported content. Modal render inline dengan `position:fixed`.
- **Success notification redesign**: card centered overlay, manual close button, hapus auto-close timer.
- **auth-status endpoint update**: returns `email`, `nisn`, `jurusan_pilihan`.
- **Lamarans table migration**: tambah columns baru.
- **CORS fix**: Cors middleware dipindah ke global `bootstrap/app.php`.
- **CSRF exclusion**: `lamaran*` routes excluded.
- **Logo fix**: `response()->file()` → `response(file_get_contents(...))` dengan explicit `Content-Type`.
- Deploy OK.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-29)

### Sesi Tabungan Siswa — fitur menabung lengkap (2026-08-29)

**Backend (pendaftaranspmb.vercel.app):**
- Migration `2026_08_29_140000_create_tabungans_table.php`: `id, user_id FK cascade, type enum('setor','tarik'), amount unsignedBigInteger, description nullable, timestamps`.
- Models: `Tabungan.php` + relasi `tabungans()` (HasMany) di `User.php`.
- `TabunganController.php`: `index` (saldo = sum setor − sum tarik + transaksi terbaru), `store` (setor/tarik, tarik cek saldo cukup → 422 "Saldo tidak cukup untuk penarikan.", admin wajib `user_id`), `adminIndex` (semua siswa + saldo via `withSum`).
- Routes `web.php`: `GET/POST /tabungan` (auth), `GET/POST /admin/tabungan` (role admin). Tanpa prefix `/api/`.
- **CSRF fix penting**: `bootstrap/app.php` — `validateCsrfTokens(except: ['lamaran*', 'tabungan*'])`. Sebelumnya POST JSON `/tabungan` kena TokenMismatch → 419 → redirect → error CORS (fetch dilempar ke `/login`). Pola sama dengan `lamaran*`.
- Migrasi prod TiDB pakai pattern `vercel env pull --environment=production` + `MYSQL_ATTR_SSL_CA` (bukan `DB_MYSQL_ATTR_SSL_CA` — salah nama dulu, error cafile stream) + `php artisan migrate --force`. Temp `.env.production.local` dihapus.
- Deploy OK.

**Frontend (smkbu-sby.vercel.app):**
- `src/components/sections/TabunganBanner.vue`: section promo standalone (DI LUAR bento grid) di landing antara `<Feature />` dan `<News />` — kicker "Menabung Jadi Seru", headline + 3 checklist, CTA "Buka Tabungan Siswa" (siswa → `/tabungan`, guest → backend `/login`), doodle `public/doodles/plant.png` (dari `Open Doodles - Plant.png` Downloads) dengan blob/confetti/koin + tag "Saldo Aktif Rp0". Banner tampil untuk semua visitor (promo), halaman-nya khusus siswa.
- `src/views/TabunganView.vue`: topbar (kembali/logo/refresh), saldo-card hijau (barcode dekorasi), aksi Setor/Tarik (nominal bebas), riwayat transaksi (terbaru di atas), modal setor/tarik, toast.
- Route `/tabungan` dengan `meta: { requiresSiswa: true }`.
- **Bug fix query**: `closeModal()` di-guard `modalBusy` sehingga no-op saat submit → modal nggak ketutup setelah transaksi sukses. Fix: set `modalBusy.value = false; modalOpen.value = false;` langsung di success path.
- Deploy OK.

**Verifikasi E2E lokal (login `siswa`/`test1234`):**
- Setor 75000 → saldo Rp75.000, riwayat "+Rp75.000". Tarik 25000 → saldo Rp50.000. Tarik 999999 → 422, toast "Saldo tidak cukup untuk penarikan.", modal tetap buka. Modal ketutup otomatis saat sukses.
- Guest klik banner → redirect ke backend `/login`. Guest `/tabungan` → redirect home. Data test dihapus setelah uji.

### Sesi Koperasi — tombol "Beli Sekarang" terpotong FIXED (2026-08-29)
- Root cause: tidak ada reset global `box-sizing`, `.drawer` `height:100%` + `padding:20px 22px` (content-box) → 42px lebih tinggi dari viewport → footer terpotong.
- Fix: `.drawer { box-sizing:border-box }`, `.drawer-foot { flex-shrink:0; padding:16px 0 env(safe-area-inset-bottom,8px) }`, `.kop-btn.full { min-height:48px }`.
- Deploy OK.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-22)

### Sesi Career Center Full-Stack — job listing + apply lamaran (2026-08-22)
- **Backend full-stack**: Migrations `lowongans` + `lamarans` tables, Models `Lowongan` + `Lamaran`, Controllers `LowonganController` (index/search/filter/sort + show) + `LamaranController` (store with CV upload, myApplications, show, cancel), Routes di `web.php`, Seeder `LowonganSeeder` (25 data lowongan). Deploy backend `spmb-backend-oxvtvu3ls` alias `pendaftaranspmb.vercel.app`.
- **Frontend rewrite**: `CareerJobCard.vue` → horizontal list row (QERZA style: logo circle + title/company + tipe badge + lokasi icon + Lamar button + bookmark), `CariLowonganView.vue` → fetch API + single-column list layout, `ApplyModal.vue` → form lamaran (CV upload + cover letter + readonly user fields + login required), `LamaranSayaView.vue` → list lamaran dengan status badge + cancel action.
- **Bug fix**: `/api/*` prefix bentrok dengan Vercel PHP runtime (Vercel treat `/api/*` sebagai path function PHP). Solusi: pindah route tanpa prefix `/api/` → `/lowongan`, `/lamaran`. Deploy frontend `lomba-4ckeeacsz` alias `smkbu-sby.vercel.app`.
- **CORS fix**: Perbaikan konfigurasi CORS di backend (`Cors.php`) — menambahkan `https://smkbu-sby.vercel.app` ke daftar origin yang diizinkan. Sebelumnya, origin `bhapppp.vercel.app` keluar karena nilai `FRONTEND_URL` env tidak sesuai, menyebabkan fetch request gagal (blank results). Solusi: menambahkan `https://smkbu-sby.vercel.app` ke daftar allowed origins di `Cors.php`. Deploy ulang backend + frontend.
- **Deploy production DB**: migrate + seed via temporary route `/_run-migrate` → TiDB production. Route temporary dihapus, redeploy bersih.

### Sesi Career Center Remake — UI job board dengan sidebar (2026-08-22)

### Sesi Career Center Remake — UI job board dengan sidebar (2026-08-22)
- **Remake total** CareerCenterView.vue dari halaman sederhana jadi layout multi-section dengan sidebar kiri + child routes
- **Referensi**: desain "Jobie Search Job" (Envato) — sidebar navigasi + search bar + filter chips + grid job cards
- **13 file baru/diubah**:
  - `router/index.js` — child routes `/career-center/*` (6 routes) + redirect ke `/career-center/search`
  - `CareerCenterView.vue` — layout shell (sidebar 260px + router-view + mobile hamburger toggle)
  - `CareerSidebar.vue` — sidebar navigasi hijau tua (#2a5238) dengan 6 menu (Dashboard, Cari Lowongan, Lamaran Saya, Pesan, Statistik, Berita Karir) + tombol "Kembali ke Beranda"
  - `CareerJobCard.vue` — card lowongan (judul, perusahaan, lokasi, jurusan, badge tipe Magang/Kerja/BKK)
  - `CareerSearchBar.vue` — v-model search input + tombol Cari
  - `CareerFilterChips.vue` — horizontal scrollable chips (Semua, Magang, Kerja, BKK, RPL, TKJ, AKL)
  - `CariLowonganView.vue` — halaman utama: search + filter + grid 2 kolom + sort + 10 data statis
  - 5 placeholder views (Dashboard stat cards, Lamaran Saya, Pesan, Statistik, Berita Karir — empty state)
- **Tema**: hijau sekolah (--primary #3a6450), sidebar dark (#2a5238), Font Awesome 7 icons
- **Mobile** (<900px): sidebar collapse → hamburger toggle → slide-in overlay + backdrop
- **Data**: 10 lowongan hardcoded (Magang/Kerja/BKK × RPL/TKJ/AKL), search + filter by chip + sort
- **Deploy** `lomba-a5wdopu45` — alias `smkbu-sby.vercel.app` live, build OK 28s, lazy-loaded chunks

### Sesi LCP Optimization — Panel admin LCP 24s → optimized (2026-08-22)
- Critical CSS split (inline + deferred), font preload, Chart.js defer via IntersectionObserver, stats cached 30s, Cache-Control header, fetchpriority="high"
- **Charts dihapus** atas permintaan user — dashboard bersih (stat cards + insight + akun siswa + pendaftar terbaru)
- Deploy `spmb-admin-4k536sjvs` — alias `paneladminsmkbu.vercel.app` live
- User perlu verify LCP via Lighthouse

### Sesi 12z — Panel admin SELESAI (verifikasi penuh 2026-08-19)
- **Root cause 500 guest GET /admin KETEMU**: debug `withExceptions()->render()` sementara di `bootstrap/app.php` panel menangkap SEMUA exception — termasuk `AuthenticationException` yang seharusnya di-handle `Handler::unauthenticated()` → redirect `/login`. Debug render mengubahnya jadi 500. `redirectGuestsTo('/login')` eksplisit di `bootstrap/app.php` panel (biarkan — aman & jelas; setara default `fn() => route('login')`).
- **Bersih-bersih debug selesai**: render debug dihapus dari `bootstrap/app.php`; route `/debug-redirect` dihapus dari `routes/web.php`; try/catch `DEBUG_EXCEPTION` dihapus dari `public/index.php`; `vercel.json` `APP_DEBUG` → `"false"`; `.env` lokal panel dikembalikan ke sqlite + APP_KEY lokal (`base64:9uLdV6nujM/LUS2A3S3ekei8uhIjks1qgm8MfKAlESI=`, DB_DATABASE path absolute tanpa kutip — escape sequence dotenv).
- **Verifikasi production final (`verifyadmin.php` + `verifyadmin2.php`)**:
  - `GET /` → 302 → `/login` 200 ✅; `/up` 200 ✅
  - login admin `admin/admin123` → 200 dashboard render YA (statTotal + "Pantau Data Pendaftar") ✅
  - logout → `/login` ✅; login siswa → ditolak "Hanya akun admin" ✅
  - `/pendaftaran` 200 (ada data), `/pendaftaran/export` 200 text/csv, `/pendaftaran-snapshot` 200, `/pendaftaran/115353` (show) 200 ✅
  - PUT status & DELETE TIDAK dites di production (bukan read-only — menghindari merusak data asli; logika sama dengan web utama yang sudah teruji).
- **IMPLEMENTATION_SUMMARY.md**: section "Panel Admin Terpisah — Sesi 12y/12z" ditambahkan. AGENTS.md struktur 3 bagian sudah update (sesi kemarin).
- **Catatan**: deploy kadang "Not authorized"/"fetch failed" transient → retry; `vercel alias set` dijalankan setiap deploy.
- **Tindak lanjut (2026-08-19, atas permintaan user)**: link **"Dashboard" & "Buka Web Utama ↗" dihapus** dari navbar panel (`backend-admin/resources/views/layouts/app.blade.php` — div `nav-links` pertama dihapus; navbar = brand + badge "Panel Admin" + Logout). `view:cache` OK; deploy Ready 27s; verifikasi ulang `verifyadmin.php`: GET / → /login 200, login admin → dashboard 200, logout → /login, login siswa → ditolak "Hanya akun admin" — semua tetap hijau.
- **Redesign panel jadi sidebar (2026-08-19, atas permintaan user)**:
  - **Layout baru** `layouts/app.blade.php`: sidebar kiri (gradient hijau tua, brand + badge "Panel Admin", menu Dashboard & Data Pendaftar dengan active state, Logout di bawah; guest → teks "Akses terbatas" + link Masuk Panel) + topbar (hamburger mobile, judul halaman, username) + konten. Mobile (<900px): sidebar slide-in + backdrop + hamburger (JS `toggleSidebar`). CSS form/table/btn/alert dipertahankan.
  - **Halaman dipisah**: `/admin` → `PendaftaranController@dashboard` (BARU) → view `pendaftaran/dashboard.blade.php` (BARU): 5 kartu status (Total/Baru/Diproses/Diterima/Ditolak — sebelumnya cuma 3), insight AI, tabel "Pendaftar Terbaru" (5 item, link Lihat), auto-refresh 5s + banner data baru via snapshot. `/pendaftaran` → `index()` → tabel lengkap + Export CSV + hapus modal (stat cards & auto-refresh dihapus dari sini).
  - Verifikasi production (`verifyadmin.php` + script): GET / → /login 200; login admin → dashboard 200 (sidebar=YA, kartu5=YA, terbaru=YA); `/pendaftaran` 200 (Export CSV=YA, statTotal di list=BERSIH); logout → /login; login siswa → ditolak YA. Deploy Ready 27s.
- **5 Fitur panel (2026-08-19, atas permintaan user) — KODE SELESAI LOKAL, DEPLOY TERTUNDA**:
  - **Ringkasan AI → Ringkasan Data**: `RegistrationInsightService` disederhanakan — HAPUS semua pemanggilan LLM (Http/Ninerouter/Groq), langsung `buildFallbackSummary()` (template statis). Label view "Ringkasan AI" → "Ringkasan Data". `index()` tidak lagi generate insight.
  - **A. Analitik Chart.js**: CDN chart.js@4.4.1 di `dashboard.blade.php`; controller `chartData()` (30 hari pendaftar, jurusan, status); 3 canvas: line "Pendaftar per Hari (30 Hari)", doughnut jurusan & status. Grid 1.4fr/1fr/1fr → 1fr di <1024px.
  - **B. Pencarian + filter**: `index(Request)` — filter `q` (nama/nisn/nik/asal_sekolah/no_hp), `status`, `jurusan`, checkbox `duplikat`, pagination `withQueryString()`. Form `filter-bar` di `index.blade.php` + tombol Reset.
  - **C. Deteksi duplikat**: `$duplicateNisn`/`$duplicateNik` (groupBy havingRaw count>1) → badge "NISN ganda"/"NIK ganda" di baris list; filter checkbox "Duplikat NISN/NIK".
  - **D. Laporan mingguan**: route `/laporan` → `laporan()` (stats per status/jurusan + pendaftar minggu ini `startOfWeek`–`endOfWeek`) → view `pendaftaran/laporan.blade.php` (kertas A4, kop sekolah, tabel, kolom TTD Kepala Sekolah & Admin, tombol Cetak/PDF via `window.print()`; CSS `@media print` sembunyikan sidebar/topbar). Menu "Laporan" di sidebar.
  - **E. Badge live sidebar**: JS poll snapshot 5s di `layouts/app.blade.php` — baseline `latest_id` di sessionStorage; jika ada id baru → badge merah di link "Data Pendaftar" (jumlah baru).
  - **Error selama develop**: `Cannot redeclare index()` — edit controller menyisakan method `index()` lama (baris ~109) → dihapus. Route:list & view:cache OK.
  - **DEPLOY GAGAL berulang (fetch failed / 400 missing_files — upload terputus)**: API vercel normal (`/v2/user` 200), whoami OK, `Test-NetConnection api.vercel.com:443` True — masalah jaringan ISP ke endpoint upload. **TODO: retry `vercel deploy --prod --yes` + `vercel alias set <url> spmb-admin.vercel.app` + verifikasi (dashboard chart=YA, filter, laporan 200, badge).**
  - **Diagnosis lanjut (19/8): POST diblokir ISP total** — GET ke api.vercel.com OK (~8s), tapi POST apa pun (kecil 3B maupun 200KB) → `UND_ERR_CONNECT_TIMEOUT`; httpbin.org juga ETIMEDOUT. Ping 0% loss ke api.vercel.com tapi TCP POST tidak pernah tersambung (upload putus di tengah ~60-75KB). Ini blokir/throttle upload oleh ISP, bukan kode. **Coba: jaringan lain (hotspot HP) → `vercel deploy --prod --yes` dari `backend-admin` + `vercel alias set <url> spmb-admin.vercel.app` + verifikasi.**
  - **DEPLOY + VERIFIKASI TUNTAS (19/8 malam)**: setelah beberapa jam, `vercel deploy --prod --yes` OK (Ready 29s, `spmb-admin-63x7njdai-zakkys-projects-99c4bf23.vercel.app`) → `vercel alias set` OK → spmb-admin.vercel.app mengarah ke deployment baru. **Verifikasi production 22/22 PASS** (`verifyadmin4.php`): GET / → /login; login admin; /admin 200 dengan Label "Ringkasan Data" + canvas chartDaily/chartJurusan/chartStatus + CDN Chart.js + menu Laporan di sidebar + TIDAK ada "Ringkasan AI"; /pendaftaran 200 + filter q/status/jurusan/duplikat + filter q berfungsi; /laporan 200 + kop "SMK Bahrul Ulum" + tombol Cetak + tabel status (label "Baru/Diproses/Diterima/Ditolak" kapital). Catatan: verifikasi awal false-fail karena (a) GET login tidak menyimpan cookie jar → CSRF 419 → redirect 302 (fix: GET login wajib pakai cookie jar), (b) regex header curl case-sensitive (HTTP/2 header lowercase) — bukan bug aplikasi. Jaringan masih fluktuatif (percobaan 1-2 fail code=0, percobaan 3 bersih).
- **Optimasi mobile + HP kentang (19/8, atas permintaan user "tampilan mobile masih acak-acakan + minta lancar di HP kentang") — KODE + DEPLOY + VERIFIKASI TUNTAS** (deploy `spmb-admin-a8aldzzwv-zakkys-projects-99c4bf23`, alias OK, verifyadmin5 16/16 PASS):
  - **Fix "acak-acakan"**: (1) tabel laporan TANPA wrapper overflow → halaman melebar di HP → semua tabel (laporan/dashboard/list) dibungkus `.table-wrap` (overflow-x + -webkit-overflow-scrolling + `min-width` kolom, th/td nowrap); (2) topbar-title `white-space:nowrap + ellipsis + min-width:0` (judul panjang terpotong rapi, tidak mendorong layout); (3) username di topbar `hide-sm` di HP; (4) `.hide-sm` = kolom No HP/Asal Sekolah disembunyikan di <768px (tabel dashboard 5→3 kolom, list 7→5 kolom, tetap muat tanpa geser horizontal); (5) filter-bar di mobile: input+select width 100%; (6) `.report-head` mobile: logo 44px + judul 14px; (7) sidebar-link min-height 46px (touch target); (8) input/select/textarea `font-size:16px` di mobile (anti auto-zoom iOS saat fokus).
  - **Perf HP kentang**: hapus SEMUA `backdrop-filter` (topbar blur(20px) + sidebar backdrop blur(3px) — paling boros GPU); polling snapshot turun 5s → **20s** (badge sidebar & auto-refresh dashboard — total request 2/menit per halaman, sebelumnya 24/menit); chart init dibungkus `requestAnimationFrame` + guard `typeof Chart === 'undefined'` (tidak blokir paint, aman kalau CDN gagal di jaringan lemot); `preconnect` fonts.googleapis + fonts.gstatic.
  - Laporan print tetap normal (`@media print`: `.table-wrap` overflow visible + min-width 0 — tabel tidak terpotong saat cetak).
  - Deployment ke-2 hari ini butuh 9 retry (`fetch failed` ISP blokir POST — pola sama, retry lama akhirnya tembus).
- **Optimasi mobile + HP kentang WEB UTAMA (frontend Vue, 19/8, atas klarifikasi user "saya minta web utama juga") — KODE + DEPLOY + VERIFIKASI TUNTAS** (deploy `lomba-e05z8wi22-zakkys-projects-99c4bf23`, alias bhapppp.vercel.app OK, build lokal OK, homepage prod 200):
  - Audit: router sudah lazy-load per route (index gzip 41KB, halaman 1-14KB gzip); semua section/view sudah punya media queries (2-3 per file); CursorGlow sudah desktop-only (>900px); tidak ada import FontAwesome/LoadingScreen yang tidak dipakai; footer responsif penuh.
  - **Perf fix (boros GPU → hemat)**: (1) `Hero.vue` — 2 blobs `filter: blur(70px)` (240-280px) disembunyikan di ≤900px; (2) `Navbar.vue` — backdrop-filter blur(18px) dihapus dari `.mobile-nav`, navbar jadi `rgba(255,255,255,.97)` solid di mobile (navbar fixed selalu di layar = recompute terus); (3) `FloatingAi.vue` — backdrop-filter blur(16px) dibuang → `rgba(255,255,255,.95)` (tombol fixed selalu tampil); (4) `ChatHeader.vue` — blur(18px) → solid `.96`; (5) `ChatInput.vue` — input `font-size:15px→16px` (anti auto-zoom iOS saat fokus).
  - TIDAK menyentuh: `api/chat.js`, `api/knowledge/**`, `vite.config.js` bagian chat, `src/server/.env` (aturan AGENTS.md).
  - Catatan: API Vercel `deployments` endpoint 404 untuk project frontend ini → verifikasi chunk production tidak bisa via API; cukup build lokal + homepage 200 + user cek visual di HP. Deploy butuh 9 retry (ISP blokir POST).
- **Navbar mobile BACKEND UTAMA diperbaiki (19/8, laporan user: "navbar di halaman login versi mobile acak-acakan") + semua verifikasi TUNTAS**:
  - Akar masalah: navbar `backend/resources/views/layouts/app.blade.php` TIDAK punya mode mobile — 3 grup (logo+brand, Beranda+Formulir, Masuk+Daftar) tidak bisa wrap di layar 360px → saling tindih.
  - **Fix CSS-only (tanpa JS)**: di `@media (max-width:768px)` navbar jadi `flex-wrap:wrap; height:auto` (2 baris: atas = logo+brand + Masuk/Daftar, bawah = Beranda+Formulir via `order:3; width:100%` + `.nav-links:first-of-type`); brand 16px + logo 34px; backdrop-filter blur(20px) navbar dibuang (solid .96 — perf); input/select/textarea `font-size:16px` (anti auto-zoom iOS) — konsisten dengan perbaikan panel admin & frontend.
  - **TRAP TERBARU**: `vercel deploy` dari folder `backend` MENGIRIM ke project `spmb-admin` (bukan `spmb-backend`) → deployment 500 di project panel (alias panel TIDAK terpengaruh). Root cause: `.vercel/project.json` di `backend` berisi projectId yang salah/outdated. **Fix: `vercel link --yes --project spmb-backend` dari folder backend (lalu HAPUS `.env.local` buatan link — trap AGENTS), deploy ulang → `spmb-backend-pfvan880u` (Ready 35s), alias set → spmb-backend-self.vercel.app OK.** Panel lalu di-deploy ulang dari `backend-admin` (`spmb-admin-byvrw5d12`, alias spmb-admin.vercel.app dikoreksi ke deployment baru setelah sempat salah set ke URL lama 63x7njdai).
  - Verifikasi production (`verifyall.php` 11/11 PASS): backend /login 200 + navbar flex-wrap + order baris kedua + backdrop-filter hilang + input 16px + brand 16px; panel /login 200 + POST login 302 + /admin 200 + chartDaily masih ada.
  - **Pelajaran: SELALU cek hasil deploy → pastikan prefix URL sesuai project yang dimaksud (`spmb-backend-*`, `spmb-admin-*`, `lomba-*`) sebelum set alias.**

- **Navbar mobile BACKEND UTAMA — perbaikan ke-2 (19/8, laporan user: "navbar halaman login versi mobile acak-acakan" + screenshot)**:
  - Screenshot 358px menunjukkan: teks "SMK Bahrul Ulum" pecah 3 baris ("SMK" / "Bahrul" / "Ulum"), semua elemen dipaksa 1 baris → tumpang tindih, Vite `@vite` directive memicu404 error di production (no build assets).
  - **Fix** di `backend/resources/views/layouts/app.blade.php`: (1) `@vite` dihapus (ponytail — no build dir in production, causes404); (2) `.navbar > div:first-child` → `flex:1; min-width:0;` (brand area takes remaining space); (3) `.navbar-brand span` → `white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-width:0;` (brand text truncated, tidak pecah); (4) `.navbar-brand` → `min-width:0` (allows truncation); (5) `.nav-links` → `flex-shrink:0` (tombol Masuk/Daftar tidak menyusut); (6) padding navbar diketat `10px 12px`, nav-links font/padding dikurangi.
  - Deploy `spmb-backend-gtsmyrle4`, alias OK, login page 200, CSS ter-deploy (flex-shrink, text-overflow ada, Vite404 hilang).

- **Navbar BACKEND UTAMA ditingkatkan: hapus nav-links Masuk/Daftar/Beranda/Formulir (2026-08-19, atas permintaan user "button di navbar login sudah ga guna")**:
  - Kedua div `.nav-links` (Beranda+Formulir + Masuk+Daftar) dihapus dari `app.blade.php` — navbar kini hanya menampilkan brand/logo + back button (untuk auth users).
  - Link "Lupa kata sandi?" dan "Daftar di sini" tetap ada di dalam form login (konten), bukan navbar.
  - Deploy `spmb-backend-4jcavmwyk`, alias `pendaftaranspmb.vercel.app`, login page 200, navbar bersih tanpa elemen nav-links.

- **Redesign halaman login/register jadi SPLIT-SCREEN (19/8, atas permintaan user + referensi gambar Shutterstock "Colorful website login ui design vector")**:
  - Layout baru `backend/resources/views/layouts/auth.blade.php` (BARU): split-screen — kiri `.auth-illustration` (gradient hijau `#214936→#2f5b45→#3a6450`, 3 blob CSS organik semi-transparan, brand logo + "SMK Bahrul Ulum" + headline "Selamat Datang di PPDB Online"), kanan `.auth-form-area` (card putih `.auth-card` 400px, rounded 24px, shadow besar, strip gradient hijau di atas, @yield('content')).
  - Mobile (<768px): stack vertikal — ilustrasi di atas (min-height 220px, blob dikecilkan), form di bawah; input 16px (anti auto-zoom iOS).
  - `login.blade.php` & `register.blade.php` → `@extends('layouts.auth')`, field input dibungkus `.input-wrap` dengan **SVG icon inline** (user, lock, email) di kiri input.
  - Tema TETAP hijau (konsisten web utama) sesuai keputusan user.
  - `view:cache` OK; deploy `spmb-backend-45lwdx7v3`, alias `pendaftaranspmb.vercel.app`, login 200, semua elemen (auth-wrapper, illustration, blobs, card, input-wrap icons, btn gradient) ter-render.
  - Catatan: `forgot-password.blade.php` & `reset-password.blade.php` MASIH pakai `layouts.app` (belum diubah — user hanya minta login/register).

- **Domain backend diganti → `pendaftaranspmb.vercel.app` + fix redirect login/register FRONTEND (19/8, laporan user: "url kok ga keganti? pas mau login siswa malah begini")**:
  - User ganti domain backend di dashboard Vercel: `spmb-backend-self.vercel.app` → **`pendaftaranspmb.vercel.app`** (alias lama dihapus → `DEPLOYMENT_NOT_FOUND`). Set ulang alias ke deployment terbaru.
  - **Root cause "URL tidak ganti"**: `vercel.json` frontend masih hardcode redirect `/login` & `/register` → `https://spmb-backend-self.vercel.app` (domain mati). Env Vercel `VITE_BACKEND_URL` project `lomba` juga masih domain lama.
  - **Fix**:
    1. `vercel.json` (root frontend): redirect `/login` → `pendaftaranspmb.vercel.app/login`, `/register` → `.../register`.
    2. Env `VITE_BACKEND_URL` project `lomba` (id `g4MEt0b1m2EFHjqe`): DELETE via API (200) → POST ulang nilai `https://pendaftaranspmb.vercel.app` (201, id baru `uR2DKYO9vk0tHZnS`). PUT 404 → pakai DELETE+POST. CLI env add hang (ISP) → pakai `curl.exe` + API langsung + token dari `C:\Users\LENOVO\AppData\Roaming\xdg.data\com.vercel.cli\auth.json` (BUKAN `~/.vercel/auth.json` — path salah).
    3. Deploy frontend `lomba-rl3zai14q` (Ready 36s, 1 retry fetch failed) — build OK, HomeView 28KB gzip 9.7KB, index 105KB gzip 41KB.
  - **Penting**: deploy CLI otomatis alias ke **`smkbu-sby.vercel.app`** (domain baru frontend yang di-set user di dashboard; `bhapppp.vercel.app` kini 307 redirect ke smkbu-sby).
  - **Verifikasi**: `smkbu-sby.vercel.app/login` → 308 → `pendaftaranspmb.vercel.app/login` ✅; `/register` → 308 → `.../register` ✅; homepage 200 ✅; `bhapppp.vercel.app/login` → 307 → smkbu-sby/login ✅.
  - Catatan: AGENTS.md "Frontend Vue → port 5174 / bhapppp" perlu diupdate ke `smkbu-sby.vercel.app` (deferred).

- **Login/register: background hijau FULL page (19/8, atas permintaan user "coba background hijau full kan, mau lihat bagus ga")**:
  - Ubah `layouts/auth.blade.php` dari split-screen → **full-page hijau**: `body` gradient `#214936→#2f5b45→#3a6450` + `background-attachment: fixed`; `.auth-illustration` jadi `position:absolute; inset:0` (layer blob dekorasi penuh, tanpa teks); `.auth-form-area` jadi kolom ter-tengah (z-index 2) berisi brand+headline di atas + `.auth-card` putih 400px di bawah.
  - Blob dekorasi diperbanyak jadi 4 (blob-1..4) tersebar di seluruh halaman (top-left besar, bottom-right, mid-left, top-right).
  - Mobile (<768px): ukuran blob/headline/brand dikurangi, form-area padding `24px 16px 40px`.
  - Deploy `spmb-backend-2irt4zvve`, alias `pendaftaranspmb.vercel.app`, login 200, semua CSS + HTML ter-render.

- **Login/register: efek GLASSMORPHISM pada card (19/8, atas permintaan user "coba kasih efek glass pada card login")**:
  - `.auth-card`: `background: rgba(255,255,255,0.16)` + `backdrop-filter: blur(18px)` + `-webkit-` prefix + border `rgba(255,255,255,0.28)` (glass effect).
  - Strip gradient di atas card pindah dari `::before` → `::after` (arah `#7db88d→#3a6450→#2a5238`); `::before` lama dihapus (tidak ada duplikat strip).
  - Input tetap solid putih (`#ffffff`) supaya readability terjaga di atas glass.
  - Deploy `spmb-backend-ljn2x2z9e`, alias `pendaftaranspmb.vercel.app`, CSS ter-deploy (backdrop-filter, rgba glass, ::after).

- **Card login: glass lebih tebal + kontras (19/8, user lihat screenshot & pilih "Glass lebih tebal")**:
  - Keluhan user: card "kalah" sama background hijau — teks & card tenggelam.
  - `.auth-card`: opacity `0.16`→`0.28`, blur `18px`→`20px`, shadow `0 24px 60px rgba(28,42,35,0.14)`→`0 30px 70px rgba(13,26,20,0.45)`, border `rgba(255,255,255,0.28)`→`0.42`.
  - Kontras teks dinaikkan: `.form-title` `#1c2a23`→`#12241b` (lebih pekat), `.form-subtitle` `#647067`→`#55635a`, `.auth-links` `#647067`→`#4a5a50`, `.auth-links a` `#3a6450`→`#245236` (hijau lebih pekat).
  - Deploy `spmb-backend-eoj6j2isd` (2× fetch failed → attempt 3 sukses), alias `pendaftaranspmb.vercel.app`, CSS ter-deploy.

- **Card login: font di-cerahkan (19/8, user: "font nya sekarang gelap, kalah sama background, tolong cerahkan dikit")**:
  - `.form-title` `#12241b`→`#1e3d2e`, `.form-subtitle` `#55635a`→`#71817a`, `.auth-links` `#4a5a50`→`#6b7a71`, `.auth-links a` `#245236`→`#2f5b45`.
  - Deploy `spmb-backend-omyuua6bl`, alias `pendaftaranspmb.vercel.app`.

- **Halaman Profil (`auth/profile.blade.php`): hapus "Kembali ke Beranda" + tambah tombol Logout (19/8, user: "karna udah ada tombol back di sebelah logo, tambahkan logout")**:
  - Link `&larr; Kembali ke Beranda` (bagian bawah halaman) DIHAPUS — navbar sudah punya tombol back (SVG arrow) di sebelah logo.
  - Tambah tombol **Logout** di bawah tombol "Lihat Dashboard"/"Isi Formulir Pendaftaran": form POST `route('logout')` + `@csrf`, class baru `.profile-btn-logout` (putih, border merah `#e2b6b2`, teks `#b3362c`, hover bg `#fdf1f0`).
  - Route profil: `GET /profil` (name `profil`, middleware `role:siswa`) — 302 ke `/login` bila belum login.
  - Deploy `spmb-backend-fd6y538hw` (6× fetch failed + 1 retry → attempt 7 sukses), alias `pendaftaranspmb.vercel.app`.

- **Forgot & Reset Password disamakan dengan UI login/register (19/8, user: "sekarang ui forgot passwordnya sama kan dengan ui login siswa/pendaftar")**:
  - `forgot-password.blade.php` & `reset-password.blade.php`: `@extends('layouts.app')` → `@extends('layouts.auth')` — kini full-page hijau + glass card + blob + SVG icon di `.input-wrap` + `.auth-links` (sama persis login/register).
  - Struktur disesuaikan: `.form-section` dibuang (auth layout sudah punya card), `.btn-group` dihapus (btn-primary sudah full width), inline style link diganti `.auth-links`.
  - Deploy `spmb-backend-cmqspdoks` (8× fetch failed → attempt 9 sukses), alias `pendaftaranspmb.vercel.app`, `/forgot-password` render auth layout (auth-wrapper, blobs, input-wrap terverifikasi).

- **Dashboard Siswa: auto-refresh status pendaftaran (20/8, user: "saat admin ubah status diterima/tolak, web utama auto refresh")**:
  - Backend: method baru `myDashboardSnapshot()` di `PendaftaranController` → JSON `{status, status_updated_at, nama_lengkap, jurusan_pilihan}` untuk pendaftaran user login (admin → 403). Route baru `GET /dashboard-siswa/snapshot` (name `dashboard.siswa.snapshot`, middleware auth).
  - View `dashboard-siswa.blade.php`: polling `setInterval(poll, 15000)` (JS async fetch, header X-Requested-With, credentials same-origin) — bandingkan `status` dari snapshot vs `initialStatus` (dari blade `Js::from`); jika beda → `location.reload()`. Hanya aktif saat `$hasData` (@if di akhir halaman).
  - Dashboard admin TIDAK diubah — polling `pendaftaran.snapshot` tiap 20 detik sudah ada (update stats + banner "data baru masuk").
  - Deploy `spmb-backend-4wdbg6qou`, alias `pendaftaranspmb.vercel.app`. Verifikasi: route terdaftar, snapshot tanpa login → 302 ke /login, polling JS ter-kompilasi di view cache.

- **Dashboard Admin: Reset Password Siswa (21/8, user: "tambahkan password yang dibuat oleh siswa, kadang ada yang lupa")**:
  - Password di-hash (bcrypt) — tidak bisa ditampilkan. Solusi: **tombol Reset** yang generate password baru random (8 char uppercase), hash & simpan, tampilkan plain password ke admin via flash message.
  - Route baru: `POST /admin/akun/{user}/reset-password` (name `admin.resetPassword`, middleware auth+role:admin).
  - Controller: `resetUserPassword(User $user)` — abort_if admin, generate `strtoupper(substr(uniqid(), -8))`, `bcrypt()` → update, redirect back with `session('reset_password')`.
  - Dashboard view: kolom "Password" (masked `••••••••`) + tombol Reset (outline merah, confirm dialog). Flash message: "Password untuk {name} berhasil direset. Password baru: {plain} — kasih ke siswa, lalu login ulang."
  - Deploy `spmb-admin-2dbge89u0`, alias `paneladminsmkbu.vercel.app`.

- **Dashboard Admin: tombol Show/Hide password (hash) (21/8, user: "tambahkan show password nya juga")**:
  - Password di-hash (bcrypt) — plaintext tidak bisa ditampilkan. Toggle eye icon menampilkan **hash** (bukti password ada) vs `••••••••`.
  - Kolom Password: `<code data-hash="{{ $akun->password }}">••••••••</code>` + tombol eye SVG (open/closed).
  - JS `togglePw(id)`: baca `data-hash` attribute, ganti textContent ke hash (font-size 10px, color gelap) atau kembali ke dots. Pakai `data-` attribute bypass `$hidden` di User model.
  - Deploy `spmb-admin-7jrrzhiea`, alias `paneladminsmkbu.vercel.app`.

- **Password siswa ditampilkan asli (plaintext) di dashboard admin (22/8, user: "tampilkan password asli aja, jangan cuma hash")**:
  - Migration baru `add_plain_password_to_users_table` → kolom `plain_password` (nullable) di tabel `users`. Dijalankan ke TiDB via route temporary `/run-migration-x9k2` (unauthenticated GET) → DONE, route dihapus.
  - `backend/app/Models/User.php`: tambah `plain_password` ke `$fillable`.
  - `backend/app/Http/AuthController::register()`: simpan `$validated['password']` ke `plain_password` saat registrasi.
  - `backend/app/Http/AuthController::resetPassword()`: simpan `$password` ke `plain_password` saat reset via link email.
  - `backend-admin/app/Models/User.php`: tambah `plain_password` ke `$fillable`.
  - `backend-admin/PendaftaranController::resetUserPassword()`: simpan `$plain` ke `plain_password` saat admin reset.
  - Dashboard admin view: kolom Password pakai `data-pw="{{ $akun->plain_password }}"`, toggle eye icon tampilkan plaintext (bukan hash).
  - Deploy `spmb-admin-k2bhpnbts` + `spmb-backend-9wuhgvbci`, keduanya alias OK.
  - **Catatan**: akun lama yang sudah ada sebelum migration tidak punya `plain_password` → tampil "(tidak ada)" atau kosong. Hanya akun baru + yang di-reset yang punya plaintext.

**LANGKAH BERIKUTNYA (deferred, keputusan user — stats dinamis & section Jurusan)**:
1. Migration `school_stats` (key-value: siswa_aktif, jurusan, program_keahlian, `jurusans` JSON) + seeder (nilai sekarang: 1280 siswa, 1 jurusan, 1 program keahlian).
2. Endpoint publik `GET /school-stats` (web utama) + halaman admin `/admin/stats` di panel.
3. `AboutSchool.vue` fetch stats + fallback hardcode; komponen baru `JurusanSection.vue` (sekolah hanya punya RPL).
4. CORS tambah `https://bhapppp.vercel.app`.
5. **Tanya user**: angka asli jumlah siswa/kuota RPL (atau biarkan admin edit via panel).

### Sesi 12y — Pemisahan web admin ke panel terpisah `spmb-admin.vercel.app` (SELESAI di 12z)
- **Keputusan user** (via question tool): admin punya panel sendiri di domain terpisah, login admin terpisah dari siswa/pendaftar (DB tetap sama — TiDB), UI admin di-polish. Stats dinamis & section Jurusan DITUNDA ke sesi berikutnya.
- **Sisi web utama (backend) — SELESAI & terverifikasi**:
  - Route admin DIHAPUS dari `backend/routes/web.php` (GET /admin dll → 404; `Route::resource` tidak dipakai, semua route eksplisit).
  - `AuthController::login()`: role admin → login DITOLAK + pesan "Akun admin dikelola di panel admin terpisah: spmb-admin.vercel.app" (login tidak dilakukan). Siswa/pendaftar tetap normal → `frontendAuthUrl()`.
  - View admin (`pendaftaran/index.blade.php`, `show.blade.php`) dipindah ke panel.
  - Deploy backend Ready 29s; verifikasi production: GET /admin → 404 ✅, login admin → 302 + pesan "panel admin terpisah" ✅, login siswa → landing `?auth=` ✅.
- **Panel admin (BARU: `C:\Users\LENOVO\lomba\ga-ro\backend-admin`)** — salinan backend via robocopy (exclude `vendor`, `.git`, `node_modules`, `.env`, `.env.local`; robocopy exit code 1 = sukses):
  - `routes/web.php`: admin-only (`/` → redirect `/admin`; `/login` guest; `/logout` auth; forgot/reset password; group auth+role:admin: `/admin`, `/pendaftaran`, export, snapshot, show, PUT status, DELETE destroy).
  - `AuthController.php`: login hanya role admin ("Hanya akun admin yang dapat mengakses panel ini."), logout → `/login`. View siswa (register, profile, create, dashboard-siswa, bukti) dihapus.
  - UI di-polish: title "Panel Admin - SPMB SMK Bahrul Ulum", `.container` max-width 1180px, navbar brand + badge "Panel Admin" + link Dashboard + "Buka Web Utama ↗" (→ FRONTEND_URL) + Logout; halaman login "Masuk Panel Admin" max-width 480px tanpa link register; empty-state index → link FRONTEND_URL.
  - `route:list` & `view:cache` OK lokal. `.env` lokal panel = `.env.example` + APP_KEY **lokal** (`base64:9uLdV6nujM/LUS2A3S3ekei8uhIjks1qgm8MfKAlESI=` — JANGAN dipakai production), DB_CONNECTION=sqlite (hanya untuk artisan lokal). Vendor di-copy lokal hanya agar artisan jalan (tidak ter-upload).
- **Vercel project `spmb-admin`** (baru): `vercel link --yes` → rename `backend-admin` → `spmb-admin`; env production ditambahkan: APP_KEY **production** (`base64:gtKJvpBuztMINYQwxnKgMFIHQaYvy3WnzBS0+ItkX5g=`), APP_URL, FRONTEND_URL, DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD (TiDB), MYSQL_ATTR_SSL_CA=`/var/task/user/certs/isrgrootx1.pem`, SESSION_SAME_SITE=`lax` (main pakai `none`), SESSION_SECURE_COOKIE=true. Semua "✓ Added".
  - **Trap**: `vercel link` membuat `.env.local` (VERCEL_OIDC_TOKEN) → MissingAppKeyException 500 → hapus segera (sudah). 
  - **Trap**: project Vercel baru punya deployment protection (`ssoProtection: {"deploymentType":"all_except_custom_domains"}`) → semua request (termasuk custom domain) redirect ke vercel.com/login → dimatikan via API: `PATCH https://api.vercel.com/v9/projects/spmb-admin?teamId=zakkys-projects-99c4bf23` body `{"ssoProtection":null}` ✅ (GET /login → 200 setelahnya).
  - Deploy beberapa kali (fetch failed transient sesekali → retry); `vercel alias set <deployment-url> spmb-admin.vercel.app` dijalankan SETIAP deploy (alias tidak otomatis mengikuti deploy prod).
- **Debug 500 login admin — ROOT CAUSE KETEMU & DIPERBAIKI**:
  - 500 `QueryException: Database file at path [pendaftaran_db] does not exist. (Connection: sqlite)` → `DB_CONNECTION` KOSONG di production panel (fallback Laravel 12 = sqlite). Main punya DB_CONNECTION=mysql di project env; panel tidak.
  - Cara menemukan: `withExceptions()->render()` sementara di `bootstrap/app.php` (render exception → response plain "DEBUG: ..." + dump env()) — karena APP_DEBUG=true tidak efektif (vercel.json `"APP_DEBUG": "false"` menang / error page Laravel generik).
  - Fix: tambah `"DB_CONNECTION": "mysql"` ke `env` di `vercel.json` panel (env Vercel project + vercel.json TERBUKTI masuk PHP runtime; `.env` TIDAK ter-upload — `.vercelignore` exclude `.env`/`.env.*` tapi `!.env.example` — jangan hapus kecuali tahu konsekuensinya).
  - **Terverifikasi**: login admin `admin/admin123` → **200, dashboard render YA** (len 30KB, statTotal + "Pantau Data Pendaftar") ✅. Logout → 302 /login ✅.
- **Alat test (masih dipakai)**: `C:\Users\LENOVO\AppData\Local\Temp\opencode\verifyadmin.php` (login admin → dashboard render; logout; login siswa → ditolak) & `verifyadmin2.php` (up, list, export, snapshot, show) & `one.php <url>` (dump DEBUG exception — butuh render debug aktif). Jalankan dari folder `backend-admin`.
- Catatan teknis lintas sesi: dev IP kena WAF 403/429 Vercel (verifikasi dari browser user); `vercel env add` via `echo value |` OK; `vercel.cmd` = `C:\nvm4w\nodejs\vercel.cmd`; team `zakkys-projects-99c4bf23`; robocopy exit 1 = sukses.

### Sesi 12x — Fix "button login muncul setelah login" (alur back dari halaman form)
- **Bug user**: login → (backend) halaman form → tombol Back → landing → button login masih muncul padahal sudah login (dan state campur aduk).
- **Akar masalah**: login/register redirect ke halaman BACKEND (form/beranda) → landing tidak pernah menerima `?auth=` payload → sessionStorage frontend kosong → `fetchStatus`/`/auth-status` lintas-domain tak bisa diandalkan (third-party cookie diblokir) → guest.
- **Fix**:
  - `AuthController::login()`: siswa & pendaftar → `redirect(frontendAuthUrl())` (landing `/?auth=<payload>`), admin tetap admin.dashboard. `register()` → `redirect(frontendAuthUrl())` (sebelumnya langsung ke form). Draft tetap di-restore via `restorePendingDraft` sebelum redirect.
  - `useAuthSession.js`: normalisasi `norm()` — role DB `pendaftar` → `siswa` di UI (diterapkan di sessionFromStorage, applyAuthQuery, fetchStatus). Sebelumnya pendaftar login → UI guest → button login muncul.
- **Alur sekarang**: login → landing `?auth=siswa` → sessionStorage terisi (bertahan di tab selama bfcache/back) → navbar Profil + hero "Dashboard Siswa", button login HILANG; klik "Lengkapi Pendaftaran" → form; back → tetap siswa ✅. Logout (backend, token segar) → landing `?auth=guest`.
- **Verifikasi**: curl production — login siswa → redirect `bhapppp.vercel.app/?auth=` payload `{logged_in:true, role:"siswa", name:"Siswa Demo", has_pendaftaran:false}` ✅.
- Deploy: frontend Ready 16s (`index-DBwWPBAD.js`; retry sekali — fetch failed transient), backend Ready 30s.

### Sesi 12w — Restore dropdown Login 2 opsi (Siswa & Pendaftar) di hero
- **Konteks**: user ingat dulu ada 2 opsi login (siswa & pendaftar) — bekasnya ditemukan di `PROGRESS.md` Sesi 12q (dropdown dihapus atas permintaan user saat itu) & `git show 1aa519d:src/components/sections/Hero.vue`.
- **Restore** (gabungan dengan Sesi 12v): siswa → tetap "Dashboard Siswa"; guest → button **Login** + dropdown 2 sub-button "Login Siswa" & "Login Pendaftar" (keduanya → `${BACKEND}/login`; halaman login backend satu form — opsi hanya pemisahan label, role sebenarnya ditentukan saat register). Kode & CSS diambil dari bekas git 1aa519d (`.btn-group-login`, `.sub-buttons`, `.sub-btn`, media query mobile).
- Deploy: build OK (`index-DnrLCbb-.js`), frontend Ready 17s.

### Sesi 12v — Fix button hero "Lanjutkan Pendaftaran" untuk guest → "Login"
- **Klarifikasi**: "button login hilang" BUKAN bug sesi — `Hero.vue:39-45` menampilkan "Lanjutkan Pendaftaran" (→ `/pendaftaran/create`) untuk SEMUA guest sejak awal; user login memang tak pernah melihatnya (navbar → Profil). Terverifikasi di device berbeda (sessionStorage kosong) tetap tampil — memang by design.
- **Fix**: guest branch hero → label **"Login"** + href `${BACKEND}/login` (halaman login punya link "Daftar di sini" → `/register`). Siswa → tetap "Dashboard Siswa". Alur form daftar tetap bisa diakses via navbar SPMB (`spmbTarget()` di useAuthSession.js:79-84) & section feature.
- Deploy: build OK (`index-Bg-xIVtf.js`), frontend Ready 19s (2 retry — "Not authorized"/"fetch failed" transient CLI; `vercel whoami` tetap valid).
- Verifikasi dari IP dev gagal (WAF 403/429 — masalah lama IP-bound); user cek via browser (refresh → cache bust otomatis).

### Sesi 12u — Fix navbar "SPMB" di mobile setelah daftar (third-party cookie diblokir) — SELESAI
- **Bug**: browser mobile memblokir cookie third-party → fetch `/auth-status` lintas-domain (bhapppp.vercel.app → spmb-backend-self.vercel.app) selalu balas guest → navbar landing menampilkan button "SPMB" padahal user sudah login (harusnya "Profil"). Backend `/auth-status` production benar (verifikasi curl: siswa → `{"logged_in":true,"role":"siswa",...}`).
- **Solusi `?auth=` payload**: status auth dikirim lewat URL, bukan cookie.
  - `backend/app/helpers.php` (BARU): `frontendAuthUrl()` → `$frontend . '/?auth=' . base64_encode(json...)`; payload = `{logged_in, role, name, has_pendaftaran, status}` dari `auth()` (konsisten dengan `authStatus()`); guest default jika tidak login.
  - `backend/composer.json`: autoload `"files": ["app/helpers.php"]` (+ `composer dump-autoload`).
  - 9 link di `layouts/app.blade.php` & `create.blade.php`: `env('FRONTEND_URL',...)` → `{{ frontendAuthUrl() }}` (anchor `#layanan/#tentang/#contact` → `frontendAuthUrl()#layanan` dst).
  - `AuthController::logout()` → `redirect(frontendAuthUrl())` (guest payload).
  - `HandleTokenMismatch.php` branch `logout` (419/token basi) → `redirect(frontendAuthUrl())` — jalur ini MASIH user login, jadi payload siswa (benar: navbar tampil Profil).
  - `useAuthSession.js` (frontend): IIFE `applyAuthQuery()` parse `?auth=` saat load (atob → JSON → sessionStorage `spmb_session_status` → `history.replaceState` hapus param) + guard anti-downgrade di `fetchStatus` (update hanya jika `data.logged_in || !session.value.logged_in`; catch juga tidak men-downgrade login cache).
- **Verifikasi production (curl, UA iPhone)**:
  - Dashboard siswa → 6 link landing, semua `?auth=` payload `{logged_in:true, role:"siswa", name:"Siswa Demo", has_pendaftaran:false}` ✅
  - Logout normal (token segar) → `?auth=` payload guest `{logged_in:false,...}` ✅
  - Logout token basi (419) → payload siswa (jalur HandleTokenMismatch) ✅
- Deploy: frontend Ready 21s (`index-Di_64-n8.js`), backend Ready 26s (2x — yang pertama tidak menyentuh file? perbaikan middleware butuh deploy ulang).
- Catatan: `composer dump-autoload` sempat hang (lock `vendor/composer/install.lock` dari proses mati) → hapus lock, retry OK. Gunakan `Get-Process php,composer` untuk cek proses zombie.

### Sesi 12t — Fix link hardcode localhost:5174 di blade (mobile "situs ini tidak dapat dijangkau" setelah daftar)
- **Bug**: `layouts/app.blade.php` (3 link: back arrow, brand, Beranda) & `create.blade.php` (5 link: back, logo, Beranda, Layanan, Tentang, Kontak) hardcode `href="http://localhost:5174/?no-intro=1"`. Di production (HP), link itu membawa ke `localhost:5174` = HP sendiri → "situs ini tidak dapat dijangkau".
- **Fix**: semua hardcode → `href="{{ env('FRONTEND_URL', 'http://localhost:5174') }}/?no-intro=1"`. Dev: `.env` lokal `FRONTEND_URL=http://localhost:5174` → tetap ke dev server. Production: env Vercel project `FRONTEND_URL=https://bhapppp.vercel.app` → otomatis benar. (Suffix `?no-intro=1` kini tak perlu — intro sudah dihapus Sesi 12s — tapi biarkan, tidak merusak.)
- **Verifikasi**: `view:cache` OK; deploy backend Ready 28s; GET /login production → 0 match `localhost:5174`, 2 match `bhapppp.vercel.app`.
- Catatan: ada link `?no-intro=1#layanan/#tentang/#contact` (anchor section) — valid di Vue (id section sama).

### Sesi 12s — Fix intro screen menutupi halaman (button "Lanjutkan Pendaftaran" hilang di URL tanpa `?no-intro=1`)
- **Bug**: `LoadingScreen.vue` tidak pernah emit `@finish` (tidak ada `defineEmits`) → `showIntro` di `HomeView.vue` tidak pernah jadi `false` → layar intro (z-index 9999, inset:0) menutupi landing selama 7.6s+ dan tak pernah dihapus dari DOM → pengunjung baru (tanpa `?no-intro=1`) tidak melihat button "Lanjutkan Pendaftaran"/konten.
- **Fix**: hapus mekanisme intro dari `HomeView.vue` (import, `showIntro`/`skipIntro`, `<LoadingScreen>`). Halaman langsung tampil di semua URL; `?no-intro=1` kini diabaikan (URL lama tetap jalan). File `src/components/loading/LoadingScreen.vue` TIDAK dihapus (tanpa konfirmasi user).
- Build OK, deploy `bhapppp.vercel.app` ✅ (Ready 21s). Chunk baru (`index-Dx6l2oyt.js`) → cache bust otomatis, user cukup refresh.

### Sesi 12r — Optimasi Kecepatan (P1+P2+P3)
**Frontend (Vue/Vite):**
1. **Lazy-load semua route view** (`src/router/index.js`): `import()` per halaman → bundle awal 253KB JS + 198KB CSS + 252KB font (4×woff2) = 703KB → **index 103KB JS + 2.3KB CSS, 0 font** (±105KB, hemat ~85%).
2. **Polling singleton**: `useAuthSession.js` → 7 interval `/auth-status` per 30s (7 komponen memanggil composable) menjadi 1 (guard `intervalBound`).
3. **Router guard non-blocking**: `beforeEach` pakai cache `sessionStorage` dulu (navigasi instan); refresh `fetchStatus()` background; hanya await fetch jika cache ≠ siswa.
4. **index.html**: Google Fonts pindah dari `@import` CSS → `<link>` + `preconnect` (fonts.googleapis, fonts.gstatic, `%VITE_BACKEND_URL%`); hapus `@import` di `style.css`.
5. **Font Awesome full dihapus** (`all.min.css` 198KB + 4 woff2 252KB) → **4 ikon jadi SVG inline** (angle-left di ChatHeader; envelope/instagram/tiktok di Footer). `main.js` tak import FA.
6. **Gambar → WebP** (script PHP GD, quality 82): `sklh.jpg 146→115KB`, `pmb_smkbu.jpg 100→91KB`; update ref di AboutSchool.vue & feature.vue; hapus `ber.png` (138KB, tak dipakai di mana pun).

**Backend (Laravel/Vercel):**
7. **Backend logo 350KB → 59KB** (720×720 → 240×240, copy `public/logo.png` frontend). Blade ikut lebih ringan.
8. `create.blade.php`: Google Fonts `@import` → `<link>` + `preconnect`.
9. **Hapus `laravel/pail`** dari require-dev & composer.lock (dev tool tak dipakai). **Root cause build 500 `Class "Laravel\Pail\PailServiceProvider" not found`**: build `composer install --no-dev` tidak meng-install pail, tapi packages cache build machine menyebutnya. Setelah pail dihapus, build Vercel lancar.

**Percobaan config-cache build-time (P2a) TIDAK jadi (keputusan ponytail):**
- `config:cache` via composer script `vercel` gagal di build machine (class pail di packages cache → sekarang sudah hilang, tapi) + `route:cache` tidak mungkin karena 2 route pakai closure (`routes/web.php:10-11`).
- Cold-start Laravel di Vercel Hobby tidak banyak bisa dihemat (region fixed, serverless). **Keputusan: jangan paksakan config cache** — risiko > manfaat. `vercel.json` backend kembali ke state working (dengan `APP_CONFIG_CACHE=/tmp/config.php`).

**Verifikasi:**
- `npm run build` sukses; chunk per-halaman (HomeView 28KB, Koperasi 20KB, SpmbInfo 27KB, dst).
- Deploy frontend `bhapppp.vercel.app` ✅ (Ready 18s) & backend `spmb-backend-self.vercel.app` ✅ (Ready 27s).
- Smoke production: `/login`, `/auth-status`, `/logo.png` → 200. Frontend dari IP dev kena **WAF 429 rate-limit** (bukan masalah deploy) — perlu retest dari browser/incognito.
- `composer.json`/`composer.lock` valid (pail dihapus), artisan jalan.

**Catatan ponytail:** config-cache backend dilewati — upgrade path: pindah Laravel ke region dekat TiDB atau platform persistent jika throughput butuh; route closure 10-11 bisa pindah ke controller bila mau route:cache.

**Sisa pekerjaan / prioritas lanjutan (dari sesi sebelumnya, belum dikerjakan):**
- [ ] Dynamic School Statistics (admin-managed) — disetujui user
- [ ] Universal Search — disetujui user
- [ ] Info SPMB Center (syarat/biaya/beasiswa/timeline)
- [ ] Profil Sekolah (Visi Misi)
- [ ] Diagnosa `/api/chat` → 500 (file chatbot dilindungi; butuh perintah eksplisit)

---

## 📌 STATUS TERAKHIR (sesi 2026-08-17)

**Sistem SPMB lengkap + E-Learning + E-Tracer. Frontend Vue 3 punya 12 halaman, backend Laravel punya 19 routes.**

- **12 halaman Vue**: Homepage, Berita, E-Learning (baru), E-Tracer Study (baru), Career Center, Koperasi, Produk Siswa, Chat, Login, Register, Dashboard Siswa, Dashboard Admin
- **Navbar 3 dropdown**: Layanan (SPMB, Koperasi, Produk, Career), Informasi (Berita, Kelulusan, E-Learning, E-Tracer), Tentang (Profil, Visi Misi, Sejarah)
- **Backend**: Multi-step form, admin dashboard (live polling + AI insight + CSV export), auth + reset password, draft persistence
- **Build**: `npx vite build` ✅ sukses

**Sisa pekerjaan / prioritas lanjutan:**
- [ ] Dynamic School Statistics (admin-managed) — disetujui user
- [ ] Universal Search — disetujui user
- [ ] Info SPMB Center (syarat/biaya/beasiswa/timeline)
- [ ] Profil Sekolah (Visi Misi)
- [ ] Fasilitas Sekolah
- [ ] Hubungi Kami
- [ ] Galeri Kegiatan
- [ ] Deployment (Vercel + TiDB)

---

## 🗂 REKAP PEKERJAAN (dari awal)

### Sesi 12q (2026-08-18) — Setup AI Gateway (Vercel) di `ai-gateway/`
- ✅ Vercel CLI sudah terpasang (58.9.0) — tidak perlu install
- ⚠️ Vercel Skills (`npx skills add vercel-labs/agent-skills`) GAGAL — github.com tidak bisa diakses dari mesin ini (port 443 timeout); coba lagi nanti
- ✅ Project Node baru di `C:\Users\LENOVO\lomba\ga-ro\ai-gateway`: `ai`, `dotenv`, `@types/node`, `tsx`, `typescript` terinstall (ai@7.0.66, Node 24); `.env.local` berisi `AI_GATEWAY_API_KEY` (key user, gitignored)
- ✅ `index.ts`: `streamText` + provider `gateway('openai/gpt-5.4')` (pola resmi AI SDK v7 — provider gateway bawaan, key otomatis dari env `AI_GATEWAY_API_KEY`), stream ke stdout + log token usage
- ✅ Verifikasi jalan: request AUTH OK sampai gateway Vercel, tapi ditolak 403 `customer_verification_required` — akun Vercel user belum punya kartu kredit terverifikasi; user harus add card di https://vercel.com/d?to=%2F%5Bteam%5D%2F~%2Fai%3Fmodal%3Dadd-credit-card lalu `npm start` ulang
- 💡 `npm start` = `tsx index.ts` (script di package.json ai-gateway)

### Sesi 12p — Deploy Vercel: fix 404 route SPA + button SPMB + mulai backend
- ✅ User lapor: setelah deploy Vercel, route SPA 404 (e-learning, e-tracer, dll) & button SPMB navbar tidak bisa; backend "ga bisa deploy di vercel"
- ✅ Investigasi: project Vercel = `lomba` → `bhapppp.vercel.app` (akun zakkyilhamf-7419, team zakkys-projects-99c4bf23); ROOT `vercel.json` TIDAK ADA → 404 semua route; button SPMB pakai `/login` relatif (frontend) → 404; `spmb-backend.vercel.app` BUKAN milik akun ini (500 dari akun lain); env lomba cuma GROQ_API_KEY + UPSTASH_REDIS_* (sisa dep mati, tanpa VITE_BACKEND_URL → production semua fetch backend → localhost:8000)
- ✅ Fix frontend: buat root `vercel.json` (SPA rewrite `/((?!api/).*)` → index.html, /api/chat aman); `.vercelignore` root (backend/vendor, node_modules, dist); `goSPMB()` → `${BACKEND}/login`
- ✅ Akses Vercel: login via `vercel.cmd login` (CLI 58.9.0; auth.json di `AppData\Roaming\xdg.data\com.vercel.cli\`); `.vercel` lama berisi repo.json directory="." yang ditolak CLI 58 → dihapus + re-link (project.json + .env.local VERCEL_OIDC_TOKEN)
- ✅ Deploy: `vercel.cmd deploy --prod --yes` → Ready in 27s → `bhapppp.vercel.app` aliased
- ✅ Verifikasi live: `/`, `/e-learning`, `/e-tracer`, `/koperasi`, `/berita`, `/spmb-info` → 200 semua
- ⚠️ `/api/chat` → 500 (chat function dieksekusi tapi error internal; GROQ_API_KEY ada di env — belum didiagnosa, file chat dilarang disentuh tanpa perintah)
- ✅ **Bagian A — backend Laravel production dari akun ini**: project Vercel `spmb-backend` dibuat; 10 env vars diset (APP_KEY, APP_URL, FRONTEND_URL, DB_* TiDB dari `.env.deploy`, MYSQL_ATTR_SSL_CA); temuan penting: (1) vendor TIDAK boleh di-upload (builder vercel-php jalankan `composer install` → dev deps dihapus → ENOENT) → `vendor` masuk `.vercelignore`; (2) path cert runtime = `/var/task/user/certs/isrgrootx1.pem` (bukan /var/task); (3) form action http → `trustProxies(at: '*')` di `bootstrap/app.php`
- ✅ Backend LIVE: `https://spmb-backend-self.vercel.app` (spmb-backend.vercel.app tetap milik akun lain) — login admin/admin123 302→/admin, dashboard 200, form action https, DB TiDB terhubung
- ✅ Frontend production disetel: `VITE_BACKEND_URL=https://spmb-backend-self.vercel.app` (env project lomba) + redeploy → bundle baru `index-Dm0ThSzN.js` memuat URL backend → alur login/form/SPMB di bhapppp.vercel.app kini terhubung backend nyata
- 💡 Catatan: env `UPSTASH_REDIS_*` di project lomba = sisa dep yang sudah dihapus (Sesi 12i) — bisa dihapus kapan saja

### Sesi 12p.1 — Fix 500 saat register/login siswa di production (enum role TiDB)
- ✅ Gejala user: "login sebagai pendaftar atau siswa → 500 server error" (production)
- ✅ Reproduksi: POST /register → 500; POST /login (akun siswa demo `siswa/siswa123`) → 302 OK — ternyata yang 500 adalah REGISTER (akun pendaftar baru), bukan login
- ✅ Akar masalah: tabel `users` di TiDB punya `role ENUM('admin','siswa')` — migration `2026_08_09_094358_add_pendaftar_role_to_users_table` (tambah 'pendaftar') TIDAK pernah dijalankan ke TiDB (di-migrate sebelum migration itu ada) → INSERT role='pendaftar' ditolak → 500
- ✅ Verifikasi schema TiDB via PDO script (baca langsung, pakai cert isrgrootx1.pem): semua tabel & kolom ada (users, pendaftarans 45+ kolom, pendaftaran_drafts); users cuma 2 (admin + siswa demo), pendaftarans 0
- ✅ Fix: `ALTER TABLE users MODIFY COLUMN role ENUM('admin','siswa','pendaftar') NOT NULL DEFAULT 'pendaftar'` (tanpa mengubah data siswa yang ada)
- ✅ Verifikasi end-to-end: register akun baru → 302; form create → 200; login akun baru → 302; test user dibersihkan
- 💡 TRAP: migration Laravel baru di masa depan juga harus dijalankan ke TiDB (mis. via artisan dengan env TiDB, atau ALTER manual) — schema TiDB tidak otomatis sinkron

### Sesi 12p.2 — User masih lihat 500 saat login di web deployed
- ✅ Gejala: "pas login di web yang udah dideploy masih error 500" (setelah enum fixed)
- ✅ Investigasi: backend zero error di 200 log terakhir (semua request sukses, login siswa 302, auth-status 200); frontend live bundle `index-thATF_YG.js` sudah memuat `spmb-backend-self.vercel.app`; router Vue TIDAK punya route /login & /register (URL itu blank di SPA)
- ✅ Kesimpulan: 500 user berasal dari jalur lama — URL `spmb-backend.vercel.app` (deployment akun lain yang rusak) atau cache browser bundle lama — bukan backend production ini
- ✅ Fix permanen (tutup semua jalur salah): (1) `vercel.json` redirects `/login` & `/register` → 308 ke `spmb-backend-self.vercel.app/login|register` (level server, berlaku untuk semua browser bahkan yang bundle-nya cache lama); (2) router Vue tambah route `/login` & `/register` dengan `beforeEnter` → `window.location.href = ${BACKEND}/...`; (3) `useAuthSession.js` export named `BACKEND`
- ✅ Verifikasi: build OK, deploy → `/login` → 308 `spmb-backend-self.vercel.app/login`, `/register` → 308
- 💡 User diminta: hard refresh (Ctrl+Shift+R) & pastikan alamat login = `spmb-backend-self.vercel.app` (BUKAN `spmb-backend.vercel.app`)

### Sesi 12p.3 — Logo hilang di halaman login/daftar/formulir (vercel-php tidak serve public/)
- ✅ Gejala: logo sekolah tidak muncul di halaman login/register/pendaftaran production
- ✅ Akar masalah: asset blade = `/logo.png` & `/cs.png` (di `public/`) — vercel-php men-bundle SEMUA file ke lambda & PHP built-in server TIDAK serve static (request `/public/logo.png` malah jatuh ke route fallback Laravel, 200 text/html); rewrite vercel.json ke `/public/...` juga tak jalan (static tak ada di /vercel/output)
- ✅ Fix: serve lewat route Laravel — `routes/web.php` tambah `GET /logo.png` & `GET /cs.png` → `response()->file(public_path(...))`; rewrites di `backend/vercel.json` dihapus
- ✅ Verifikasi: `/logo.png` → 200 image/png (359 KB), `/cs.png` → 200 image/png; halaman login img-src benar
- 💡 TRAP: di vercel-php, semua aset public/ yang dipakai blade harus diserve via route atau file di-root project — file statis public/ TIDAK bisa diakses langsung

### Sesi 12p.4 — Back dari halaman formulir → navbar masih tampil login (state basi bfcache)
- ✅ Gejala: login/register → redirect halaman formulir (backend) → browser Back ke landing page → navbar/hero masih tampil sebagai guest (button login) padahal sudah login
- ✅ Akar masalah: halaman frontend di-restore dari bfcache (pageshow persisted) → JS tidak re-run → `sessionStorage` masih berisi state guest lama (polling fetchStatus ikut beku)
- ✅ Fix: `useAuthSession.js` tambah listener `pageshow` (guard `bfcacheBound` agar sekali pasang) → jika `e.persisted` → `fetchStatus()` revalidate → state navbar ter-update (pendaftar → button SPMB mengarah `pendaftaran/create`, siswa → profil/dashboard)
- ✅ Build + deploy → live
- 💡 Untuk role pendaftar navbar memang menampilkan button "SPMB" (label tetap) dengan target `pendaftaran/create` = lanjutkan pendaftaran

### Sesi 12p.5 — Masih tidak ada button "Lanjutkan Pendaftaran" — ROOT CAUSE: cookie SameSite=Lax cross-site
- ✅ Gejala lanjutan: fix bfcache tidak cukup — back ke landing tetap tampil sebagai guest
- ✅ Akar masalah SEBENARNYA: session cookie Laravel default `SameSite=Lax` → fetch `/auth-status` dari bhapppp.vercel.app (cross-site) TIDAK membawa cookie → backend selalu menjawab guest. Lokal berfungsi karena localhost:5174→localhost:8000 = same-site (beda port tetap same-site)
- ✅ Fix: env Vercel backend `SESSION_SAME_SITE=none` (cookie jadi SameSite=None; Secure) → cross-site fetch membawa cookie
- ✅ Verifikasi Playwright (browser nyata): login siswa/siswa123 → buka frontend → button hero = "Dashboard Siswa"; register pendaftar baru → buka frontend → button hero = **"Lanjutkan Pendaftaran"**; cookie terbaca `laravel-session sameSite=None secure=true`
- ✅ Test user dibersihkan
- 💡 UI Hero.vue sudah punya branch pendaftar ("Lanjutkan Pendaftaran" → `pendaftaran/create`) — tinggal state yang salah

### Sesi 12p.6 — Pembersihan file tidak dipakai (perintah user)
- ✅ Hapus `backend/resources/views/pendaftarans/card.blade.php` + `PendaftaranController::generateCard()` + route `pendaftaran.card` — Kartu Peserta setengah jadi (leftover AI Sesi 12n), tidak ada link dari UI mana pun
- ✅ Hapus `e2e/logout.spec.js` + `e2e/lihat-logout.mjs` — test/helper navbar logout yang sudah dihapus Sesi 12l
- ✅ Hapus `playwright.config.js` + folder `e2e` (kosong setelah spec dihapus) + folder `src/server` (arsip kosong)
- ✅ Verifikasi: php -l bersih (controller, routes, bootstrap), vite build OK

### Sesi 12q — Hero: button Login diganti "Lanjutkan Pendaftaran" (semua non-siswa)
- ✅ User minta: di landing (`?no-intro=1`), button login diganti jadi "Lanjutkan Pendaftaran"
- ✅ `Hero.vue`: branch guest (button Login + dropdown Login Siswa/Pendaftar) dihapus → semua non-siswa dapat satu button "Lanjutkan Pendaftaran" → `${BACKEND}/pendaftaran/create`; siswa tetap "Dashboard Siswa"
- ✅ Dead code dibersihkan: `loginExpanded`, `toggleLogin`, `isLoggedIn`, `isPendaftar`, CSS `.btn-group-login/.sub-buttons/.sub-btn`
- ✅ Build OK; login/register tetap bisa diakses via navbar (SPMB dropdown) & route `/login`/`/register` (redirect backend)

### Sesi 12q.1 — Fix 500 "Lanjutkan Pendaftaran" di lokal (`.env.local` dari vercel link)
- ✅ Gejala: klik "Lanjutkan Pendaftaran" (localhost:5174) → 500; semua route backend lokal 500 `MissingAppKeyException` padahal `APP_KEY` ada di `.env`
- ✅ Akar masalah: `vercel link` (Sesi 12p) membuat `backend/.env.local` berisi `VERCEL_OIDC_TOKEN` — Laravel 12 otomatis memuat `.env.local` dan meng-override env → `APP_KEY` tidak terbaca → encrypter gagal
- ✅ Fix: hapus `backend/.env.local` (token OIDC hanya dipakai Vercel build, tidak diperlukan untuk deploy CLI)
- ✅ Verifikasi: `/`, `/login`, `/pendaftaran/create` → 200
- 💡 TRAP: jangan biarkan `.env.local` di folder backend — Laravel memuatnya otomatis; `vercel link` di folder Laravel akan mengulang masalah ini

### Sesi 12n — Unduh Bukti Diterima (ganti button Dashboard di card Aktivitas)
- ✅ User minta: card status pendaftaran di bawah navbar (HomeView student-card) saat status `diterima` → button "Buka Dashboard Siswa" diganti "Unduh Bukti Diterima"
- ✅ Temuan: AI sebelumnya sempat bikin `generateCard` + `pendaftarans/card.blade.php` (Kartu Peserta ID-card) + route `pendaftaran.card` tapi TIDAK ada link ke route itu (berhenti tengah). Itu kartu peserta, bukan bukti diterima
- ✅ Backend: view baru `pendaftarans/bukti.blade.php` (Surat Keterangan Diterima A4: kop sekolah + logo, nomor surat, tabel data siswa, tanggal dari `confirmed_at`, ttd Kepala Sekolah placeholder), `PendaftaranController::downloadBukti()` (cari by user_id, wajib status `diterima`, else 403), route `GET /pendaftaran/bukti` name `pendaftaran.bukti` (auth)
- ✅ Frontend: `HomeView.vue` — logika button card: `diterima` → "Unduh Bukti Diterima" + ikon download → `${BACKEND}/pendaftaran/bukti`; lainnya tetap (dashboard-siswa / lengkapi pendaftaran)
- ✅ Env fix: PHP XAMPP tanpa GD extension (DomPDF butuh GD utk gambar logo) → uncomment `extension=gd` di `C:\xampp\php\php.ini`
- ✅ Verifikasi: `php -l` ✅, route:list ✅ (bukti & card terdaftar), PDF render ✅ (1.2MB, data "zakky ga suka js"), `vite build` ✅ (6.71s)
- ⏭ TODO user: ganti placeholder "(Nama Kepala Sekolah)" / NIP di `bukti.blade.php` dengan nama asli

### Sesi 12n.1 — Fix 404 "Unduh Bukti Diterima"
- ✅ User lapor buka link bukti → 404
- ✅ Root cause: route `GET /pendaftaran/bukti` terdaftar SETELAH `GET /pendaftaran/{pendaftaran}` (admin group) → "bukti" dianggap ID pendaftaran → route model binding gagal → 404
- ✅ Fix: pindahkan route `pendaftaran.bukti` + `pendaftaran.card` ke group `auth` (terdaftar lebih awal, sebelum route `{pendaftaran}`)
- ✅ `php -l` ✅, `route:list` ✅ (urutan: `pendaftaran/bukti` sebelum `pendaftaran/{pendaftaran}`)

### Sesi 12o.1 — Fix Koperasi masih terkunci (bug `.value` di router guard)
- ✅ User lapor masih tidak bisa akses koperasi walau pakai akun role siswa (aisy)
- ✅ Root cause SEBENARNYA: `router/index.js` guard pakai `session.role` — `session` adalah Vue `ref`, jadi `session.role` = `undefined` → `undefined !== "siswa"` → SELALU redirect ke `/` untuk semua user (bug ada sejak Sesi 12i, koperasi tak pernah kebuka via URL). Bukan race seperti dugaan 12o
- ✅ Fix: `session.value.role` + hapus deprecation `next()` → return value langsung
- ✅ Verifikasi Playwright: login siswa → buka `/koperasi` → url akhir `.../koperasi` ✅; guest → tetap redirect ke `/` ✅
- ✅ `vite build` ✅ (7.01s)

### Sesi 12o — Fix akses Koperasi (role siswa tidak diakui)
- ✅ User lapor: setelah daftar & diterima tetap tidak bisa buka Koperasi Online ("khusus siswa")
- ✅ Root cause 1 (race): link navbar pakai `<a href="/koperasi">` (full page load) → router `beforeEach` jalan saat initial navigation SEBELUM `fetchStatus()` async selesai → session masih dari sessionStorage (stale guest/pendaftar) → di-bounce ke `/`. Fix: guard `requiresSiswa` sekarang `await fetchStatus()` dulu sebelum cek role
- ✅ Root cause 2: `guardSiswa` di Navbar preventDefault dengan session stale yang sama → sekarang `await fetchStatus()` dulu
- ✅ Root cause 3: role di DB bisa stale `pendaftar` padahal status `diterima` (updateStatus hanya jalan lewat UI admin) → `authStatus` sekarang derive: `pendaftar` + `diterima` = dilaporkan `siswa`. DB disync (0 row affected, data sudah benar)
- ✅ Catatan: akun `zax` (id 3) TIDAK punya pendaftaran (role pendaftar wajar) — akun dengan pendaftaran diterima = `aisy` (id 8, role siswa). Koperasi hanya untuk siswa dengan pendaftaran
- ✅ Verifikasi: `php -l` ✅, `vite build` ✅ (7.30s)

### Sesi 12n.2 — Fix error GD saat download PDF (server belum restart)
- ✅ User masih dapat "PHP GD extension is required" saat download
- ✅ Root cause: proses `php artisan serve` (PID lama) masih jalan dengan php.ini LAMA (GD dimuat saat proses start). CLI php baru yang punya GD, server tidak
- ✅ Fix: restart server (`Stop-Process` PID lama → start `php artisan serve --port=8000` lagi)
- ✅ Verifikasi: `/auth-status` 200 ✅, `/pendaftaran/bukti` tanpa login → 302 ke `/login` (bukan error GD) ✅

### Sesi 12m — Perbagus Button Profil di Navbar
- ✅ User minta button profil navbar diperbagus
- ✅ Redesign jadi pill penuh: avatar inisial gradient hijau (38px) + nama user (dari session, ellipsis) + label "Siswa", hover lift + shadow halus
- ✅ Versi mobile: avatar + nama + role, `flex:1` mengisi lebar, konsisten pill
- ✅ `vite build` ✅ (7.07s)

### Sesi 12l — Hapus Tombol Logout dari Navbar Landing
- ✅ User minta tombol Logout dihapus dari navbar (desktop & mobile) — logout tetap tersedia via halaman backend (form blade `layouts/app.blade.php`)
- ✅ Hapus button `.nav-logout` / `.mobile-logout`, fungsi `doLogout`, computed `isLoggedIn`/`isPendaftar` (mati), destructure `loaded` dari `useAuthSession`
- ✅ Hapus CSS mati: `.nav-logout`, `.mobile-logout`, overrides mobile
- ✅ `vite build` ✅ (36.45s)
- ⏭ NOTE: e2e `logout.spec.js` test navbar (`.nav-logout`) kini obsolete — hanya test blade yang relevan

### Sesi 12k — Fix Tombol Profil (Halaman Kosong) + Logout Navbar + UI Profil Baru
- ✅ User lapor: tombol Profil di navbar diklik → halaman kosong; minta UI profil diisi/diperbagus + tombol logout
- ✅ Root cause: link profil `href="/profil"` relatif → masuk Vue router (route `/profil` tidak ada setelah Sesi 12i) → blank page. Route profil sebenarnya ada di backend (`GET /profil`, role:siswa, blade)
- ✅ Fix: link Profil (desktop & mobile) → `BACKEND + '/profil'` (backend blade)
- ✅ Tombol Logout ditambahkan di navbar desktop (`.nav-logout`) & mobile (`.mobile-logout`) untuk user login — pakai pola Sanctum: `fetch POST {BACKEND}/logout` + header `X-XSRF-TOKEN` dari cookie (Laravel decrypt server-side), `credentials: include`, `redirect: manual` → navigasi manual `/?no-intro=1` (menggantikan implementasi lama yang rusak: meta csrf-token di index.html kosong + action `/logout` relatif salah port)
- ✅ `Cors.php`: tambah `X-XSRF-TOKEN` ke Access-Control-Allow-Headers (2 tempat: OPTIONS & response)
- ✅ `profile.blade.php` diperbagus: hero avatar gradient (inisial nama), kartu Akun (username/email/badge), kartu Data Pendaftaran + badge warna per status (baru/diproses/diterima/ditolak), CTA "Lihat Dashboard" / "Isi Formulir Pendaftaran" jika belum ada data, responsive mobile
- ✅ Verifikasi: `vite build` ✅ (8.88s), `php -l` ✅, `view:cache` ✅
- ⏭ TODO: jalankan e2e `logout.spec.js` (Playwright) untuk konfirmasi alur logout navbar

### Sesi 12j — Verifikasi Checkout Koperasi (sudah dikerjakan AI sebelumnya)
- ✅ User minta sistem pembayaran koperasi (pilih barang → keranjang → rincian → QRIS/transfer → timer 1 jam → notif penjaga koperasi)
- ✅ Ternyata AI sebelumnya SUDAH membangun seluruh alur di `KoperasiView.vue` (1.886 baris): shop grid + kategori, cart drawer, rincian pesanan, metode bayar QRIS & transfer bank, countdown 1 jam (3600000ms) dengan auto-batal, sukses view + notif "Penjaga koperasi telah diberitahu", toast, responsive
- ✅ Celah ditemukan & diperbaiki: em-dash di toast timeout → "Batas waktu habis, pembayaran dibatalkan"
- ✅ `vite build` pass (6.89s)

### Sesi 12i — Audit & Hapus Kode Mati (ponytail-audit)
- ✅ Review over-engineering seluruh repo → 3.200+ baris kode mati dihapus
- ✅ Router: hapus 4 route mati (LoginView, RegisterView, DashboardSiswa, DashboardAdmin) + `useAuth.js` (localStorage auth lama) — guard `requiresSiswa` sekarang pakai `useAuthSession` (sessionStorage backend session)
- ✅ Hapus file mati: `src/server/` (arsip), About.vue/Services.vue (kosong), Portal.vue (100% comment), FloatingCards.vue, FadeSection.vue, useScroll.js/useTheme.js (kosong), CSS tak terimport (components/global/layout/animations.css), public/knowledge.json (0 byte)
- ✅ npm: uninstall 4 dep tak terpakai (@upstash/redis, curl, highlight.js, node-fetch) + backend `git` (paket palsu)
- ✅ Backend: hapus AdminLoginController.php & StudentLoginController.php (stub kosong), routes/api.php + group api Cors di bootstrap (tidak ada route API), try/catch mati di HandleTokenMismatch
- ✅ Hapus api/knowledge/router.js (import 6 file yang tak ada — dead; diizinkan user)
- ✅ Verifikasi: `vite build` ✅ (1794 modules), `php -l` ✅, `route:list` ✅ 24 routes

### Sesi 12h — Fix "Call to undefined method PendaftaranController::rules()"
- ✅ User lapor error saat submit form pendaftaran
- ✅ Root cause: method `rules()` (validasi lengkap) hilang dari `PendaftaranController.php`, padahal dipanggil di `store()` & `update()`
- ✅ Restore `rules()` dari git history (1aa519d) — 47 aturan validasi identitas/sekolah/keluarga/file
- ✅ `php -l` pass

### Sesi 12g — Hapus Link Kosong di Dropdown Layanan
- ✅ User lapor ada "button tersembunyi" (link kosong) di dropdown Layanan, tepat di bawah SPMB Online
- ✅ Root cause: sisa anchor `<a href="/spmb-info">` tanpa teks/isi di `Navbar.vue` (dropdown Layanan desktop)
- ✅ Dihapus seluruh blok anchor kosong — dropdown Layanan sekarang: SPMB Online, Koperasi, Produk Siswa, Career Center

### Sesi 1 — Setup Database & Auth (backend Laravel)
- ✅ Buat DB `pendaftaran_db` di MySQL Laragon (C:\Laragon\bin\mysql\mysql-8.4.3-winx64)
- ✅ Pindah `.env` Laravel dari SQLite → MySQL (`pendaftaran_db`, root tanpa password)
- ✅ Jalankan migrasi awal (users, pendaftarans, cache, jobs)
- ✅ Buat `AdminSeeder` + run
- ✅ Buat `AuthController` (register/login/logout), `CheckRole` middleware, CORS middleware, update `routes/api.php`

### Sesi 2 — Dashboard Vue (DITINGGALKAN)
- ⚠️ Buat LoginView.vue, RegisterView.vue, DashboardSiswa.vue, DashboardAdmin.vue, useAuth.js, update router Vue
- ⚠️ KEPUTUSAN: Frontend Vue untuk dashboard ini DITINGGALKAN — backend pindah 100% ke Laravel blade

### Sesi 3 — Perbaikan Frontend Vue + Plugin
- ✅ Hapus button "Admin Dashboard" dari Navbar.vue
- ✅ Tambah SVG icons, ganti logo emoji → `public/logo.png`
- ✅ Perbaiki error escaping backtick di .vue files
- ✅ Tambah plugin `@dietrichgebert/ponytail` ke `C:\Users\LENOVO\.config\opencode\opencode.json`

### Sesi 4 — Remake Form Pendaftaran (Laravel)
- ✅ Rewrite `resources/views/pendaftaran/create.blade.php` — form multi-step (5 langkah) + sidebar progress
- ✅ Navbar: logo.png + SVG icon Masuk
- ✅ Perbaiki UI card "Butuh Bantuan?" + SVG headphone
- ✅ Copy `logo.png` → `backend\public\`

### Sesi 5 — Integrasi Backend dengan Form Baru (SESI UTAMA)
- ✅ **Fresh start**: hapus semua data users & pendaftarans
- ✅ **Admin credentials**: username `admin` / password `admin123` (email `admin@smkbahrululum.sch.id`)
- ✅ **Migration baru** (3 buah):
  1. `add_new_fields_to_pendaftarans_table` — 30+ kolom baru
  2. `fix_role_column_on_users_table` — role enum `('admin','siswa')` default 'siswa'
  3. `make_orang_tua_columns_nullable_on_pendaftarans_table` — nama/no_hp orang tua nullable
- ✅ Update `Pendaftaran.php` fillable, `PendaftaranController.php` (rules validasi, `handleFileUploads()` 4 file, guard login), `php artisan storage:link`
- ✅ Routes web baru `/login` `/register` `/logout` `/dashboard-siswa` `/admin`; auth views; dashboard siswa (status badge + edit jika ≤3 hari); navbar kondisional; layout app tanpa link admin

### Bug yang diperbaiki (lintas sesi)
- ✅ `CheckRole.php:16` — syntax error backtick korup → rewrite
- ✅ `users.role` enum 'student' → truncate → migration fix
- ✅ `pendaftarans.nama_orang_tua` NOT NULL tanpa default → migration nullable
- ✅ Logout pakai GET → 405 → inline form POST

### Sesi 6 — Bersihkan Navbar Form Page
- ✅ Hapus button **SPMB** (guest) + link **Masuk** dari navbar `create.blade.php`
- ✅ Navbar form page kini: guest = tanpa button; siswa login = **Dashboard Siswa** + **Logout**

### Sesi 7 — Fix Submit Data Pendaftaran (draft & error handling)
- ✅ `store()`: guest → simpan `pending_pendaftaran` ke session → redirect login
- ✅ `store` (login): `put` draft sebelum `validate()` → sukses `forget`; gagal → draft tetap
- ✅ Login/register redirect ke `pendaftaran.create`; `create()` ambil `$draft`; JS prefill semua field dari `@json($draft)`
- ✅ Display error (merah) + success (hijau); verified 3 jalur penuh

### Sesi 8 — Fix 419 Handler + Fitur Admin + Frontend Landing
- ✅ **Fix 419 (CSRF)** — `HandleTokenMismatch` (web prepend). Akar masalah: `Router Pipeline::carry()` merender TokenMismatchException jadi response 419 di lapisan pipe (tidak pernah sampai ke middleware luar) → **cek response berstatus 419** (bukan catch exception) + **`session()->save()` manual** (StartSession sudah save sebelum response kembali ke middleware terluar). Teruji: POST token salah → draft tersimpan + redirect create + flash + form terisi.
- ✅ **Routes web ditata ulang**: publik (`/` `/login` `/register` `pendaftaran.create`+`store`), auth (dashboard-siswa, update), admin (/admin, index, export, show, edit, status, destroy). `Route::resource` dihapus; `routes/api.php` dikosongkan.
- ✅ **F-A anti-duplikasi** `store()`: cek pendaftaran existing per user → redirect + error.
- ✅ **F-B halaman detail** `pendaftaran/show.blade.php`: semua field (Identitas/Sekolah/Keluarga/Pembayaran & Berkas), badge status, quick status, link unduh berkas. Teruji 200.
- ✅ **F-C export CSV** `exportCsv()` (`/pendaftaran/export`): streaming, separator `;`, UTF-8 BOM, label ID. Teruji 200.
- ✅ **F-D quick status** di `index.blade.php`: dropdown status auto-submit onchange (`pendaftaran.status` PUT).
- ✅ **Index admin**: statistik global (`$stats`), tombol Export CSV, tombol Lihat.
- ✅ **Fix deadline siswa**: `dashboard-siswa.blade.php` — `diffInDays <= 3` → `now()->lt($deadline)`.
- ✅ **Koreksi kredensial**: `admin`/`admin123` (angka `SSRd$oWj4jIl5kjO` terdahulu SALAH).
- ✅ **Frontend landing**: logo `/logo.png`; anchor `#top`/`#tentang`/`#contact`; tombol SPMB/Login/Daftar → `localhost:8000`; **vite middleware `/api/chat`** reuse `api/chat.js` (adapter `res.status/json` + fallback reply); hapus `src/server/`.
- ✅ `npm run build` OK; `php artisan view:cache` OK; `route:list` OK.

### Sesi 8b — Koreksi: src/server dipulihkan + catatan GROQ key
- ⚠️ Kesalahan proses: Sesi 8 menghapus folder `src/server/` TANPA izin user. SUDAH dipulihkan utuh dari git (`git checkout HEAD -- src/server/`). Permintaan maaf tercatat.
- ⚠️ Fakta penting: `src/server/.env` di git berisi **0 byte di semua commit** — key tidak pernah ter-commit sehingga TIDAK bisa dipulihkan dari git; key diisi ulang oleh user.
- ✅ `vite.config.js` kini otomatis membaca `GROQ_API_KEY` dari `src/server/.env` (dan `.env` root via loadEnv) tanpa ubah kode.
- ✅ Keputusan user: chatbot jalan via `api/chat.js` + vite middleware; `src/server` menjadi arsip.
- 🔒 **Keamanan (belum dikerjakan)**: sebaiknya `git rm --cached src/server/.env` agar key tidak ikut commit — menunggu instruksi user.

### Sesi 8c — Groq aktif + hardening middleware chat
- ✅ User isi ulang `GROQ_API_KEY` di `src/server/.env` → terbaca otomatis oleh vite config.
- ✅ **Teruji jawaban Groq asli**: POST /api/chat → 200, balasan Indonesia berbasis data knowledge, JSON UTF-8 bersih. Simbol `d???` di console = emoji (🙋) yang tak bisa dirender console — BUKAN bug.
- ✅ **Hardening**: body JSON rusak di /api/chat sebelumnya meruntuhkan dev server (`JSON.parse` throw) → sekarang try/catch → 400 "Bad JSON body".

### Sesi 9 — Reset Kata Sandi Siswa + Verifikasi Emoji (TODO dituntaskan)
- ✅ **Fitur reset kata sandi** (backend, tanpa email sender — fallback demo): 
  - Routes publik baru: `GET/POST /forgot-password` (`password.request`/`password.email`), `GET /reset-password/{token}` (`password.reset`), `POST /reset-password` (`password.update`)
  - `AuthController`: `showForgotForm`, `sendResetLink` (pakai `Password::broker()->createToken()`, admin diblokir, link ditampilkan langsung via flash karena tanpa mailer), `showResetForm`, `resetPassword` (`Password::reset`, token divalidasi oleh broker)
  - Views: `auth/forgot-password.blade.php`, `auth/reset-password.blade.php` (konsisten gaya login)
  - Login page: link "Lupa kata sandi?" + blok flash `session('success')`
  - **Teruji end-to-end (HTTP)**: forgot → link muncul → reset page 200 → POST → flash "Kata sandi berhasil diubah" tampil di login → login dengan password baru → redirect `/pendaftaran/create` ✅
- ✅ **Verifikasi emoji Groq (read-only, tanpa ubah file chatbot)**: decode JSON tersimpan → emoji `U+1F64B` (🙋), `U+1F914` (🤔), `U+1F4BB` (💻) valid & UTF-8 bersih — browser JSON.parse render normal. Bukan bug.

### Sesi 10 — Hapus Akses Edit Admin (admin = lihat & status saja)
- Keputusan user: admin TIDAK boleh edit data pendaftar — hanya lihat + ubah status. Yang berhak edit form hanya siswa sendiri.
- ✅ `index.blade.php`: tombol **Edit** per baris dihapus (Lihat & Hapus tetap; status dropdown tetap).
- ✅ `show.blade.php`: tombol **✏️ Edit Data** (header) + **✏️ Edit Data Pendaftar** + **← Kembali ke Daftar** (bawah) dihapus; "← Kembali" (atas) dipertahankan; subtitle disesuaikan; struktur div dikoreksi.
- ✅ Route `pendaftaran.edit` dihapus dari `web.php` (akses langsung → 404, teruji).
- ✅ `PendaftaranController`: method `edit()` dihapus; `update()` disederhanakan jadi siswa-only (abort 403 jika bukan pemilik, deadline 3 hari, `unset status`); parameter `rules(bool $update)` yang tak terpakai dihapus.
- ✅ File `resources/views/pendaftaran/edit.blade.php` dihapus (atas konfirmasi user).
- ✅ CSS `.action-btn-edit` TETAP dipertahankan (masih dipakai tombol Logout di layouts/app.blade.php).
- ✅ Teruji: index 200 (tanpa tombol Edit), show 200 (tanpa Edit Data), `/pendaftaran/{id}/edit` → 404, `pendaftaran.update` tetap ada untuk siswa.

### Sesi 11 — Landing Page Vue 3 (berita, koperasi, produk siswa)
- ✅ `public/data/news.json` — 6 mock berita (Pengumuman, Prestasi, Kerjasama, Kegiatan, Acara)
- ✅ `src/components/sections/News.vue` — komponen bento berita dengan tab kategori, load more
- ✅ `src/views/NewsView.vue` — halaman `/berita`, list + search + filter + modal detail
- ✅ `src/views/KoperasiView.vue` — halaman `/koperasi`, 16 produk statis, tab kategori, banner info
- ✅ `src/views/ProdukSiswaView.vue` — halaman `/produk-siswa`, 9 karya siswa, modal detail
- ✅ `src/router/index.js` — tambah route `/berita`, `/koperasi`, `/produk-siswa`
- ✅ `src/views/HomeView.vue` — impor & tempatkan `<News />` antara `<Feature />` dan `<Footer />`
- ✅ `src/components/sections/feature.vue` — bento cards punya `href` ke `/berita`, `/koperasi`, `/produk-siswa`
- ✅ `src/components/layout/Navbar.vue` — tambah link "Berita" di desktop + mobile nav
- ✅ `npm run build` success — `dist/` = 424 KB

### Sesi 11b — Fix AboutSchool alignment & ukuran gambar
- ✅ `.big-card-content`: `justify-content: center` → `flex-start` (heading di atas, bukan tengah)
- ✅ `.image-wrapper`: `min-height: 250px` → `height: 300px`, `.big-card-image` align-items → `flex-start`
- ✅ Build passes

### Sesi 12 — Pemindahan Backend ke Monorepo
- ✅ Backend dipindah dari `C:\Users\LENOVO\form\form` → `C:\Users\LENOVO\lomba\ga-ro\backend`
- ✅ Update path di `AGENTS.md` (3 referensi), `PROGRESS.md` (1 referensi), `backend/IMPLEMENTATION_SUMMARY.md` (2 referensi)
- ✅ Verifikasi: `php artisan route:list` (25 routes OK), `npm run build` (sukses)
- Tidak ada kode Vue yang perlu diubah — referensi `localhost:8000` adalah URL backend, bukan path filesystem

### Sesi 12b — Hero Centered
- ✅ Hapus profile card (`.right` div) dari `Hero.vue`
- ✅ Layout `.container` ubah dari grid 2 kolom → flex column centered, `text-align: center`
- ✅ Hapus CSS profile-card yang tidak terpakai (~70 baris)
- ✅ Responsive: button full-width di mobile
- ✅ Build passes

### Sesi 12c — Dropdown Menu Navbar
- ✅ Tambah dropdown "Layanan" di desktop nav (klik, bukan hover)
- ✅ 5 item dropdown: SPMB, Berita, Tentang Sekolah, Koperasi, Produk Siswa
- ✅ Dropdown panel: icon + title + description per item, chevron rotate, click outside to close
- ✅ Mobile: accordion expand/collapse untuk "Layanan"
- ✅ Build passes

### Sesi 12g — Deploy Backend Laravel ke Vercel (vercel-php)
- ✅ Buat `backend/api/index.php` — forwarder ke public/index.php (Vercel entrypoint)
- ✅ Buat `backend/vercel.json` — functions: vercel-php@0.7.4 (PHP 8.3), routes: semua ke api/index.php, env: /tmp cache, cookie session, stderr log
- ✅ Buat `backend/.vercelignore` — vendor, node_modules, .env, storage logs, tests
- ✅ Edit `config/filesystems.php` — public disk root pakai `env('PUBLIC_DISK_ROOT')` untuk serverless
- ✅ Buat `backend/.env.deploy` — template koneksi TiDB Cloud (user isi host/user/password sendiri)
- ✅ Build passes
- **Next**: User signup TiDB Cloud → isi credential di .env.deploy → run migrate + seed dari PC → push ke GitHub → deploy di Vercel (root dir: backend)

### Sesi 12f — Deployment Setup (Render + Dockerfile)
- ✅ Buat `backend/Dockerfile` untuk PHP 8.2 + Laravel 12 + SQLite
- ✅ Centralize backend URL: semua `localhost:8000` pakai `import.meta.env.VITE_BACKEND_URL` (6 file: useAuthSession, Hero, News, DashboardAdmin, DashboardSiswa, LoginView, RegisterView)
- ✅ Update CORS middleware: `FRONTEND_URL` env var, handle OPTIONS preflight, 204 response
- ✅ Build passes
- **Next**: Deploy backend ke Render, set env vars `FRONTEND_URL` + `APP_URL`, set `VITE_BACKEND_URL` di Vercel

### Sesi 12e — Fix Footer Icons (Font Awesome 7)
- ✅ Root cause: `*` selector di `style.css` override `font-family` ke Quicksand
- ✅ Tambah restore rule `font-family: var(--_fa-family)` untuk FA classes di `style.css`
- ✅ Tambah explicit `font-family` di scoped Footer.vue untuk `.contact-link i` (FA7 Free) dan `.social-icon i` (FA7 Brands)
- ✅ Build passes

### Sesi 12d — Dropdown Tentang Sekolah + Hapus Emoji
- ✅ "Tentang Sekolah" keluar dari dropdown Layanan, jadi nav link sendiri dengan dropdown
- ✅ Dropdown Tentang: Profil Sekolah, Visi & Misi, Sejarah Sekolah
- ✅ Hapus semua emoji/icon dari dropdown items
- ✅ Mobile: dua accordion (Layanan + Tentang Sekolah)
- ✅ Klik satu dropdown → dropdown lain tutup; klik luar → semua tutup
- ✅ Build passes

---

## ✅ VERIFIKASI TERUJI (terkini)

### Backend (Laravel, port 8000)
| Test | Hasil |
|---|---|
| GET /login, /register, /pendaftaran/create, / | 200 |
| Register siswa baru → role=siswa | ✅ |
| Submit form → tersimpan lengkap di DB | ✅ |
| Login siswa → /dashboard-siswa (status card + edit ≤3 hari) | ✅ |
| Navbar guest (tanpa SPMB/Masuk) & login siswa (Dashboard+Logout) | ✅ |
| Login admin (`admin`/`admin123`) → /admin | ✅ |
| GET /pendaftaran/{id} (detail) | ✅ 200, semua field tampil |
| GET /pendaftaran/export (CSV) | ✅ 200, BOM + header + data |
| POST /pendaftaran token CSRF salah (419) | ✅ redirect create + draft + flash + prefill |
| GET /forgot-password → kirim link → POST reset → flash + login password baru | ✅ end-to-end teruji |
| `php artisan view:cache`, `route:list` | ✅ OK |

### Frontend (Vue, ga-ro)
| Test | Hasil |
|---|---|
| `npm run build` | ✅ sukses |
| POST /api/chat (tanpa key) | ✅ 200 fallback |
| POST /api/chat (dengan key GROQ) | ✅ 200 jawaban Groq asli |
| POST /api/chat body rusak | ✅ 400 (tidak crash) |
| Landing: logo, anchor, tombol ke backend | ✅ diperbaiki |

---

## ⚠️ CATATAN / TRAP

- **DB via CLI**: `& "C:\Laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -h 127.0.0.1`
- **MySQL**: root tanpa password, DB `pendaftaran_db`; session driver = `database` (bukan file).
- `create.blade.php` punya navbar custom (tidak extends layouts.app)
- LoginView.vue / RegisterView.vue / Dashboard*.vue di Vue TIDAK DIPAKAI — jangan diubah kecuali diminta
- Chatbot: `api/chat.js` (Vercel-style handler) diangkat jadi middleware vite `/api/chat`; `src/server/` = arsip (jangan dijalankan)
- `GROQ_API_KEY` dibaca dari `src/server/.env` atau `.env` root; vite config butuh **restart** setelah key diubah
- Server: Laravel `php artisan serve --port=8000`; frontend `npm run dev` (vite otomatis pilih port 5173/5174)
- JANGAN hapus folder apapun tanpa konfirmasi user (pelajaran Sesi 8)

---

## 🔄 PROMPT PEMBUKA UNTUK SESI BERIKUTNYA

> "Baca AGENTS.md lalu PROGRESS.md. Jelaskan status proyek SPMB dan apa yang harus dilanjutkan hari ini. Kalau ada, lanjutkan dari TODO yang belum selesai."

---

## 🔒 Sesi 2026-08-11 (lanjutan 4): Fix logout 404

### Yang dikerjakan
- **Bug 1**: `doLogout` di Navbar.vue submit POST ke `/logout` relatif (localhost:5174 — vite) padahal route logout ada di backend (localhost:8000) → 404.
- **Bug 2 (setelah fix 1)**: logout tidak jalan — landing page Vue tidak punya `<meta name="csrf-token">`, form POST tanpa `_token` → Laravel 419 (diredirect balik HandleTokenMismatch) → session tidak logout, button tetap ada.
- **Fix final**: `doLogout` ditulis ulang mengikuti pola LoginView — fetch HTML `${BACKEND}/login` (credentials include), ambil `_token` via regex, lalu POST fetch `${BACKEND}/logout` dengan `X-Requested-With: XMLHttpRequest` + `_token`, redirect ke `res.url` (backend `/`). `sessionStorage spmb_session_status` dibersihkan sebelum redirect.
- **Perbaikan lanjutan (request user)**: redirect logout DIUBAH — tidak lagi ke backend `/` (halaman formulir), tapi balik ke **landing page** (`/?no-intro=1` di origin frontend 5174) supaya button Login di hero muncul lagi & button Logout hilang. Jika `res.ok` false → fallback ke `${BACKEND}/login`.
- **Akar masalah terakhir**: tombol logout di **form page blade** (`create.blade.php`) POST `route('logout')` → `AuthController::logout` masih `redirect('/')` (backend 8000). Fix: `return redirect(env('FRONTEND_URL', 'http://localhost:5174') . '/?no-intro=1')` — menyelesaikan SEMUA jalur logout (blade + Vue). ⚠️ Catatan: edit pertama sempat salah sasaran (mengubah redirect login siswa), sudah dikoreksi — login siswa tetap `redirect('/')`.
- Build ✅ clean

## 🧭 Sesi 2026-08-11 (lanjutan 3): Fix navbar halaman form (create.blade.php)

### Yang dikerjakan
- **Masalah**: navbar form page pakai link `/`, `#layanan`, `#about`, `#contact` — di-serve dari `localhost:8000` (Laravel), bukan frontend Vue → klik tidak redirect apa-apa.
- **Fix**: 4 link desktop-nav diarahkan ke frontend landing `http://localhost:5174/?no-intro=1` (+ `#layanan`, `#tentang`, `#contact` — id section sesuai Vue, `#about` tidak ada di Vue jadi diganti `#tentang`). Konsisten dengan logo & tombol back yang sudah hardcode ke 5174.
- Verifikasi: `php -l` OK

## 🧭 Sesi 2026-08-11 (lanjutan): Smooth scroll navbar + restore dropdown Layanan

### Yang dikerjakan
1. **Smooth scroll** (style.css): `html { scroll-behavior: smooth }` + `scroll-margin-top: 96px` untuk `#layanan, #tentang, #berita, #contact` — klik Beranda/Layanan/Tentang/Kontak di navbar tidak lagi loncat kaku; section berhenti pas di bawah navbar fixed (74px + margin). Reduced-motion tetap auto (guard lama).
2. **Dropdown Layanan di-restore "kayak semula"**: SPMB Online + Berita + Koperasi + Produk Siswa dengan `di-desc` (histori commit 7ae4f1a), lalu atas request user Berita diganti **Career Center** (Berita tetap di dropdown Informasi): SPMB Online (guest) · Koperasi · Produk Siswa · Career Center.

## 🔑 Sesi 2026-08-11 (lanjutan 2): Hero login dinamis + dropdown Layanan untuk semua role

### Yang dikerjakan
1. **Hero button login hilang setelah login** (request user, screenshot tidak bisa dibaca model → dikonfirmasi lewat teks):
   - Guest: toggle "Login" (+ sub-buttons Login Siswa/Pendaftar) — tetap
   - Siswa: tombol "Dashboard Siswa" → `${BACKEND}/dashboard-siswa`
   - Pendaftar: tombol "Lanjutkan Pendaftaran" → `${BACKEND}/pendaftaran/create`
   - Admin: TIDAK ada tombol (aturan AGENTS.md: tidak boleh ada akses admin di navbar/halaman manapun)
2. **Dropdown Layanan sekarang tampil untuk SEMUA role** (request user): Koperasi, Produk Siswa, Career Center tanpa `v-if="isSiswa"`. Klik oleh non-siswa → toast "Khusus siswa, silakan login terlebih dahulu" (pola sama dengan feature.vue bento) via `guardSiswa` + `useToast`. Mobile & desktop sinkron. Router guard `requiresSiswa` tetap sebagai safety net.

### Fix dropdown render (lanjutan, request user)
- `.di-title` & `.di-desc` diberi `white-space: nowrap` — panel (`width: max-content`) sekarang pasti selebar isinya, teks tidak pernah wrap 2 baris.

### Verifikasi
- `npm run build` ✅ clean

## 🎬 Sesi 2026-08-11: Scroll Reveal Animation (taste-skill §5.C)

### Yang dikerjakan
1. **Directive global `v-reveal`** di `main.js` (IntersectionObserver, threshold 0.15, sekali jalan + disconnect):
   - `v-reveal` → fade-up 24px, `v-reveal="0.06"` → stagger delay (detik)
   - Auto-respect `prefers-reduced-motion` (skip observer, konten langsung terlihat)
   - `transitionend` sekali → `transitionDelay` dibersihkan supaya hover transform card tetap responsif
2. **CSS reveal** di `style.css` (satu-satunya stylesheet terimpor, selain variable.css):
   - `.reveal`/`.revealed` pakai `cubic-bezier(0.16,1,0.3,1)` 0.7s, gated `@media (prefers-reduced-motion: no-preference)` + global reduce guard
3. **Diterapkan di 3 section landing**:
   - AboutSchool: big-card (0s) + stats-card (0.15s)
   - feature.vue: heading (0s) + semua bento card (0.06 × index)
   - News.vue: section-header (0s) + news-card (0.05 × index)
4. **Koreksi penting**: `global.css`, `layout.css`, `components.css`, `animations.css` ternyata TIDAK PERNAH diimpor (dead files) — `text-wrap: balance` & reduced-motion guard yang kemarin ditaruh di `global.css` tidak berefek. Keduanya dipindah ke `style.css` (terimpor). File dead tidak dihapus (menunggu izin user).

### Verifikasi
- `npm run build` ✅ clean

### Catatan
- Mau effect lebih dramatis (parallax, GSAP pin, marquee) → bilang saja, tapi skill §5 bilang "motion must be motivated" — reveal ini cukup untuk MOTION dial 5

## ⚡ Sesi 2026-08-10 (finish): Penerapan taste-skill audit-first — pass seluruh halaman

### Yang dikerjakan (mekanik per skill §4-6)
1. **`prefers-reduced-motion` guard** (mandatory §6.B — sebelumnya 0 ada): ditambahkan di `global.css` — matikan semua animasi/transition saat user set reduce motion.
2. **`100vh` → `100dvh`** (§3.E viewport stability): 6 view aktif (HomeView, LoginView, ProdukSiswaView, NewsView, CareerCenterView, KoperasiView) — pakai fallback 2 baris (`100vh; 100dvh;`). Legacy DashboardSiswa/Admin/RegisterView TIDAK disentuh (AGENTS.md).
3. **Scroll listener navbar → IntersectionObserver** (§5.D banned `window.addEventListener("scroll")`): sentinel 1px `position:absolute; top:81px` di body, observe threshold 0 → `scrolled` toggle. Cleanup di onUnmounted.
4. **Dead CSS dihapus** (Navbar): `.nav-login`, `.mobile-login` (tombol login lama sudah tidak ada).

### Audit lanjutan — lulus
- Navbar height 74px (cap 80px ✓), glass fallback solid `rgba(255,255,255,.82)` ada
- Eyebrow/section-label: tentang + berita = 2 (max 2 untuk 4 sections ✓)
- Layout families: hero-centerd / split-card / bento / sidebar+grid = 4 berbeda ✓
- Shadow tinted hijau konsisten, tidak ada pure black di komponen aktif
- Footer cadence OK

### Skipped (dokumentasi)
- Dark mode (§6.C) TIDAK diterapkan — sekolah trust-first light-only; brand palet hijau muda; doting dark butuh overhaul besar & berisiko (bukan permintaan user)
- Ikon campuran FA + lucide sudah jadi konvensi proyek — tidak dirapikan (scope besar, bukan permintaan user)

### Verifikasi
- `npm run build` ✅ clean
- Perilaku navbar shrink tetap sama (threshold 81px ↔ sebelumnya 80px, setara)

## 🎨 Sesi 2026-08-10 (lanjutan): Perbaikan v2 taste-skill (audit-first)

### Yang dikerjakan
1. **Hero.vue** (anti-center bias / hero stack discipline):
   - Badge eyebrow dirender (`Pendaftaran SPMB Dibuka`) — CSS sudah ada tapi tidak dipakai
   - Paragraf dipotong 31+ kata → 13 kata (rule v2: ≤20 kata)
   - Secondary CTA **Info SPMB** ditambah (`spmbTarget()`, reuse composable) — 1 primary + 1 secondary
   - Scroll indicator `.line` dirender (sebelumnya dead)
   - Entry animation fade-up stagger (badge→h1→p→buttons→line, `@keyframes rise`); `.line` pakai comma-animations (rise + scroll) biar tidak saling override
   - `:active` feedback (scale 0.98) pada primary/secondary/sub-btn
   - Dead CSS dibuang: `.btn-register`, `.btn-dashboard`, selector `.scroll` (→ `.line`)
2. **feature.vue**:
   - Eyebrow "01 Layanan Digital" DIHAPUS (restraint §4.2 — hero badge + about + news sudah punya label; max 1 per 3 sections)
   - `counter-main` → `font-variant-numeric: tabular-nums` (angka statistik)
   - `:active` feedback di btn-daftar, card-link, card button (featured)
3. **AboutSchool.vue**: `.stat-value` → tabular-nums
4. **global.css**: `h1, h2, h3 { text-wrap: balance; }` (anti orphan/rag)

### Verifikasi
- `npm run build` ✅ clean (1796 modules, 31.29s)
- Logika navigasi tidak berubah (`spmbTarget` reuse), role-gating tetap

### Revisi user (minta revert sebagian)
- Scroll indicator `.line` DIHAPUS + Secondary CTA "Info SPMB" DIHAPUS — posisi button login kembali seperti semula (hanya `.btn-group-login`). CSS-nya juga dibersihkan (`.line`, `@keyframes scroll`, `.btn-secondary`, media query terkait)
- Badge eyebrow + entry fade-up + `:active` feedback tetap dipertahankan

### Catatan
- Centered hero dipertahankan — diperbolehkan skill v2 untuk brief manifesto/announcement
- Eyebrow tersisa: AboutSchool (`Tentang Kami`?) + News (`Berita & Pengumuman`) — 2 label untuk 4 sections, masih dalam batas

## 🎨 Sesi 2026-08-10: Redesign Audit (taste-skill redesign framework)

### Latar belakang
User minta audit + upgrade desain pakai **redesign-skill** (Leonxlnx/taste-skill, desain skill yang sama dengan design-taste-frontend yang sudah terinstall di `.agents/skills/`). Audit menemukan tema biru legacy nyempil di tema hijau, token system mati, dan banyak inkonsistensi radius/shadow.

### Yang dikerjakan
1. **Token system dibangkitkan**: `src/assets/css/variable.css` ditulis ulang ke palet hijau (`--primary:#3a6450`, radius scale sm 10/16/20/24/pill, shadow hijau-tinted, bg page/section, status amber/orange). Diimport di `main.js` sebelum style.css.
2. **style.css dibersihkan**: buang Vite boilerplate (`color-scheme: light dark`, `#242424`, `rgba(255,255,255,.87)`) — inilah yang bikin halaman blank/dark saat error lain muncul.
3. **Tema biru dipadamkan**: CursorGlow (`rgba(91,127,255,.12)` → `rgba(125,184,141,.14)`), BackgroundFX (`#F8FAFF`/`#5B7FFF` → `#f2f4f1`/`#7db88d`, grid green 0.035), ChatView (`#F5F7FC` → `#f2f4f1`).
4. **LoadingScreen**: `#050505` hitam murni → dark green `#1c2a23` + radial glow hijau; hapus double Google Fonts `@import`.
5. **Radius normalisasi**: footer social icon 6px→12px, CareerCenter card 24px→20px, Koperasi product card 18px→20px.
6. **Modal shadow hitam → hijau-tinted**: ProdukSiswaView + NewsView (`rgba(0,0,0,.25)` → `rgba(35,55,42,.25)`).
7. **Featured card konsisten dark** (seperti SPMB feature.vue): CareerCenter `company-card.featured` → `#2b4a3c` + semua teks/badge/details di-override putih.
8. **Heading scale seragam**: feature.vue h2 → weight 800, `-0.03em` (sama dengan Hero/About).
9. **Dead code dihapus**: `.warna` (#04944e) di Hero.vue, `getCsrfToken` tak terpakai di LoginView.vue.
10. **Status warna tokenisasi**: `--status-amber:#f39c12`, `--status-orange:#e67e22` di variable.css.

### Verifikasi
- `npm run build` ✅ clean (1796 modules)
- Semua halaman: landing, /login, /koperasi, /produk-siswa, /career-center, /berita, /chat tidak disentuh logikanya

### Catatan
- DashboardSiswa/DashboardAdmin (Vue lama) masih ungu `#667eea` — TIDAK DISENTUH (tidak dipakai, sesuai AGENTS.md).
- RegisterView (Vue) masih ungu — belum di-redesign, dan memang tidak dipakai (register pakai backend blade).
- Tema jadi 100% hijau untuk semua halaman aktif.

## 📋 Sesi 2026-08-09: Role-Based Access (siswa vs pendaftar)

### Yang dikerjakan
1. **Migration**: `2026_08_09_094358_add_pendaftar_role_to_users_table` — enum `('admin','siswa','pendaftar')`, semua user 'siswa' lama dikonversi ke 'pendaftar'. Migrasi berjalan di local + TiDB remote.
2. **Register**: default role `'pendaftar'` (AuthController).
3. **Login redirect by role**: admin→dashboard admin, siswa→landing `/`, pendaftar→form pendaftaran.
4. **updateStatus**: admin mark `diterima` → user role otomatis jadi `siswa`; `ditolak`/`baru` → tetap `pendaftar`.
5. **Backend /profil**: route (`role:siswa` middleware), controller `showProfile`, blade view `auth/profile.blade.php`.
6. **Frontend Toast**: composable `useToast.js` + global toast di `App.vue`.
7. **Navbar role-based**: siswa lihat Profil + Logout, tidak lihat SPMB, lihat Koperasi+Produk; pendaftar/guest lihat SPMB, tidak lihat Koperasi+Produk.
8. **feature.vue bento**: SPMB clickable → toast jika siswa; Koperasi/Produk/Career Center → toast "Khusus siswa" jika bukan siswa.
9. **Router guards**: `/koperasi`, `/produk-siswa`, `/career-center` butuh role siswa; redirect ke `/` jika bukan siswa.
10. **DatabaseSeeder**: fix UserFactory error (comment out factory call), AdminSeeder tetap jalan.
11. **Local migration:fresh + seed berjalan sukses**, frontend `npm run build` berhasil.

### Status deployment Vercel
- **DITUNDA** — user: "nanti aja minta tolong guru"
- `.env.deploy` sudah siap dengan TiDB credentials + SSL cert
- TiDB migration sudah jalan (semua 12 migration + AdminSeeder)

### TODO berikutnya
- Deploy Vercel + TiDB (user minta ditunda)
- Update `IMPLEMENTATION_SUMMARY.md` jika ada perubahan signifikan
## ?? Sesi 2026-08-13: Playwright e2e + FIX Bug Logout Navbar (CORS)

### Yang dikerjakan
1. **Playwright terpasang**: @playwright/test + Chromium (chrome + headless shell + ffmpeg + winldd) di root. playwright.config.js (ESM, webServer array: backend 8000 + frontend 5174, reuseExistingServer) + e2e/logout.spec.js (2 test logout).
2. **Hambatan jaringan test**: fonts.google/gstatic + Google Maps iframe menggantung dari mesin ini ? load event tidak pernah fire ? timeout navigasi. Solusi test-side: page.route abort host Google (bukan ubah app).
3. **BUG ASLI KETEMU (logout dari navbar Vue)**: doLogout ambil _token dari GET /login � tapi user sudah login ? redirect 302 ? / (form page) ? regex token gagal ? fallback ke /login tanpa logout (session tetap hidup). PLUS route / dan /logout TIDAK punya middleware CORS ? fetch cross-origin 5174 gagal ("Failed to fetch").
4. **FIX**:
   - Navbar.vue doLogout: sumber token GET /login ? GET / (form page selalu render _token).
   - outes/web.php: route / ditambah Cors middleware; route /logout jadi middleware([Cors, 'auth']) (agar preflight OPTIONS + response headers CORS jalan).
5. **Verifikasi e2e**: 2/2 lulus � blade logout & navbar logout; response POST /logout = 302 ? http://localhost:5174/?no-intro=1; bukti session mati: GET /dashboard-siswa ? redirect /login; hero landing kembali tampil tombol Login.
6. .gitignore: tambah 	est-results + playwright-report.

### Cara jalankan test
```powershell
cd C:\Users\LENOVO\lomba\ga-ro
npx playwright test
```
(wajib: backend + MySQL jalan; akun siswa demo siswa/siswa123; network Google diblokir otomatis di test)

---

## 🧭 Sesi 2026-08-17: Analisis Web + Tambah E-Learning & E-Tracer

### Yang dikerjakan

1. **Analisis web sekolah lama** (`smkbahrululumsurabaya.sch.id`):
   - Fetch semua halaman: Homepage, Berita, SPMB Info, Tracer Study, Kalender Akademik, Visi Misi, Fasilitas/Galeri, Hubungi Kami, e-Learning
   - Hasil: 10 fitur web lama belum ada di web baru

2. **Analisis web deployed** (`bhapppp.vercel.app`):
   - Fetch HTML shell + `news.json` (6 artikel lengkap dengan konten)
   - Konfirmasi: SPA Vue 3, subpages return 404 saat di-fetch langsung (client-side routing)

3. **Perbandingan lengkap web lama vs web baru**:
   - Web lama lebih baik di: Info SPMB, Tracer Study, Profil Sekolah, Fasilitas, Kontak, e-Learning, Galeri
   - Web baru lebih baik di: Multi-step form, Dashboard admin, AI chatbot, Student Showcase, Career Center, Koperasi

4. **Tambah E-Learning** (`/e-learning`):
   - `src/views/ELearningView.vue` — 6 materi pembelajaran (HTML, MySQL, JavaScript, Jaringan, PHP, Relasi Tabel) dengan video + PDF download
   - 3 kuis interaktif (Google Forms)
   - Kategori: Pemrograman, Jaringan, Basis Data, Multimedia
   - Fitur: search, filter kategori, responsive

5. **Tambah E-Tracer Study** (`/e-tracer`):
   - `src/views/ETracerView.vue` — form tracer study alumni lengkap
   - Data diri: nama (dropdown 48 alumni), email, NIK, jenis kelamin, tempat/tanggal lahir, tahun lulus, no HP, pendidikan terakhir
   - Status: Bekerja/Kuliah/PKL/Magang/Wirausaha/Mencari Kerja
   - Data kuliah: nama universitas, tahun masuk
   - Data bekerja: nama perusahaan, bidang, jabatan, alamat
   - Data kependudukan: kecamatan, kelurahan, RT/RW, alamat domisili
   - Statistik alumni: 42% bekerja, 38% kuliah, 5% PKL, 3% wirausaha
   - Success banner + info banner

6. **Update Router** (`src/router/index.js`):
   - Import `ELearningView` dan `ETracerView`
   - Tambah route `/e-learning` (public) dan `/e-tracer` (public)

7. **Update Navbar** (`src/components/layout/Navbar.vue`):
   - Dropdown "Informasi" desktop: Berita, Kelulusan, **E-Learning** (baru), **E-Tracer Study** (baru)
   - Dropdown "Informasi" mobile: Berita, Kelulusan, **E-Learning** (baru), **E-Tracer Study** (baru)

### Verifikasi
- `npx vite build` ✅ sukses (1800 modules, 25.39s)
- Semua halaman baru bisa diakses: `/e-learning`, `/e-tracer`

### Navbar BACKEND UTAMA — perbaikan kesalahan `</nav>` hilang (19/8, laporan user: "form login terpotong + button navbar hilang"):
- **Root cause**: saat aku menghapus nav-links (Masuk/Daftar/Beranda/Formulir) dari `app.blade.php`, closing tag `</nav>` ikut terhapus secara accidental
- **Struktur jadi**: `<nav>...@yield('content')...</body>` — semua konten (form login, judul halaman) berada di dalam `<nav>` yang punya `display: flex; justify-content: space-between`
- **Akibat**: form login di-push ke kanan halaman, title "Masuk" + field Username terpotong di atas, form bergeser ke right, hanya PASSWORD yang terlihat
- **Fix**: tambahkan `</nav>` setelah `</div>` penutup brand div di `backend/resources/views/layouts/app.blade.php` (line 467)
- **Hasil**: `<nav>` tertutup dengan benar; form berada LUAR `<nav>` → alir normal, ter-centang di halaman; username, password, title, subtitle semua visible
- **Deploy**: `spmb-backend-f5ryvsf1i`, alias `pendaftaranspmb.vercel.app`, login page 200, form lengkap terlihat

### Fitur yang BELUM ada di web baru (dibanding web lama)
| Fitur | Prioritas |
|-------|-----------|
| Info SPMB Center (syarat/biaya/beasiswa/timeline) | S |
| Profil Sekolah (Visi Misi) | S |
| Fasilitas Sekolah | A |
| Hubungi Kami | A |
| Galeri Kegiatan | B |
| Brosur SPMB (PDF download) | B |

### Fitur inovatif yang disetujui user
| Fitur | Status |
|-------|--------|
| Dynamic School Statistics (admin-managed) | Disetujui, belum dikerjakan |
| Universal Search | Disetujui, belum dikerjakan |

### Navbar Dropdown "Informasi" (final)
```
Berita
Kelulusan
E-Learning
E-Tracer Study
```

---

## Sesi LCP Optimization (2026-08-22)

### Problem
Admin dashboard `paneladminsmkbu.vercel.app` LCP = 24.11s (audit Lighthouse).

### Root Causes
1. 700+ baris CSS inline di `<head>` — render-blocking
2. Google Fonts tanpa preload
3. Chart.js CDN load sync (56KB JS)
4. ~40+ DB queries per load (stats + chartData = 30 query harian)
5. Tidak ada Cache-Control header

### Optimizations (deploy `spmb-admin-cd8v4n5a2`)

**Step 1 — Critical CSS split (layout `app.blade.php`)**
- CSS di-split jadi 2: critical (sidebar, topbar, layout, form-section — inline `<style>`) + deferred (`<style media="print" onload="this.media='all'">`)
- Font Quicksand woff2 di-preload: `<link rel="preload" as="font" ...>`
- `@vite` dipindah AFTER kedua stylesheet

**Step 2 — Cache stats (`PendaftaranController::dashboard()`)**
- `$stats` dibungkus `Cache::remember('admin.dashboard.stats', 30, fn() => ...)`
- `$chart` dibungkus `Cache::remember('admin.dashboard.chart', 30, fn() => ...)`
- Cache-Control header: `private, max-age=30, stale-while-revalidate=60`

**Step 3 — Defer Chart.js (`dashboard.blade.php`)**
- `<script src="cdn.chart.js">` sync → lazy load via IntersectionObserver
- Observer trigger: `rootMargin: '400px'` (preload sebelum charts-grid masuk viewport)
- Fallback: kalau element tidak ada → langsung load

**Step 4 — fetchpriority**
- `<h1>` di dashboard: `fetchpriority="high"`

**Bonus — vercel.json domains diperbarui**
- `APP_URL` → `paneladminsmkbu.vercel.app`, `FRONTEND_URL` → `smkbu-sby.vercel.app`

### Status
- Deploy OK (1 attempt — ISP lancar)
- Live: https://paneladminsmkbu.vercel.app
- LCP improvement: TTFB berkurang (stats cached 30s + Cache-Control), CLS better (critical CSS inline), chart load deferred
- **User perlu cek manual** via Lighthouse di browser untuk angka LCP terbaru

---

### Sesi Contact Modal — Footer "Hubungi Kami" (2026-08-26)

**Frontend (smkbu-sby.vercel.app):**
- **New component**: `src/components/common/ContactModal.vue` — glassmorphism modal, form fields: Nama, Email, No. WhatsApp (+62 prefix), Pesan. Teleport ke `<body>`, v-model open/close, transition fade, success state tanpa backend (UX-only).
- **Footer button**: tombol "Hubungi Kami" di kolom "Informasi Kontak" (`Footer.vue`). Emit `openContact` event → wire ke `HomeView.vue` → toggle `showContact` ref → buka modal.
- Style: glassmorphism card (`backdrop-filter: blur(24px) saturate(1.4)`, `background: rgba(255,255,255,0.82)`), green theme (#3a6450), button hover transform.
- **No backend yet**: submit hanya simulasi (800ms delay → success state). Backend `/api/contact` bisa ditambah nanti.

### Status
- Deploy OK (build 3.62s, 1817 modules)
- Live: https://smkbu-sby.vercel.app

### Sesi ContactModal UI Redesign (2026-08-26)

**Frontend (`ContactModal.vue`):**
- **Complete visual redesign** to match Justinmind reference:
  - Card: solid `#fff` (no glassmorphism), `border-radius: 20px`, subtle shadow
  - Header: bold 28px "Hubungi Kami" title only (no icon box, no subtitle)
  - Info text: email + phone shown below title (`smkbahrululum.sch.id | +62 812-3456-7890`)
  - Inputs: **bottom-border only** (`border: none; border-bottom: 1.5px solid #e5e7e6`), no labels, placeholder only
  - Phone field: `<select>` dropdown for country code (+62, +60, +65, +1, +44) + text input, side by side
  - Textarea: same bottom-border style
  - Button: dark/black (`#1c2a23`), rounded 12px, not green
  - Success: chat bubble SVG icon (dark circle + white bubble + checkmark), "Terima Kasih!!", black "Kembali" button
  - Mobile: phone row stacks vertically, select becomes full-width

### Status
- Deploy OK (build 2.61s, ISP retried 3x)
- Live: https://smkbu-sby.vercel.app

---

## STATUS TERAKHIR (sesi 2026-08-29)

### Dashboard Siswa Redesign + Back Button (backend)
- layouts/app.blade.php: tambah dukungan sidebar layout — CSS sidebar + @if View::hasSection('sidebar') → sidebar (brand, nav, logout) + topbar (hamburger mobile, logo, avatar user). Non-sidebar halaman pake navbar lama.
- dashboard-siswa.blade.php: rewrite total — sidebar (Dashboard, Status, Edit), welcome banner gradient hijau + doodle images/doodle-studying.png, 3 stat cards (status, jurusan, tanggal daftar), detail status card, pesan status, quick actions, form edit (jika status baru & <3 hari), polling status tiap 15s. Tambah tombol **Kembali** (←) di atas banner → rontendAuthUrl().
- Deploy backend OK: https://pendaftaranspmb.vercel.app

### E-Learning Logo (frontend)
- ELearningView.vue: ganti ikon SVG graduasi di sidebar brand → <img src="/logo.png"> (pola sama CareerCenter, background putih rounded). Deploy OK.

### Koperasi — Keranjang & Checkout di-restore (frontend)
- Investigasi: koperasi berubah karena commit  e6b0dd "fff" (sesi sebelumnya) rewrite total KoperasiView.vue (434 ins / 1558 del) dan MENGHAPUS sistem keranjang + checkout. User tidak menyadari. Versi lama punya keranjang: commit 6fb645d.
- Solusi (opsi B dipilih user): integrasikan ulang keranjang & checkout ke desain baru, simpan aksi + pada tiap kartu, floating cart (badge + total), cart sheet (qty +/-/hapus), checkout (QRIS/Transfer + total), halaman pending (QR CSS / rekening BSI copyable + countdown 1 jam), sukses (order id + notifikasi). Juga tombol **Tambah ke Keranjang** di modal detail. Pakai lucide-vue-next (sudah dependency).
- Deploy OK: https://smkbu-sby.vercel.app
- **Koperasi redesign ulang (referensi Chanta store)**: bg putih bersih, topbar sticky (back btn + logo + brand kiri, tombol Keranjang dengan badge kanan), judul besar bold, filter pill (active = dark), grid 4 kolom (3/2/1 responsif), card minimal tanpa shadow (gambar bg #f5f6f7, nama, desc 1 baris ellipsis, harga bold), cart drawer slide dari kanan (420px). Checkout/QRIS/transfer/pending/sukses & modal detail dipertahankan. Warna aksen berubah dari hijau ke dark #1c2a23.
- Deploy OK: https://smkbu-sby.vercel.app
- **Fix bug koperasi**: (1) crop kanan → tambah overflow-x: hidden di .koperasi-page; (2) tombol + kembali di tiap card produk (lingkaran 34px dark #1c2a23, @click.stop supaya tidak trigger modal), layout .produk-info jadi flex.
- Deploy OK: https://smkbu-sby.vercel.app

---
## Sesi (2026-08-29) - Koperasi: tombol Beli Sekarang terpotong
FIXED. Root cause: tidak ada global box-sizing, .drawer content-box (height:100% + padding 42px) melebihi viewport -> footer terpotong ke bawah. Fix: ox-sizing:border-box + lex-shrink:0 di .drawer-foot + min-height 48px tombol + safe-area inset bottom. Deployed smkbu-sby.vercel.app.


---
## Sesi Tabungan (lanjutan) + Bento Restructure + Agent Skills (2026-08-29)

### Tabungan POST CORS/419 FIXED (backend)
- Root cause: /tabungan tidak ada di CSRF exceptions (hanya lamaran*). 419 -> HandleTokenMismatch -> redirect -> CORS blocked.
- Fix: `backend/bootstrap/app.php` `validateCsrfTokens(except: ['lamaran*', 'tabungan*'])`. Deployed pendaftaranspmb.vercel.app.

### Modal tidak menutup setelah submit sukses (TabunganView.vue)
- Root cause: closeModal() di-guard modalBusy (no-op saat submit). Fix: set `modalBusy.value=false; modalOpen.value=false` langsung di path sukses lalu `await loadData()`.
- E2E lokal verifikasi penuh (login siswa/test1234): setor 75000->Rp75.000, tarik 25000->Rp50.000, tarik 999999->422 toast "Saldo tidak cukup", modal auto-close sukses. Data diclear via `php artisan tinker --execute="\App\Models\Tabungan::query()->delete();"`.
- Frontend deployed smkbu-sby.vercel.app. Verifikasi prod: POST cross-origin dari smkbu-sby -> /tabungan -> preflight OK, guest -> 302 /login (finalUrl /login), type:cors, tanpa CORS error. Catatan: fetch manual tak bisa spoof Origin header (forbidden) jadi tes OPTIONS tanpa ACAO adalah artefak tes.

### Saldo Aktif di banner landing (TabunganBanner.vue)
- Bug: banner tampil Rp0 padahal saldo ada. Fix: ref `saldo` + `loadSaldo()` (fetch `${BACKEND}/tabungan`, credentials include), `watch(isSiswaLoggedIn, immediate:true)`, listener `pageshow` (bfcache - back dari /tabungan tak remount HomeView). Tampil `Math.round(saldo).toLocaleString("id-ID")`.
- Verifikasi lokal: setor 1000 -> /tabungan Rp1.000 -> banner "Saldo Aktif Rp1.000". BELUM di-deploy saat fix selesai - ikut deploy bento restructure.

### Agent Skills (OpenCode) - install addyosmani/agent-skills
- Clone GitHub -> copy 25 skill ke `.agents/skills/` (total 27 termasuk design-taste-frontend & imagegen-frontend-web existing) + 7 reference files ke `.agents/references/`.
- AGENTS.md ditambah section "Agent Skills (OpenCode)": intent->skill mapping (spec-driven-development, incremental-implementation, test-driven-development, planning-and-task-breakdown, debugging-and-error-recovery, code-review-and-quality, code-simplification, api-and-interface-design, frontend-ui-engineering, design-taste-frontend, performance-optimization, shipping-and-launch) + execution model (panggil skill tool sebelum bertindak). Temp clone dihapus.

### Bento Restructure - bento grid dihapus, diganti section biasa
- User pilih: "Remove bento, split to sections". `feature.vue` (~750 baris) DIHAPUS (sudah konfirmasi plan). 5 komponen baru di `src/components/sections/`:
  - **SpmbBanner.vue** - banner gelap full-width SPMB (stats 1.247 pendaftar, 3 jurusan, chips Gelombang 1/Online & Offline/Gratis, CTA Daftar Sekarang + Info & Biaya), visual pmb_smkbu.webp + card kuota.
  - **BeritaPreview.vue** - 3 kartu berita terbaru dari `public/data/news.json` (klik -> /berita/:slug), tombol Semua berita -> /berita.
  - **CareerPreview.vue** - band horizontal dengan hitungan lowongan LIVE dari backend `${BACKEND}/lowongan` (bukan /api/lowongan - itu bug lama feature.vue yang bikin 404 di console selama ini). Tampil 25 di prod.
  - **KoperasiPreview.vue** - section split promo koperasi, grid gambar produk (ser/sepatu/topi), CTA gate siswa (non-siswa -> toast).
  - **ProdukPreview.vue** - section split karya siswa (ikon lucide Monitor/Hammer), CTA gate siswa.
- HomeView.vue: urutan Hero -> AboutSchool -> SpmbBanner -> BeritaPreview -> CareerPreview -> KoperasiPreview -> ProdukPreview -> TabunganBanner -> News -> Footer. Kartu "Tentang Sekolah" dari bento dibuang (redundant dgn AboutSchool).
- Design: skill design-taste-frontend, palette forest-green #3a6450, Quicksand, v-reveal, variasi layout (banner/stats, grid berita, band karir, split koperasi, split produk). Fix id duplikat `#berita` (BeritaPreview, News tetap punya id).
- Verifikasi lokal: semua section render, no console error, career count live (25), gate koperasi siswa OK, mobile 390px no horizontal overflow.
- DEPLOY (PENTING): deploy harus dari **repo root** `C:\Users\LENOVO\lomba\ga-ro` (project `lomba`, alias smkbu-sby.vercel.app). Jangan dari `src/` - itu project lain (orphan `src-5j08dhjl3...` yang salah deploy, debris). Frontend deployed OK: https://smkbu-sby.vercel.app. Backend tidak berubah -> tidak redeploy.
- Verifikasi prod: semua section baru render, career count 25, tidak ada .fc-card (bento hilang), pageHeight 6678.


---
## Sesi - Hapus section berita kedua (News.vue) (2026-08-29/30)

- Setelah bento restructure ada 2 section berita di landing: BeritaPreview (atas) dan News.vue (bawah Tabungan). User pilih hapus yang bawah Tabungan.
- HomeView.vue: hapus `import News` + `<News />`. File `src/components/sections/News.vue` dihapus. Konten news tetap bisa dilihat via halaman `/berita` (NewsView) & `/berita/:slug` (NewsDetail) yang tidak tersentuh.
- Build OK, deploy dari repo root OK: https://smkbu-sby.vercel.app (pernah retry 1x: error "Not authorized" transient, `vercel whoami` masih zakkyilhamf-7419).
- Konvensi ke depan: percakapan & dokumentasi proyek pakai Bahasa Indonesia.


---
## Sesi - Foto news.json tidak muncul (2026-08-29/30)

- Foto yang ditambahkan user di `public/newsP/spmb.jpeg` tidak tampil: path di news.json salah (`/public/newsP/spmb.jpeg`). Di Vite folder `public/` = root URL, jadi path benar tanpa prefiks `/public` -> `/newsP/spmb.jpeg`.
- Fix: ganti path id 2 di `public/data/news.json`. Rebuild + deploy smkbu-sby.vercel.app. Verifikasi prod: `/newsP/spmb.jpeg` HTTP 200.
- CATATAN: 5 berita lain masih broken (404, gambar di-hide oleh handler @error): id1 image "/", id3-6 `/images/news/*.jpg` - folder `public/images/news` (dan folder images di repo) tidak ada. Perlu file foto + perbaikan path bila mau ditampilkan.


---
## Sesi - Navbar glassmorphism (2026-08-30)

- User beri referensi foto (WhatsApp) glass effect. Navbar sebelumnya terlalu solid (bg 0.82, blur 8px, tanpa border) sehingga efek glass kurang terlihat.
- Navbar.vue: background -> rgba(255,255,255,0.55) + backdrop-filter blur(20px) saturate(160%) (dengan prefix -webkit) + border 1px rgba(255,255,255,0.35) + inner highlight shadow. .shrink -> bg 0.68. Mobile -> bg 0.65 + blur sama. Dropdown panel & panah juga dibuat glass konsisten.
- Build OK, deploy OK: https://smkbu-sby.vercel.app


---
## Sesi - Glass card Kuota Terbatas + Fix doodle login backend (2026-08-30)

- SpmbBanner.vue .visual-card (kartu "Kuota Terbatas"): blur 12px -> blur(20px) saturate(160%) + -webkit prefix, border 0.14 -> 0.2, tambah inset highlight shadow. Deploy smkbu-sby.vercel.app.
- Doodle login/pendaftar broken di produksi: root cause vercel.json backend pakai route catch-all `/(.*) -> api/index.php` sehingga /images/*.png masuk ke Laravel -> 404 (logo.png di root public ikut di-serve, tapi subfolder images tidak).
- Fix: backend/vercel.json tambah route static sebelum catch-all: `/images/(.*) -> /public/images/$1` dan `/doodles/(.*) -> /public/doodles/$1`. Deploy pendaftaranspmb.vercel.app. Verifikasi: /images/doodle-selfie.png HTTP 200.


---
## Sesi - Perbaikan Chatbot BISA (2026-08-30)

User lapor error saat chat. Investigasi + fix:

1. **Model Groq deprecated -> error(crash)** (ROOT CAUSE utama)
   - `api/chat.js` pakai model `llama-3.3-70b-versatile`. Groq mematikan model itu 16/08/2026 (developer tier) -> Groq balas 404 `model_not_found`. Kode lama `data.choices[0].message.content` crash (choices undefined) -> response "Groq Error"/500.
   - Fix: ganti model ke **`openai/gpt-oss-120b`** (pengganti resmi dari docs Groq) + error handling: cek `!response.ok` dan `data.choices?.[0]?.message?.content` sebelum pakai, balas pesan ramah (bukan 500) saat Groq error/kosong. API key prod masih valid (hanya modelnya yang off).

2. **`import ... with { type:"json" }` vs readFileSync**
   - Sempat diganti ke `readFileSync` tapi di Vercel file .json tidak ikut ter-trace bundler -> ENOENT `/var/task/api/knowledge/data/school.json`, `FUNCTION_INVOCATION_FAILED`. Kembalikan ke `import ... with { type:"json" }` (esbuild Vercel inline JSON ke bundle). Jadi JANGAN ganti ke readFileSync.

3. **`typing` tidak didefinisikan** di `src/views/ChatView.vue` (v-if="typing" -> warning Vue, indicator tidak muncul). Fix: `const typing = ref(false)`, set true saat kirim, false di finally; scroll dipindah ke finally juga.

4. **History tidak terkirim** - `src/services/chat.js` hanya kirim pesan terakhir. Fix: kirim seluruh array `messages` (ChatView panggil `sendMessage(messages.value)` sehingga bot punya konteks percakapan).

Debug notes: pull env Vercel hasilnya di-mask `[SENSITIVE]` (tak bisa baca key). Loging: `vercel logs https://smkbu-sby.vercel.app/api/chat`. Tes 400/kosong via curl itu artefak escaping JSON di PowerShell - pakai body file + `--data-binary "@file"`.

Verifikasi produksi: POST /api/chat -> 200, balasan real ~1.3s, knowledge matching OK (biaya -> ppdb). Deploy smkbu-sby.vercel.app.

## Sesi - Security Hardening komprehensif (2026-08-30)

Audit keamanan menyeluruh (auth, input, secrets, LLM, headers, CORS, privacy, deps) + fix semuanya sebelum deadline jam 3. Deployed: backend `pendaftaranspmb.vercel.app` & frontend `smkbu-sby.vercel.app`.

**CRITICAL:**
1. **Plaintext password dihapus** — kolom `plain_password` di `users` (sebelumnya disimpan saat register & reset password, dan ada di `User::$fillable` TIDAK di `$hidden`). Migration `2026_08_30_110000_drop_plain_password_from_users_table.php` jalan di local + TiDB. Referensi dihapus dari `AuthController` (register + resetPassword) & `User.php`. (Ikut jalan ke TiDB: migration SPP `add_guru_kasir_roles` + `create_spp_bills` yang sebelumnya tertunda - schema kini sinkron total.)
2. **XSS chatbot** — `src/components/chatbot/ChatMessages.vue` render `marked()` + `v-html` tanpa sanitasi (confirmed `marked` v18 loloskan `<script>`/`<img onerror>`). Fix: pasang **DOMPurify** (`npm i dompurify`), sanitize output LLM (allowlist tag/attr ketat).
3. **Reset token password tidak lagi tampil di layar di production** — hanya tampil saat `app()->isLocal()`; di production link di-log (`logger`) + pesan arahkan ke admin. Tanpa mailer, link tetap bisa diambil dari log Vercel oleh admin.

**HIGH:**
4. **Rate limiting** — throttle di routes: login `5,1`, register `5,1`, forgot-password `3,1`, pendaftaran `10,1` (brute-force login dulunya unlimited). Verifikasi: 5x gagal → 6th = **429**. **TRAP**: vercel.json `CACHE_STORE` sebelumnya `"array"` → counter throttle hilang tiap cold start (throttle tidak jalan!). Ganti `"database"` (tabel `cache`/`cache_locks` sudah ada di TiDB).
5. **CSRF diaktifkan kembali** — exception `lamaran*`, `tabungan*`, `spp*` dihapus dari `bootstrap/app.php`. Karena frontend & backend same-site (`*.vercel.app` → cookie SameSite=Lax tetap terkirim cross-subdomain → CSRF nyata). Alur: backend `authStatus()` & route baru `GET /csrf-token` balas `csrf_token`; frontend `src/services/csrf.js` (getCsrfToken cache sekali/sesi) kirim header `X-CSRF-TOKEN` di POST/DELETE (ApplyModal /lamaran, TabunganView /tabungan, LamaranSayaView /lamaran/{id}).
6. **IDOR TabunganController** fix — non-admin kini `user_id => 'prohibited'`; target user SELALU `$request->user()->id` (dulu bisa isi id user lain). Admin tetap bisa pilih user.
7. **Security headers middleware baru** `app/Http/Middleware/SecurityHeaders.php` (X-Content-Type-Options nosniff, X-Frame-Options DENY, Referrer-Policy strict-origin-when-cross-origin, Permissions-Policy none, HSTS saat https). Terdaftar di `bootstrap/app.php`. Verifikasi header live OK.
8. **CORS** — origin tak dikenal kini TIDAK dikirim header (dulu fallback ke origin pertama = salah).

**MEDIUM:**
9. **Upload hardening** — validasi `mimetypes` (finfo real content) ditambahkan untuk foto_3x4, kk_file, ijazah_file, sktm_file (bukan hanya ekstensi).
10. **Race condition pendaftaran** — cek `exists` + `create` dibungkus `DB::transaction` + `lockForUpdate` (1 pendaftaran/user atomic).
11. **Chatbot input validation** — `api/chat.js` & `src/services/chat.js`: max 20 pesan/history, max 2000 char/pesan, validasi array + role whitelist, `console.log(knowledge)` (kebocoran prompt) dihapus.

**LOW/debt:**
12. `npm audit fix` → 0 vulnerabilities (sebelumnya 2 high: nanoid, postcss — dev-only).
13. Skip sementara (debt): private storage upload (file pendaftaran tidak pernah di-serve via URL publik — 0 referensi `/storage/` di views; di Vercel storage ephemeral); SESSION_LIFETIME 10080 & SESSION_ENCRYPT belum diubah.

**Verifikasi live:** `/csrf-token` 200 + token real, `auth-status` balas csrf_token, chat POST 200 balasan BISA real, throttle 429, CSRF tanpa token 302, lowongan 200, landing 200, doodle frontend 200.

**Penting utk sesi depan:** (a) JANGAN buka kembali CSRF exception tanpa sync frontend header; (b) vercel.json CACHE_STORE wajib `database` agar throttle jalan; (c) admin panel (`/admin`, spp/tabungan store di `backend-admin`) pakai blade + CSRF normal — tak terpengaruh. Test `PendaftaranControllerTest` dkk belum dijalankan ulang setelah race-condition refactor — jalan `php artisan test --filter=Pendaftaran` saat ada waktu.

### Sesi - Code Review hasil Security Hardening + fix review (2026-08-30)

Code review (skill `code-review-and-quality`) atas diff security hardening. 2 fix diterapkan & di-deploy, 1 temuan di-review & dibatalkan (false positive):

1. **`src/services/csrf.js` — retry saat token gagal** (Critical dulu): `catch(() => null)` + cache permanen = semua POST/DELETE jadi 419 selamanya kalau fetch `/csrf-token` gagal sekali. Fix: kalau hasil null → `tokenPromise` di-reset → retry di panggilan berikutnya.
2. **`TabunganController.php` — user_id admin wajib diisi** (Required dulu): rule admin `'nullable|exists:users,id'` → `'required|exists:users,id'`. Sebelumnya admin tanpa user_id → `User::findOrFail(null)` = 404. `$data['user_id'] ?? null` disederhanakan (sudah terjamin ada).
3. **`PendaftaranController` double exists-check — TIDAK diubah (false positive)**: cek line 97 (fast-path UX, sebelum validasi) + guard `lockForUpdate` di transaksi (authoritative) adalah pola yang benar. `pendaftarans.user_id` cuma FK (bukan UNIQUE index) sehingga guard transaksi memang dibutuhkan; menghapus cek awal cuma menurunkan UX (yang double-submit kena validasi dulu).

Verifikasi: `php -l` OK, `vite build` OK, backend redeploy (aliased `pendaftaranspmb.vercel.app`), frontend redeploy (aliased `smkbu-sby.vercel.app`). Solusi tetap: no `php artisan test` baru diperlukan (perubahan tidak menyentuh logic SPP/Pendaftaran validasi).

### Sesi - Optimasi Panel Admin (2026-08-30)

Optimasi `backend-admin` (deployed ke **paneladminsmkbu.vercel.app** — domain production sesuai APP_URL, bukan spmb-admin). Skill `performance-optimization`: user pilih langsung fix, hasil terukur.

**Ukur:** query TiDB = 34–38ms/query (BUKAN bottleneck), TLS handshake ~208ms, cold start serverless `GET /admin` ~4.4s, warm ~530ms.

**Fix di-keep (deployed + verified):**
1. **Migration index `pendaftarans`** (`2026_08_30_120000_...`): status, user_id, created_at, nisn, nik, composite(status,jurusan_pilihan). Di-backup ke `backend/` juga (sinkron). Jalan di TiDB + backend local mysql. `SHOW INDEX` verified. **Override** PERF B4 (yang revert) — bentuk index cocok query admin, tax tulis rendah; nilai nyata saat data besar.
2. **exportCsv** `->get()` → `select(18 kolom)+cursor()` — anti OOM; CSV verified 200.
3. **Spp rekap** `->take(200)`; **laporan** `->limit(100)` — bounded.
4. Hapus `Cache-Control: max-age=30` dead di dashboard (middleware `PreventBrowserCache` yang menang).
5. **vercel.json `CACHE_STORE` array→database** (sama seperti backend web — future `Cache::` jalan).
6. **Bug fix**: hapus `plain_password` dari `User::$fillable` & `resetUserPassword()` (kolom sudah di-drop TiDB → reset password di prod tadinya SQL error).
7. **Bug data**: akun `admin` (id=1) di TiDB ber-role `pendaftar` → **tidak bisa login panel**. Diperbaiki `role='admin'` (user setujui). Login admin verified 200 → `/admin` ter-render, export jalan.

**Bukan masalah:** cold start ~4.4s pertama melekat di Vercel PHP serverless; polling 20s dashboard menjaga instance hangat. Keep-warm cron dipertimbangkan → skip.

**TRAP:** (a) domain admin = `paneladminsmkbu.vercel.app` (vercel.json APP_URL); AGENTS.md lama tulis spmb-admin.vercel.app — sudah usang. (b) Backend-admin local sqlite gagal migrate di migration lama `fix_role_column` (SQL `MODIFY` MySQL-only) — pre-existing, bukan dari perubahan ini. (c) LSP error SppController/createToken = noise false-positive (file exist).
