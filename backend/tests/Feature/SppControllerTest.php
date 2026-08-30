<?php

namespace Tests\Feature;

use App\Models\SppBill;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SppControllerTest extends TestCase
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

    private function makeUser(string $role, string $name, string $uname): User
    {
        return User::create([
            'name' => $name,
            'username' => $uname,
            'email' => $uname . '@test.com',
            'password' => bcrypt('secret'),
            'role' => $role,
        ]);
    }

    private function makeBill(User $siswa, int $nominal = 150000): SppBill
    {
        return SppBill::create([
            'user_id' => $siswa->id,
            'periode' => '2026-08',
            'nominal' => $nominal,
            'status' => 'belum',
        ]);
    }

    public function test_kasir_input_pembayaran_langsung_terlihat_di_siswa(): void
    {
        $kasir = $this->makeUser('kasir', 'Kasir Satu', 'kasir1');
        $siswa = $this->makeUser('siswa', 'Siswa Satu', 'siswa1');
        $bill = $this->makeBill($siswa);

        $this->actingAs($kasir)->postJson('/spp/pay', [
            'bill_id' => $bill->id,
            'metode' => 'tunai',
            'amount' => 150000,
        ])->assertStatus(201)->assertJsonPath('bill.status', 'lunas');

        $this->actingAs($siswa)->getJson('/spp')
            ->assertStatus(200)
            ->assertJsonCount(1, 'bills')
            ->assertJsonPath('bills.0.nominal', 150000)
            ->assertJsonPath('bills.0.status', 'lunas');
    }

    public function test_pembayaran_sebagian_tidak_langsung_melunasi(): void
    {
        $kasir = $this->makeUser('kasir', 'Kasir Satu', 'kasir1');
        $siswa = $this->makeUser('siswa', 'Siswa Satu', 'siswa1');
        $bill = $this->makeBill($siswa, 150000);

        $this->actingAs($kasir)->postJson('/spp/pay', [
            'bill_id' => $bill->id,
            'metode' => 'tunai',
            'amount' => 50000,
        ])->assertStatus(201);

        $this->actingAs($siswa)->getJson('/spp')
            ->assertStatus(200)
            ->assertJsonPath('bills.0.status', 'belum')
            ->assertJsonPath('bills.0.sisa', 100000);
    }

    public function test_siswa_tidak_bisa_menginput_pembayaran(): void
    {
        $siswa = $this->makeUser('siswa', 'Siswa Satu', 'siswa1');
        $bill = $this->makeBill($siswa);

        $this->actingAs($siswa)->postJson('/spp/pay', [
            'bill_id' => $bill->id,
            'metode' => 'tunai',
            'amount' => 50000,
        ])->assertStatus(403);
    }

    public function test_admin_bisa_input_pembayaran_dan_lihat_rekap(): void
    {
        $admin = $this->makeUser('admin', 'Admin', 'admin');
        $siswa = $this->makeUser('siswa', 'Siswa Satu', 'siswa1');
        $bill = $this->makeBill($siswa);

        $this->actingAs($admin)->postJson('/spp/pay', [
            'bill_id' => $bill->id,
            'metode' => 'transfer',
            'amount' => 150000,
        ])->assertStatus(201);

        $this->actingAs($admin)->getJson('/admin/spp')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Siswa Satu')
            ->assertJsonPath('0.bills.0.status', 'lunas');
    }
}
