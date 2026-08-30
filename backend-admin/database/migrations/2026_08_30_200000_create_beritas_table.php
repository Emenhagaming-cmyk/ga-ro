<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category'); // Pengumuman, Prestasi, Kerjasama, Kegiatan, Acara
            $table->string('category_color')->default('#3a6450');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image_path')->nullable(); // path di storage
            $table->string('author')->default('Humas SMK Bahrul Ulum');
            $table->date('published_at');
            $table->boolean('featured')->default(false);
            $table->string('read_time')->default('3 menit');
            $table->boolean('is_published')->default(true);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};
