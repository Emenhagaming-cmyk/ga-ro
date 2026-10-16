# Career Center Audit

Audit of the Career Center feature in SPMB SMK Bahrul Ulum project.

---

## Arsitektur Fitur

### Backend (Laravel)

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/lowongan` | GET | List lowongan (filter: search, type, jurusan; sort: newest/company/type) |
| `/lowongan/count` | GET | Jumlah lowongan aktif |
| `/lowongan/{id}` | GET | Detail lowongan |
| `/lamaran` | POST | Kirim lamaran (validasi: lowongan_id, cover_letter 50-2000 char, CV optional pdf/doc/docx 2MB) |
| `/lamaran/saya` | GET | List lamaran user + relasi lowongan |
| `/lamaran/{id}` | GET | Detail lamaran (own only) |
| `/lamaran/{id}` | DELETE | Batalkan lamaran (hanya status `pending`) |

**Controllers:**
- `backend/app/Http/Controllers/LowonganController.php` — index, show, count
- `backend/app/Http/Controllers/LamaranController.php` — store, myApplications, show, cancel

**Models:**
- `backend/app/Models/Lowongan.php`
  - title, company, location, jurusan, type (enum: Magang/Kerja/BKK), description, deadline, is_active
- `backend/app/Models/Lamaran.php`
  - user_id, lowongan_id, cv_path, cover_letter, status, catatan_admin
  - nama_lengkap, email, nisn, jurusan_pilihan (applicant fields)

**Database Tables:**
- `lowongans` (2026_08_22_100000_create_lowongans_table.php)
- `lamarans` (2026_08_22_100001_create_lamarans_table.php, 2026_08_23_000000_add_applicant_fields_to_lamarans_table.php, 2026_08_23_100000_add_email_to_lamarans_table.php)

**Constraints:**
- Unik constraint: `unique(['user_id', 'lowongan_id'])` — satu user satu lamaran per lowongan
- IDOR protection: user hanya bisa akses lamaran sendiri di show/cancel

---

### Frontend (Vue 3)

**Route:** `/panel-siswa/career-center` (harus login siswa, `meta.requiresSiswa`) → redirect ke `/career-center/search`

**Router config (src/router/index.js):**
```js
{
  path: "/career-center",
  component: () => import("../views/CareerCenterView.vue"),
  redirect: "/career-center/search",
  children: [
    { path: "search", name: "career-search", component: () => import("../views/career/CariLowonganView.vue") },
    { path: "dashboard", name: "career-dashboard", component: () => import("../views/career/DashboardView.vue") },
    { path: "applications", name: "career-applications", component: () => import("../views/career/LamaranSayaView.vue") },
    { path: "messages", name: "career-messages", component: () => import("../views/career/PesanView.vue") },
    { path: "statistics", name: "career-statistics", component: () => import("../views/career/StatistikView.vue") },
    { path: "news", name: "career-news", component: () => import("../views/career/BeritaKarirView.vue") },
  ],
}
```

**Views:**

| Tab | View File | Status |
|-----|-----------|--------|
| **Cari Lowongan** | `src/views/career/CariLowonganView.vue` | ✅ Fungsional — search, filter chip, sort, card list, apply modal |
| **Dashboard** | `src/views/career/DashboardView.vue` | 🔧 **Placeholder** — stat cards hardcoded |
| **Lamaran Saya** | `src/views/career/LamaranSayaView.vue` | ✅ Fungsional — list, status, cancel |
| **Pesan** | `src/views/career/PesanView.vue` | 🔧 **Placeholder** — "Pesan dari perusahaan..." |
| **Statistik** | `src/views/career/StatistikView.vue` | 🔧 **Placeholder** — "Grafik dan data statistik..." |
| **Berita Karir** | `src/views/career/BeritaKarirView.vue` | 🔧 **Placeholder** — "Tips karir, pengumuman..." |

**Components:**
- `src/components/career/CareerSearchBar.vue` — input + tombol cari
- `src/components/career/CareerFilterChips.vue` — chip filter (Semua, Magang, Kerja, BKK, RPL, TKJ, AKL)
- `src/components/career/CareerJobCard.vue` — card lowongan dengan aksi "Lamar" & "Bookmark"
- `src/components/career/ApplyModal.vue` — modal apply lamaran

---

## Status Features

### Yang Sudah Jalan (Working)

1. **Public API** — `/lowongan` dan `/lowongan/count` dipakai oleh `CareerPreview.vue` di landing page
2. **Cari Lowongan** — search, filter, sort, daftar lowongan, modal apply
3. **Lamaran Saya** — fetch daftar lamaran, display status, batalkan (pending only)
4. **Auth guard** — route career center hanya akses role `siswa`/`pendaftar`
5. **IDOR protection** — backend validasi kepemilikan lamaran

### Yang Placeholder (Not Implemented)

| Feature | File | Issues |
|---------|------|--------|
| **Dashboard** | `DashboardView.vue` | Stat card hardcoded (12/5/8/3), tidak ambil data real dari backend |
| **Messages** | `PesanView.vue` | Placeholder kosong, tidak ada backend messaging |
| **Statistics** | `StatistikView.vue` | Placeholder kosong, tidak ada endpoint agregasi |
| **Berita Karir** | `BeritaKarirView.vue` | Placeholder kosong, tidak ada endpoint berita karir |

---

## Technical Debt / Bug / Gap

### 1. Bookmark Button Does Nothing
**File:** `src/components/career/CareerJobCard.vue:37-38`
- `CareerJobCard` emit event `bookmark` 
- Tapi `CariLowonganView.vue` tidak handle `@bookmark` 
- Bookmark tidak tersimpan ke localStorage atau backend — pure no-op

### 2. ApplyModal Prefill Tidak Lengkap
**File:** `src/components/career/ApplyModal.vue:170-173`
```js
const formName = ref(user.value?.name || '')
const formEmail = ref(user.value?.email || '')
const formNisn = ref(user.value?.nisn || '')
const formJurusan = ref(user.value?.jurusan || '')
```
- `User` model tidak punya kolom `nisn` atau `jurusan` — ini ada di `pendaftarans` table
- Prefill hanya akan terisi dengan `name` dan `email`
- `nisn` dan `jurusan` akan selalu kosong

### 3. No Admin CRUD
- Career center tidak ada di `backend-admin/`
- `LowonganController` dan `LamaranController` hanya ada di `backend/`
- Tidak ada panel admin untuk mengelola lowongan atau melihat lamaran masuk
- Lowongan harus di-seed manual via DB/tinker

### 4. DashboardView Hardcoded Data
**File:** `src/views/career/DashboardView.vue`
- Stat card values hardcoded: 12 lowongan, 5 lamaran, 8 perusahaan, 3 diterima
- Harusnya fetch dari API `/lamaran/saya` + `/lowongan`

### 5. Double Filtering
**File:** `src/views/career/CariLowonganView.vue`
- Filter dikirim ke backend via URL params (`type`, `jurusan`, `search`)
- Tapi juga difilter lagi di frontend via `filteredJobs` computed
- Backend sudah filter, jadi frontend filter redundan — potential race condition

### 6. CV Upload Storage
- CV disimpan ke `storage/app/public/cv` via `store('cv', 'public')`
- Butuh `php artisan storage:link` agar accessible
- Tidak ada cleanup untuk CV lama saat update lamaran

---

## Data Flow

1. **Landing Page** (`HomeView.vue`) → `CareerPreview.vue` → fetch `/lowongan/count` → tampilkan jumlah + link ke `/panel-siswa/career-center`
2. **Career Center** → auth guard → redirect ke `/career-center/search`
3. **Cari Lowongan** → fetch `/lowongan?params` → display `CareerJobCard` list
4. **Apply** → klik tombol "Lamar" → open `ApplyModal` → prefill dari session → POST `/lamaran` (FormData)
5. **Lamaran Saya** → fetch `/lamaran/saya` → display list dengan status + tombol batalkan

---

## Auth & Access

- Route `/career-center` berada di dalam `/panel-siswa` layout yang membutuhkan `meta.requiresSiswa: true`
- Auth guard di `router/index.js:93-103` — cek `session.value.role !== "siswa"` lalu fetch `/auth-status`
- Role `pendaftar` diperlakukan sebagai `siswa` di frontend via `useAuthSession.js` normalisasi
- Login redirect ke backend (`${BACKEND}/login`)

---

## Credentials (Lokal Dev)

| Item | Value |
|------|-------|
| Siswa demo | username `siswa` / password `siswa123` |
| Backend URL | `http://localhost:8000` |
| Frontend URL | `http://localhost:5174` |

---

## Last Updated

2026-10-16 — Full audit selesai.