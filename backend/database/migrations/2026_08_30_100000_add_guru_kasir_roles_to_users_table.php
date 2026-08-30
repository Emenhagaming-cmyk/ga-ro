<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','siswa','pendaftar','guru','kasir') NOT NULL DEFAULT 'pendaftar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','siswa','pendaftar') NOT NULL DEFAULT 'pendaftar'");
    }
};
