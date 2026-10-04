<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto profil disimpan sebagai data URI base64 (hasil resize GD, maks 256px)
     * supaya tidak bergantung pada storage disk — file storage tidak persisten
     * di environment serverless/VPS ephemeral.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->mediumText('avatar')->nullable()->after('password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }
};
