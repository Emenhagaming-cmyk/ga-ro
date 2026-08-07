# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

---

## 📌 STATUS TERAKHIR (sesi 2026-08-04)

**Sistem SPMB lengkap & berjalan end-to-end: register → form (multi-step) → dashboard siswa → dashboard admin (via URL `/admin`).**

- **419/CSRF diperbaiki tuntas** — token kadaluarsa pada submit form tidak lagi muncul halaman 419; data student disimpan draft otomatis ke session, redirect ke form, terisi ulang (teruji HTTP).
- **Fitur admin rampung: F-A anti-duplikasi, F-B halaman detail, F-C export CSV, F-D quick-status** (semua teruji 200).
- **Chatbot BISA AKTIF** dengan `GROQ_API_KEY` user — menjawab berbasis data knowledge `api/knowledge/**`, berjalan via vite middleware `/api/chat` (reuse `api/chat.js`). `src/server/` diarsipkan (tidak dijalankan).
- **Frontend landing** dibenahi: logo, anchor, tombol Login/Daftar/SPMB → backend `localhost:8000`, build OK.

**Sisa pekerjaan / prioritas lanjutan (belum dikerjakan):**
- [ ] Deployment (jika dibutuhkan): key `GROQ_API_KEY` harus diset di env produksi + pastikan `src/server/.env` TIDAK ikut ter-commit (menunggu izin user — user melarang sentuh `.env`)
- [ ] Fine-tune UI form pendaftaran `create.blade.php` (cocokkan lebih dekat ke referensi desain jika diminta)

---

## 🗂 REKAP PEKERJAAN (dari awal)

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