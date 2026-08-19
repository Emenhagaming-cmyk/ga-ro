# IMPLEMENTATION_SUMMARY.md — SPMB SMK Bahrul Ulum

Ringkasan teknis seluruh implementasi proyek.

---

## Arsitektur

```
┌─────────────────────────────────────────────────┐
│                  Frontend (Vue 3)                │
│  Port 5174 — Vite dev server                    │
│  Landing page + 12 halaman view                 │
│  AI Chatbot BISA (Groq-powered)                 │
└────────────────────┬────────────────────────────┘
                     │ HTTP (CORS)
┌────────────────────▼────────────────────────────┐
│                 Backend (Laravel 12)             │
│  Port 8000 — php artisan serve                  │
│  Session-based auth (bukan Sanctum)             │
│  Blade views + API endpoints                    │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│              Database (MySQL Laragon)            │
│  pendaftaran_db — 3 tabel utama                 │
└─────────────────────────────────────────────────┘
```

---

## Frontend Vue 3 — 12 Halaman

| # | Halaman | Route | Akses | Keterangan |
|---|---------|-------|-------|------------|
| 1 | Homepage | `/` | Public | Loading screen, Hero, AboutSchool, Features, News, Footer, FloatingAi |
| 2 | Berita | `/berita` | Public | 6 artikel, search, filter kategori, modal detail |
| 3 | E-Learning | `/e-learning` | Public | 6 materi (video+PDF), 3 kuis, filter kategori |
| 4 | E-Tracer Study | `/e-tracer` | Public | Form tracer alumni, statistik, success banner |
| 5 | Career Center | `/career-center` | Login Siswa | 4 listing magang/lowongan |
| 6 | Koperasi | `/koperasi` | Login Siswa | 16 produk, tab kategori |
| 7 | Produk Siswa | `/produk-siswa` | Login Siswa | 9 karya, tech tags, modal detail |
| 8 | Chat (BISA) | `/chat` | Public | AI chatbot Groq-powered |
| 9 | Login | `/login` | Guest | Form login |
| 10 | Register | `/register` | Guest | Form register |
| 11 | Dashboard Siswa | `/dashboard-siswa` | Login Siswa | Status pendaftaran |
| 12 | Dashboard Admin | `/dashboard-admin` | Login Admin | Live polling, AI insight |

---

## Backend Laravel 12 — Routes

### Public
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Form pendaftaran (create) |
| GET | `/login` | Login page |
| POST | `/login` | Process login |
| GET | `/register` | Register page |
| POST | `/register` | Process register |
| GET | `/forgot-password` | Forgot password form |
| POST | `/forgot-password` | Send reset link |
| GET | `/reset-password/{token}` | Reset password form |
| POST | `/reset-password` | Process reset |
| GET | `/pendaftaran/create` | Registration form |
| POST | `/pendaftaran` | Submit registration |

### Auth (siswa)
| Method | URI | Description |
|--------|-----|-------------|
| POST | `/logout` | Logout |
| GET | `/dashboard-siswa` | Student dashboard |
| PUT | `/pendaftaran/{id}` | Edit own pendaftaran |
| GET | `/profil` | Student profile |

### Admin
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/admin` | Admin dashboard |
| GET | `/pendaftaran` | Data table |
| GET | `/pendaftaran/export` | CSV export |
| GET | `/pendaftaran-snapshot` | Live stats JSON |
| GET | `/pendaftaran/{id}` | Detail view |
| PUT | `/pendaftaran/{id}/status` | Quick status update |
| DELETE | `/pendaftaran/{id}` | Delete pendaftaran |

---

## Database (pendaftaran_db)

### Tabel `users`
- id, name, username, email, password (hashed)
- role: enum('admin','siswa','pendaftar') default 'pendaftar'
- timestamps

### Tabel `pendaftarans` (~45 kolom)
- **Identitas**: nama_lengkap, nama_panggilan, nisn, nik, tempat_lahir, tanggal_lahir, umur, agama, kewarnegaraan, kategori_pendaftar, jenis_kelamin, alamat, rt_rw, kode_pos, no_hp, email
- **Sekolah**: asal_sekolah, gelombang, tahun_lulus, rata_rata_nilai, jurusan_pilihan
- **Keluarga**: jumlah_saudara, anak_ke, status_keluarga, nama_ayah, pendidikan_ayah, pekerjaan_ayah, penghasilan_ayah, alamat_ayah, hp_ayah, nama_ibu, pendidikan_ibu, pekerjaan_ibu, penghasilan_ibu, alamat_ibu, hp_ibu, nama_wali, hubungan_wali, email_orang_tua
- **Lain**: jenis_pembayaran, berkas_tambahan, foto_3x4, kk_file, ijazah_file, sktm_file
- **Status**: status (baru/diproses/diterima/ditolak), data_confirmed, confirmed_at, status_updated_at, user_id (FK)
- **Legacy**: nama_orang_tua, no_hp_orang_tua (nullable)

### Tabel `pendaftaran_drafts`
- key (string), payload (json), timestamps

---

## Komponen Utama

### Vue Components (19)
- **Layout**: Navbar.vue, Footer.vue
- **Sections**: Hero.vue, AboutSchool.vue, About.vue, feature.vue, News.vue, Portal.vue, Services.vue, FloatingCards.vue
- **Chatbot**: ChatHeader.vue, ChatInput.vue, ChatMessages.vue, FloatingAi.vue, TypingIndicator.vue
- **Common**: CursorGlow.vue, BackgroundFX.vue, FadeSection.vue
- **Loading**: LoadingScreen.vue

### Vue Composables (5)
- `useAuth.js` — token-based auth (legacy)
- `useAuthSession.js` — session-based via /auth-status polling
- `useToast.js` — toast notifications
- `useTheme.js` — empty
- `useScroll.js` — empty

### Laravel Controllers (3)
- `AuthController.php` — 8 methods (register, login, logout, authStatus, forgotPassword, resetPassword, profile)
- `PendaftaranController.php` — 9 methods (index, myDashboard, create, store, show, update, updateStatus, destroy, snapshot, exportCsv)
- `Controller.php` — base

### Laravel Middleware (4)
- `CheckRole.php` — role guard
- `Cors.php` — allow localhost:5174
- `HandleTokenMismatch.php` — 419 recovery with draft save
- `PreventBrowserCache.php` — no-cache headers

---

## Fitur Kunci

### Multi-step Registration Form (5 langkah)
1. Data Diri (nama, NISN, TTL, umur, agama, jenis kelamin, alamat, no HP, email)
2. Data Sekolah (asal sekolah, gelombang, tahun lulus, rata-rata nilai, jurusan)
3. Data Orang Tua (ayah & ibu: nama, pendidikan, pekerjaan, penghasilan, alamat, HP)
4. Upload Berkas (foto 3x4, KK, ijazah, SKTM) + data wali + jenis pembayaran
5. Konfirmasi (review semua data + centang persetujuan)

### Draft Persistence
- Guest submit → data disimpan ke session + `pendaftaran_drafts` table
- Login/register → redirect ke form, data terisi ulang otomatis
- Submit sukses → draft dihapus; validasi gagal → draft tetap

### Admin Dashboard
- Stats cards (total, baru, diproses, diterima)
- Live polling 5 detik via `/pendaftaran-snapshot`
- AI insight (RegistrationInsightService)
- Quick status change (dropdown auto-submit)
- CSV export (semicolon, UTF-8 BOM, label Indonesia)

### AI Chatbot BISA
- Powered by Groq API
- Knowledge base dari `api/knowledge/**`
- Vite middleware `/api/chat`
- Fallback reply jika API error

### E-Learning
- 6 materi (HTML, MySQL, JavaScript, Jaringan, PHP, Relasi Tabel)
- Video YouTube + PDF download
- 3 kuis interaktif (Google Forms)
- Filter kategori: Pemrograman, Jaringan, Basis Data, Multimedia

### E-Tracer Study
- Form lengkap: data diri, status kerja/kuliah, data perusahaan/universitas
- Dropdown 48 nama alumni
- Statistik: 42% bekerja, 38% kuliah, 5% PKL, 3% wirausaha
- Success banner + info banner

---

## Credentials

| Item | Value |
|------|-------|
| Admin | username `admin` / password `admin123` |
| Siswa demo | username `siswa` / password `siswa123` |
| MySQL | root (tanpa password), DB `pendaftaran_db` |
| Backend URL | `http://localhost:8000` |
| Frontend URL | `http://localhost:5174` |

---

## Command Penting

```powershell
# Backend
cd C:\Users\LENOVO\lomba\ga-ro\backend
php artisan serve --port=8000
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan view:cache

# Frontend
cd C:\Users\LENOVO\lomba\ga-ro
npm run dev
npm run build

# Test
npx playwright test
```

---

## File Penting

| File | Keterangan |
|------|------------|
| `AGENTS.md` | Konteks permanen proyek |
| `PROGRESS.md` | Log pekerjaan per sesi |
| `IMPLEMENTATION_SUMMARY.md` | Dokumen ini |
| `src/router/index.js` | 12 routes Vue |
| `src/components/layout/Navbar.vue` | Navbar dengan 3 dropdown |
| `backend/routes/web.php` | 19 routes Laravel |
| `backend/app/Http/Controllers/PendaftaranController.php` | CRUD + export |
| `backend/app/Http/Controllers/AuthController.php` | Auth + reset |
| `src/views/ELearningView.vue` | Halaman E-Learning |
| `src/views/ETracerView.vue` | Halaman E-Tracer Study |

---

## Deployment Production (Vercel) — Sesi 12p

- **Frontend**: project `lomba` → `https://bhapppp.vercel.app` (akun zakkyilhamf-7419, team zakkys-projects-99c4bf23). SPA rewrite di root `vercel.json` (`/((?!api/).*)` → `/index.html`); `.vercelignore` root (backend/vendor dll); `VITE_BACKEND_URL=https://spmb-backend-self.vercel.app` (env project). Button SPMB navbar → `${BACKEND}/login`.
- **Backend**: project `spmb-backend` → `https://spmb-backend-self.vercel.app` (vercel-php 0.7.4, `backend/api/index.php`). `spmb-backend.vercel.app` dipegang akun lain — jangan dipakai.
- **Env backend (Vercel project)**: APP_KEY, APP_URL, FRONTEND_URL, DB_* (TiDB gateway ap-southeast-1:4000, DB pendaftaran_db), `MYSQL_ATTR_SSL_CA=/var/task/user/certs/isrgrootx1.pem`.
- **Trap vercel-php**: JANGAN upload `vendor` (builder jalankan `composer install` → dev deps dihapus → ENOENT). Path runtime = `/var/task/user/...`. Form action http → butuh `trustProxies(at: '*')` di `bootstrap/app.php`.
- **Deploy ulang**: di root → `vercel.cmd deploy --prod --yes`; di backend → sama dengan workdir backend (CLI 58.9.0, auth tersimpan di `AppData\Roaming\xdg.data\com.vercel.cli\auth.json`).

---

## Optimasi Performa — Sesi 12r

- **Lazy-load route**: semua view di `src/router/index.js` → `import()` per halaman. Bundle awal 253KB JS + 198KB CSS + 252KB font (4×woff2) = 703KB → **index ±105KB** (103KB JS gzip 41KB + 2.3KB CSS, 0 font).
- **Font Awesome dihapus total**: `main.js` tidak import `all.min.css`; 4 ikon dipakai (`angle-left`, `envelope`, `instagram`, `tiktok`) diganti SVG inline (path FA6) di `ChatHeader.vue` & `Footer.vue`. CSS `.back-btn svg` tetap `stroke: currentColor`.
- **Polling singleton**: `useAuthSession.js` guard `intervalBound` → 1 interval `/auth-status` per 30s (dulu 7).
- **Guard non-blocking**: `router.beforeEach` pakai cache `sessionStorage`; `fetchStatus()` background; `await` hanya saat cache ≠ siswa.
- **Font**: Google Fonts dari `@import` → `<link>` + preconnect (`index.html`, `create.blade.php`); `%VITE_BACKEND_URL%` di index.html di-substitusi Vite.
- **Gambar WebP** (GD, q82): `sklh.jpg 146→115KB`, `pmb_smkbu.jpg 100→91KB`; hapus `ber.png` (138KB, unused). Backend logo 720×720/350KB → 240×240/59KB.
- **Backend deps**: hapus `laravel/pail` (build `--no-dev` → `PailServiceProvider not found` saat packages cache menyebutnya; root cause build 500). `composer.json`/lock valid.
- **Config-cache build dilewati**: cold-start Laravel di Vercel Hobby tak banyak bisa dihemat (region fixed); `route:cache` tak mungkin (2 route closure di `routes/web.php:10-11`). `vercel.json` backend kembalikan `APP_CONFIG_CACHE=/tmp/config.php`.
- **Verifikasi**: `npm run build` OK; frontend & backend deployed (Ready); smoke `/login`, `/auth-status`, `/logo.png` 200. Frontend dari IP dev kena WAF 429 (IP-bound, bukan deploy).

---

## Autentikasi Lintas-Domain (mobile) — Sesi 12u

Browser mobile memblokir cookie third-party → fetch `/auth-status` lintas-domain selalu balas guest → navbar landing salah tampil "SPMB" padahal user login. Solusi: **status auth dikirim lewat URL** (`?auth=`), bukan cookie.

- `backend/app/helpers.php` (baru): `frontendAuthUrl()` → `FRONTEND_URL . '/?auth=' . base64_encode(JSON)`; payload `{logged_in, role, name, has_pendaftaran, status}` konsisten dengan `authStatus()`; dipakai oleh semua link ke landing di blade (9 link, 2 file), `AuthController::logout()`, dan branch `logout` di `HandleTokenMismatch` (419/token basi → payload siswa karena belum logout).
- Autoload composer: `"files": ["app/helpers.php"]`.
- `useAuthSession.js`: IIFE `applyAuthQuery()` parse `?auth=` (atob → JSON → sessionStorage `spmb_session_status` → `history.replaceState` strip param) + **guard anti-downgrade** `fetchStatus` (update hanya jika `data.logged_in || !session.value.logged_in`; catch juga pertahankan cache login).
- Verifikasi production (curl UA iPhone): dashboard siswa → 6 link landing payload siswa; logout normal → payload guest; logout token basi (419) → payload siswa.

---

## Panel Admin Terpisah — Sesi 12y/12z

Web admin dipisah ke domain sendiri `https://spmb-admin.vercel.app` (project Vercel `spmb-admin`), kode di `backend-admin\` (salinan backend). DB sama (TiDB). Login admin tidak lagi di form utama.

- **Web utama (backend)**: route admin DIHAPUS (GET /admin → 404); `AuthController::login` menolak role admin + pesan arahkan ke panel; view admin (`pendaftaran/index.blade.php`, `show.blade.php`) pindah ke panel.
- **Panel (backend-admin)**: `routes/web.php` admin-only (`/`→`/admin`; `/login` guest; `/logout`; forgot/reset-password; group `auth+role:admin`: dashboard, list, export CSV, snapshot, show, PUT status, DELETE). `AuthController::login` hanya role admin ("Hanya akun admin..."), logout → `/login`. View siswa dihapus. UI polish: navbar brand + badge "Panel Admin" + "Buka Web Utama ↗", login page 480px.
- **Vercel**: project direname `backend-admin` → `spmb-admin`; env production = APP_KEY prod (`base64:gtKJvpBuztMINYQwxnKgMFIHQaYvy3WnzBS0+ItkX5g=`), APP_URL, FRONTEND_URL, DB_* TiDB, `MYSQL_ATTR_SSL_CA=/var/task/user/certs/isrgrootx1.pem`, `SESSION_SAME_SITE=lax` + `SESSION_SECURE_COOKIE=true` (web utama: `none`). Alias: `vercel alias set <deployment-url> spmb-admin.vercel.app` SETIAP deploy (tidak otomatis).
- **Trap project Vercel baru**: deployment protection default (`ssoProtection`) → matikan via API `PATCH https://api.vercel.com/v9/projects/spmb-admin?teamId=zakkys-projects-99c4bf23` body `{"ssoProtection":null}`. `vercel link` membuat `.env.local` → hapus (MissingAppKeyException).
- **Trap env**: env Vercel project + `vercel.json` env masuk PHP runtime; `.env`/`.env.*` exclude via `.vercelignore` (tapi `!.env.example` di-upload). `DB_CONNECTION` WAJIB di vercel.json/env (fallback Laravel 12 = sqlite → `QueryException: Database file at path [pendaftaran_db] does not exist`).
- **Debug 500 "AuthenticationException Unauthenticated" (guest GET /admin)**: sebenarnya redirect login BEKERJA — debug `withExceptions()->render()` sementara di `bootstrap/app.php` menangkap SEMUA exception termasuk AuthenticationException → selalu render 500. Hapus render debug → redirect `/login` normal. Pelajaran: debug render di `withExceptions` mengalahkan `unauthenticated()` handler.
- **Verifikasi production**: GET / → 302 /login 200; login admin → dashboard 200 (statTotal + "Pantau Data Pendaftar"); logout → /login; login siswa → ditolak ("Hanya akun admin"); `/pendaftaran` 200, export CSV 200 (text/csv), snapshot 200, show 200, `/up` 200.
- `.env` lokal panel = sqlite + APP_KEY lokal (`base64:9uLdV6nujM/LUS2A3S3ekei8uhIjks1qgm8MfKAlESI=`) — hanya artisan lokal, JANGAN dipakai production.

---

## TODO

- [ ] Dynamic School Statistics (admin-managed) — disetujui user
- [ ] Universal Search — disetujui user
- [ ] Info SPMB Center (syarat/biaya/beasiswa/timeline)
- [ ] Profil Sekolah (Visi Misi)
- [ ] Fasilitas Sekolah
- [ ] Hubungi Kami
- [ ] Galeri Kegiatan
- [ ] Deployment (Vercel + TiDB)
