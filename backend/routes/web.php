<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;

Route::get('/', [PendaftaranController::class, 'create'])->name('home')->middleware(\App\Http\Middleware\Cors::class);

// Static asset via route (vercel-php tidak serve public/ — file dibundel ke lambda)
Route::get('/logo.png', fn () => response()->file(public_path('logo.png')));
Route::get('/cs.png', fn () => response()->file(public_path('cs.png')));

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware([\App\Http\Middleware\Cors::class, 'auth']);
Route::get('/auth-status', [AuthController::class, 'authStatus'])->middleware(\App\Http\Middleware\Cors::class);

// Reset kata sandi (tanpa email sender: link ditampilkan langsung di halaman)
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Publik: form pendaftaran + submit (guest ditangani di store via draft session)
Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

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
