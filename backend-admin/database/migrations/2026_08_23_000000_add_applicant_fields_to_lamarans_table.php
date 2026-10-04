<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('user_id');
            $table->string('nisn')->nullable()->after('nama_lengkap');
            $table->string('jurusan_pilihan')->nullable()->after('nisn');
        });
    }

    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropColumn(['nama_lengkap', 'nisn', 'jurusan_pilihan']);
        });
    }
};
