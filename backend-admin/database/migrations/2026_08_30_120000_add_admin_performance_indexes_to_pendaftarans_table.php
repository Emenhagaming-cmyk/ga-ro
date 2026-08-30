<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('nisn');
            $table->index('nik');
            $table->index(['status', 'jurusan_pilihan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['nisn']);
            $table->dropIndex(['nik']);
            $table->dropIndex(['status', 'jurusan_pilihan']);
        });
    }
};