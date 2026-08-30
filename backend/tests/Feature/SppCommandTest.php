<?php

namespace Tests\Feature;

use App\Models\SppBill;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SppCommandTest extends TestCase
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

    public function test_generate_membuat_tagihan_untuk_semua_siswa(): void
    {
        $this->makeUser('siswa', 'Siswa A', 'siswa_a');
        $this->makeUser('siswa', 'Siswa B', 'siswa_b');
        $this->makeUser('guru', 'Guru A', 'guru_a');

        $exit = Artisan::call('spp:generate', ['--periode' => '2026-08', '--nominal' => 200000]);

        $this->assertSame(0, $exit);
        $this->assertSame(2, SppBill::where('periode', '2026-08')->count());
        $this->assertSame(200000, SppBill::where('periode', '2026-08')->first()->nominal);
    }

    public function test_generate_idempotent_tidak_membuat_duplikat(): void
    {
        $this->makeUser('siswa', 'Siswa A', 'siswa_a');

        Artisan::call('spp:generate', ['--periode' => '2026-08', '--nominal' => 150000]);
        $exit = Artisan::call('spp:generate', ['--periode' => '2026-08', '--nominal' => 150000]);

        $this->assertSame(0, $exit);
        $this->assertSame(1, SppBill::where('periode', '2026-08')->count());
    }
}
