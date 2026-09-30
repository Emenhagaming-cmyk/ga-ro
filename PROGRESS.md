# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

---

## STATUS TERAKHIR (2026-09-30) — Hierarki Dashboard Siswa: Pengumuman Kelulusan Naik ke Posisi Paling Atas

**Permintaan user:** card pengumuman lulus/tidak di dashboard siswa ("hierarki ke siswa lebih kena") — semula tersembunyi di tengah halaman.

**Keputusan (konfirmasi user):** posisi **tepat setelah banner sambutan** (awalnya "paling atas sebelum banner", lalu user meminta **"dibawah banner aja deh"** → diubah), **semua status naik** (diterima, ditolak, diproses, edit berakhir), dan **gaya dibedakan** jadi banner berwarna + tombol Unduh Bukti ikut di dalamnya.

**Perubahan di `backend/resources/views/pendaftaran/dashboard-siswa.blade.php`:**
- `@php $badge` di-hoist dari dalam `.ds-stats` ke atas (dipakai announce + stats + badge sama).
- Blok pengumuman (`ds-card--status-message`) dipindah ke posisi **4** → Kembali → alert → **banner sambutan** → **pengumuman** → statistik → detail → aksi → edit — di-rename jadi `.ds-announce` + modifier `ds-announce--{status}`:
  - `diterima` hijau, `ditolak` merah, `diproses` biru, `baru` (lewat deadline) amber → tinted bg + border kiri 6px + ikon SVG + eyebrow uppercase + teks (wording lama tetap).
  - `role="status"` untuk aksesibilitas.
  - Wrapper tetap `@if (!$canEdit)` — cakupan 4 kondisi sama persis; status `baru` yang masih bisa edit memang tanpa pesan (tidak dibuat baru).
- Tombol **Unduh Bukti Diterima** kini di dalam card pengumuman (hanya `diterima`); `.ds-actions` dibungkus `@if ($canEdit)` (sisa isinya cuma tombol Edit — div kosong tak lagi muncul saat diproses/ditolak).
- CSS lama `.ds-card--status-message` & `.ds-status-text` dihapus; tambah blok `.ds-announce` + modifier + ikon.

**Test baru `tests/Feature/DashboardSiswaHierarchyTest.php` (5 kasus):** assert posisi string di HTML — `ds-announce` muncul **setelah** `ds-banner` (urutan: banner → pengumuman) untuk diterima/ditolak/diproses/baru-lewat-deadline; tombol Unduh ada saat diterima & hilang saat ditolak; status `baru` yang masih bisa edit **tidak** menampilkan pengumuman tapi tetap ada Edit + form.

**Verifikasi:** `php artisan view:cache` OK (error LSP = false positive parser Blade di dalam HTML/CSS). `php artisan test` → **39 passed (144 assertions)** (34 lama + 5 baru). Belum di-commit/deploy.

---

## STATUS TERAKHIR (2026-09-30) — Ikon Instagram Footer Diperbaiki (Kenapa Tampil Jelek)

**Masalah:** ikon Instagram di footer (`Footer.vue`) tampil jelek/bolong, padahal TikTok tampil normal.

**Akar masalah:** IG memakai `<i class="fa-brands fa-instagram">` (Font Awesome) yang butuh font **"Font Awesome 7 Brands"** — font itu **tidak pernah di-load** di seluruh proyek Vue (grep `fa-brands|font-awesome|@fortawesome` → cuma 1 kemunculan, di Footer itu sendiri). Tanpa font tersebut browser merender placeholder glyph. Sementara TikTok memakai **inline SVG** — itu sebabnya TikTok bagus, IG jelek.

**Perbaikan:**
- `src/components/layout/Footer.vue` — ganti `<i class="fa-brands fa-instagram">` dengan **inline SVG** path resmi Instagram (diambil dari `cdn.simpleicons.org` — garis besar Instagram Simple Icons, viewBox `0 0 24 24`, `fill="currentColor"`, ukuran 18×18) sehingga mengikuti pola TikTok dan otomatis mengikuti warna hover (hijau brand ⇄ putih).
- Hapus aturan CSS `.social-icon i { font-family: "Font Awesome 7 Brands" }` yang tak terpakai.

**Verifikasi:** `npm run build` sukses (dist/ ter-ignore git). Belum di-commit/deploy.

---

## STATUS TERAKHIR (2026-09-30) — Foto Profil Tidak Muncul di Navbar Landing Page (Vue)

**Laporan user:** foto profil tampil di dashboard siswa & halaman profil, tetapi **tidak tampil di navbar/topbar landing page** (Vue). Padahal sidebar/topbar backend (`layouts/app.blade.php`) sudah menampilkan fotonya.

**Analisis (explore menyeluruh, 3 akar masalah):**
1. Backend `/auth-status` (`AuthController::authStatus()`) **tidak mengirim `avatar`** — padahal frontend menyimpan seluruh response apa adanya di `session` ref + `sessionStorage` (`useAuthSession.js`).
2. Payload login `?auth=` dari `frontendAuthUrl()` (`helpers.php`) hanya membawa 5 field — avatar tidak ikut, padahal itu jalur yang dipakai saat cookie third-party diblokir.
3. `Navbar.vue` hanya merender **inisial** (`<span class="nav-avatar">{{ initial }}</span>`) — tidak pernah ada `<img>` sama sekali.

**Perubahan:**
- `backend/app/Http/Controllers/AuthController.php` — `authStatus()` menambahkan `'avatar' => $request->user()->avatar` pada response login, dan `'avatar' => null` pada response guest (bentuk konsisten).
- `backend/app/helpers.php` — `frontendAuthUrl()` menambahkan `'avatar' => $user->avatar` ke payload `?auth=` (logged-in) dan `null` (guest).
- `src/composable/useAuthSession.js` — `GUEST` mendapat `avatar: null`.
- `src/components/layout/Navbar.vue` — avatar **desktop** (`nav-avatar`) dan **mobile** (`mobile-avatar`) kini menampilkan `<img>` foto profil bila `session.avatar` ada (pola overlay sama seperti topbar backend: inisial tetap di bawah, gambar di atas; `@error` → gambar disembunyikan, inisial tetap tampil). Tambah state `avatarBroken` / `avatarBrokenMobile`, CSS `position: relative + img{position:absolute;inset:0;object-fit:cover}` + `overflow:hidden`.

**Verifikasi:**
- `backend`: 3 test baru di `ProfileAvatarTest` → `/auth-status` mengirim avatar (login + guest), dan payload `frontendAuthUrl()` menyertakan avatar. `php artisan test` → **34 passed (113 assertions)** (31 lama + 3 baru).
- `frontend`: `npm run build` sukses (dist/ regenerated, tapi ter-ignore git — tidak menodai repo).
- Cek visual: setelah login di landing page (`npm run dev` + backend `php artisan serve --port=8000`), navbar desktop & drawer mobile harus menampilkan foto profil; polling 30 detik / `?auth=` juga sudah membawa avatar.
- **Belum di-commit / di-deploy** (user handle git sendiri).

**Catatan scope:** widget profil di `src/views/ELearningView.vue` (panel berbentuk inisial di side panel) ikut memakai `session.name`/`email` tapi **belum** otomatis menampilkan foto — di luar keluhan navbar; kalau mau disamakan, tinggal bilang.

---

## STATUS TERAKHIR (2026-09-30) — Redesign Halaman Profil Siswa + Foto Profil (base64)

**Permintaan user:** redesign halaman profil siswa (`/profil`) mengikuti gambar referensi yang dikirim, dengan tab **Profil | Pendaftaran | Keluarga**, warna hijau brand, plus **siswa bisa upload foto profil** sendiri dan avatar di topbar ikut memakai foto tersebut.

**Keputusan user:** tab 3 tersebut; palet hijau; foto disimpan **base64 di kolom DB** (bukan file storage) karena `public/storage` tidak ada symlink, `backend/.env.deploy` memakai `PUBLIC_DISK_ROOT=/tmp`, dan `vercel.json` tidak merutekan `/storage/*` → file-based tidak persisten di production. Link topbar menuju `/profil`; middleware `/profil` **dilonggarkan** dari `role:siswa` → `role:siswa,pendaftar` (pendaftar yang belum dinyatakan diterima tetap boleh buka, konsisten dengan `dashboard-siswa` yang middleware-nya hanya `auth`).

**Perubahan (7 file):**
1. **BARU** `backend/database/migrations/2026_09_30_090000_add_avatar_to_users_table.php` — `$table->mediumText('avatar')->nullable()->after('password')` (mediumText, bukan text: base64 ≈ 4/3 ukuran file, `text` cuma 64KB). Dijalankan di MySQL lokal.
2. `backend/app/Models/User.php` — `'avatar'` masuk `$fillable`, dan juga masuk `$hidden` supaya data URI tidak ikut bocor saat `toArray()`/JSON.
3. `backend/app/Http/Controllers/AuthController.php`:
   - `showProfile()` → tambah variabel `$canEdit` (status `baru` **dan** belum lewat 3 hari, aturan bisnis yang sama dengan `PendaftaranController::update()`), juga pakai import `Pendaftaran` (sebelumnya FQCN).
   - **BARU** `updateAvatar()` — validasi `required|image|mimes:jpg,jpeg,png,webp|max:512` dengan pesan error Bahasa Indonesia.
   - **BARU** `destroyAvatar()` — kosongkan kolom.
   - **BARU** `avatarToDataUri()` (private) — resize pakai GD (sisi terpanjang → 256px, aspect ratio dijaga) → JPEG quality 82, latar putih dulu supaya PNG transparan tidak jadi hitam; bila hasil masih > 350KB, ukuran diturunkan bertahap 256→192→160→128. Fallback ke base64 file asli **hanya** bila ekstensi GD tidak tersedia. **File rusak / bukan gambar → `null`** (tidak pernah disimpan). `detectMimeFromBytes()` private helper.
4. `backend/routes/web.php` — `POST /profil/avatar` (`profil.avatar.update`) + `DELETE /profil/avatar` (`profil.avatar.destroy`), keduanya `role:siswa,pendaftar`; middleware `/profil` dilonggarkan.
5. `backend/resources/views/auth/profile.blade.php` — **rewrite total** mengikuti referensi: header bar (chevron kembali · judul "Profil Siswa" · ikon gear → dashboard), kartu profil (avatar + badge kamera sebagai `<label>` file picker + nama + meta NISN/Jurusan/Status pill + ikon pensil → `dashboard-siswa#edit-section` hanya saat `$canEdit`), tab bar 3 tab dengan `role="tablist"` + arrow-key navigation, 4 kartu statistik gradasi hijau (Jurusan · Nilai Rata-rata · Status · Gelombang, tiap kartu ada tombol "Lihat detail" → pindah tab), tabel "Informasi Pribadi", panel Pendaftaran (Data Sekolah & Seleksi · Status Pendaftaran · Berkas Upload — berkas **dibiarkan teks** "Terunggah/Belum diunggah", tidak ditautkan karena URL storage memang rusak), panel Keluarga (Data Keluarga + dua kartu Ayah/Ibu + Wali), empty state "Isi Formulir Pendaftaran" bila belum daftar, footer berisi tombol Logout + Kembali ke Dashboard. Format tanggal Bahasa Indonesia ditulis lokal di view (helper `formatPeriode` hanya untuk periode SPP "Y-m").
6. `backend/resources/views/layouts/app.blade.php` — `.app-topbar-user` jadi elemen `<a>` (link `/profil` hanya bila role `siswa`/`pendaftar`, selain itu tanpa `href` agar kasir/guru tidak kena error), avatar topbar menampilkan foto bila ada (fallback inisial via CSS overlay + `onerror` hide img), `mb_substr` untuk inisial, CSS hover untuk `a.app-topbar-user`.
7. `backend/tests/Feature/ProfileAvatarTest.php` — **BARU**, 12 test (lihat bagian Verifikasi).

**Penyesuaian setelah review user:** tombol **pengaturan (gear)** dihapus, dan tombol **back (chevron)** di bawah navbar juga dihapus karena navbar sudah punya tombol kembali di sebelah logo sekolah (branch navbar di `layouts/app` — tombol itu hanya muncul saat `@auth`, dan halaman profil selalu butuh auth). Header halaman kini hanya berisi judul terpusat **"Profil Siswa"**. Selector CSS `.pf-header` (grid 3 kolom) dan `.pf-icon-btn` ikut dihapus karena sudah tidak dipakai. `frontendAuthUrl()` tidak lagi dipanggil di view profil. Ikon **pensil (edit data)** dan **kamera (upload foto)** tetap ada karena bukan tombol navigasi.

**Dua bug yang ketahuan saat verifikasi (dan sudah diperbaiki):**
- Gambar **rusak** sempat lolos: versi pertama `avatarToDataUri()` jatuh ke fallback base64 mentah dan menyimpan sampah. Diperbaiki: fallback mentah hanya bila GD tidak ada; bila GD ada tapi `imagecreatefromstring()` gagal → `null` + pesan error.
- View memakai `$errors` yang **hanya di-share middleware web** → view crash saat dirender di luar HTTP (mis. testing/CLI). Diperbaiki: `$errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag`.

**Verifikasi (lokal, tanpa deploy):**
- `php artisan migrate --force` → `users.avatar` ada (dicek via `Schema::hasColumn`).
- `php artisan route:list --path=profil` → 3 route terdaftar.
- `php artisan view:cache` + `view:clear` → blade compiles bersih.
- `php artisan test` → **31 passed (106 assertions)** (19 test lama tetap hijau + 12 test baru). Test baru mencakup: siswa & pendaftar boleh akses profil, role lain (guru) kena middleware, halaman profil + semua elemen utama, empty state tanpa pendaftaran, upload → base64 + resize terverifikasi (`getimagesizefromstring` menghasilkan 256×192 dari 1600×1200), foto tampil di halaman, file >512KB ditolak, file non-gambar ditolak, gambar rusak tidak disimpan, hapus foto, avatar tidak bocor di serialisasi, dan link profil di topbar hanya untuk siswa.
- Render nyata (script PHP yang boot Laravel, dihapus setelah dipakai): akun dengan data lengkap → 4 kartu statistik, 3 tab, 46 baris data, seluruh section Pendaftaran & Keluarga ada; akun tanpa pendaftaran → empty state + 0 ikon pensil + inisial fallback; pipeline resize 3000×2000 → 256×171 dengan data-URI 1883 byte.
- **Belum di-commit / di-deploy** (user ingin melakukan commit & push sendiri).

**Catatan production (VPS/Vercel):** migrasi `2026_09_30_090000_add_avatar_to_users_table` **wajib dijalankan** di DB production (TiDB / MySQL VPS) saat deploy berikutnya, kalau tidak kolom `users.avatar` tidak ada dan upload foto akan error. Tidak ada perubahan `.env`, `vercel.json`, atau frontend Vue.

**Catatan:** diagnostic LSP di file blade (`at-rule or selector expected`, `Property assignment expected`) tetap **false positive** — LSP memparse `<style>`/`<script>` inline sebagai CSS/JS murni sehingga `{{ }}` Blade dianggap selector tidak valid. Acuan validasi tetap `php artisan view:cache` + `php artisan test`.

---

## STATUS TERAKHIR (2026-09-30) — Alas Stiker Bulat untuk Doodle Banner Dashboard Siswa

**Permintaan user:** doodle di banner dashboard siswa `nyatu sama warna hijau` sehingga tidak kelihatan jelas → minta diberi "apa gitu" di belakangnya agar kontras.

**Analisis:** `images/doodle-studying.png` adalah line art **hitam** (buku pink) dengan background transparan, langsung menempel di banner gradasi hijau tua `#2f5b45 → #4a7a62` → kontrasnya rendah. Panel admin tidak punya doodle banner, jadi cakupannya hanya 1 file.

**Pilihan user:** gaya **badge bulat putih (stiker)**, dashboard siswa saja.

**Perubahan (1 file — `backend/resources/views/pendaftaran/dashboard-siswa.blade.php`):**
- HTML: `<img class="ds-banner-doodle">` dibungkus `<div class="ds-banner-doodle-wrap">` (sekarang img jadi anak dari wrapper, bukan flex child langsung).
- CSS: blok `.ds-banner-doodle` diganti jadi 2 rule baru — wrapperlingkaran 176×176px `border-radius:50%` dengan `background: radial-gradient(circle at 35% 30%, #ffffff 0%, #f1f7f0 72%)` + `box-shadow: 0 14px 30px rgba(11,30,20,.30), 0 0 0 8px rgba(255,255,255,.14)`; img di dalamnya `height:132px` + `filter: drop-shadow(...)` halus.
- Animasi `ds-float` **dipindah dari img ke wrapper** → stiker utuh yang mengambang; `@keyframes ds-float` tidak diubah.
- Media query `max-width:768px`: aturan lama `.ds-banner-doodle{height:120px;margin-top:20px;margin-left:0}` diganti jadi wrapper 140×140px + img 104px (proporsional saat banner berubah jadi kolom).

**Verifikasi (lokal, tanpa deploy):**
- `php artisan view:cache` + `view:clear` → blade ter-compile tanpa error.
- Render nyata dicek via `php artisan tinker` (view `pendaftaran.dashboard-siswa` dengan `Pendaftaran` pertama dari DB + `Auth::login`, read-only): `wrapper=3` (1 HTML + 2 CSS), `cssBadge=1`, `anim=1`, `img132=1`, `mobile=1`, `doodleImg=1`; markup ban terpotong sesuai harapan (wrapper membungkus img).
- `php artisan test` → **19 passed (55 assertions)**.
- Catatan: login HTTP via akun demo `siswa` **tidak bisa** dipakai untuk cek banner karena akun demo tidak punya data pendaftaran (halaman render empty-state, banner tidak ikut muncul) — makanya verifikasi dilakukan lewat render view langsung. Cek visual akhir tetap di sisi user (server dev port 8000 sudah jalan, cukup refresh).
- **Belum di-commit / di-deploy** (user ingin melakukan commit & push sendiri untuk belajar git).

**Catatan:** diagnostic LSP yang muncul di file blade ini (error "at-rule or selector expected" di baris `class="ds-stat-icon ds-stat-icon--{{ ... }}"`, dll.) adalah **false positive** — LSP memarse `<style>`/`<script>` inline sebagai CSS/JS murni sehingga `{{ }}` Blade dianggap selector tidak valid. Tidak related dengan perubahan ini; acuan validasi tetap `php artisan view:cache`.

---

## STATUS TERAKHIR (2026-09-30) — Tombol Mata (Lihat/Sembunyikan Password) di Halaman Login & Daftar

**Permintaan user:** tambah ikon mata di input password — halaman login (siswa + panel admin), lalu dilanjutkan ke form daftar.
Frontend Vue tidak diubah karena route `/login` & `/register` di Vue memang redirect ke backend.

**Perubahan (5 file view/CSS/JS — tanpa controller, route, atau DB):**
- `backend/resources/views/auth/login.blade.php` — `<div class="input-wrap">` password diberi modifier `pw-field`; tombol `<button type="button" class="pw-toggle" onclick="togglePw(this)">` setelah input, berisi 2 SVG inline (`.eye-open` tampil, `.eye-off` `display:none`) — path SVG sama dengan yang sudah dipakai di `backend-admin/.../pendaftaran/dashboard.blade.php`.
- `backend/resources/views/auth/register.blade.php` — tombol mata yang sama pada **dua** field: `password` dan `password_confirmation` (masing-masing wrap `.input-wrap pw-field` sendiri, jadi toggle satu tidak memengaruhi yang lain).
- `backend/resources/views/layouts/auth.blade.php` — CSS `.pw-field input{padding-right:44px}` + `.pw-toggle` (absolut `right:10px`, tengah vertikal, transparan, `color:#7d8a80`, hover `#3a6450`); **selector lama `.input-wrap svg` diubah jadi `.input-wrap > svg`** supaya ikon di dalam tombol tidak ikut rule `left:14px` milik ikon kunci; fungsi JS `togglePw(btn)` ditambahkan sebelum `</body>` (ditempatkan di layout agar dipakai bersama oleh login + register tanpa duplikasi).
- `backend-admin/resources/views/auth/login.blade.php` — input password dibungkus `.pw-wrap` + tombol mata; CSS page-scoped di dalam `<style>` (pola yang sama dipakai `berita/create.blade.php`, tidak menyentuh `admin.css`/layout bersama). Panel admin tidak punya halaman daftar.
- `backend-admin/resources/views/layouts/app.blade.php` — `togglePw(btn)` ditambahkan di blok `<script>` yang sudah ada, sebelah `toggleSidebar`.

**Perilaku toggle:** `input.type` `password`↔`text`, tukar visibilitas `.eye-open`/`.eye-off`, update `aria-pressed` + `aria-label` + `title`, fokus dikembalikan ke input. Tombol `type="button"` → tidak ikut submit form.

**Verifikasi (lokal, tanpa deploy):**
- `php artisan view:cache` + `view:clear` di `backend` **dan** `backend-admin` → semua blade ter-compile tanpa error.
- `php artisan serve` + cek HTML hasil render: `GET /register` → 200 dengan 2 tombol `.pw-toggle` / 2 `.pw-field` / 2 `.eye-open` / 2 `.eye-off`; `GET /login` → 200 dengan 1 tombol (tetap aman); port 8001 (panel admin `/login`) → 200 dengan tombol + CSS `.pw-wrap`/`.pw-toggle` + fungsi `togglePw`.
- `php artisan test` (backend) → **19 passed (55 assertions)**.
- Server dev dimatikan setelah verifikasi. **Belum di-deploy** (sesuai instruksi user; user ingin melakukan commit/push sendiri).
- Verifikasi visual klik ikon (tampilan mata) belum dilakukan — silakan cek langsung di browser.

**Catatan lanjutan:** `togglePw` di kedua layout sudah siap dipakai, jadi penambahan tombol mata di halaman `forgot-password`/`reset-password` cukup menyalin markup tombolnya (belum dikerjakan — belum diminta).

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
