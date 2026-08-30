# Plan: Kelola Tabungan Siswa — Panel Admin

## Goal
Tambah menu **Kelola Tabungan** di panel admin (`backend-admin`) untuk memantau saldo, riwayat setor/tarik, dan mengetahui siapa yang melakukan setiap transaksi tabungan.

## Current State
- Tabel `tabungans` sudah ada (migration hanya di `backend/`), kolom: `id, user_id, type, amount, description, timestamps`
- Backend sudah punya `TabunganController` dengan `index` (siswa), `store`, `adminIndex` (JSON list siswa + saldo)
- Backend-admin belum punya model/controller/view untuk tabungan
- Belum ada kolom untuk mencatat **siapa yang menabung** (`input_by`)

## Changes

### 1. Migration — tambah kolom `input_by`
- **Backend**: migration baru `YYYY_MM_DD_HHMMSS_add_input_by_to_tabungans_table.php`
  - `input_by` nullable, FK ke `users.id`, `nullOnDelete`
- **Backend-admin**: copy migration yang sama
- Pattern deploy: pull env production → `php artisan migrate --force` → hapus temp env

### 2. Backend (`backend/`)
- `app/Models/Tabungan.php`: tambah `input_by` ke `$fillable`, tambah relasi `inputter()` belongsTo User
- `app/Http/Controllers/TabunganController.php`:
  - `store()`: set `input_by` = `$request->user()->id` saat create transaksi
  - Tambah method `adminShow(int $userId)`: return JSON { siswa, saldo, transaksi: [{id,type,amount,description,created_at,inputter:{name,username}}] }
- `routes/web.php`: tambah `GET /admin/tabungan/{user}` (auth + role:admin)

### 3. Backend-Admin (`backend-admin/`)
- Copy `app/Models/Tabungan.php` dari backend
- Buat `app/Http/Controllers/TabunganController.php`:
  - `adminIndex()`: list semua siswa dengan saldo (query `withSum` setor/tarik), return view `tabungan.index`
  - `adminShow($userId)`: detail siswa + riwayat transaksi + form tambah transaksi, return view `tabungan.show`
  - `adminStore(Request $request)`: admin setor/tarik untuk siswa, validasi amount/min:1, set `input_by` = auth user
- `routes/web.php`: tambah routes
  - `GET /admin/tabungan` → `TabunganController@adminIndex`
  - `GET /admin/tabungan/{user}` → `TabunganController@adminShow`
  - `POST /admin/tabungan` → `TabunganController@adminStore`
- `resources/views/tabungan/index.blade.php`:
  - Stat cards: total saldo, jumlah siswa, total setor, total tarik
  - Tabel siswa: Nama, Username, Saldo, Setor, Tarik, Aksi (Lihat)
  - Responsive `table-wrap`
- `resources/views/tabungan/show.blade.php`:
  - Header: nama siswa + saldo besar
  - Tabel transaksi: Tanggal, Tipe (badge setor/tarik), Jumlah, Deskripsi, **Dicatat Oleh** (inputter name), Aksi
  - Form tambah transaksi: select tipe (setor/tarik), input nominal, input deskripsi, tombol Simpan
- `resources/views/layouts/app.blade.php`: tambah menu **Kelola Tabungan** di sidebar setelah Rekap SPP, icon wallet/coins

## Validation
1. Migration berhasil: kolom `input_by` ada di `tabungans` (NULL untuk data lama)
2. Admin login panel → sidebar ada menu Kelola Tabungan
3. `/admin/tabungan` → 200, tabel siswa + saldo tampil
4. Klik siswa → detail transaksi tampil, kolom "Dicatat Oleh" terisi
5. Admin tambah setor 50000 untuk siswa X → saldo bertambah, `input_by` = admin id
6. Siswa add via `/tabungan` → `input_by` = siswa id
7. Build Vite + deploy backend + backend-admin → verifikasi production

## Out of Scope
- Edit transaksi yang sudah ada
- Hapus transaksi
- Filter/export CSV tabungan
- Notifikasi transaksi baru
