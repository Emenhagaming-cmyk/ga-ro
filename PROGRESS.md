# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

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
