# IMPLEMENTATION_SUMMARY.md — SPMB SMK Bahrul Ulum

Rangkuman implementasi sistem SPMB online. Backend 100% Laravel blade (session auth), frontend landing page Vue 3 terpisah.

---

## Arsitektur (realita — bukan API lama)

| Layer | Posisi | Teknologi |
|---|---|---|
| Backend SPMB | `C:\Users\LENOVO\lomba\ga-ro\backend` | Laravel 12, MySQL `pendaftaran_db`, session auth (blade) |
| Landing page | `C:\Users\LENOVO\lomba\ga-ro` | Vue 3 + Vite (port dev otomatis 5173/5174) |
| Chatbot BISA | `ga-ro/api/chat.js` | Serverless‑style handler → Groq (`GROQ_API_KEY`); di dev diangkat jadi middleware Vite `/api/chat` |

Auth memakai Laravel session (login/register/logout via blade), **bukan** Sanctum/API. File `LoginView.vue`, `DashboardSiswa.vue`, `DashboardAdmin.vue`, dll di Vue **TIDAK DIPAKAI**. `routes/api.php` dikosongkan.

---

## Alur Pendaftaran

1. Guest buka `/pendaftaran/create` → submit → data disimpan ke session `pending_pendaftaran` **dan** ke tabel DB `pendaftaran_drafts` + cookie `pending_draft` → redirect login/register → setelah login/register `AuthController::restorePendingDraft()` memindah draft DB+cookie ke session → form terisi otomatis (JS prefill dari `@json($draft)`).
   - Tabel `pendaftaran_drafts` (migration `2026_08_05_000000_create_pendaftaran_drafts_table.php`) — **draft tidak hilang** meski session/cache browser hilang (penyimpanan ganda: session + DB cookie).
2. Login siswa → `/dashboard-siswa`: menampilkan **status card**, **edit** hanya bila `status = baru` & `created_at + 3 hari` belum lewat.
3. Admin akses `/admin` via URL (tidak ada tombol di UI user). Status: `baru` → `diproses` → `diterima`/`ditolak`.

---

## 🛠️ Perubahan Dashboard Siswa \& Navbar Admin (Sesi Kedua)

- **`PendaftaranController::myDashboard`**
  - Admin diarahkan ke `/admin` (tidak menampilkan dashboard siswa).
  - Siswa yang belum mengisi form tidak di‑redirect; view menerima `$pendaftaran = null` sehingga akan menampilkan *empty state*.

- **`dashboard-siswa.blade.php`**
  - Ditambahkan **empty state** (pesan & tombol “Isi Formulir Sekarang”) bila `$pendaftaran` kosong.
  - **Mode preview** untuk admin dihapus; kini hanya menampilkan data milik user yang login.
  - Redesain ringan: kartu status, badge, dan form edit (hanya muncul bila `$canEdit`).
  - Guard `$hasData` ditambahkan di blok `@php` agar tidak terjadi “Undefined variable $hasData” pada siswa tanpa data (bug yang sempat menyebabkan 500).

- **Navbar (`layouts/app.blade.php` \& `pendaftaran/create.blade.php`)**
  - Link **Beranda** \u0026 **Formulir** disembunyikan untuk role `admin` (navbar hanya logo + logout).
  - Button **Dashboard Siswa** pada halaman form hanya muncul bila `Auth::user()->role === 'siswa'`.
  - Guest \u0026 siswa tetap melihat Beranda \u0026 Formulir.

- **Dropdown status (admin table)**
  - Diberi kelas `.status-select` dengan ukuran kompak agar sejajar dengan tombol Lihat/Edit.

- **Catatan verifikasi**
  - Admin login → `/admin` (200) tanpa tombol dashboard siswa.
  - Siswa tanpa data → `/dashboard-siswa` menampilkan empty state (200).
  - Siswa dengan data → tampilan lengkap, edit tersedia bila izin.
  - **Error `Serialization of 'Illuminate\Http\UploadedFile' is not allowed` saat submit form**: penyebabnya `store()` (dan `HandleTokenMismatch`) menyimpan seluruh input termasuk file upload ke session `pending_pendaftaran`. Solusi: helper `PendaftaranController::sanitizeDraft()` menolak nilai `Illuminate\Http\UploadedFile` sebelum disimpan ke session; diterapkan juga di `HandleTokenMismatch`. Submit form kini 302 → dashboard-siswa tanpa error log.

---

## 🔄 Update 2026‑08‑04 (Sesi Keempat)

- **419 protection** (session lifetime 10080, `PreventBrowserCache` middleware, handler `HandleTokenMismatch`) **ON‑GOING**.
- Semua fitur utama (login username, validasi step, admin preview) **PRODUCTION READY**.
- Dashboard admin bersih dari tombol Formulir/Beranda.
- Status dropdown kompak.

---

## 🔒 Proteksi CSRF (419) — fix penting

- Masalah: handler `TokenMismatchException` via `ExceptionHandler::renderable()` tidak pernah jalan karena Laravel 12 `prepareException()` mengubahnya jadi `HttpException(419)`.
- Solusi (`app/Http/Middleware/HandleTokenMismatch.php`, prepend web group):
  1. Cek **response keluar berstatus 419** (bukan catch exception).
  2. Sebelum `session()->put()`/flash panggil `session()->save()` manual, karena `StartSession` (inner) sudah save sebelum middleware terluar menerima response.
  3. `/pendaftaran` → simpan draft + redirect ke create + flash error; path lain → redirect back + flash.
- Teruji HTTP: POST token salah → 302 ke `/pendaftaran/create`, draft + flash tampil, form terisi.

---

## 📊 Fitur Admin

- `GET /admin` — dashboard: statistik global (total, dikerjakan, diterima, ditolak), tabel pendaftar, tombol **Export CSV**, tombol **Lihat**, dropdown **status** per baris (auto‑submit, `PUT /pendaftaran/{id}/status`), edit, hapus.
- **Auto‑refresh real‑time**: `GET /pendaftaran-snapshot` (endpoint JSON, admin-only, CSRF-exempt) dipoll setiap 5 detik dari `pendaftaran/index.blade.php`. Ketika `latest_id` bertambah (ada pendaftaran baru), statistik kartu ter-update otomatis dan muncul banner "🔔 Data pendaftaran baru masuk" + tombol **Muat Ulang** untuk me-refresh tabel. Banner tampil hanya jika data benar-benar bertambah di DB.
- **Troubleshooting notif tidak muncul**: bila form siswa gagal dikirim (akun sudah punya pendaftaran → anti-duplikasi), tidak ada data baru di DB sehingga banner tidak muncul. Fix: `dashboard-siswa` kini menampilkan `session('error')` "⚠️ Anda sudah mengirim pendaftaran. Pantau status Anda di dashboard." sehingga pengguna tahu penyebabnya.
- Navbar admin **hanya logo + Logout** — link Beranda/Formulir disembunyikan untuk role admin (`layouts/app.blade.php`).
- Dropdown status per baris dibuat kompak (class `.status-select`).
- `GET /pendaftaran/{id}` — halaman detail semua field (Identitas / Sekolah / Keluarga / Pembayaran & Berkas), badge status, quick status, link unduh berkas.
- `GET /pendaftaran/export` — CSV streaming, separator `;`, UTF‑8 BOM, label kolom Indonesia.

---

## 🔐 Anti‑duplikasi

- `store()` menolak jika user sudah punya pendaftaran (kunci `user_id`), redirect + pesan error.

---

## 🔑 Key Files (Backend)

- `routes/web.php`, `routes/api.php` (kosong)
- `app/Http/Controllers/AuthController.php`, `PendaftaranController.php`
- `app/Http/Middleware/HandleTokenMismatch.php`, `CheckRole.php`, `Cors.php` (origin `http://localhost:5174`), `PreventBrowserCache.php`
- `bootstrap/app.php` (middleware registrasi; `withExceptions()` kosong — wajib agar binding ExceptionHandler tetap ada)
- `resources/views/pendaftaran/` (create, show, index, edit, dashboard-siswa), `auth/` (login, register), `layouts/app.blade.php`

---

## 🔧 Key Files (Frontend Landing)

- `vite.config.js` — plugin middleware `/api/chat` (reuse `api/chat.js` + adapter `res.status/json` + try/catch 400 untuk JSON rusak), loadEnv `GROQ_API_KEY` (juga baca `src/server/.env`)
- `api/chat.js` — handler chat (fallback ramah jika key kosong)
- `src/server/` — ARSIP (dipulihkan atas permintaan user, tidak dijalankan); `src/server/.env` = tempat key Groq user
- `src/components/layout/Navbar.vue`, `Footer.vue`, `src/components/sections/Hero.vue` — anchor id benar (`#top`, `#layanan`, `#tentang`, `#contact`), logo `/logo.png`, tombol SPMB/Login/Daftar ke backend `localhost:8000`
- `src/services/chat.js` — POST `/api/chat`

---

## 📋 Credentials & DB

- Admin: username `admin` / password `admin123`
- MySQL root tanpa password, DB `pendaftaran_db`

---

## 🔄 Reset Kata Sandi (tanpa mailer)

- `GET/POST /forgot-password` → buat token (`Password::broker()->createToken`) → link ditampilkan langsung di halaman (flash) karena tidak ada mail config — cukup untuk demo/lokal.
- `GET /reset-password/{token}` + `POST /reset-password` → `Password::reset` memvalidasi token & mengubah password. Admin diblokir dari reset.

---

## 📋 Commands

```powershell
# Backend
cd C:\Users\LENOVO\lomba\ga-ro\backend; php artisan serve --port=8000
php artisan migrate; php artisan db:seed --class=AdminSeeder; php artisan view:cache

# Frontend
cd C:\Users\LENOVO\lomba\ga-ro; npm run dev
```

---

## ✅ Verifikasi terakhir (sesi 8c)

| Test | Hasil |
|---|---|
| POST /pendaftaran token CSRF salah | ✅ redirect create, draft + flash tersimpan, form terisi |
| Login admin (`admin/admin123`) | ✅ masuk dashboard, seluruh halaman admin 200 |
| GET /pendaftaran/{id} (detail) | ✅ 200, semua field tampil |
| GET /pendaftaran/export (CSV) | ✅ 200, BOM + header + data |
| `npm run build` (landing) | ✅ sukses |
| POST /api/chat (dengan key GROQ) | ✅ 200 jawaban Groq asli berbasis knowledge |
| POST /api chat body rusak | ✅ 400 (dev server tidak crash) |
| Reset password siswa (forgot → link → reset → login baru) | ✅ end‑to‑end teruji |

---

**Last Updated:** 2026‑08‑05
**Status:** ✅ Production Ready (admin‑only navbar, siswa dashboard dengan empty‑state, auto‑refresh admin).

## 🔙 Update 2026-08-05 (Sesi Kesebelas) — Navbar Login

- Di `layouts/app.blade.php`, user yang sudah login (siswa/admin) **tidak lagi melihat link Beranda/Formulir/Dashboard Siswa**.
- Diganti **tombol back (panah ◀)** di pojok kiri sebelah logo → menuju **landing page** `http://localhost:5174/` (bukan ke form).
- Guest tetap melihat link **Beranda** + **Formulir** di navbar.

## 📐 Update 2026-08-05 (Sesi Keduabelas) — Landing Page Diperkecil

**Lokasi:** `ga-ro` (Vue 3 landing, port 5174).

### Ukuran sebelum → sesudah

| Metrik | Sebelum | Sesudah |
|---|---|---|
| Total `dist/` (build) | 4,180 KB (±4 MB) | **997 KB** |
| `pmb_smkbu.png` | 2,019.9 KB | **100.1 KB** (`pmb_smkbu.jpg`, 1000×432, q75) |
| `sklh.png` | 1,116.9 KB | **145.7 KB** (`sklh.jpg`, 540×960, q82) |
| `logo.png` | 350.6 KB | **58.8 KB** (240×240) |

### Skala tipografi & spacing (diperkecil ±25–35%)
- **Hero** — `min-height 100vh→88vh`, padding `120→96px`, judul `clamp 64-92px → 48-68px`, bg-word `110-170 → 90-130px`, tombol `56→48px`, bukan `19→17px`.
- **Feature** — padding `132→96px`, judul `66→48px`, bento row `180→150px`, banner SPMB `240→190px`, borderRadius `20→18px`.
- **AboutSchool** — padding `96→72px`, judul `52→42px`, paragraf `17→15.5px`, statistik `38→32px`, gambar `360→300px`.
- **Footer** — padding `60→48px`.
- Responsive breakpoints disesuaikan (Hero/Feature/About mobile padding & font diperkecil).

**Referensi gambar diubah:** `feature.vue` (`/pmb_smkbu.png → .jpg`) dan `AboutSchool.vue` (`/sklh.png → .jpg`). PNG lama dihapus.

**Build:** `npm run build` sukses. `dist/` sekarang <1 MB (997.1 KB).

## 🎓 Update 2026-08-05 (Sesi Ketigabelas) — Akses Cepat Dashboard di Landing Page

- **Endpoint baru** `GET /auth-status` (web + CORS `localhost:5174`, session-based): JSON `{logged_in, role, name, has_pendaftaran, status}`. Dipakai landing page untuk deteksi status login lintas origin.
- **`src/composable/useAuthSession.js`** (baru) — fetch status dengan `credentials:'include'`; helper `spmbTarget()`: siswa login & sudah daftar → `/dashboard-siswa`, selain itu → `/pendaftaran/create`.
- **Landing `HomeView.vue`** — card akses cepat **di bawah navbar, atas Hero** (hanya saat siswa login):
  - Sudah daftar: "✅ Anda telah mendaftar di SPMB… Status: BARU/DIPROSES/…" + tombol **Buka Dashboard Siswa**
  - Login belum isi form: "📄 Lengkapi pendaftaran SPMB Anda" + tombol **Lengkapi Pendaftaran**
- **Navbar & card SPMB** (`Navbar.vue`, `feature.vue`) — tombol SPMB kini pintar: siswa sudah daftar → langsung **dashboard siswa**, bukan form.
- **`create.blade.php`** — button **Dashboard Siswa di navbar form dihapus** (nav-auth hanya Logout).
- **AboutSchool diperkecil lagi** — padding `72→56px`, judul `42→34px max`, paragraf `15.5→14px`, statistik `32→26px`, gambar `300→250px`, gap/padding card & stats dipadatkan.
- **Bug "register → admin dashboard"** — diverifikasi ulang: register selalu redirect ke `/pendaftaran/create` (form), bukan admin; admin yang mengakses `/register` di-redirect ke `/` (guest middleware). Tidak terulang.

## 📝 Rencana (BELUM DIKERJAKAN) — Isi Card SPMB di Bento Grid

**Lokasi:** `ga-ro/src/components/sections/feature.vue` — card featured index 0 (SPMB Online).

**Masalah:** card SPMB terlihat kosong — banner 220px + judul 1 baris + deskripsi 2 baris + tombol, sisa ruang kosong di tengah (`card-content` justify `space-between`).

**Rencana yang disetujui: A + D (statistik chips + badge jurusan):**

1. **Statistic chips** (baris 3 chip kecil di bawah deskripsi):
   - `🗓 Gelombang 1 · 2025/2026`
   - `🎓 3 Jurusan`
   - `⏱ Daftar Online`
   - Style: pill kecil `background rgba(255,255,255,0.12)`, font 12px, gap 8px, `flex-wrap`

2. **Mini-badge jurusan** (RPL / TKJ / AKL):
   - 3 badge kecil di bawah chips atau samping deskripsi
   - Style: `background rgba(255,255,255,0.1)`, border `1px solid rgba(255,255,255,0.25)`, font 11px, padding 4px 10px

3. **Penyesuaian layout**:
   - `card-content` tetap `flex column space-between` — chips+badges ditaruh di area tengah (setelah `<p>`, sebelum tombol)
   - `margin-top: auto` pada grup chips agar tombol tetap di bawah
   - Tidak menambah tinggi kartu (bento row `minmax(170px, auto)` menyesuaikan otomatis)

**File yang disentuh:** hanya `feature.vue` (template + style scoped). Tanpa perubahan backend/DB.