<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Siswa bisa bayar SPP dari saldo tabungan → metode pembayaran bertambah 'tabungan'.
     * MODIFY COLUMN MySQL-only (seperti 3 migration lama) → skip di sqlite (DB lokal/test).
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE spp_payments MODIFY COLUMN metode ENUM('tunai', 'transfer', 'tabungan') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE spp_payments MODIFY COLUMN metode ENUM('tunai', 'transfer') NOT NULL");
    }
};
