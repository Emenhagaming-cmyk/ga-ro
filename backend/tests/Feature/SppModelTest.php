<?php

namespace Tests\Feature;

use App\Models\SppBill;
use App\Models\SppPayment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SppModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('pendaftar');
            $table->timestamps();
        });

        Schema::create('spp_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('periode');
            $table->unsignedBigInteger('nominal');
            $table->string('status')->default('belum');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();
        });

        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('spp_bills')->onDelete('cascade');
            $table->string('metode');
            $table->unsignedBigInteger('amount');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('spp_payments');
        Schema::dropIfExists('spp_bills');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_users_role_accepts_guru_dan_kasir(): void
    {
        $guru = User::create([
            'name' => 'Guru Satu',
            'username' => 'guru1',
            'email' => 'guru1@test.com',
            'password' => bcrypt('secret'),
            'role' => 'guru',
        ]);
        $this->assertEquals('guru', $guru->fresh()->role);

        $kasir = User::create([
            'name' => 'Kasir Satu',
            'username' => 'kasir1',
            'email' => 'kasir1@test.com',
            'password' => bcrypt('secret'),
            'role' => 'kasir',
        ]);
        $this->assertEquals('kasir', $kasir->fresh()->role);
    }

    public function test_spp_bill_bisa_menyimpan_dan_berelasi_ke_user(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Satu',
            'username' => 'siswa1',
            'email' => 'siswa1@test.com',
            'password' => bcrypt('secret'),
            'role' => 'siswa',
        ]);

        $bill = SppBill::create([
            'user_id' => $siswa->id,
            'periode' => '2026-08',
            'nominal' => 150000,
            'status' => 'belum',
        ]);

        $this->assertEquals('belum', $bill->status);
        $this->assertSame($siswa->id, $bill->user->id);
        $this->assertCount(1, $siswa->sppBills);
    }

    public function test_spp_payment_berelasi_ke_bill_dan_inputter(): void
    {
        $kasir = User::create([
            'name' => 'Kasir Satu',
            'username' => 'kasir1',
            'email' => 'kasir1@test.com',
            'password' => bcrypt('secret'),
            'role' => 'kasir',
        ]);

        $siswa = User::create([
            'name' => 'Siswa Satu',
            'username' => 'siswa1',
            'email' => 'siswa1@test.com',
            'password' => bcrypt('secret'),
            'role' => 'siswa',
        ]);

        $bill = SppBill::create([
            'user_id' => $siswa->id,
            'periode' => '2026-08',
            'nominal' => 150000,
            'status' => 'belum',
        ]);

        $payment = SppPayment::create([
            'bill_id' => $bill->id,
            'metode' => 'tunai',
            'amount' => 150000,
            'paid_at' => now(),
            'input_by' => $kasir->id,
        ]);

        $this->assertSame($bill->id, $payment->bill->id);
        $this->assertSame($kasir->id, $payment->inputter->id);
        $this->assertCount(1, $bill->payments);
    }
}
