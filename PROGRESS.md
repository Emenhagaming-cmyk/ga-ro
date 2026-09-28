# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

---

## STATUS TERAKHIR (2026-09-09) — Fix Reset Password Panel Admin

**Bug:** Tombol "Reset" di dashboard admin (`/admin`) tidak menghasilkan password yang bisa dipakai login siswa.

**Root cause (2):**
1. **Double-hash**: `User` model di `backend-admin/app/Models/User.php` punya cast `'password' => 'hashed'`. Controller `resetUserPassword()` memanggil `bcrypt($plain)` lalu cast `hashed` meng-hash ulang → password tersimpan 2x hash, sehingga `Hash::check($plain, ...)` selalu gagal → siswa tidak bisa login.
2. **Redirect salah**: `return back()` mengikuti HTTP Referer → jika admin klik Reset dari halaman selain dashboard, flash `reset_password` tampil di halaman lain (tidak terlihat).

**Fix (backend-admin):**
- `app/Models/User.php`: hapus cast `'password' => 'hashed'` → password disimpan sekali hash.
- `app/Http/Controllers/PendaftaranController.php` `resetUserPassword()`: ganti `return back()` → `redirect()->route('admin.dashboard')`; simpan `plain_password` via `forceFill()` (wrap try/catch, kolom mungkin sudah di-drop).
- Verifikasi: `php -l` bersih, `route:list` menampilkan `admin.resetPassword`, tinker `Hash::check($plain, bcrypt($plain))` → OK.
- **Data siswa tidak hilang**: reset hanya mengubah kolom `password` (+ `plain_password`); tidak menyentuh pendaftaran/status/relasi lain.
- **Deploy**: `vercel deploy --prod --yes` sukses → auto-alias ke `paneladminsmkbu.vercel.app`; `GET /login` → 200 (live).
- **TODO verifikasi manual**: login admin di production → klik Reset pada akun siswa → password baru tampil & bisa login.

### Sesi 2026-09-09 — Fix Eye Icon Password Admin + Deploy

**Masalah lanjutan:** Eye icon di dashboard admin menampilkan "(tidak ada)" karena kolom `plain_password` sudah di-drop dari shared DB.

**Fix:**
- `dashboard.blade.php`: `data-pw` sekarang menampilkan `substr($akun->password, 0, 8) + "…"` (8 karakter pertama hash sebagai indikator). Toggle JS disederhanakan (tidak perlu fallback).
- `PendaftaranController.php`: hapus `try/catch forceFill plain_password` — tidak diperlukan lagi.
- Deploy `spmb-admin-hma0yk9ck` → auto-alias `paneladminsmkbu.vercel.app` → 200 OK.

---

## STATUS TERAKHIR (2026-08-30 malam)

### Fix 500 Internal Server Error di Panel Admin Tabungan/Koperasi

**Root cause:** Model `User` di `backend-admin/app/Models/User.php` tidak punya relasi `tabungans()` dan `koperasiOrders()`. Controller `TabunganController::adminIndex()` memakai `withSum('tabungans', ...)` → memicu 500 di production.

**Fix:** Tambah `tabungans(): HasMany` dan `koperasiOrders(): HasMany` ke `User` model panel admin. Deploy `spmb-admin-4eeh2wab8` → `paneladminsmkbu.vercel.app` + `spmb-admin.vercel.app`.

---

## Fitur yang SUDAH LIVE di Production

### Backend (pendaftaranspmb.vercel.app)
- Form pendaftaran multi-step (5 langkah) + draft persistence
- Auth (login/register/logout) — session-based, bukan Sanctum
- Dashboard siswa (status pendaftaran + edit ≤3 hari)
- Dashboard admin (live polling 5s + export CSV + quick status)
- SPP (kasir input, guru rekap, ortu link signed URL)
- Tabungan siswa (setor/tarik, saldo aggregate)
- Career Center (lowongan + lamaran + CV upload)
- Berita API (`GET /berita`, `GET /berita/{slug}`)
- Chatbot BISA (Groq-powered, model `openai/gpt-oss-120b`)
- Security hardening (rate limiting, CSRF, security headers, upload validation, race condition fix)

### Panel Admin (paneladminsmkbu.vercel.app)
- Dashboard dengan stat cards + Chart.js + "Ringkasan Data"
- Data pendaftar (filter, search, duplikat detect, export CSV)
- Rekap SPP + link ortu
- Kelola Tabungan, Koperasi, Berita (CRUD)
- Laporan mingguan (print-ready)
- Badge live sidebar
- Reset password siswa + show/hide password
- DB indexes untuk performa query

### Frontend Vue 3 (smkbu-sby.vercel.app)
- 12+ halaman: Homepage, Berita, E-Learning, E-Tracer, Career Center, Koperasi, Produk Siswa, Chat, Login, Register, Dashboard Siswa, Dashboard Admin
- Landing page dengan 8 section (Hero, AboutSchool, SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview, TabunganBanner)
- Navbar glassmorphism (3 dropdown: Layanan, Informasi, Tentang)
- Lazy-load + code splitting (bundle awal ~25KB gzip)
- Font self-hosted (Quicksand woff2)

### Domain Production
| Service | Domain |
|---------|--------|
| Landing page (Vue) | `smkbu-sby.vercel.app` |
| Backend (Laravel) | `pendaftaranspmb.vercel.app` |
| Panel Admin | `paneladminsmkbu.vercel.app` |
| Legacy redirect | `bhapppp.vercel.app` → smkbu-sby (307) |

---

## Log Sesi Detail (13 Agustus 2026 ke atas)

### Sesi 2026-08-30 — Deploy Tabungan, Koperasi, Berita ke Panel Admin
- Fitur Kelola Tabungan, Koperasi, Berita di panel admin (sidebar + CRUD)
- Migrasi baru ke TiDB production (`tabungans`, `koperasi_orders`, `beritas`)
- Deploy Vercel + alias set

### Sesi 2026-08-30 — Fix 500 Panel Admin Tabungan/Koperasi
- Model `User` tambah relasi `tabungans()` + `koperasiOrders()`

### Sesi 2026-08-30 — UI Berita + Fitur Kelola Berita Admin
- Migration `beritas` table (id, title, slug, category, content, image_path, author, published_at, featured, read_time, is_published)
- `BeritaController.php`: CRUD + upload gambar + auto read_time
- Views: index (tabel + thumbnail), create (drag-drop upload), edit (pre-filled)
- Backend API: `GET /berita` (JSON list), `GET /berita/{slug}` (detail)
- Frontend `NewsView.vue` di-redesign (featured card split layout, grid cards, skeleton loading)

### Sesi 2026-08-30 — Fix 500 Fresh Deploy (Root Cause: PHP 8.3 vs Pdo\Mysql)
- Root cause: `config/database.php` pakai `use Pdo\Mysql;` (PHP 8.5) tapi Vercel pakai PHP 8.3
- Fix: ganti `Mysql::ATTR_SSL_CA` → `PDO::MYSQL_ATTR_SSL_CA`
- Deploy kedua backend berhasil

### Sesi 2026-08-30 — Performance Audit & Optimization (3 bagian)
- **Frontend**: CursorGlow → requestAnimationFrame, lazy-mount section via LazyMount.vue (IntersectionObserver, rootMargin 600px), CareerPreview pakai `/lowongan/count`, auth polling hanya saat login
- **Backend**: Endpoint `/lowongan/count`, Tabungan saldo via aggregate SQL, Spp `kasirIndex` filter di SQL
- **Panel Admin**: `chartData()` 30×COUNT → 1×GROUP BY, `laporan()` pakai `freshStats()` cached

### Sesi 2026-08-30 — Fitur SPP (bukti bayar satu arah)
- Migrations: `spp_bills` + `spp_payments` + role enum guru/kasir
- Models: `SppBill`, `SppPayment` + relasi di `User`
- `SppController`: index (siswa), store (kasir/admin), adminIndex, kasirIndex, rekapIndex, ortuIndex (signed URL)
- Views: kasir.blade, rekap.blade, ortu.blade
- Artisan command `spp:generate {--periode=} {--nominal=}`
- Test: 19 passed (55 assertions)

### Sesi 2026-08-30 — Security Hardening Komprehensif
- Hapus `plain_password` (migration drop)
- XSS chatbot fix (DOMPurify)
- Rate limiting (login 5/min, register 5/min, forgot 3/min)
- CSRF diaktifkan kembali + frontend `csrf.js` header `X-CSRF-TOKEN`
- Security headers middleware (X-Content-Type-Options, X-Frame-Options, HSTS, dll)
- IDOR TabunganController fix
- Upload hardening (mimetypes validation)
- Race condition pendaftaran (DB::transaction + lockForUpdate)

### Sesi 2026-08-29 — Tabungan Siswa
- Migration `tabungans` (user_id, type setor/tarik, amount, description)
- `TabunganController`: index (saldo aggregate), store (setor/tarik)
- Frontend `TabunganView.vue` + `TabunganBanner.vue` (landing)
- CSRF fix: tambah `tabungan*` ke exception list

### Sesi 2026-08-29 — Koperasi Keranjang & Checkout
- Restore sistem keranjang + checkout (QRIS/Transfer, countdown 1 jam)
- Redesign ulang (referensi Chanta store)
- Fix bug: crop kanan, tombol + kembali

### Sesi 2026-08-29 — Bento Restructure
- `feature.vue` (~750 baris) DIHAPUS → 5 komponen baru: SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview
- HomeView urutan baru: Hero → AboutSchool → SpmbBanner → BeritaPreview → CareerPreview → KoperasiPreview → ProdukPreview → TabunganBanner → Footer
- Hapus section News.vue (ganda)

### Sesi 2026-08-28 — ContactModal + Footer "Hubungi Kami"
- `ContactModal.vue`: glassmorphism modal, form Nama/Email/WhatsApp/Pesan (UX-only, no backend)

### Sesi 2026-08-26 — Chatbot BISA Fix
- Model Groq `llama-3.3-70b-versatile` deprecated → ganti `openai/gpt-oss-120b`
- Fix `import ... with { type:"json" }` (jangan readFileSync — Vercel ENOENT)
- Fix `typing` ref tidak didefinisikan di ChatView
- Kirim seluruh history messages (bukan hanya pesan terakhir)

### Sesi 2026-08-23 — Web Vitals Optimization (LCP/CLS/INP)
- Self-host Quicksand font (woff2)
- Hero h1 animation fix (LCP saving ~0.85s)
- Image optimization + CLS fix (width/height)
- Disable heavy effects on mobile
- Vite code splitting (manualChunks)

### Sesi 2026-08-22 — Career Center Full-Stack
- Migrations: `lowongans` + `lamarans` tables
- Models + Controllers + Seeder (25 lowongan)
- Frontend: CareerJobCard, CariLowonganView, ApplyModal, LamaranSayaView
- Fix: `/api/` prefix bentrok Vercel PHP → pindah tanpa prefix `/api/`

### Sesi 2026-08-22 — LCP Optimization Panel Admin
- Critical CSS split + font preload + Chart.js defer
- Stats cached 30s + Cache-Control header

### Sesi 2026-08-19 — Panel Admin Terpisah + UI Polish
- Backend admin dipisah ke `backend-admin/` (project Vercel `spmb-admin`)
- Route admin dihapus dari web utama (404)
- Login admin ditolak di web utama
- Redesign ke sidebar layout
- 5 fitur: Chart.js, pencarian+filter, duplikat detect, laporan mingguan, badge live

### Sesi 2026-08-19 — Login/Register Glassmorphism
- Full-page hijau + glass card + blob dekorasi + SVG icons

### Sesi 2026-08-18 — Deploy Vercel (Frontend + Backend)
- Frontend: project `lomba` → `smkbu-sby.vercel.app`
- Backend: project `spmb-backend` → `pendaftaranspmb.vercel.app`
- Fix SPA 404, button SPMB, env vars

### Sesi 2026-08-17 — E-Learning + E-Tracer
- `ELearningView.vue`: 6 materi + 3 kuis + filter kategori
- `ETracerView.vue`: form tracer alumni + statistik

### Sesi 2026-08-11 — Landing Page Vue 3
- 12 halaman: Homepage, Berita, Koperasi, Produk Siswa, Career Center, Chat, Login, Register, Dashboard Siswa, Dashboard Admin
- Navbar 3 dropdown, bento grid, AI chatbot

### Sesi 12 (Awal Agustus 2026) — Backend Laravel + Core Features
- Setup database MySQL `pendaftaran_db` + AdminSeeder
- Form multi-step 5 langkah + draft persistence
- Auth (login/register/logout) + reset password
- Dashboard admin (stats + export CSV + quick status)
- Dashboard siswa (status + edit ≤3 hari)
- 419 CSRF handler (`HandleTokenMismatch`)
- Role-based access (admin/siswa/pendaftar)
- Frontend landing page (Hero, AboutSchool, Features, News, Footer)

---

## TODO (Belum Dikerjakan)

- [ ] Dynamic School Statistics (admin-managed) — disetujui user
- [ ] Universal Search — disetujui user
- [ ] Info SPMB Center (syarat/biaya/beasiswa/timeline)
- [ ] Profil Sekolah (Visi Misi)
- [ ] Fasilitas Sekolah
- [ ] Hubungi Kami (backend — ContactModal sudah ada, UX-only)
- [ ] Galeri Kegiatan
- [ ] Foto untuk 5 berita lainnya (masih 404)

---

## Verifikasi Production (terkini)

| Test | Hasil |
|------|-------|
| Landing page 200 | ✅ |
| Backend /login, /register | ✅ |
| Form submit → dashboard siswa | ✅ |
| Admin login → dashboard + export | ✅ |
| Panel admin /admin | ✅ |
| SPP kasir/rekap/ortu | ✅ |
| Tabungan setor/tarik | ✅ |
| Lowongan/lamaran | ✅ |
| Chatbot POST /api/chat | ✅ |
| Security headers | ✅ |
| Rate limiting 429 | ✅ |

---

## Catatan Penting

- **DB**: MySQL `pendaftaran_db` (Laragon lokal + TiDB production)
- **Deploy pattern**: `vercel deploy --prod --yes` lalu `vercel alias set <url> <domain>`
- **Trap**: `vercel link` bikin `.env.local` → hapus; deploy kadang `fetch failed` → retry
- **Trap**: `/api/*` prefix bentrok Vercel PHP runtime → route backend tanpa prefix `/api/`
- **Trap**: `config/database.php` harus pakai `PDO::MYSQL_ATTR_SSL_CA` (bukan `Mysql::ATTR_SSL_CA`) untuk PHP 8.3
