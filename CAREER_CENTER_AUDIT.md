# Career Center Audit & Implementation Plan

**Tanggal:** 2026-10-16  
**Audited oleh:** Code agent  
**Status:** Tahap 1 — Audit & Rencana (menunggu persetujuan sebelum implementasi)

---

## A. Ringkasan Kondisi Aktual

### Fitur yang Sudah Berfungsi

| Fitur | Lokasi | Status |
|-------|--------|--------|
| Lowongan list API | `backend/app/Http/Controllers/LowonganController.php:10-48` | ✅ Backend `index`, `show`, `count` — filter search/type/jurusan, sort by newest/company/type |
| Career Preview (landing) | `src/components/sections/CareerPreview.vue` | ✅ Fetch `/lowongan/count` + link ke `/panel-siswa/career-center` |
| Cari Lowongan | `src/views/career/CariLowonganView.vue` | ✅ Filter chip, search, sort, CareerJobCard list, ApplyModal |
| Apply Modal | `src/components/career/ApplyModal.vue` | ✅ Prefill otomatis (name, email), upload CV (2MB), cover letter (50-2000 char) |
| Lamaran Saya | `src/views/career/LamaranSayaView.vue` | ✅ List lamaran user, status badge, cancel (pending only) |
| Lamaran API | `backend/app/Http/Controllers/LamaranController.php:11-92` | ✅ Store, myApplications, show (IDOR-protected), cancel |
| Auth guard | `src/router/index.js:47-59` | ✅ `meta.requiresSiswa` — route hanya untuk role siswa/pendaftar |
| CareerPreview text | `src/components/sections/CareerPreview.vue:9` | ✅ "untuk siswa dan alumni" (tapi alumni belum diimplementasi) |

### Fitur yang Belum Berfungsi (Placeholder)

| Fitur | View File | Keterangan |
|-------|-----------|------------|
| Dashboard | `src/views/career/DashboardView.vue` | 🔧 Stat card hardcode (12/5/8/3) — bukan data real |
| Messages | `src/views/career/PesanView.vue` | 🔧 Placeholder kosong — tidak ada backend messaging |
| Statistics | `src/views/career/StatistikView.vue` | 🔧 Placeholder kosong — tidak ada endpoint agregasi |
| Berita Karir | `src/views/career/BeritaKarirView.vue` | 🔧 Placeholder kosong — tidak ada konsep berita karir terpisah |

### Temuan yang Berbeda dari Audit Dokumen CAREER_CENTER_AUDIT.md

1. **Bookmark button di CareerJobCard emit event tapi tidak di-handle**  
   `src/components/career/CareerJobCard.vue:37-38` — `$emit('bookmark', job)` tapi `CariLowonganView.vue` tidak ada `@bookmark` handler. Ini benar seperti didokumentasikan.

2. **User model tidak punya kolom nisn/jurusan**  
   `backend/app/Models/User.php` memang hanya punya: name, username, email, password, role, avatar.  
   NISN dan jurusan ada di tabel `pendaftarans`.  
   **`ApplyModal.vue:172-173`** mencoba `user.value?.nisn` dan `user.value?.jurusan` — ini akan selalu kosong. Ini BUG kecil, tapi tidak fatal karena field ini optional (nullable) di backend.

3. **CareerCenterView.vue adalah wrapper kosong**  
   ```vue
   <template>
     <RouterView />
   </template>
   ```
   Route career-center di `src/router/index.js:48-59` memakai nested children — layout wrapper di luar, child view di dalam.

### Masalah Keamanan atau Konsistensi

| Issue | File | Risk Level | Deskripsi |
|-------|------|------------|-----------|
| Double filtering | `CariLowonganView.vue:59-89` | Low | Backend filter (params) + frontend `filteredJobs` computed — redundant |
| No pagination | `LowonganController.php:37` | Med | GET `/lowongan` mengembalikan semua lowongan — bisa berat bila data besar |
| No file validation backend | `LamaranController.php:37-38` | Med | CV upload divalidasi size/mimes tapi tidak ada MIME sniffing check di sisi server |
| No admin CRUD | `backend/` only | Info | Lowongan/lamaran tidak bisa dikelola di panel admin |

---

## B. Rencana Implementasi per Fitur

### A. Bookmark Lowongan

**Perilaku yang diharapkan:**
- User bisa bookmark lowongan dari career search list & career detail modal
- Bookmark persisten — tersimpan di backend, terhubung ke user + lowongan
- User bisa lihat daftar lowongan yang di-bookmark di tab sendiri
- Tidak boleh duplicate bookmark
- Hanya owner yang bisa akses bookmark miliknya

**File yang akan dibuat/diubah:**
1. `backend/database/migrations/2026_10_16_100000_create_bookmarks_table.php` — migration baru
2. `backend/app/Models/Bookmark.php` — model baru
3. `backend/app/Http/Controllers/BookmarkController.php` — controller baru (index, store, destroy)
4. `backend/routes/web.php` — tambah route bookmark
5. `backend/database/seeders/` — seed contoh lowongan (sudah ada)
6. `src/components/career/CareerJobCard.vue` — handle `@bookmark` event
7. `src/views/career/CariLowonganView.vue` — integrasi bookmark logic
8. `src/views/career/BookmarkView.vue` — NEW view: daftar lowongan tersimpan

**Migration:**

```php
Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('lowongan_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
$table->unique(['user_id', 'lowongan_id']);
```

**Endpoint:**
- `GET /bookmarks` — list lowongan yang di-bookmark user (dengan relasi lowongan)
- `POST /bookmarks` — toggle bookmark (body: `lowongan_id`)
- `DELETE /bookmarks/{lowongan}` — hapus bookmark

**Validation & Authorization:**
- `auth` middleware (semua endpoint bookmark)
- `lowongan_id` required & exists
- Unique constraint `user_id + lowongan_id` di DB mencegah duplikat
- Toggle pattern: jika sudah ada → delete; jika belum → insert

**Risk & Dependencies:**
- Menambah 1 tabel baru — migration harus dijalankan dari `backend/`
- `backend-admin/` migration juga harus copy (per AGENTS.md, kedua folder harus sinkron migration)
- Landing page tidak dipengaruhi (hanya pakai `/lowongan/count`)
- Tidak ada dependency ke fitur lain

**Testing:**
- Backend: bookmark CRUD dengan auth, cek unique constraint, IDOR protection
- Frontend: bookmark button toggle UI, list bookmark, empty state

---

### B. Penyempurnaan Filter Lowongan

**Perilaku yang diharapkan:**
- Pertahankan: search, filter type (Magang/Kerja/BKK), filter jurusan (RPL/TKJ/AKL), sort (newest/company/type)
- Tambah: filter deadline (aktif/semuanya), filter perusahaan (autocomplete search)
- Hapus: double filtering di frontend (biarkan backend handle semua)

**Evaluasi tambahan filter:**
- **Lokasi pekerjaan** — kolom ada di DB (`location`), tapi isinya kota (Surabaya, Sidoarjo, dll). Bisa filter per kota.
- **Sistem kerja (onsite/hybrid/remote)** — kolom TIDAK ada di DB. Perlu migration. **Risk:** data lama tidak punya sistem kerja — akan null.
- **Pagination** — belum ada. **Recomended:** tambahkan `paginate()` di backend.

**Keputusan untuk fitur ini:**
- **Menerapkan filter lokasi** (karena kolom sudah ada)
- **Menambah kolom `work_arrangement`** (onsite/hybrid/remote) pada `lowongans` table — nullable, data lama null
- **Menggunakan single source of truth:** semua filter di backend, frontend hanya menampilkan hasil
- **Menambahkan pagination** di backend (`paginate(12)`)

**File yang akan diubah:**
1. `backend/database/migrations/2026_10_16_100001_add_work_arrangement_to_lowongans_table.php` — NEW migration (nullable)
2. `backend/app/Models/Lowongan.php` — tambah `work_arrangement` ke fillable
3. `backend/app/Http/Controllers/LowonganController.php` — update index() dengan filter baru + pagination
4. `src/views/career/CariLowonganView.vue` — refactor filter, remove frontend filtering, add pagination UI
5. `src/components/career/CareerFilterChips.vue` — mungkin tambah chip lokasi/work arrangement

**Risk:** 
- Endpoint `/lowongan` sekarang mengembalikan pagination object (bukan array langsung) — akan merusak `CareerPreview.vue` yang expect array dan `fetchJobs()` di `CariLowonganView.vue`
- Solusi: kirim pagination data (`data`, `meta`, `links`) dan update semua consumer, atau gunakan resource yang konsisten

**Testing:**
- Backend: filter kombinasi, pagination, backward compatibility
- Frontend: semua filter kombinasi, reset, pagination controls

---

### C. Dashboard Siswa Dinamis

**Perilaku yang diharapkan:**
- Jumlah lamaran yang dikirim pengguna
- Jumlah lamaran yang masih `pending` (diproses)
- Jumlah lowongan yang di-bookmark
- Deadline terdekat dari lowongan yang di-bookmark (jika masih relevan)
- Loading state, empty state, error state

**Analisis data yang tersedia:**
- `/lamaran/saya` — sudah ada, mengembalikan semua lamaran user + relasi lowongan
- `/lowongan` — sudah ada, bisa diff dengan bookmark count
- `/bookmarks` (fitur A) — baru, akan ada setelah implementasi bookmark

**Keputusan:**
- Buat 1 endpoint baru: `GET /career/dashboard` — mengambil semua statistik dalam 1 response
- Respons:
```json
{
  "total_lamaran": 5,
  "lamaran_pending": 2,
  "bookmark_count": 8,
  "upcoming_deadline": {
    "title": "Frontend Developer",
    "company": "Velocity Corp",
    "deadline": "2026-08-30"
  }
}
```

**File yang akan diubah:**
1. `backend/app/Http/Controllers/LamaranController.php` (atau buat controller CareerController.php) — tambah method `dashboard()`
2. `backend/routes/web.php` — tambah route `GET /career/dashboard`
3. `src/views/career/DashboardView.vue` — replace hardcoded dengan fetch ke endpoint baru

**Risk:**
- Endpoint baru harus auth-protected
- `upcoming_deadline` = deadline terdekat dari bookmark lowongan yang masih aktif dan belum lewat

**Testing:**
- Backend: endpoint kembalikan statistik akurat, handle user tanpa lamaran/bookmark
- Frontend: loading state, empty state, error handling

---

### D. Timeline Status Lamaran

**Audit status lamaran saat ini:**

Migration `2026_08_22_100001_create_lamarans_table.php:17`:
```php
$table->enum('status', ['pending', 'diterima', 'ditolak', 'dibatalkan'])->default('pending');
```

**Status yang ada (4):**
| Status | Deskripsi | Label frontend |
|--------|-----------|----------------|
| `pending` | Lamaran baru dikirim | "Diproses" |
| `diterima` | Diterima oleh perusahaan | "Diterima" |
| `ditolak` | Ditolak oleh perusahaan | "Ditolak" |
| `dibatalkan` | Dibatalkan oleh user | "Dibatalkan" |

**Timeline yang diusulkan (6 tahap):**
1. Lamaran dikirim → `pending` ✓ ada
2. Sedang ditinjau → tidak ada (gabung dengan pending)
3. Lolos administrasi → tidak ada
4. Dipanggil wawancara → tidak ada
5. Diterima → `diterima` ✓ ada
6. Tidak diterima → `ditolak` ✓ ada

**Keputusan:**
- **TIDAK** menambah status baru — akan merusak data migration yang sudah ada dan butuh data alumni migration
- **PERTAHAN** 4 status existing untuk backward compatibility
- Mapping visual timeline di frontend:
```
[✓] Terkirim → [•] Diproses → [•] Diterima/Ditolak → [✓] Selesai
```
- `dibatalkan` ditampilkan sebagai status final terpisah

**Timeline history:**
- Tabel `lamaran_status_histories` untuk track perubahan status (status_sebelum, status_sesudah, user_id/pengubah, catatan, timestamp)
- Hanya admin yang bisa update status (via backend-admin — belum ada, akan diluar scope)
- User tidak boleh ubah status

**File yang akan dibuat/dijalankan:**
1. `backend/database/migrations/2026_10_16_100002_create_lamaran_status_histories_table.php` — NEW migration
2. `src/views/career/LamaranSayaView.vue` — tambah timeline display per lamaran
3. `src/components/career/LamaranTimeline.vue` — NEW komponen timeline
4. Backend: `LamaranController.php` — update endpoint show/myApplications untuk include last status change time

**Risk:**
- History table baru — tapi tidak mengubah lamarans schema yang existing
- Tidak ada endpoint admin update status — jadi history baru akan kosong sampai ada fitur admin (fuul scope)

**Testing:**
- Backend: lamaran baru default `pending`, history record terbuat otomatis di saat status berubah (via model observer)
- Frontend: timeline display, status label, empty timeline state

---

## C. Urutan Implementasi

Berdasarkan dependensi antar fitur:

```
1. Bookmark Lowongan (Feature A)
   ├── Migration: bookmarks table
   ├── Model: Bookmark
   ├── Controller: BookmarkController
   ├── Routes
   ├── Frontend: handle @bookmark event, BookmarkView.vue

2. Filter Penyempurnaan (Feature B)  
   ├── Migration: work_arrangement (nullable)
   ├── Model: Lowongan fillable update
   ├── Controller: filter + pagination
   ├── Frontend: refactor CariLowonganView, update filter chips
   └── Update CareerPreview.vue (pagination awareness)

3. Dashboard Dinamis (Feature C)
   ├── Endpoint: GET /career/dashboard
   ├── Frontend: DashboardView.vue (fetch real data)
   └── Tergantung pada fitur A (bookmark count) dan fitur terdapat (lamaran count)

4. Timeline Status (Feature D)
   ├── Migration: lamaran_status_histories
   ├── Model: LamaranStatusHistory + observer
   ├── Frontend: LamaranTimeline.vue, update LamaranSayaView
   └── Tergantung pada data lamaran yang sudah ada
```

**Dependensi kritis:**
- Feature C (dashboard) butuh Feature A (bookmark) untuk `bookmark_count`
- Feature B (pagination) memengaruhi CareerPreview & CariLowonganView — harus update semua consumer

---

## D. Daftar Perubahan Database

### Migration Wajib

| No | Migration | Table | Kolom/Perubahan | Notes |
|----|----------|-------|-----------------|-------|
| 1 | `2026_10_16_100000_create_bookmarks_table` | `bookmarks` | id, user_id (FK cascade), lowongan_id (FK cascade), timestamps | Unique constraint `[user_id, lowongan_id]` |
| 2 | `2026_10_16_100001_add_work_arrangement_to_lowongans_table` | `lowongans` | `work_arrangement` enum(onstite, hybrid, remote) nullable | Data lama = NULL |
| 3 | `2026_10_16_100002_create_lamaran_status_histories_table` | `lamaran_status_histories` | id, lamaran_id (FK cascade), status_from, status_to, action_by (FK users), catatan, timestamps | Untuk tracking perubahan status |

### Migration Opsional (menunggu approval)

| No | Migration | Deskripsi |
|----|----------|-----------|
| 4 | `add_location_filter_indexes` | Index pada kolom `location` dan `work_arrangement` untuk performa filter |

### Catatan Migration
- Semua migration harus disalin ke `backend-admin/database/migrations/` (sync dengan AGENTS.md)
- Migration `work_arrangement` harus MySQL-guarded (cek driver, karena ada migration MySQL-only lama yang error di sqlite)

---

## E. Rencana Pengujian

### Backend (PHPUnit)

**Bookmark (Feature A):**
- `BookmarkControllerTest.php`
  - Test create bookmark (auth user)
  - Test toggle (create → delete → tidak duplicate)
  - Test IDOR protection (user A tidak bisa lihat bookmark user B)
  - Test unique constraint (duplicate bookmark throw error)

**Filter + Pagination (Feature B):**
- `LowonganControllerTest.php`
  - Test filter search term
  - Test filter type
  - Test filter jurusan
  - Test filter work_arrangement
  - Test sort by company/type/newest
  - Test pagination structure (data, meta, links)
  - Test backward compatibility (CareerPreview fetch /lowongan/count masih jalan)

**Dashboard (Feature C):**
- `CareerDashboardControllerTest.php`
  - Test authenticated user dapat statistik
  - Test forbidden untuk guest
  - Test empty state (user tanpa lamaran/bookmark)
  - Test upcoming_deadline null when no bookmark

**Timeline (Feature D):**
- `LamaranStatusHistoryTest.php`
  - Test model observer create history record
  - Test status default pending
  - Test only admin can update status (authorize)

### Frontend (Manual / Build verification)
- Build sukses: `npm run build` atau `npm run dev`
- Bookmark: toggle bookmark di CareerJobCard, lihat efek di BookmarkView, reload halaman, state persisten
- Filter: kombinasi filter, reset filter, pagination navigation
- Dashboard: loading → data real atau empty state
- Timeline: lamaran detail tampilkan status + history

---

## F. Hal yang Membutuhkan Keputusan Anda

Berikut keputusan yang tidak dapat ditentukan oleh audit kode semata:

1. **Timeline status rekrutmen — approve 4 status existing atau ingin ditambah tahap baru?**
   - Saat ini: `pending`, `diterima`, `ditolak`, `dibatalkan` (4 status)
   - Cadangan: `pending`, `diterima`, `ditolak`, `dibatalkan`, + opsional `sedang_ditinjau`, `wawancara` jika ingin granular
   - Jika ingin tahap tambahan, migration + observer perlu dirancang

2. **Alumni access — CareerPreview sebutkan "siswa dan alumni"**
   - Saat ini semua role `siswa`/`pendaftar` — tidak ada role `alumni`
   - Apakah ingin tambah role `alumni` di User model? Atau cukup perlakukan semua user terdaftar sebagai "alumni yang bisa akses"?

3. **Work arrangement filter — approve penambahan kolom `work_arrangement` (onsite/hybrid/remote)?**
   - Kolom baru ini nullable — data lama akan NULL
   - Perlu keputusan apakah ingin backfill data atau biarkan NULL

4. **Pagination — berapa per-page di backend?**
   - Usul: 12 per halaman
   - Perlu update di frontend (CareerPreview count API tidak terdampak, tapi GET /lowongan akan mengembalikan pagination object)

5. **Berita Karir (BeritaKarirView.vue) — pakai tabel `beritas` yang sudah ada atau butuh tabel terpisah?**
   - `beritas` table sudah ada (untuk BeritaApiController di landing page)
   - Bisa filter berita dengan category="karir" jika mau reuse, atau butuh tabel baru

6. **Admin CRUD lowongan/lamaran — scope dalam fitur ini atau diluar?**
   - Career center tidak ada di `backend-admin/`
   - Perlu implementasi di panel admin juga jika ingin kelola lowongan/lamaran

---

## G. Persetujuan

**Saya menunggu persetujuan Anda untuk melanjutkan ke Tahap 2 (Implementasi).**

Jika Anda setuju dengan rencana di atas, beri tahu:
1. Fitur mana yang ingin dikerjakan pertama (urutan implementasi sudah diusulkan)
2. Jawaban keputusan di bagian F (nomor 1-6)
3. Konfirmasi bahwa saya bisa memulai mengubah kode dan menjalankan migration

Jika ada revisi pada rencana, silakan beri tahu perubahan yang diinginkan.

---

*File ini akan diupdate sesuai keputusan dan progres implementasi.*
**Last Updated:** 2026-10-16