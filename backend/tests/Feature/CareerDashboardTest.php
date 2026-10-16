<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use App\Models\Bookmark;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CareerDashboardTest extends TestCase
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
            $table->string('work_arrangement')->nullable();
            $table->text('description');
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'lowongan_id']);
        });

        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained()->onDelete('cascade');
            $table->string('cv_path')->nullable();
            $table->text('cover_letter');
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'dibatalkan'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nisn')->nullable();
            $table->string('email')->nullable();
            $table->string('jurusan_pilihan')->nullable();
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
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('lowongans');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    private function makeUser(string $username = 'siswa'): User
    {
        return User::create([
            'name' => 'Siswa Test',
            'username' => $username,
            'email' => $username . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
    }

    private function makeLowongan(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'title' => 'Frontend Developer',
            'company' => 'Tech Corp',
            'location' => 'Surabaya',
            'jurusan' => 'RPL',
            'type' => 'Kerja',
            'work_arrangement' => 'onsite',
            'description' => 'Test job',
            'is_active' => true,
        ], $overrides));
    }

    public function test_dashboard_mengembalikan_statistik_user()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $lowongan1 = $this->makeLowongan(['title' => 'Job 1']);
        $lowongan2 = $this->makeLowongan(['title' => 'Job 2']);

        Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan1->id,
            'cover_letter' => 'Test cover letter',
            'status' => 'pending',
        ]);

        Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan2->id,
            'cover_letter' => 'Test cover letter 2',
            'status' => 'diterima',
        ]);

        Bookmark::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan1->id,
        ]);

        $response = $this->getJson('/career/dashboard');

        $response->assertOk()
            ->assertJsonPath('total_lamaran', 2)
            ->assertJsonPath('lamaran_pending', 1)
            ->assertJsonPath('lamaran_diterima', 1)
            ->assertJsonPath('bookmark_count', 1);
    }

    public function test_dashboard_guest_ditolak()
    {
        $this->getJson('/career/dashboard')->assertUnauthorized();
    }

    public function test_dashboard_empty_state()
    {
        $user = $this->makeUser('empty_user');
        $this->actingAs($user);

        $response = $this->getJson('/career/dashboard');

        $response->assertOk()
            ->assertJsonPath('total_lamaran', 0)
            ->assertJsonPath('lamaran_pending', 0)
            ->assertJsonPath('bookmark_count', 0)
            ->assertJsonPath('upcoming_deadline', null);
    }
}
