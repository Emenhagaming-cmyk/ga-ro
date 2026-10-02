# AGENTS.md — SPMB SMK Bahrul Ulum

Konteks permanen repo ini (1 git repo, 3 deployable terpisah). Baca juga `PROGRESS.md` (status terakhir + TODO) dan `IMPLEMENTATION_SUMMARY.md` (ringkasan teknis).

## Peta Repo

| Path | Deployable | Domain / Port |
|------|-----------|---------------|
| `backend/` | Laravel 12 — web publik (auth, form, dashboard siswa, JSON API) | `pendaftaranspmb.vercel.app` / dev `:8000` |
| `backend-admin/` | Laravel 12 — panel admin (JAUHANKAN dari `backend`) | `paneladminsmkbu.vercel.app` |
| root (`src/`, `api/`, `public/`) | Vue 3 + Vite landing page | `smkbu-sby.vercel.app` / dev `:5174` |

- Ketiganya deploy sebagai 3 project Vercel terpisah (`lomba`, `spmb-backend`, `spmb-admin`) dari 3 folder berbeda.
- `backend/` + `backend-admin/` = satu DB yang sama (`pendaftaran_db`: MySQL lokal Laragon, TiDB Cloud production).
- `api/chat.js` + `api/knowledge/**` = serverless function chatbot Groq, hanya milik project root.
- `.agents/skills/` = agent skills (lihat bagian bawah). `.playwright-mcp/` + `test-results/` = artefak MCP, bukan kode.

## ⚠️ `backend-admin` = FORK dari `backend`, bukan hasil build

Perubahan tidak otomatis tercermin. Setelah edit di `backend`, salin manual ke `backend-admin` bila file-nya memang/shared. File yang saat ini **berbeda isi**:

`routes/web.php`, `app/helpers.php`, `AuthController`, `PendaftaranController`, `SppController`, `TabunganController`, `KoperasiController`, `Models/User`, `Services/RegistrationInsightService`, `Middleware/Cors`.

`backend-admin` **tidak punya** `Lowongan/Lamaran`, `BeritaApiController`, `SecurityHeaders`, `GenerateSppBills`, dan **5 migrasi** (`create_tabungans`, `create_lowongans`, `create_lamarans` ×3, `drop_plain_password`, `add_avatar`). ⇒ Di `backend-admin` jangan pernah `migrate:fresh`/`migrate:rollback`; hanya `migrate` (tabel `migrations` production sudah mencatat semuanya).

## Command

```powershell
# Web backend (:8000)
cd backend; php artisan serve --port=8000
# Panel admin (:8001, opsional)
cd backend-admin; php artisan serve --port=8001
# Landing page (:5174, strictPort)
npm run dev

# Verifikasi — tidak ada lint/typecheck/formatter di repo ini
cd backend; php artisan test              # 39 passed / 144 assertions, ~25 dtk
cd backend; php artisan test --filter=SppControllerTest   # 1 file
php artisan view:cache                    # satu-satunya cek blade yang valid
npm run build                             # cek Vue
```

- **PowerShell memblokir `npm`** (ExecutionPolicy) → pakai `& "C:\nvm4w\nodejs\npm.cmd" run build`. CLI Vercel juga: `& "C:\nvm4w\nodejs\vercel.cmd"`.
- `php artisan test` jalan di **sqlite `:memory:`** (`phpunit.xml`) → MySQL tidak perlu hidup.
- `backend-admin` test suite **MERAH sejak awal**: 2 gagal (`ExampleTest` 302≠200, `RegistrationInsightServiceTest` — service-nya sengaja fallback-only). Bukan regresi kamu; jangan "perbaiki" tanpa diminta.
- PHP lokal 8.2; production Vercel PHP 8.3.
- `php artisan migrate` untuk lokal saja. Production = TiDB; jalankan migrasi ke production **hanya atas perintah eksplisit user**.

## Gotcha Teknis yang Mudah Terlewat

1. **Tidak boleh ada prefix `/api/` di route Laravel** — Vercel PHP runtime mengintercept `/api/*` sebagai function path. Semua JSON endpoint ada di root: `/berita`, `/lowongan`, `/lamaran`, `/spp`, `/tabungan`, `/koperasi`, `/auth-status`, `/csrf-token`.
2. **Auth = Laravel session lintas-domain, bukan token/Sanctum.** Mobile memblokir cookie third-party ⇒ handoff ke landing pakai `?auth=<base64 json>` (`frontendAuthUrl()` di `app/helpers.php`, dibaca `src/composable/useAuthSession.js`). Jangan "rapikan" mekanisme ini.
3. **Request Vue → Laravel wajib `Accept: application/json`** (`src/services/fetchJson.js`), kalau tidak sesi habis membalas HTML login dan `res.json()` crash. CSRF: `GET /csrf-token`, di-cache sekali per sesi (`src/services/csrf.js`).
4. **`CACHE_STORE` production wajib `database`**, kalau `array`/`file` rate limiting diam-diam mati. `backend/.env.deploy` masih menua (`array`, `SESSION_DRIVER=file`, `FRONTEND_URL` domain lama) → jangan dipakai acuan production; acuan = env project Vercel + blok `env` di `backend-admin/vercel.json`.
5. **Upload file production tidak persisten**: disk `public` di `config/filesystems.php` memakai `PUBLIC_DISK_ROOT` = `/tmp` di production. Fitur upload baru butuh storage eksternal.
6. **vercel-php tidak menyajikan `public/`** → aset statis harus lewat route eksplisit (lihat `routes/web.php:16-17` dan blok `routes` di `vercel.json`).
7. `config/database.php` harus pakai `PDO::MYSQL_ATTR_SSL_CA` (bukan `Mysql::ATTR_SSL_CA`) — wajib untuk PHP 8.3 di Vercel.
8. **Error LSP di dalam HTML/CSS Blade itu false positive.** `php artisan view:cache` yang jadi acuan.
9. Ringkasan AI di dashboard admin pakai env `NINEROUTER_URL` / `NINEROUTER_MODEL` / `NINEROUTER_KEY` (OpenAI-compatible). Kalau kosong → jatuh ke `buildFallbackSummary()` (ringkasan lokal).
10. `backend-admin/.env` lokal = sqlite + APP_KEY lokal, **hanya** untuk artisan. `vendor/` di kedua folderbackend di-copy lokal, tidak ikut deploy.

## Deploy (Vercel)

```powershell
vercel deploy --prod --yes
vercel alias set <deployment-url> <domain>   # WAJIB tiap deploy — alias tidak otomatis
```

- `vercel link` membuat `.env.local` → **hapus** setelahnya.
- `fetch failed` saat deploy → retry.
- Project Vercel baru bisa punya deployment protection (`ssoProtection`) → matikan via API PATCH `{"ssoProtection":null}`.
- Root `.env.local` / `.env.production` berisi `VERCEL_OIDC_TOKEN` (gitignored) — jangan pernah di-commit.

## Aturan Bisnis yang Jangan Dilanggar

- **Web publik hanya untuk siswa/pendaftar.** Login admin **ditolak** di `backend` (`AuthController::login`) dan tidak ada route `/admin` di sana (→ 404). **Dilarang** menambah link/button/navbar admin di halaman publik.
- Role DB `users.role` = `admin | siswa | pendaftar | guru | kasir`. Frontend hanya mengenal `admin` vs `siswa`: role `pendaftar` dinormalisasi jadi `siswa` bila pendaftaran.status = `diterima`.
- Redirect setelah login (`backend`): `kasir` → `/spp/kasir`, `guru` → `/spp/rekap`, lainnya → landing.
- Status: `baru` → `diproses` → `diterima` / `ditolak`. Hanya admin panel yang mengubahnya.
- Siswa hanya bisa edit form bila `status = 'baru'` **dan** `created_at` ≤ 3 hari.
- Draft pendaftaran guest: disimpan di session `pending_pendaftaran` **dan** baris `pendaftaran_drafts` (di-key cookie `pending_draft`), dipulihkan `AuthController::restorePendingDraft()` setelah login. Hapus hanya setelah submit sukses.
- Upload: `store('pendaftaran', 'public')`.

## Konvensi Kode

- Bahasa UI: Indonesia.
- Blade `extends 'layouts.app'` — kecuali `resources/views/pendaftaran/create.blade.php` (full custom + navbar sendiri) dan `auth/profile.blade.php`.
- Route ditulis manual, **`Route::resource` tidak dipakai** di repo ini.
- Nama tabel/kolom snake_case; enum status/role memakai string Bahasa Indonesia.

## House Rules (dari user, berulang kali ditegaskan)

- **WAJIB update `PROGRESS.md` segera setelah pekerjaan selesai** — tambah entri sesi baru di bagian atas, jangan ditunda. Sinkronkan `IMPLEMENTATION_SUMMARY.md` kalau ada fakta teknis/verifikasi baru.
- **JANGAN** mengubah `.env` mana pun, `api/chat.js`, `api/knowledge/**`, atau bagian chat di `vite.config.js` — kecuali diperintah eksplisit.
- **JANGAN** hapus folder/file tanpa konfirmasi user.
- Jangan commit/push kecuali diminta. Gaya commit: pendek Bahasa Indonesia, langsung ke branch default (`ll`, `jj`, `memek`).

## Alur Kerja Agent

1. Cek skill yang cocok di `.agents/skills/<name>/SKILL.md` → panggil tool `skill` **sebelum** bertindak, dan ikuti workflow-nya utuh.
   Intent → skill: fitur baru = `spec-driven-development` (+ `incremental-implementation`, `test-driven-development`); bug = `debugging-and-error-recovery`;planning = `planning-and-task-breakdown`; review = `code-review-and-quality`; UI = `frontend-ui-engineering`; redesign landing = `design-taste-frontend`; deploy = `shipping-and-launch`. Checklist bersama: `.agents/references/`.
2. Review config/`.md` dulu sebelum implementasi. Bila butuh desempenho, `PERF.md` punya ledger optimasi yang sudah dipakai.
3. Verifikasi dengan urutan: `php artisan view:cache` → `php artisan test` → `npm run build`.
4. Selesai → update `PROGRESS.md`.

## Kredensial Dev

- Admin panel: `admin` / `admin123` (backend menolak login admin; pakai `backend-admin`).
- Siswa demo: `siswa` / `siswa123`. MySQL lokal: `root`, tanpa password, DB `pendaftaran_db`.