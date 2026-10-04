<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('jurusan');
            $table->enum('type', ['Magang', 'Kerja', 'BKK']);
            $table->text('description');
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
