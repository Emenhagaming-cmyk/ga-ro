# AGENTS.md — Proyek SPMB SMK Bahrul Ulum

Konteks permanen proyek. Dibaca otomatis oleh opencode di setiap sesi baru.
Baca juga `PROGRESS.md` untuk status terakhir & TODO lanjutan.

## Struktur Proyek (2 bagian)

- **Backend Laravel 12** → `C:\Users\LENOVO\form\form`
  - Server: `php artisan serve --port=8000`
  - DB: MySQL Laragon, database `pendaftaran_db` (host 127.0.0.1, port 3306, user root, password kosong)
  - Auth: Laravel session (bukan Sanctum/API) — login/register/logout pakai blade
  - Views: `resources/views/` (auth/, pendaftaran/, layouts/)
  - Form pendaftaran multi-step: `resources/views/pendaftaran/create.blade.php`
  - Routes: `routes/web.php` (web), `routes/api.php` (API Lama — tidak dipakai frontend baru)

- **Landing page Vue 3** → `C:\Users\LENOVO\lomba\ga-ro`
  - Server: `npm run dev` (port 5174)
  - File dashboard Vue lama (DashboardSiswa.vue, DashboardAdmin.vue, LoginView.vue, RegisterView.vue) TIDAK DIPAKAI — tidak aktif. Backend pakai Laravel blade.
  - Logo sekolah: `C:\Users\LENOVO\lomba\ga-ro\public\logo.png` (copy juga ke `form\form\public\logo.png`)

## Command Penting

```powershell
# Backend
cd C:\Users\LENOVO\form\form
php artisan serve --port=8000
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan view:cache

# Frontend (landing page)
cd C:\Users\LENOVO\lomba\ga-ro
npm run dev
```

## Credentials

- **Admin**: username `admin` / password `admin123` (email `admin@smkbahrululum.sch.id`)
- MySQL: root (tanpa password)
- DB: `pendaftaran_db`

## Roles & Alur Akses

- **Role admin**: akses dashboard via URL `/admin` saja — TIDAK BOLEH ada button/navbar admin di halaman manapun (keamanan).
- **Role siswa**: register → isi form pendaftaran → dashboard `/dashboard-siswa` untuk cek status & edit form.
- **Navbar form page** (`create.blade.php`) kondisional:
  - Login siswa: button **Dashboard Siswa** + **Logout**
  - Guest: TIDAK ADA button (SPMB & Masuk dihapus)
  - Tidak ada akses admin di navbar
- **Navbar layout `layouts/app.blade.php`**: Beranda, Formulir, Dashboard Siswa (jika siswa), Login/Daftar (guest) atau Logout.

## Aturan Bisnis

- Status pendaftaran: `baru` → `diproses` → `diterima` / `ditolak`. Hanya admin yang bisa ubah (via edit page di `/admin`).
- Siswa bisa EDIT form hanya jika: status = `baru` DAN `created_at` ≤ 3 hari (deadline `created_at + 3 hari`).
- Setelah diproses/diterima/ditolak, siswa tidak bisa edit — hanya lihat status.
- User lama dihapus (fresh start). Data baru: register → form.
- **Draft pendaftaran**: jika guest submit form → data disimpan ke session `pending_pendaftaran` → redirect login. Setelah login/register → redirect ke form, form terisi ulang otomatis (JS prefill dari `@json($draft)`). Draft dihapus saat submit sukses; tetap jika validasi gagal. Pesan error/success tampil di form page.

## Tabel Database (pendaftaran_db)

- `users`: id, name, email, password (hashed), role enum('admin','siswa') default 'siswa', timestamps
- `pendaftarans`: ~45 kolom termasuk field form baru:
  - Identitas: nama_lengkap, nama_panggilan, nisn, nik, tempat_lahir, tanggal_lahir, umur, agama, kewarnegaraan, kategori_pendaftar, jenis_kelamin, alamat, rt_rw, kode_pos, no_hp, email
  - Sekolah: asal_sekolah, gelombang, tahun_lulus, rata_rata_nilai, jurusan_pilihan (RPL/TKJ/AKL)
  - Keluarga: jumlah_saudara, anak_ke, status_keluarga, nama_ayah, pendidikan_ayah, pekerjaan_ayah, penghasilan_ayah, alamat_ayah, hp_ayah, nama_ibu, pendidikan_ibu, pekerjaan_ibu, penghasilan_ibu, alamat_ibu, hp_ibu, nama_wali, hubungan_wali, email_orang_tua
  - Lain: jenis_pembayaran, berkas_tambahan, foto_3x4, kk_file, ijazah_file, sktm_file (file path, storage/app/public/pendaftaran)
  - Status: status, data_confirmed, confirmed_at, status_updated_at, user_id (FK), timestamps
  - Legacy (nullable): nama_orang_tua, no_hp_orang_tua

## Backend Files Kunci

- `app/Http/Controllers/AuthController.php` — login/register/logout (session)
- `app/Http/Controllers/PendaftaranController.php` — CRUD, myDashboard, rules validasi lengkap, handleFileUploads
- `app/Http/Middleware/CheckRole.php` — guard role (admin/siswa)
- `app/Http/Middleware/HandleTokenMismatch.php` — penanganan 419/CSRF (cek response, bukan catch exception; `session()->save()` manual)
- `app/Http/Middleware/Cors.php` — allow http://localhost:5174
- `app/Models/Pendaftaran.php` — fillable semua field
- `database/seeders/AdminSeeder.php` — admin account
- Routes: `web.php` (publik: `/` `/login` `/register` `pendaftaran/create`+`store`; auth: dashboard-siswa, update; admin: `/admin`, index, export, show, edit, status, destroy) — `Route::resource` tidak dipakai

## Style & Konvensi

- Bahasa UI: Indonesia
- Blade: extends `layouts.app` (kecuali `create.blade.php` yang full custom + navbar sendiri)
- Frontend Vue lama tidak disentuh kecuali diminta — fokus backend Laravel
- File upload: `store('pendaftaran', 'public')`
- **WAJIB: update PROGRESS.md SETIAP selesai mengerjakan sesuatu** (perintah/pengerjaan apapun) — tambahkan entri sesi baru langsung setelah pekerjaan selesai, jangan ditunda ke akhir sesi. Sinkronkan juga `IMPLEMENTATION_SUMMARY.md` jika ada perubahan teknis/verifikasi baru.
- **JANGAN sentuh/mengubah `.env` (termasuk `src/server/.env` & key Groq) dan file chatbot (`api/chat.js`, `api/knowledge/**`, `vite.config.js` bagian chat) KECUALI diperintah user secara eksplisit.**
- JANGAN hapus folder/file tanpa konfirmasi user (pelajaran Sesi 8).
