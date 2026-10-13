# Implementation Plan — SPMB SMK Bahrul Ulum Bug Fixes + Chatbot Agent

## Context
- **Current state**: VPS shutdown, fokus development lokal (MariaDB Laragon, PHP 8.2, Node 20+)
- **Production target nanti**: VPS dengan 3 subdomain (`smkbu-sby.my.id`, `api.smkbu-sby.my.id`, `admin.smkbu-sby.my.id`)
- **Chatbot**: Laravel job + scheduler di VPS (bukan Vercel serverless)
- **NineRouter**: Optional AI insight untuk admin dashboard (fallback template sudah ada)

---

## Phase 1: Local Bug Fixes (Critical + Medium)

### 1.1 SPP Payment — Kasir/Admin Bisa Bayar
**File**: `backend/app/Http/Controllers/SppController.php:50`
**Fix**: Hapus cek `$bill->user->role !== 'siswa'`. Validasi cukup `$bill->user->role === 'siswa'` (bill owner).
```php
// Before
if ($bill->user->role !== 'siswa') { return 422; }

// After — only validate bill belongs to siswa
if ($bill->user->role !== 'siswa') { abort(404); } // bill not found for non-siswa
```

### 1.2 Tabungan — Admin/Kasir Bisa Setor/Tarik untuk Siswa
**File**: `backend/app/Http/Controllers/TabunganController.php:52`
**Fix**: Sama, hapus cek role user autentikasi.
```php
// Before
if ($targetUser->role !== 'siswa') { return 422; }

// After — already correct, targetUser is the siswa
```

### 1.3 Koperasi Checkout — Cross-Origin Credentials
**File**: `src/views/KoperasiView.vue:607`
**Fix**: `credentials: "same-origin"` → `"include"`

### 1.4 Pendaftaran Edit — File Upload Tidak Terhapus
**File**: `backend/app/Http/Controllers/PendaftaranController.php:208-210`
**Fix**: Simpan existing file paths sebelum `handleFileUploads()`, merge:
```php
$existingFiles = ['foto_3x4', 'kk_file', 'ijazah_file', 'sktm_file'];
foreach ($existingFiles as $field) {
    $data[$field] = $data[$field] ?? $pendaftaran->$field;
}
```

### 1.5 Pendaftaran Store — Draft Hanya untuk Guest
**File**: `backend/app/Http/Controllers/PendaftaranController.php:95-96`
**Fix**: Pindah `session()->put('pending_pendaftaran', ...)` **SETelah** cek `exists()`.

### 1.6 SPP Link Ortu — Generate Signed URL Endpoint
**Files**:
- `backend/app/Http/Controllers/SppController.php` — tambah method `generateOrtuLink(User $user)`
- `backend/routes/web.php` — route `GET /spp/ortu-link/{user}` (middleware `auth,role:kasir,admin`)
- `backend/resources/views/spp/kasir.blade.php` — tombol "Generate Link Ortu"

### 1.7 Frontend Role Normalization — Hanya Diterima Jadi Siswa
**File**: `src/composable/useAuthSession.js:26-30`
**Fix**: `norm()` cek status `diterima`:
```js
function norm(d) {
  if (d && d.logged_in && d.role === 'pendaftar' && d.status === 'diterima') {
    return { ...d, role: 'siswa' };
  }
  return d;
}
```

### 1.8 Top Jurusan — Deterministic Tie-Breaking
**File**: `backend/app/Services/RegistrationInsightService.php:70-81`
**Fix**: Secondary sort by jurusan name:
```php
$max = max($jurusan);
$top = array_filter($jurusan, fn($v) => $v === $max);
ksort($top); // RPL < TKJ < AKL alphabetical
return array_key_first($top);
```

### 1.9 Reset Password — Crypto-Secure Random
**File**: `backend-admin/app/Http/Controllers/PendaftaranController.php:215`
**Fix**: `Str::password(12)` (Laravel 12) atau `Str::random(16)`.

### 1.10 SPP Controller — N+1 Query Fix
**File**: `backend/app/Http/Controllers/SppController.php:16,64,130`
**Fix**: 
- `index()`: pakai `$bill->payments->sum('amount')` (collection, already eager-loaded)
- `adminIndex()`: add `with('payments')` to query

### 1.11 Dead Code Removal — Backend PendaftaranController
**File**: `backend/app/Http/Controllers/PendaftaranController.php`
**Hapus methods**: `index`, `show`, `updateStatus`, `destroy`, `snapshot`, `exportCsv` (tidak ada route di `backend/routes/web.php`)

### 1.12 Local Env Setup
**Files**:
- `backend/.env` (copy dari `.env.example`, set `DB_CONNECTION=mysql`, `DB_DATABASE=pendaftaran_db`, `FRONTEND_URL=http://localhost:5174`)
- `backend-admin/.env` (copy dari backend, `FRONTEND_URL=http://localhost:5174`)
- Root: `VITE_BACKEND_URL=http://localhost:8000` (di `.env.local` atau shell export)

---

## Phase 2: Chatbot Agent — Laravel VPS Productionization

### 2.1 Architecture
```
User (frontend) → POST /api/chat → Laravel Controller
    → Dispatch Job: CrawlAndAnswerJob
    → Queue Worker: crawl → extract → embed → search → answer
    → Return JSON response (async via polling atau WebSocket)
```

### 2.2 Components to Build

| Component | Location | Description |
|-----------|----------|-------------|
| **Crawl Job** | `backend/app/Jobs/CrawlAndAnswerJob.php` | Queueable, implements `ShouldQueue` |
| **Crawl Service** | `backend/app/Services/WebCrawlerService.php` | Guzzle + DOM parsing, respect robots.txt, rate limit |
| **Extractor** | `backend/app/Services/ContentExtractor.php` | Extract main content, links, metadata dari HTML |
| **Indexer** | `backend/app/Services/SearchIndexer.php` | Store ke DB (fulltext) atau Meilisearch/Typesense kalau available |
| **Search Service** | `backend/app/Services/SemanticSearch.php` | Keyword + vector search (pgvector/Meilisense) |
| **Answer Generator** | `backend/app/Services/AnswerGenerator.php` | Groq LLM call dengan context dari search results |
| **Controller** | `backend/app/Http/Controllers/ChatController.php` | `POST /api/chat` → dispatch job, return job ID; `GET /api/chat/{jobId}` → poll result |

### 2.3 Database Schema (Migration)
```php
// chat_sessions
- id, user_id (nullable), question, status (pending/processing/completed/failed), answer, sources (JSON), created_at, updated_at

// crawled_pages  
- id, url, title, content, hash (dedup), metadata (JSON), crawled_at

// search_index (kalau pakai DB fulltext)
- page_id, content_vector (kalau pgvector), keywords
```

### 2.4 Queue & Scheduler Config
**Queue Worker**: `php artisan queue:work --queue=chat --tries=3 --timeout=120`
**Scheduler** (`backend/app/Console/Kernel.php`):
```php
// Re-crawl entire site weekly
$schedule->command('chat:crawl-full')->weekly();
// Re-index changed pages daily
$schedule->command('chat:reindex')->daily();
```

### 2.5 Commands
- `chat:crawl-full` — crawl sitemap `smkbu-sby.my.id`, extract & index all pages
- `chat:reindex` — re-index pages dengan hash berubah
- `chat:test {question}` — test end-to-end tanpa queue

### 2.6 Frontend Integration
**File**: `src/services/chat.js` (baru) atau update `src/views/ChatView.vue`
- POST `/api/chat` dengan `{ question, session_id? }`
- Poll `GET /api/chat/{jobId}` setiap 2 detik sampai `completed`
- Fallback ke static knowledge (`api/knowledge/data/*.json`) kalau job gagal

### 2.7 Groq Integration
- Env: `GROQ_API_KEY`, `GROQ_MODEL` (default `llama-3.3-70b-versatile`)
- Service: `backend/app/Services/GroqClient.php` (wrapper HTTP dengan retry)

---

## Phase 3: VPS Hardening (When VPS Online)

### 3.1 SSL Certificates
```bash
certbot --nginx -d api.smkbu-sby.my.id -d smkbu-sby.my.id -d admin.smkbu-sby.my.id
```

### 3.2 Nginx Config (3 Server Blocks)
1. **Frontend** (`smkbu-sby.my.id`) → root `/var/www/frontend/dist`, try_files `$uri /index.html`
2. **Backend API** (`api.smkbu-sby.my.id`) → proxy_pass `unix:/run/php/php8.2-fpm-backend.sock`
3. **Admin Panel** (`admin.smkbu-sby.my.id`) → proxy_pass `unix:/run/php/php8.2-fpm-admin.sock`

### 3.3 PHP-FPM Pools (2 Pools)
- `www-backend` → user `www-backend`, socket `/run/php/php8.2-fpm-backend.sock`
- `www-admin` → user `www-admin`, socket `/run/php/php8.2-fpm-admin.sock`

### 3.4 CORS Allowlist (Production)
**File**: `backend/app/Http/Middleware/Cors.php` & `backend-admin/app/Http/Middleware/Cors.php`
```php
$allowedOrigins = array_values(array_unique(array_filter([
    'https://smkbu-sby.my.id',
    'https://admin.smkbu-sby.my.id',
    'https://api.smkbu-sby.my.id',
    env('FRONTEND_URL'), // fallback
])));
```

### 3.5 SecurityHeaders Middleware
- Copy `backend/app/Http/Middleware/SecurityHeaders.php` → `backend-admin/app/Http/Middleware/`
- Register di `backend-admin/bootstrap/app.php`

### 3.6 Migration Guards (3 MySQL-only files)
Tambah di `up()` method:
```php
if (DB::getDriverName() !== 'mysql') return;
```
Files: `2026_08_03_152852`, `2026_08_03_153258`, `2026_08_09_094358`

### 3.7 Knowledge JSON Population
Populate 6 zero-byte files di `api/knowledge/data/`:
- `berita.json`, `faq.json`, `guru.json`, `organisasi.json`, `pkl.json`, `prestasi.json`
- Minimal: `[]` atau data seed dari DB

### 3.8 Env Production
- `APP_KEY` generate
- `APP_DEBUG=false`
- `SESSION_DRIVER=database` (shared DB → SSO)
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database` (untuk chat jobs)
- `GROQ_API_KEY` set

---

## Validation Plan

### Local (Phase 1)
| Test | Command |
|------|---------|
| Backend tests | `cd backend && composer test` |
| Frontend build | `npm.cmd run build` |
| Manual: SPP bayar kasir | Login kasir → `/spp/kasir` → bayar tagihan siswa |
| Manual: Tabungan admin | Login admin → `/admin/tabungan` → setor untuk siswa |
| Manual: Koperasi checkout | Login siswa → `/koperasi` → checkout QRIS/Transfer |
| Manual: Edit pendaftaran | Login siswa (status baru < 3 hari) → edit form tanpa upload file → cek file tetap ada |
| Manual: Link ortu | Login kasir → generate link → buka di incognito → lihat rekap SPP |

### Chatbot (Phase 2)
| Test | Criteria |
|------|----------|
| Unit: Crawler | `php artisan chat:test "apa itu SPMB"` → return answer dengan sources |
| Unit: Search | Query "biaya daftar" → return halaman berisi "biaya" |
| Integration: Full flow | Frontend chat → job dispatched → poll → answer received < 30s |
| Fallback | Groq down → static knowledge answer |

### VPS (Phase 3)
| Check | Command |
|-------|---------|
| SSL valid | `curl -I https://api.smkbu-sby.my.id` → 200, valid cert |
| CORS headers | `curl -H "Origin: https://smkbu-sby.my.id" -H "Access-Control-Request-Method: POST" -X OPTIONS https://api.smkbu-sby.my.id/auth-status` → 204 + ACAO header |
| Security headers | `curl -I https://api.smkbu-sby.my.id` → CSP, HSTS, X-Frame-Options present |
| Queue worker | `supervisorctl status` → chat queue RUNNING |
| Scheduler | `crontab -l` → `schedule:run` every minute |

---

## Dependencies & Order

```
Phase 1 (independent, parallelizable)
├── 1.1, 1.2, 1.3, 1.4, 1.5, 1.7, 1.8, 1.9, 1.10, 1.11
├── 1.6 (needs 1.1 done for route)
└── 1.12 (prereq for all local testing)

Phase 2 (sequential)
├── 2.1-2.3 (DB migration first)
├── 2.4 (Crawl Service)
├── 2.5 (Indexer + Search)
├── 2.6 (Answer Generator + Groq)
├── 2.7 (Job + Controller)
├── 2.8 (Commands + Scheduler)
└── 2.9 (Frontend integration)

Phase 3 (VPS only, after Phase 1-2 stable)
├── 3.1-3.5 (Infra)
├── 3.6-3.7 (Code)
└── 3.8 (Deploy)
```

---

## Open Questions (Marked Out of Scope for Now)

1. **Vector search backend**: DB fulltext vs Meilisearch vs Typesense vs pgvector? → Default DB fulltext dulu, upgrade nanti.
2. **Real-time chat**: Polling vs WebSocket (Laravel Reverb)? → Polling dulu (simpler).
3. **Multi-language**: Hanya Bahasa Indonesia? → Ya, per AGENTS.md.
4. **Chat history persistence**: Simpan ke DB? → Ya, `chat_sessions` table.
5. **Rate limiting chat**: Per IP/user? → Tambah di Phase 2.7.

---

## Rollback Plan
- Phase 1: Git revert per commit (atomic fixes)
- Phase 2: Feature flag `CHATBOT_ENABLED=false` di `.env` → disable route + job
- Phase 3: Blue-green deploy VPS (nginx upstream switch)

---

## Plan File Location
`C:\Users\LENOVO\lomba\ga-ro\.kilo\plans\1791872718506-spmb-bugfix-chatbot-plan.md`