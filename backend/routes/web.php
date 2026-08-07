<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;

Route::get('/', [PendaftaranController::class, 'create'])->name('home');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
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
    Route::put('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
});

// Admin only (akses via URL saja, tanpa button di UI)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [PendaftaranController::class, 'index'])->name('admin.dashboard');
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/export', [PendaftaranController::class, 'exportCsv'])->name('pendaftaran.export');
    Route::get('/pendaftaran-snapshot', [PendaftaranController::class, 'snapshot'])->name('pendaftaran.snapshot')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::put('/pendaftaran/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.status');
    Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
});
