# IMPLEMENTATION_SUMMARY.md — SPMB SMK Bahrul Ulum

Ringkasan teknis seluruh implementasi. File ini adalah **sumber utama** — `backend/` dan `backend-admin/` punya salinan yang disinkronkan.

---

## Arsitektur

```
┌─────────────────────────────────────────────────┐
│              Frontend (Vue 3 + Vite)             │
│  smkbu-sby.vercel.app — port dev 5174           │
│  Landing page + 12 halaman view                  │
│  AI Chatbot BISA (Groq-powered)                  │
└────────────────────┬────────────────────────────┘
                     │ HTTP (CORS)
┌────────────────────▼────────────────────────────┐
│             Backend (Laravel 12)                 │
│  pendaftaranspmb.vercel.app — port dev 8000     │
│  Session-based auth (bukan Sanctum)              │
│  Blade views + API endpoints                     │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│          Database (MySQL — TiDB production)      │
│  pendaftaran_db — 8 tabel utama                  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│         Panel Admin (Laravel 12 — terpisah)      │
│  paneladminsmkbu.vercel.app                     │
│  DB sama (TiDB), login admin-only               │
│  Sidebar layout + Chart.js + CRUD               │
└─────────────────────────────────────────────────┘
```

---

## Domain Production

| Service | Domain | Project Vercel |
|---------|--------|----------------|
| Landing page (Vue) | `smkbu-sby.vercel.app` | `lomba` |
| Backend (Laravel) | `pendaftaranspmb.vercel.app` | `spmb-backend` |
| Panel Admin | `paneladminsmkbu.vercel.app` | `spmb-admin` |
| Legacy | `bhapppp.vercel.app` | (307 → smkbu-sby) |

---

## Frontend Vue 3 — 12 Halaman

| # | Halaman | Route | Akses | Keterangan |
|---|---------|-------|-------|------------|
| 1 | Homepage | `/` | Public | Hero, AboutSchool, SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview, TabunganBanner |
| 2 | Berita | `/berita` | Public | List + detail dari backend API |
| 3 | E-Learning | `/e-learning` | Public | 6 materi (video+PDF), 3 kuis |
| 4 | E-Tracer Study | `/e-tracer` | Public | Form tracer alumni, statistik |
| 5 | Career Center | `/career-center` | Login Siswa | Lowongan + lamaran |
| 6 | Koperasi | `/koperasi` | Login Siswa | Produk + keranjang + checkout |
| 7 | Produk Siswa | `/produk-siswa` | Login Siswa | Galeri karya |
| 8 | Chat (BISA) | `/chat` | Public | AI chatbot Groq-powered |
| 9 | Login | `/login` | Guest | Redirect ke backend |
| 10 | Register | `/register` | Guest | Redirect ke backend |
| 11 | Dashboard Siswa | `/dashboard-siswa` | Login Siswa | Blade backend |
| 12 | Dashboard Admin | `/dashboard-admin` | Login Admin | Blade panel admin |

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
| GET | `/berita` | Berita list (JSON) |
| GET | `/berita/{slug}` | Berita detail (JSON) |
| GET | `/lowongan` | Lowongan list (JSON) |
| GET | `/lowongan/count` | Lowongan count (JSON) |
| GET | `/lowongan/{lowongan}` | Lowongan detail |
| GET | `/auth-status` | Auth status (JSON) |
| GET | `/csrf-token` | CSRF token (JSON) |
| GET | `/school-stats` | School stats (JSON) |

### Auth (siswa)
| Method | URI | Description |
|--------|-----|-------------|
| POST | `/logout` | Logout |
| GET | `/dashboard-siswa` | Student dashboard |
| PUT | `/pendaftaran/{id}` | Edit own pendaftaran |
| GET | `/profil` | Student profile |
| GET | `/spp` | SPP bills siswa |
| GET | `/tabungan` | Tabungan siswa |
| POST | `/tabungan` | Setor/tarik tabungan |
| POST | `/lamaran` | Apply lamaran |
| GET | `/lamaran/saya` | List lamaran saya |
| DELETE | `/lamaran/{lamaran}` | Cancel lamaran |
| GET | `/dashboard-siswa/snapshot` | Status snapshot (JSON) |
| GET | `/pendaftaran/bukti` | Download bukti diterima (PDF) |

### Auth (kasir/guru/admin)
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/spp/kasir` | SPP kasir view |
| POST | `/spp/pay` | Input pembayaran SPP |
| GET | `/admin/spp` | SPP rekap (JSON) |
| GET | `/admin/spp/rekap` | SPP rekap view |
| GET | `/spp/ortu/{user}` | SPP ortu (signed URL) |

### Admin (web utama — DITOLAK, arahkan ke panel)
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/admin` | 404 (dihapus) |

---

## Database (pendaftarans — 8 tabel)

### `users`
- id, name, username, email, password (hashed)
- role: enum('admin','siswa','pendaftar','guru','kasir') default 'siswa'
- timestamps

### `pendaftarans` (~45 kolom)
- **Identitas**: nama_lengkap, nama_panggilan, nisn, nik, tempat_lahir, tanggal_lahir, umur, agama, kewarnegaraan, kategori_pendaftar, jenis_kelamin, alamat, rt_rw, kode_pos, no_hp, email
- **Sekolah**: asal_sekolah, gelombang, tahun_lulus, rata_rata_nilai, jurusan_pilihan (RPL/TKJ/AKL)
- **Keluarga**: jumlah_saudara, anak_ke, status_keluarga, nama_ayah, pendidikan_ayah, pekerjaan_ayah, penghasilan_ayah, alamat_ayah, hp_ayah, nama_ibu, pendidikan_ibu, pekerjaan_ibu, penghasilan_ibu, alamat_ibu, hp_ibu, nama_wali, hubungan_wali, email_orang_tua
- **Lain**: jenis_pembayaran, berkas_tambahan, foto_3x4, kk_file, ijazah_file, sktm_file
- **Status**: status (baru/diproses/diterima/ditolak), data_confirmed, confirmed_at, status_updated_at, user_id (FK)
- Legacy: nama_orang_tua, no_hp_orang_tua (nullable)
- Indexes: status, user_id, created_at, nisn, nik, composite(status,jurusan_pilihan)

### `pendaftaran_drafts`
- key (string), payload (json), timestamps

### `spp_bills`
- user_id (FK), periode (Y-m), nominal, status (belum/lunas), jatuh_tempo, timestamps

### `spp_payments`
- bill_id (FK), metode (tunai/transfer), amount, paid_at, input_by (FK users), timestamps

### `tabungans`
- user_id (FK cascade), type enum('setor','tarik'), amount (unsignedBigInteger), description (nullable), timestamps

### `koperasi_orders`
- user_id (FK), items (json), total, metode, status, timestamps

### `beritas`
- title, slug (unique), category, category_color, excerpt, content, image_path, author, published_at, featured, read_time, is_published, user_id (FK), timestamps

---

## Komponen Utama

### Vue Components
- **Layout**: Navbar.vue, Footer.vue
- **Sections**: Hero.vue, AboutSchool.vue, SpmbBanner.vue, BeritaPreview.vue, CareerPreview.vue, KoperasiPreview.vue, ProdukPreview.vue, TabunganBanner.vue
- **Chatbot**: ChatHeader.vue, ChatInput.vue, ChatMessages.vue, FloatingAi.vue, TypingIndicator.vue
- **Common**: CursorGlow.vue, BackgroundFX.vue, LazyMount.vue, ContactModal.vue
- **Loading**: LoadingScreen.vue

### Vue Composables
- `useAuthSession.js` — session-based via /auth-status polling + ?auth= URL payload
- `useToast.js` — toast notifications

### Laravel Controllers (Backend)
- `AuthController.php` — register, login, logout, authStatus, forgotPassword, resetPassword, profile
- `PendaftaranController.php` — CRUD, myDashboard, myDashboardSnapshot, rules, handleFileUploads, snapshot, exportCsv, downloadBukti
- `SppController.php` — index, store, adminIndex, kasirIndex, rekapIndex, ortuIndex
- `TabunganController.php` — index (saldo aggregate), store, adminIndex, adminShow
- `LowonganController.php` — index (search/filter/sort), show
- `LamaranController.php` — store (CV upload), myApplications, show, cancel
- `BeritaApiController.php` — index (JSON), show (JSON)

### Laravel Controllers (Panel Admin)
- `PendaftaranController.php` — dashboard, index, show, exportCsv, snapshot, updateStatus, destroy, chartData, laporan, resetUserPassword
- `TabunganController.php` — adminIndex, adminShow
- `BeritaController.php` — CRUD (index, create, store, edit, update, destroy)

### Laravel Middleware
- `CheckRole.php` — role guard
- `Cors.php` — allow origins (smkbu-sby.vercel.app, localhost:5174)
- `HandleTokenMismatch.php` — 419 recovery with draft save
- `PreventBrowserCache.php` — no-cache headers
- `SecurityHeaders.php` — X-Content-Type-Options, X-Frame-Options, HSTS, Referrer-Policy, Permissions-Policy

---

## Fitur Kunci

### Multi-step Registration Form (5 langkah)
1. Data Diri → 2. Data Sekolah → 3. Data Orang Tua → 4. Upload Berkas → 5. Konfirmasi
- Draft persistence: session + DB `pendaftaran_drafts` + cookie

### SPP (Sumbangan Pendidikan)
- Kasir input pembayaran → status langsung terlihat siswa/guru/admin/ortu
- Link ortu tanpa akun = Laravel signed URL
- Auto-tagihan: `php artisan spp:generate`

### Tabungan Siswa
- Setor/tarik dengan saldo validation
- Saldo via aggregate SQL (SUM CASE WHEN)
- Banner landing menampilkan saldo aktif

### Security Hardening
- Rate limiting, CSRF aktif, security headers
- XSS chatbot (DOMPurify), upload hardening (mimetypes)
- Race condition fix (DB::transaction + lockForUpdate)
- IDOR prevention

---

## Commands

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

# Panel Admin (same backend commands, different folder)
cd C:\Users\LENOVO\lomba\ga-ro\backend-admin
```

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

## Deployment

### Vercel Deploy Pattern
```powershell
# Backend
cd C:\Users\LENOVO\lomba\ga-ro\backend
& "C:\nvm4w\nodejs\vercel.cmd" deploy --prod --yes
& "C:\nvm4w\nodejs\vercel.cmd" alias set <deployment-url> pendaftaranspmb.vercel.app

# Panel Admin
cd C:\Users\LENOVO\lomba\ga-ro\backend-admin
& "C:\nvm4w\nodejs\vercel.cmd" deploy --prod --yes
& "C:\nvm4w\nodejs\vercel.cmd" alias set <deployment-url> paneladminsmkbu.vercel.app

# Frontend
cd C:\Users\LENOVO\lomba\ga-ro
& "C:\nvm4w\nodejs\vercel.cmd" deploy --prod --yes
```

### Trap Vercel
- `vercel link` bikin `.env.local` → hapus (MissingAppKeyException)
- Deploy kadang `fetch failed` → retry
- `/api/*` prefix bentrok Vercel PHP runtime → route tanpa prefix
- `config/database.php` harus `PDO::MYSQL_ATTR_SSL_CA` (bukan `Mysql::ATTR_SSL_CA`) untuk PHP 8.3
- `CACHE_STORE` harus `database` (bukan `array`) agar throttle jalan
- `vercel alias set` tidak otomatis — dijalankan SETIAP deploy

---

## Last Updated: 2026-08-30
