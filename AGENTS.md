# AGENTS.md — Proyek SPMB SMK Bahrul Ulum

Konteks permanen proyek. Dibaca otomatis di setiap sesi baru.
Baca juga `PROGRESS.md` (status terakhir + TODO), `IMPLEMENTATION_SUMMARY.md` (detail teknis),
`PERF.md` (ledger optimasi).

> **Path repo: `C:\Users\User\ga-ro`.** Dokumen lama (`IMPLEMENTATION_SUMMARY.md`) masih menyebut
> `C:\Users\LENOVO\lomba\ga-ro` — path mesin lama + path CLI vercel yang sudah tidak ada. Selalu
> pakai path relatif dari root repo.

## Monorepo: 3 deployable, bukan monorepo tool

Tidak ada workspace root. Tiga folder berdiri sendiri, masing-masing punya `package.json`/
`composer.json` sendiri.

| Folder | Isi | Domain | Vercel project |
|---|---|---|---|
| `backend/` | Laravel 12 — web siswa & API (port dev 8000) | `pendaftaranspmb.vercel.app` | `spmb-backend` |
| `backend-admin/` | Laravel 12 — panel admin (salinan `backend`, bukan symlink) | `paneladminsmkbu.vercel.app` | `spmb-admin` |
| root (`src/`) | Vue 3 + Vite 7 — landing page (port dev 5174) | `smkbu-sby.vercel.app` | `lomba` |

- `backend/` dan `backend-admin/` **pakai satu DB yang sama** (`pendaftaran_db` di TiDB Cloud).
  Migrate selalu dari `backend/` saja (lihat "Migrasi DB").
- Admin dashboard (`/admin`, daftar/export/status pendaftar, Kelola Berita/Tabungan/Koperasi,
  Laporan) **hanya ada di `backend-admin/`**. `backend/routes/web.php` tidak punya route `/admin`
  → `GET /admin` = 404 di web utama.
- `backend/` TETAP punya route ber-role admin/kasir/guru: `/admin/tabungan`, `/admin/spp`,
  `/admin/spp/rekap`, `/spp/kasir`, `/spp/pay`.
- Tambah fitur yang menyentuh model/tabel/validasi **harus di kedua backend** (copy-paste sinkron);
  `IMPLEMENTATION_SUMMARY.md` ada di root + kedua folder sebagai salinan.

## Setup lokal (fresh clone perlu ini)

`vendor/` dan `node_modules/` di-gitignore → **belum ada / belum lengkap** di clone baru.
`.env` juga tidak di-commit (dan saat ini memang tidak ada di mana pun).

```powershell
# 1. Root — WAJIB pakai npm.cmd / vercel.cmd (lihat "Execution policy" di bawah)
npm.cmd install
npm.cmd run dev            # port 5174, strictPort (gagal kalau 5174 dipakai proses lain)

# 2. Backend
cd backend
composer install
copy .env.example .env
php artisan key:generate  # .env.example punya APP_KEY kosong
```

**Execution policy (verified):** PowerShell memblokir semua `.ps1` di mesin ini.
`npm`, `vercel`, `npx` polos = error `cannot be loaded because running scripts is disabled`.
Selalu tulis **`npm.cmd` / `vercel.cmd`** (`vercel.cmd` ada di `$env:APPDATA\npm\`, bukan
`C:\nvm4w\nodejs\` seperti tertulis di `IMPLEMENTATION_SUMMARY.md`).

Isi `.env` lokal: DB harus **MySQL** Laragon (`DB_CONNECTION=mysql`, db `pendaftaran_db`,
`127.0.0.1:3306`, user `root`, password kosong) + `FRONTEND_URL=http://localhost:5174`.
`.env.example` default-nya `sqlite` **dan `APP_KEY` kosong**. `FRONTEND_URL` /
`NINEROUTER_*` tidak ada di `.env.example` → set manual.

- `backend-admin/` **tidak punya `.env.example` sama sekali** → copy dari `backend/.env.example`
  bila butuh artisan lokal di folder itu. `composer run setup` di sana akan gagal (script-nya
  `copy .env.example .env`) dan script `setup` juga menjalankan `migrate --force` — jangan
  dijalankan sembarangan.
- `php artisan storage:link` dibutuhkan kalau view pakai `asset('storage/...')` (upload pendaftar).

## Command

```powershell
# Test (backend) — sqlite :memory:, TIDAK butuh MySQL
cd backend; composer test          # = php artisan config:clear + php artisan test
php artisan test --filter=SppControllerTest
php artisan test tests/Feature/SppControllerTest.php

# Frontend
npm.cmd run build                  # hanya build; TIDAK ada lint/typecheck/e2e di repo ini

# Migrasi — hanya dari backend/
cd backend
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan spp:generate --periode=2026-09 --nominal=500000   # auto-tagihan SPP bulanan
```

Deploy — **cwd harus folder target** (`backend/` | `backend-admin/` | root), tidak dari root:

```powershell
vercel.cmd deploy --prod --yes
vercel.cmd alias set <deployment-url> paneladminsmkbu.vercel.app   # lihat catatan di bawah
```

Alias: `backend-admin/vercel.json` tidak punya field `alias`, jadi domain production ditentukan
project Vercel. Sesi 2026-09 melaporkan **auto-alias sudah jalan** (deploy → 200 di domain);
sesi lama harus `alias set` manual. Kalau deploy selesai tapi domain lama, jalankan
`vercel.cmd alias set`.

## Verifikasi / test quirk

- **Tidak ada lint, format, typecheck, atau e2e di repo ini.** Tidak ada `.github/`, tidak ada
  `eslint`/`prettier`/`pint.json`. Verifikasi = `php artisan test` + `npm.cmd run build`
  (frontend) atau `php -l` untuk cek syntax PHP cepat.
- `@playwright/test` ada di devDependency tapi **tidak ada config/spec** — jangan coba
  `npx playwright test`.
- **Test BUTUH `APP_KEY`** (verified). `phpunit.xml` tidak menyetel `APP_KEY`, jadi tanpa
  `.env` + `php artisan key:generate` semua test HTTP gagal `MissingAppKeyException`.
  Cara cepat tanpa bikin `.env`:
  `$env:APP_KEY='base64:<32 byte acak>'; php artisan test`.
- **`vendor/` bisa ada tapi rusak** (verified): sisa `composer install` terputus hanya berisi
  `vendor/composer/tmp-*.zip`, tidak ada `autoload.php` → `composer test` fatal
  `Failed to open vendor/autoload.php`. Fix = jalankan `composer install` penuh.
- `backend-admin/vendor` belum tentu ada → `composer install` dulu sebelum test di sana.
- **Suite `backend-admin` merah dari sananya (verified, 2 dari 3 gagal)** — bukan regresi kamu:
  1. `ExampleTest` minta `GET /` = 200, tapi `/` redirect 302 ke `/admin`.
  2. `RegistrationInsightServiceTest` identik punya `backend/`, tapi
     `backend-admin/app/Services/RegistrationInsightService.php` sudah dipangkas jadi
     **selalu return fallback** (tidak memanggil HTTP sama sekali) → assert gagal.
  Test SPP (`SppControllerTest`, `SppModelTest`, `SppCommandTest`) hanya ada di `backend/`.
- Test SPP **tidak pakai `RefreshDatabase`**; `setUp()` membuat tabel manual via `Schema::create`
  + `tearDown()` drop. Schema test bisa meleset dari migration asli — kalau test gagal, cek dulu
  apakah kolomnya memang ada di DB.
- `phpunit.xml` set `DB_CONNECTION=sqlite` + `:memory:` → test jalan tanpa MySQL.
  Tanpa `.env`, semua test berstatus *warning* (`file_get_contents(.env)`) tapi **exit code tetap 0**.
- Verifikasi cepat tanpa HTTP: `php artisan route:list` (jalan tanpa `.env`).

## Migrasi DB

- **Jalankan `php artisan migrate` hanya di `backend/`.** Set migration `backend-admin/` tidak
  lengkap — ketinggalan 6 file: `2026_08_22_100000_create_lowongans`,
  `2026_08_22_100001_create_lamarans`, `2026_08_23_000000_add_applicant_fields_to_lamarans`,
  `2026_08_23_100000_add_email_to_lamarans`, `2026_08_29_140000_create_tabungans`,
  `2026_08_30_110000_drop_plain_password` (padahal punya
  `..._add_input_by_to_tabungans_table`). Migrate dari `backend-admin` = data/tabel tidak sinkron.
- Jangan `migrate:fresh` / `rollback` tanpa konfirmasi user — `backend` & `backend-admin` berbagi
  satu DB production.
- 3 migration lama (`2026_08_03_152852`, `2026_08_03_153258`, `2026_08_09_094358`) MySQL-only
  (`ALTER TABLE ... MODIFY`) **tanpa driver guard** → `migrate` di sqlite akan gagal. Kalau
  menambah migration destruktif, ikuti pola `2026_08_30_100000` yang cek
  `DB::getDriverName() !== 'mysql'` lalu `return`.
- `config/database.php` pakai `PDO::MYSQL_ATTR_SSL_CA` (bukan `Mysql::ATTR_SSL_CA`) — wajib untuk
  TiDB SSL.
- `env()` dipakai langsung di `app/helpers.php` dan `Cors.php` (bukan lewat `config/`) →
  **jangan jalankan `config:cache`/`optimize`** dulu, `FRONTEND_URL` jadi null.

## Autentikasi & boundary antar app

- Auth = **Laravel session**, bukan Sanctum/token. Blade login di `backend/`.
- **Tidak ada `routes/api.php`.** `bootstrap/app.php` hanya mendaftarkan `web.php` + `console.php`;
  semua endpoint JSON (`/berita`, `/lowongan`, `/auth-status`, `/csrf-token`) ada di `routes/web.php`.
- Web utama (`backend/`) **menolak login role admin** di `AuthController::login` → "Akun admin
  dikelola di panel admin terpisah". Role `kasir` → `/spp/kasir`, role `guru` → `/spp/rekap`.
- Panel admin (`backend-admin/`) **menolak semua role selain admin**.
- Middleware role = alias `role` (`CheckRole`). Salah role → redirect `/login` + error flash
  (atau 401/403 kalau `Accept: application/json`).
- Role DB: `admin | siswa | pendaftar | guru | kasir` (default kolom = `pendaftar`, bukan `siswa`).
  Frontend menormalisasi `pendaftar` → `siswa` (`src/composable/useAuthSession.js:norm()`).
- **Route backend TIDAK BOLEH berawalan `/api/`.** Vercel PHP runtime memotong `/api/*` sebagai
  path function. Route API ditulis polos: `/lowongan`, `/berita`, `/auth-status`, `/csrf-token`.
- Credential frontend ditentukan `VITE_BACKEND_URL` (`src/composable/useAuthSession.js:3`), default
  `http://localhost:8000`. Tidak ada `.env.example` di root — set manual bila perlu.
- Cross-origin: `app/Http/Middleware/Cors.php` hanya mengizinkan
  `https://smkbu-sby.vercel.app`, `localhost:5174`, dan `env(FRONTEND_URL)`. Origin lain = tanpa
  header CORS (request tetap jalan, browser yang memblokir).
- Handler 419/CSRF: `HandleTokenMismatch` bekerja di **layer response**
  (`$response->getStatusCode() === 419`), bukan `catch (TokenMismatchException)` — exception-nya
  tidak pernah sampai ke middleware luar.
- `layouts/app.blade.php` **tidak ada `@vite`** (sengaja dihapus; tidak ada build dir di production).
  CSS admin dimuat manual dari `/css/admin.css`. Jangan tambahkan `@vite` di blade.

## Aturan bisnis

- Status: `baru` → `diproses` → `diterima` / `ditolak`. Hanya admin yang mengubah (di panel admin).
- Siswa bisa edit form **hanya** bila `status = 'baru'` **dan** `now() < created_at + 3 hari`
  (`backend/app/Http/Controllers/PendaftaranController.php:202`).
- **Draft pendaftaran**: submit form sebagai guest → data disimpan ke session `pending_pendaftaran`
  (+ tabel `pendaftaran_drafts` + cookie `pending_draft`) → redirect login. Setelah login/register,
  `restorePendingDraft()` memulihkan draft dan form di-prefill dari `@json($draft)`. Draft dihapus
  hanya saat submit sukses. `HandleTokenMismatch` juga rescuing draft saat 419.
- Upload pendaftar: `store('pendaftaran', 'public')` → `storage/app/public/pendaftaran`.
- Role `pendaftar` + status `diterima` → dianggap `siswa` (ada di 3 tempat: `frontendAuthUrl()`
  di `app/helpers.php`, `AuthController@authStatus` di kedua backend).

## File penting

Backend (kedua folder):
- `bootstrap/app.php` — wiring middleware (Cors prepend, HandleTokenMismatch prepend, alias `role`)
- `app/Http/Controllers/AuthController.php` — login/register/logout + tolak role berlawanan
- `app/Http/Controllers/PendaftaranController.php` — `create/store/myDashboard/update`,
  `handleFileUploads`; di `backend-admin` juga `dashboard` + `updateStatus` + `resetUserPassword`
- `app/Http/Controllers/{SppController,TabunganController,KoperasiController}.php`
- `app/Http/Controllers/{LowonganController,LamaranController,BeritaApiController}.php` — **hanya `backend/`**
- `app/Http/Controllers/BeritaController.php` — **hanya `backend-admin/`** (CRUD Kelola Berita)
- `app/Http/Middleware/{CheckRole,Cors,HandleTokenMismatch,SecurityHeaders,PreventBrowserCache}.php`
- `app/Models/{Pendaftaran,User,SppBill,SppPayment,Tabungan,KoperasiOrder,Berita}.php`
- `app/Services/RegistrationInsightService.php` — versi `backend/` panggil NineRouter
  (`NINEROUTER_URL/MODEL/KEY`) + fallback; versi `backend-admin/` **sudah dipangkas, selalu fallback**
- `app/Console/Commands/GenerateSppBills.php` — `spp:generate`
- `app/helpers.php` — `frontendAuthUrl()` (handoff auth via query `?auth=<base64>`),
  `formatPeriode()`, `formatPeriodeShort()`
- `database/seeders/AdminSeeder.php` — **seeding juga siswa demo** (`siswa`/`siswa123`), bukan hanya admin

Frontend:
- `src/composable/useAuthSession.js` — sumber tunggal `BACKEND`, polling `/auth-status` 30 dtk
  (hanya saat login), baca `?auth=`
- `src/services/fetchJson.js` — fetch terautentikasi; `Accept: application/json` wajib agar 401
  jadi JSON, bukan HTML login
- `src/services/csrf.js` — cache token per sesi (retry kalau gagal), bukan per request
- `src/router/index.js` — `meta.requiresSiswa` guard; `/login` & `/register` cuma redirect ke `BACKEND`
- `vite.config.js` — plugin `chatApiPlugin` menyajikan `/api/chat` di dev; baca `GROQ_API_KEY` dari
  env root atau `src/server/.env` (folder ini tidak ada di repo, kode aman kalau hilang)
- `api/chat.js` + `api/knowledge/**` — serverless function Groq untuk production (dipakai juga
  FloatingAI). `api/knowledge/data/*.json` banyak yang **0 byte** (berita, faq, guru, organisasi,
  pkl, prestasi).

## JANGAN (tanpa instruksi eksplisit user)

- **Jangan ubah file `.env` / `src/server/.env` / key Groq.** `backend-admin/vercel.json`
  memuat kredensial DB production secara plaintext — jangan dicetak ulang ke jawaban/log.
- **Jangan sentuh chatbot**: `api/chat.js`, `api/knowledge/**`, bagian chat di `vite.config.js`.
- **Jangan hapus folder/file tanpa konfirmasi user.**
- **Jangan tambah dependency baru** tanpa diminta (repo sengaja ramping).
- Setelah selesai mengerjakan sesuatu, **WAJIB update `PROGRESS.md`** (entri sesi baru, langsung
  setelah pekerjaan, jangan ditunda) dan `IMPLEMENTATION_SUMMARY.md` bila ada perubahan teknis.
- Bahasa UI: Indonesia. Blade extends `layouts.app`, kecuali `pendaftaran/create.blade.php` yang
  full-custom. Bahasa komentar juga Indonesia/awet (banyak bertekan "ponytail" = catatan optimasi).

## Git

- Branch tunggal `main`, remote `https://github.com/Emenhagaming-cmyk/ga-ro.git`, tanpa CI.
- Commit message: prefix conventional (`feat:`, `fix:`, `docs:`) + deskripsi Bahasa Indonesia.
- `docs:` commit untuk update `PROGRESS.md`/`IMPLEMENTATION_SUMMARY.md` adalah pola yang biasa dipakai.
- **`npm install` (npm 11) mengubah `package-lock.json`** (menghapus flag `dev: true`) — jangan
  ikut ter-commit bersama perubahan fitur.
- `.playwright-mcp/*.yml|png` (46 file) adalah artefak snapshot browser yang ikut ter-commit —
  jangan dianggap fixture tes.

## Agent Skills

Skills di `.agents/skills/<name>/SKILL.md`; checklist bersama di `.agents/references/`
(accessibility, security, testing, performance, observability, definition-of-done, orchestration).

Intent → skill: fitur baru → `spec-driven-development` + `incremental-implementation` +
`test-driven-development`; planning → `planning-and-task-breakdown`; bug → `debugging-and-error-recovery`;
review → `code-review-and-quality`; refactor → `code-simplification`; API design → `api-and-interface-design`;
UI → `frontend-ui-engineering`; redesign landing → `design-taste-frontend`; performa → `performance-optimization`;
deploy → `shipping-and-launch`.

Panggil skill **sebelum** bertindak dan ikuti workflow-nya utuh — jangan separuh-separuh.
