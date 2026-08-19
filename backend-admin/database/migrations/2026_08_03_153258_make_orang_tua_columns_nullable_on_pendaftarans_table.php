<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pendaftarans MODIFY nama_orang_tua VARCHAR(255) NULL');
        DB::statement('ALTER TABLE pendaftarans MODIFY no_hp_orang_tua VARCHAR(20) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pendaftarans MODIFY nama_orang_tua VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE pendaftarans MODIFY no_hp_orang_tua VARCHAR(20) NOT NULL');
    }
};