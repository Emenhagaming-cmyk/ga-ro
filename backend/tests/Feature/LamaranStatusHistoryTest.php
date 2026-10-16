<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use App\Models\LamaranStatusHistory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LamaranStatusHistoryTest extends TestCase
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

        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained()->onDelete('cascade');
            $table->string('cv_path')->nullable();
            $table->text('cover_letter');
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'dibatalkan'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });

        Schema::create('lamaran_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lamaran_id')->constrained()->onDelete('cascade');
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('lamaran_status_histories');
        Schema::dropIfExists('lamarans');
        Schema::dropIfExists('lowongans');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    private function makeUser(string $role = 'siswa', string $username = 'testuser'): User
    {
        return User::create([
            'name' => 'Test User',
            'username' => $username,
            'email' => $username . '@test.com',
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }

    private function makeLowongan(): Lowongan
    {
        return Lowongan::create([
            'title' => 'Test Position',
            'company' => 'Test Company',
            'location' => 'Jakarta',
            'jurusan' => 'RPL',
            'type' => 'Magang',
            'description' => 'Test description',
        ]);
    }

    public function test_lamaran_baru_membuat_history_otomatis()
    {
        $user = $this->makeUser();
        $this->actingAs($user);
        $lowongan = $this->makeLowongan();

        $lamaran = Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
            'cover_letter' => 'Test cover letter',
            'status' => 'pending',
        ]);

        $history = LamaranStatusHistory::where('lamaran_id', $lamaran->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals('pending', $history->status_to);
        $this->assertNull($history->status_from);
    }

    public function test_update_status_membuat_history_baru()
    {
        $user = $this->makeUser();
        $this->actingAs($user);
        $lowongan = $this->makeLowongan();

        $lamaran = Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
            'cover_letter' => 'Test cover letter',
            'status' => 'pending',
        ]);

        $lamaran->update(['status' => 'diterima']);

        $history = LamaranStatusHistory::where('lamaran_id', $lamaran->id)
            ->where('status_from', 'pending')
            ->where('status_to', 'diterima')
            ->first();
        $this->assertNotNull($history);
    }

    public function test_api_show_mengembalikan_status_history()
    {
        $user = $this->makeUser();
        $this->actingAs($user);
        $lowongan = $this->makeLowongan();

        $lamaran = Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
            'cover_letter' => 'Test cover letter',
            'status' => 'pending',
        ]);

        $lamaran->update(['status' => 'diterima']);

        $response = $this->getJson('/lamaran/' . $lamaran->id);

        $response->assertOk()
            ->assertJsonStructure(['status_histories']);
        $this->assertCount(2, $response->json('status_histories'));
    }

    public function test_api_my_applications_mengembalikan_status_history()
    {
        $user = $this->makeUser('history_user');
        $this->actingAs($user);
        $lowongan = $this->makeLowongan();

        Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
            'cover_letter' => 'Test cover letter',
            'status' => 'pending',
        ]);

        $response = $this->getJson('/lamaran/saya');

        $response->assertOk();
        $this->assertArrayHasKey('status_histories', $response->json()[0]);
    }
}
