<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\KoperasiController;

// Panel admin terpisah dari web utama (spmb-backend-self.vercel.app)
// Database sama (TiDB) — data yang dimonitor = data dari web utama.

// Root: arahkan sesuai sesi — supaya tidak ada rantai / -> /admin -> /login -> / (loop).
Route::get('/', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403, 'Hanya akun admin yang dapat mengakses panel ini.');
});

// Auth (admin only — role dicek di AuthController::login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest', 'throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Reset kata sandi (tanpa email sender: link ditampilkan langsung di halaman)
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [PendaftaranController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/laporan', [PendaftaranController::class, 'laporan'])->name('pendaftaran.laporan');
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/export', [PendaftaranController::class, 'exportCsv'])->name('pendaftaran.export');
    Route::get('/pendaftaran-snapshot', [PendaftaranController::class, 'snapshot'])->name('pendaftaran.snapshot')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::put('/pendaftaran/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.status');
    Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    Route::post('/admin/akun/{user}/reset-password', [PendaftaranController::class, 'resetUserPassword'])->name('admin.resetPassword');

    // SPP (rekap read-only untuk admin)
    Route::get('/admin/spp', [SppController::class, 'rekapIndex'])->name('admin.spp.index');

    // Kelola Berita (CRUD + upload gambar)
    Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/create', [BeritaController::class, 'create'])->name('berita.create');
    Route::post('/berita', [BeritaController::class, 'store'])->name('berita.store');
    Route::get('/berita/{berita}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{berita}', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{berita}', [BeritaController::class, 'destroy'])->name('berita.destroy');

    // Kelola Tabungan
    Route::get('/tabungan', [TabunganController::class, 'adminIndex'])->name('tabungan.index');
    Route::get('/tabungan/{user}', [TabunganController::class, 'adminShow'])->name('tabungan.show');
    Route::post('/tabungan', [TabunganController::class, 'adminStore'])->name('tabungan.store');

    // Kelola Koperasi
    Route::get('/koperasi', [KoperasiController::class, 'adminIndex'])->name('koperasi.index');
    Route::get('/koperasi/{order}', [KoperasiController::class, 'adminShow'])->name('koperasi.show');
});