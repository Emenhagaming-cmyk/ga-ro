# SPMB SMK Bahrul Ulum

Sistem Penerimaan Murid Baru (SPMB) online untuk SMK Bahrul Ulum Surabaya.

## Tech Stack

- **Frontend**: Vue 3 + Vite — [smkbu-sby.vercel.app](https://smkbu-sby.vercel.app)
- **Backend**: Laravel 12 — [pendaftaranspmb.vercel.app](https://pendaftaranspmb.vercel.app)
- **Panel Admin**: Laravel 12 — [paneladminsmkbu.vercel.app](https://paneladminsmkbu.vercel.app)
- **Database**: MySQL (TiDB Cloud production)
- **AI Chatbot**: Groq-powered (model `openai/gpt-oss-120b`)

## Fitur

- Form pendaftaran multi-step (5 langkah) + draft persistence
- Dashboard siswa (status pendaftaran, edit ≤3 hari)
- Panel admin terpisah (stat cards, Chart.js, export CSV, filter, laporan)
- SPP (kasir input, guru rekap, ortu lihat via signed URL)
- Tabungan siswa (setor/tarik)
- Career Center (lowongan + lamaran)
- Koperasi (keranjang + checkout QRIS/Transfer)
- Berita/News
- Chatbot BISA
- Security hardening (rate limiting, CSRF, XSS protection, upload validation)

## Quick Start

```powershell
# Backend (port 8000)
cd backend
php artisan serve --port=8000
php artisan migrate
php artisan db:seed --class=AdminSeeder

# Frontend (port 5174)
npm install
npm run dev
```

## Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Siswa demo | `siswa` | `siswa123` |

## Documentation

- `AGENTS.md` — Konteks permanen proyek (dibaca AI agent)
- `IMPLEMENTATION_SUMMARY.md` — Ringkasan teknis lengkap
- `PROGRESS.md` — Log pekerjaan per sesi
- `PERF.md` — Ledger optimasi performa
