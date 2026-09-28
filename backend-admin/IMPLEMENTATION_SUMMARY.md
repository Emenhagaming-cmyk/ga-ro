# IMPLEMENTATION_SUMMARY.md — Panel Admin SPMB SMK Bahrul Ulum

> **Catatan**: File ini adalah salinan dari root `IMPLEMENTATION_SUMMARY.md` (section Panel Admin).
> Update di file utama, bukan di sini.

---

## Arsitektur

| Layer | Posisi | Teknologi |
|---|---|---|
| Panel Admin | `backend-admin/` | Laravel 12, MySQL `pendaftaran_db` (sama), session auth |
| Domain | `paneladminsmkbu.vercel.app` | Project Vercel `spmb-admin` |

Login **hanya admin**. Siswa/pendaftar ditolak ("Hanya akun admin yang dapat mengakses panel ini."). DB sama dengan backend utama (TiDB production).

---

## Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Redirect `/admin` |
| GET | `/login` | Login admin |
| POST | `/login` | Process login |
| GET | `/logout` | Logout |
| GET | `/forgot-password` | Forgot password |
| GET | `/admin` | Dashboard (stats, Chart.js, insight) |
| GET | `/pendaftaran` | Data pendaftar (filter, search, export) |
| GET | `/pendaftaran/export` | CSV export |
| GET | `/pendaftaran-snapshot` | Live stats JSON |
| GET | `/pendaftaran/{id}` | Detail pendaftar |
| PUT | `/pendaftaran/{id}/status` | Quick status update |
| DELETE | `/pendaftaran/{id}` | Hapus pendaftaran |
| GET | `/admin/spp/rekap` | Rekap SPP |
| GET | `/admin/tabungan` | Kelola tabungan |
| GET | `/admin/koperasi` | Kelola koperasi |
| GET | `/berita` | Kelola berita (CRUD) |
| POST | `/admin/akun/{user}/reset-password` | Reset password siswa |

---

## Key Files

- `routes/web.php` — admin-only routes
- `app/Http/Controllers/PendaftaranController.php` — dashboard, index, show, export, snapshot, chartData, laporan, resetUserPassword
- `app/Http/Controllers/TabunganController.php` — adminIndex, adminShow
- `app/Http/Controllers/BeritaController.php` — CRUD berita
- `app/Models/User.php` — relasi tabungans(), koperasiOrders()
- `resources/views/layouts/app.blade.php` — sidebar layout
- `resources/views/pendaftaran/` — dashboard, index, show, laporan, berita views

---

## Fitur

- **Dashboard**: stat cards + Chart.js + "Ringkasan Data" + auto-refresh
- **Data Pendaftar**: filter (q/status/jurusan/duplikat), search, pagination, export CSV streaming
- **Detail Pendaftar**: semua field + badge status + quick status
- **Laporan Mingguan**: print-ready A4 + kop sekolah
- **Badge Live Sidebar**: polling 20s, notif data baru
- **Rekap SPP**: tabel rekap + link ortu (copy)
- **Kelola Tabungan/Koperasi**: admin index + show
- **Kelola Berita**: CRUD lengkap + upload gambar + draft/tayang
- **Reset Password Siswa**: generate random password + tampilkan plain

---

## Credentials

- Admin: `admin` / `admin123`
- `.env` lokal panel: sqlite + APP_KEY lokal (JANGAN dipakai production)

---

## Deploy

```powershell
cd C:\Users\LENOVO\lomba\ga-ro\backend-admin
& "C:\nvm4w\nodejs\vercel.cmd" deploy --prod --yes
& "C:\nvm4w\nodejs\vercel.cmd" alias set <url> paneladminsmkbu.vercel.app
```

**Last Updated:** 2026-08-30
**Status:** Production Ready
