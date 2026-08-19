<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','siswa','pendaftar') NOT NULL DEFAULT 'pendaftar'");

        DB::statement("UPDATE users SET role = 'pendaftar' WHERE role = 'siswa'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'siswa' WHERE role = 'pendaftar'");

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa'");
    }
};
