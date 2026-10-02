<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->dropForeign(['input_by']);
            $table->dropColumn('input_by');
        });
    }
};
