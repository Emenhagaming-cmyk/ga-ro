<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('periode');
            $table->unsignedBigInteger('nominal');
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();
        });

        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('spp_bills')->onDelete('cascade');
            $table->enum('metode', ['tunai', 'transfer']);
            $table->unsignedBigInteger('amount');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
        Schema::dropIfExists('spp_bills');
    }
};
