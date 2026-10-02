# PERF.md — Ledger Optimasi Performa

Log setiap percobaan optimasi (treap & yang di-revert) supaya tidak di-ulang dua kali.
Rujukan kode: langkah `measure → identify → fix → verify → guard`.

### Sesi 2026-10-01 — Aset gambar hero (PNG → WebP)

| # | Ide | Baseline → Hasil | Verdict | Catatan |
|---|-----|------------------|---------|---------|
| F5 | Hero ilustrasi 3D convert **PNG → WebP** (alpha) | **338 KB → 41.7 KB** (−88%) | kept | `public/hero-siswa.webp`. Pillow lokal (Python 12.1.0 sudah ada di mesin) — **tanpa dependency baru**, sesuai guard "tanpa dependency baru". Alpha terverifikasi (4 sudut `(0,0,0,0)`). Konsisten dengan `sklh.webp`/`pmb_smkbu.webp` |
| F6 | `width`/`height` + `decoding="async"` di `<img>` hero, **tanpa** `loading="lazy"` | — | kept | hero = above fold & elemen LCP → `lazy` justru memperlambat LCP. Atribut dimensi reserve aspect ratio → cegah CLS |

### Guard (aset gambar baru)
- Gambar **di atas fold / LCP**: sertakan `width`+`height` (anti-CLS), **jangan** `loading="lazy"`, pertimbangkan WebP.
- Gambar **di bawah fold**: pakai `loading="lazy"` (seperti `AboutSchool.vue` `/sklh.webp`).
- Konversi alpha PNG→WebP: pakai Pillow lokal, **jangan** tambah dependency npm untuk ini.

## Sesi 2026-08-30 — Audit & optimasi menyeluruh (3 bagian)

Gejala user: halaman lambat load, interaksi berat (klik/geser/scroll), API/backend lambat.

### Frontend (Landing Vue — smkbu-sby.vercel.app)

| # | Ide | Baseline → Hasil | Verdict | Catatan |
|---|-----|------------------|---------|---------|
| F1 | **CursorGlow** pakai rAF + tulis el.style, hapus reactivity & CSS transition left/top | INP/geser: tiap mousemove dulu mutasi DOM Vue + transisi layout pada elemen fixed 260px → kini 1 style write di rAF, tanpa re-render | kept | penyebab utama INP geser/scroll di desktop; `passive:true` |
| F2 | **Lazy-load + lazy-mount** section home di bawah fold (SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview, TabunganBanner) | bundle route awal: HomeView 37.33kB→**25.36kB** (gz 11.33→8.34); CSS 46.2→28.3kB; tiap section jadi chunk ~1-1.6kB gz, di-fetch saat masuk viewport (rootMargin 600px) | kept | rootMargin 600px → mount off-screen, tanpa CLS terlihat |
| F3 | **CareerPreview** pakai `/lowongan/count` (bukan fetch semua lowongan) | dulu unduh seluruh list (25 baris + description) utk satu angka → kini 1 COUNT, respon `{"total":25}` | kept | butuh endpoint baru (B1) |
| F4 | **Auth polling**: `/auth-status` tiap 30s HAnya saat login | guest dulu fetch tiap 30s tanpa henti → kini di-skip utk guest; interval singleton modul | kept | hemat jaringan/baterai mobile |

### Backend (Laravel — pendaftaranspmb.vercel.app)

| # | Ide | Baseline → Hasil | Verdict | Catatan |
|---|-----|------------------|---------|---------|
| B1 | Endpoint **`GET /lowongan/count`** (ringan) | — | kept | dipakai F3; route sebelum `/{lowongan}` |
| B2 | **Tabungan saldo via aggregate** `SUM(CASE WHEN type...)` di `index` & `store` | dulu load semua transaksi → reduce di PHP (2x/req) → kini 1 query aggregate. **Verifikasi: hasil identik** (agg=1000, php=1000, match=YES) | kept | `saldoFor()` helper pribadi |
| B3 | Spp `kasirIndex` filter belum-lunas di **SQL** (`havingRaw paid < nominal`) | dulu load semua bill lalu filter di PHP | kept | pakai `withSum('payments as paid')` |
| B4 | Index DB (status, jurusan, is_active, etc.) | — | **reverted/tidak dibuat** | tabel kecil (~25 lowongan, ratusan pendaftaran) → seq scan lebih murah, index = pajak tulis tanpa bukti query plan. Sesuai kriteria skip skill |

### Panel Admin (backend-admin — spmb-admin.vercel.app)

| # | Ide | Baseline → Hasil | Verdict | Catatan |
|---|-----|------------------|---------|---------|
| A1 | `chartData()` 30×COUNT jadi **1×GROUP BY DATE(created_at)** + reuse `freshStats()` cache | 37 query → ~2 query | kept | `chartData` saat ini tak dipanggil (dead code) — aman |
| A2 | `laporan()` 8×COUNT terpisah → **pakai `freshStats()` cached** | 8 query uncached → 1 cached | kept | bentuk `$stats` sama, view tetap jalan |

### Catatan / dipertimbangkan-tapi-dilewati
- Hard limit `/lowongan` (tabel 25 baris): ditunda — halaman cari lowongan butuh list penuh utk filter client-side; over-fetch di home sudah dituntaskan via count endpoint.
- LoadingScreen.vue: tidak dipakai DI MANAPUN (hanya definisi) — bukan bottleneck produksi.
- Instruksi: JANGAN sentuh `.env`, chatbot (`api/chat.js`, `knowledge/**`, bagian chat vite.config), tanpa dependency baru.

### Verifikasi
- `php -l` bersih semua controller (backend & admin).
- `route:list` bersih; `/lowongan/count` urutan sebelum `/{lowongan}`.
- HTTP `/lowongan/count` → `{"total":25}` (dev server 8000).
- Aggregat Tabungan == reduce PHP (real data, match).
- Build Vite sukses (20.5s), bundle route awal turun.
- Browser (port dev): semua section lazy render benar saat scroll; tidak ada error JS baru (error console murni CORS karena test pakai port 5175 ≠ 5174 diizinkan).

### Guard (untuk mencegah regresi)
- Jika menambah section home baru → letakkan di bawah fold pakai `LazyMount`.
- Jangan re-introduce render-blocking fecth `/lowongan` di home utk count.
- Polling auth/jangan dibuka utk guest.

## Sesi 2026-08-30 (2) — Optimasi Panel Admin (backend-admin → paneladminsmkbu.vercel.app)

Perintah user: "optimalisasi halaman admin dashboard". Skill performance disarankan `measure first`, user pilih **langsung fix yang jelas** (traffic rendah, temuan eksplorasi sudah konkret).

### MEASURE (hasil terverifikasi live, bukan asumsi)

| Feat | Waktu terukur |
|------|---------------|
| SQL queries ke TiDB (freshStats groupBy, dupNisn/Nik, latest) | **34–38 ms** per query |
| TLS handshake TiDB (dari region deploy) | ~208 ms |
| `GET /admin` setelah idle (cold start serverless) | **~4.4 s** |
| `GET /admin` hangat (warm) | **~530 ms** |
| `GET /login` warm | ~436 ms |

**IDENTIFY: bottleneck BUKAN SQL** (35 ms) tapi cold start Vercel-PHP per request. Index DB tidak berubah angka pada skala 5 baris — nilainya arsitektural (scale-up) & dibuktikan via `SHOW INDEX`.

### FIX (backend-admin; deployed, terverifikasi)

| # | Ide | Baseline → Hasil | Verdict | Catatan |
|---|-----|------------------|---------|---------|
| A3 | **Index DB** `pendaftarans`: status, user_id, created_at, nisn, nik, composite(status,jurusan_pilihan) | query terukur 35 ms; index terpasang (`SHOW INDEX` verified) | kept | **Override B4** (yang revert di backend web) atas persetujuan user — bentuk index sesuai query GROUP BY/filter/ORDER FK yang dipakai admin; pajak tulis kecil (5 baris, volume masukan pendaftaran rendah). NILAI NYATA baru terasa saat puluhan ribu baris. |
| A4 | **exportCsv** `->get()` → `select(18 kolom)+cursor()` streaming | ALL rows+45 kolom di RAM → 1 baris/sejarah | kept | mencegah OOM di Vercel (1024MB) saat data membesar; CSV terverifikasi 200 + isi benar |
| A5 | **Spp rekap** `->get()` → `->take(200)` | hydrate ALL siswa+bills+payments → bounded | kept | safety limit; pane rekap tetap utuh untuk ratusan siswa |
| A6 | **laporan** weekly `->get()` → `->limit(100)` | unbounded → bounded | kept | week padat aman |
| A7 | Hapus `->header('Cache-Control: max-age=30')` di dashboard | dead code — middleware `PreventBrowserCache` override | kept | middleware no-cache benar utk panel admin |
| A8 | **Fix bug**: `plain_password` (kolom sudah di-drop di TiDB) dari `User::fillable` + `resetUserPassword()` | `resetUserPassword` di prod → SQL error "unknown column" → bersih | kept | ditemukan saat review; akun admin pun ber-role `pendaftar` di TiDB (bug data) — diperbaiki `role='admin'`, login panel terverifikasi |

### GUARD & keputusan
- **Cold start (4.4 s) tetap ada** — melekat di Vercel-PHP serverless; polling dashboard 20 s di browser otomatis menjaga instance hangat saat panel terbuka. Keep-warm cron dipertimbangkan → **skip** (nilai marginal, Hobby plan, user tidak minta).
- Jangan tambah index baru tanpa ukuran query plan; tulis budget tulis vs baca di sini.
- Endpoint dinamis `/admin` dll tidak bisa di-cache CDN (session-cookie + per-admin) — cache layer yang tepat sudah dipakai (in-memory stats 10 s; `CACHE_STORE=database` sekarang aktif utk future `Cache::`).
