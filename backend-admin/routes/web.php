<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;

// Panel admin terpisah dari web utama (spmb-backend-self.vercel.app)
// Database sama (TiDB) — data yang dimonitor = data dari web utama.

Route::get('/', fn () => redirect('/admin'));

// Static asset via route (vercel-php tidak serve public/)
Route::get('/logo.png', fn () => response()->file(public_path('logo.png')));

// Auth (admin only — role dicek di AuthController::login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Reset kata sandi (tanpa email sender: link ditampilkan langsung di halaman)
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [PendaftaranController::class, 'index'])->name('admin.dashboard');
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/export', [PendaftaranController::class, 'exportCsv'])->name('pendaftaran.export');
    Route::get('/pendaftaran-snapshot', [PendaftaranController::class, 'snapshot'])->name('pendaftaran.snapshot')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::put('/pendaftaran/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.status');
    Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
});