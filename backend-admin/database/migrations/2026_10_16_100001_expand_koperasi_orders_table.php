<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('koperasi_orders', function (Blueprint $table) {
            $table->string('kode')->nullable()->unique()->after('user_id');
            $table->string('pickup_code', 8)->nullable()->after('total');
            $table->string('snap_token')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->timestamp('picked_up_at')->nullable();
        });

        // Order lama (prototipe) belum punya kode → beri kode unik.
        foreach (DB::table('koperasi_orders')->whereNull('kode')->pluck('id') as $id) {
            DB::table('koperasi_orders')->where('id', $id)->update([
                'kode' => 'KOP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            ]);
        }

        // Enum hanya bisa diubah di MySQL (pola 2026_10_15_100000). Status lama 'lunas'
        // dipetakan ke 'dibayar' supaya alur baru (pending → dibayar → diambil) konsisten.
        if (DB::getDriverName() === 'mysql') {
            DB::table('koperasi_orders')->where('status', 'lunas')->update(['status' => 'dibayar']);
            DB::statement("ALTER TABLE koperasi_orders MODIFY COLUMN status ENUM('pending','dibayar','diambil','batal','expired') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE koperasi_orders MODIFY COLUMN metode ENUM('qris','transfer','tabungan','midtrans') NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('koperasi_orders')->whereIn('status', ['dibayar', 'diambil'])->update(['status' => 'lunas']);
            DB::statement("ALTER TABLE koperasi_orders MODIFY COLUMN status ENUM('pending','lunas','batal') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE koperasi_orders MODIFY COLUMN metode ENUM('qris','transfer') NULL");
        }

        Schema::table('koperasi_orders', function (Blueprint $table) {
            $table->dropColumn(['kode', 'pickup_code', 'snap_token', 'midtrans_order_id', 'picked_up_at']);
        });
    }
};
