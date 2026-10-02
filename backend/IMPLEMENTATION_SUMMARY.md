# IMPLEMENTATION_SUMMARY.md — Backend SPMB SMK Bahrul Ulum

> **Catatan**: File ini adalah salinan dari root `IMPLEMENTATION_SUMMARY.md`. 
> Update di file utama, bukan di sini.

Ringkasan implementasi backend Laravel 12. Auth session-based (bukan Sanctum), blade views.

---

## Arsitektur

| Layer | Posisi | Teknologi |
|---|---|---|
| Backend SPMB | `backend/` | Laravel 12, MySQL `pendaftaran_db`, session auth (blade) |
| Landing page | root `ga-ro/` | Vue 3 + Vite (port 5174) |
| Panel Admin | `backend-admin/` | Laravel 12 (salinan backend), admin-only |

Auth memakai Laravel session (login/register/logout via blade), **bukan** Sanctum/API. File Vue `LoginView.vue`, `DashboardSiswa.vue`, `DashboardAdmin.vue` **TIDAK DIPAKAI**.

---

## Alur Pendaftaran

1. Guest submit → data ke session `pending_pendaftaran` + DB `pendaftaran_drafts` + cookie
2. Login/register → restore draft → form terisi otomatis (JS prefill dari `@json($draft)`)
3. Login siswa → `/dashboard-siswa` (status + edit ≤3 hari)
4. Admin akses `/admin` via URL (panel terpisah). Status: `baru` → `diproses` → `diterima`/`ditolak`

---

## Key Files

- `routes/web.php` — semua routes (web, bukan API)
- `app/Http/Controllers/AuthController.php` — auth + reset
- `app/Http/Controllers/PendaftaranController.php` — CRUD + export + bukti PDF
- `app/Http/Controllers/SppController.php` — SPP (kasir/rekap/ortu)
- `app/Http/Controllers/TabunganController.php` — tabungan siswa
- `app/Http/Controllers/LowonganController.php` — career center
- `app/Http/Controllers/LamaranController.php` — lamaran
- `app/Http/Controllers/BeritaApiController.php` — berita API
- `app/Http/Middleware/` — CheckRole, Cors, HandleTokenMismatch, PreventBrowserCache, SecurityHeaders
- `app/helpers.php` — frontendAuthUrl(), formatPeriode()
- `resources/views/` — blade templates

---

## Credentials

- Admin: `admin` / `admin123`
- MySQL: root tanpa password, DB `pendaftaran_db`

---

**Last Updated:** 2026-08-30
**Status:** Production Ready
