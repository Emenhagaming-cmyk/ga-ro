<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookmarkControllerTest extends TestCase
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

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'lowongan_id']);
        });
    }

    protected function tearDown(): void
    {
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

    private function makeLowongan(): Lowongan
    {
        return Lowongan::create([
            'title' => 'Frontend Developer',
            'company' => 'Tech Corp',
            'location' => 'Surabaya',
            'jurusan' => 'RPL',
            'type' => 'Kerja',
            'description' => 'Deskripsi lowongan',
        ]);
    }

    public function test_guest_ditolak_bookmark()
    {
        $this->getJson('/bookmarks')->assertUnauthorized();
        $this->postJson('/bookmarks', ['lowongan_id' => 1])->assertUnauthorized();
        $this->deleteJson('/bookmarks/1')->assertUnauthorized();
    }

    public function test_bookmark_lowongan()
    {
        $user = $this->makeUser();
        $lowongan = $this->makeLowongan();

        $response = $this->actingAs($user)->postJson('/bookmarks', [
            'lowongan_id' => $lowongan->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Lowongan berhasil disimpan.');

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
        ]);
    }

    public function test_duplicate_bookmark_ditolak()
    {
        $user = $this->makeUser('siswa1');
        $lowongan = $this->makeLowongan();

        $this->actingAs($user)->postJson('/bookmarks', [
            'lowongan_id' => $lowongan->id,
        ])->assertCreated();

        $this->actingAs($user)->postJson('/bookmarks', [
            'lowongan_id' => $lowongan->id,
        ])->assertStatus(422);
    }

    public function test_hapus_bookmark()
    {
        $user = $this->makeUser('siswa2');
        $lowongan = $this->makeLowongan();

        Bookmark::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $response = $this->actingAs($user)->deleteJson('/bookmarks/' . $lowongan->id);

        $response->assertOk()
            ->assertJsonPath('message', 'Bookmark dihapus.');

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
        ]);
    }

    public function test_hapus_bookmark_tidak_ada()
    {
        $user = $this->makeUser('siswa3');
        $lowongan = $this->makeLowongan();

        $response = $this->actingAs($user)->deleteJson('/bookmarks/' . $lowongan->id);

        $response->assertNotFound()
            ->assertJsonPath('message', 'Bookmark tidak ditemukan.');
    }

    public function test_list_bookmark_user()
    {
        $user = $this->makeUser('siswa4');
        $lowongan = $this->makeLowongan();

        Bookmark::create([
            'user_id' => $user->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $response = $this->actingAs($user)->getJson('/bookmarks');

        $response->assertOk()
            ->assertJsonStructure([[
                'id', 'user_id', 'lowongan_id', 'created_at', 'updated_at', 'lowongan',
            ]]);
    }

    public function test_user_hanya_lihat_bookmark_sendiri()
    {
        $userA = $this->makeUser('userA');
        $userB = $this->makeUser('userB');
        $lowongan = $this->makeLowongan();

        Bookmark::create([
            'user_id' => $userA->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $response = $this->actingAs($userB)->getJson('/bookmarks');
        $response->assertOk();
        $this->assertCount(0, $response->json());
    }
}
