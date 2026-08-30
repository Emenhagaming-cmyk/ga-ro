<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\SppController;

Route::get('/', [PendaftaranController::class, 'create'])->name('home');

// Static asset via route (vercel-php tidak serve public/ — file dibundel ke lambda)
Route::get('/logo.png', fn () => response(file_get_contents(public_path('logo.png')), 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=604800']));
Route::get('/cs.png', fn () => response(file_get_contents(public_path('cs.png')), 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=604800']));

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest', 'throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest', 'throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/auth-status', [AuthController::class, 'authStatus']);
Route::get('/csrf-token', [AuthController::class, 'csrfToken']);

// Reset kata sandi (tanpa email sender: link ditampilkan langsung di halaman)
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('guest', 'throttle:3,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Publik: form pendaftaran + submit (guest ditangani di store via draft session)
Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store')->middleware('throttle:10,1');

// Login siswa & admin: dashboard siswa + update form sendiri
Route::middleware('auth')->group(function () {
    Route::get('/dashboard-siswa', [PendaftaranController::class, 'myDashboard'])->name('dashboard.siswa');
    Route::get('/dashboard-siswa/snapshot', [PendaftaranController::class, 'myDashboardSnapshot'])->name('dashboard.siswa.snapshot');
    Route::put('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profil')->middleware('role:siswa');

// Surat keterangan diterima (bukti kelulusan) untuk pemilik pendaftaran
    Route::get('/pendaftaran/bukti', [PendaftaranController::class, 'downloadBukti'])
        ->name('pendaftaran.bukti');
});

// Career Center API
Route::get('/lowongan', [LowonganController::class, 'index']);
Route::get('/lowongan/{lowongan}', [LowonganController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::post('/lamaran', [LamaranController::class, 'store']);
    Route::get('/lamaran/saya', [LamaranController::class, 'myApplications']);
    Route::get('/lamaran/{lamaran}', [LamaranController::class, 'show']);
    Route::delete('/lamaran/{lamaran}', [LamaranController::class, 'cancel']);
});

// Tabungan Siswa
Route::middleware('auth')->group(function () {
    Route::get('/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');
    Route::post('/tabungan', [TabunganController::class, 'store'])->name('tabungan.store');
});

// Tabungan (admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/tabungan', [TabunganController::class, 'adminIndex'])->name('admin.tabungan.index');
    Route::post('/admin/tabungan', [TabunganController::class, 'store'])->name('admin.tabungan.store');
});

// SPP Siswa
Route::middleware('auth')->group(function () {
    Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
});

// SPP (kasir / admin) input pembayaran
Route::middleware(['auth', 'role:kasir,admin'])->group(function () {
    Route::get('/spp/kasir', [SppController::class, 'kasirIndex'])->name('spp.kasir');
    Route::post('/spp/pay', [SppController::class, 'store'])->name('spp.pay');
});

// SPP (admin & guru) rekap semua (read-only untuk guru)
Route::middleware(['auth', 'role:guru,admin'])->group(function () {
    Route::get('/admin/spp', [SppController::class, 'adminIndex'])->name('admin.spp.index');
    Route::get('/admin/spp/rekap', [SppController::class, 'rekapIndex'])->name('spp.rekap');
});

// SPP (publik) link orang tua — tanpa akun, via signed URL
Route::get('/spp/ortu/{user}', [SppController::class, 'ortuIndex'])->name('spp.ortu');
