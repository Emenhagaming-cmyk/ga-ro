<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaApiController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\TabunganTopupController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\KoperasiController;

Route::get('/', [PendaftaranController::class, 'create'])->name('home');

// Static asset via route (vercel-php tidak serve public/ — file dibundel ke lambda)
Route::get('/logo.png', fn () => response(file_get_contents(public_path('logo.png')), 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=604800']));
Route::get('/cs.png', fn () => response(file_get_contents(public_path('cs.png')), 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=604800']));

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest', 'throttle:login');
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
Route::get("/pendaftaran", [PendaftaranController::class, "create"])->name("pendaftaran.root");
Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::get('/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create.alias');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store')->middleware('throttle:10,1');

// Login siswa & admin: dashboard siswa + update form sendiri
Route::middleware('auth')->group(function () {
    // /dashboard-siswa & /profil kini jembatan redirect ke halaman Vue di frontend;
    // data halaman itu diambil via /siswa/data (JSON, dipakai view dashboard + profil)
    Route::get('/dashboard-siswa', [PendaftaranController::class, 'myDashboard'])->name('dashboard.siswa');
    Route::get('/dashboard-siswa/snapshot', [PendaftaranController::class, 'myDashboardSnapshot'])->name('dashboard.siswa.snapshot');
    Route::get('/siswa/data', [PendaftaranController::class, 'myDashboardData'])
        ->name('siswa.data')->middleware('role:siswa,pendaftar');
    Route::put('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    // "pendaftar" (belum dinyatakan diterima) juga boleh buka profil agar link dari topbar tidak error
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profil')->middleware('role:siswa,pendaftar');
    Route::post('/profil/avatar', [AuthController::class, 'updateAvatar'])->name('profil.avatar.update')->middleware('role:siswa,pendaftar');
    Route::delete('/profil/avatar', [AuthController::class, 'destroyAvatar'])->name('profil.avatar.destroy')->middleware('role:siswa,pendaftar');

// Surat keterangan diterima (bukti kelulusan) untuk pemilik pendaftaran
    Route::get('/pendaftaran/bukti', [PendaftaranController::class, 'downloadBukti'])
        ->name('pendaftaran.bukti');
});

// Career Center API
Route::get('/lowongan/count', [LowonganController::class, 'count']);
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
    Route::post('/tabungan/topup', [TabunganTopupController::class, 'create'])->name('tabungan.topup');
    Route::get('/tabungan/topup/{orderId}', [TabunganTopupController::class, 'status'])->name('tabungan.topup.status');
    Route::post('/tabungan/topup/{orderId}/verify', [TabunganTopupController::class, 'verify'])->name('tabungan.topup.verify');
});

// Webhook Midtrans — tanpa auth/CSRF, signature diverifikasi di controller
Route::post('/midtrans/notification', [TabunganTopupController::class, 'notification'])
    ->middleware('throttle:120,1')
    ->name('midtrans.notification');

// Tabungan (admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/tabungan', [TabunganController::class, 'adminIndex'])->name('admin.tabungan.index');
    Route::get('/admin/tabungan/{user}', [TabunganController::class, 'adminShow'])->name('admin.tabungan.show');
    Route::post('/admin/tabungan', [TabunganController::class, 'store'])->name('admin.tabungan.store');
});

// SPP Siswa
Route::middleware('auth')->group(function () {
    Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/pay-tabungan', [SppController::class, 'payWithTabungan'])
        ->middleware('role:siswa')
        ->name('spp.pay-tabungan');
});

// SPP (kasir / admin) input pembayaran
Route::middleware(['auth', 'role:kasir,admin'])->group(function () {
    Route::get('/spp/kasir', [SppController::class, 'kasirIndex'])->name('spp.kasir');
    Route::post('/spp/pay', [SppController::class, 'store'])->name('spp.pay');
    Route::get('/spp/ortu-link/{user}', [SppController::class, 'generateOrtuLink'])->name('spp.ortu-link');
});

// SPP (admin & guru) rekap semua (read-only untuk guru)
Route::middleware(['auth', 'role:guru,admin'])->group(function () {
    Route::get('/admin/spp', [SppController::class, 'adminIndex'])->name('admin.spp.index');
    Route::get('/admin/spp/rekap', [SppController::class, 'rekapIndex'])->name('spp.rekap');
});

// Berita publik — JSON API untuk frontend Vue
Route::get('/berita', [BeritaApiController::class, 'index']);
Route::get('/berita/{slug}', [BeritaApiController::class, 'show']);

// Koperasi Siswa — katalog boleh dilihat publik, pesanan & pembayaran butuh login
Route::get('/koperasi', [KoperasiController::class, 'index'])->name('koperasi.index');

Route::middleware('auth')->group(function () {
    Route::get('/koperasi/orders', [KoperasiController::class, 'myOrders'])->name('koperasi.orders');
});

Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::post('/koperasi/checkout', [KoperasiController::class, 'store'])->name('koperasi.checkout');
    Route::get('/koperasi/orders/{kode}', [KoperasiController::class, 'status'])->name('koperasi.order-status');
    Route::post('/koperasi/orders/{kode}/verify', [KoperasiController::class, 'verify'])->name('koperasi.verify');
    Route::post('/koperasi/orders/{kode}/pay-tabungan', [KoperasiController::class, 'payWithTabungan'])->name('koperasi.pay-tabungan');
    Route::post('/koperasi/orders/{kode}/midtrans', [KoperasiController::class, 'createMidtrans'])->name('koperasi.midtrans');
});

// Webhook Midtrans koperasi — tanpa auth/CSRF, signature diverifikasi di controller
Route::post('/koperasi/midtrans/notification', [KoperasiController::class, 'midtransNotification'])
    ->middleware('throttle:120,1')
    ->name('koperasi.midtrans.notification');

// SPP (publik) link orang tua — tanpa akun, via signed URL
Route::get('/spp/ortu/{user}', [SppController::class, 'ortuIndex'])->name('spp.ortu');
