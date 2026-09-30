<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
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
            $table->mediumText('avatar')->nullable();
            $table->string('role')->default('pendaftar');
            $table->timestamps();
        });

        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarnegaraan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('gelombang')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->string('rata_rata_nilai')->nullable();
            $table->string('jurusan_pilihan')->nullable();
            $table->string('kategori_pendaftar')->nullable();
            $table->string('jenis_pembayaran')->nullable();
            $table->text('berkas_tambahan')->nullable();
            $table->string('jumlah_saudara')->nullable();
            $table->string('anak_ke')->nullable();
            $table->string('status_keluarga')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->text('alamat_ayah')->nullable();
            $table->string('hp_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->text('alamat_ibu')->nullable();
            $table->string('hp_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('hubungan_wali')->nullable();
            $table->string('email_orang_tua')->nullable();
            $table->string('foto_3x4')->nullable();
            $table->string('kk_file')->nullable();
            $table->string('ijazah_file')->nullable();
            $table->string('sktm_file')->nullable();
            $table->string('status')->default('baru');
            $table->boolean('data_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
        });

        // Dipakai oleh halaman SPP rekap (guru) yang juga memakai topbar
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
        Schema::dropIfExists('pendaftarans');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    private function makeUser(string $role, string $uname): User
    {
        return User::create([
            'name' => 'Zakky Pratama',
            'username' => $uname,
            'email' => $uname . '@test.com',
            'password' => bcrypt('secret1234'),
            'role' => $role,
        ]);
    }

    private function makePendaftaran(User $user, string $status = 'baru'): Pendaftaran
    {
        return Pendaftaran::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Zakky Pratama',
            'nisn' => '321654879',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '2009-05-17',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kewarnegaraan' => 'Indonesia',
            'no_hp' => '08123456789',
            'asal_sekolah' => 'SMPN 1 Sidoarjo',
            'gelombang' => 'Gelombang 1',
            'tahun_lulus' => '2026',
            'rata_rata_nilai' => '87.5',
            'jurusan_pilihan' => 'RPL',
            'nama_ayah' => 'Bapak Zakky',
            'nama_ibu' => 'Ibu Zakky',
            'status' => $status,
        ]);
    }

    public function test_siswa_bisa_akses_halaman_profil_dan_melihat_data()
    {
        $user = $this->makeUser('siswa', 'siswa01');
        $this->makePendaftaran($user, 'diterima');

        $response = $this->actingAs($user)->get(route('profil'));

        $response->assertOk();
        $response->assertSee('Profil Siswa');
        $response->assertSee('Zakky Pratama');
        $response->assertSee('321654879');
        $response->assertSee('RPL');
        // Tiga tab sesuai referensi desain
        $response->assertSee('data-pf-tab="profil"', false);
        $response->assertSee('data-pf-tab="pendaftaran"', false);
        $response->assertSee('data-pf-tab="keluarga"', false);
        $response->assertSee('Informasi Akademik', false);
    }

    public function test_pendaftar_yang_belum_diterima_tetap_bisa_akses_profil()
    {
        $user = $this->makeUser('pendaftar', 'pendaftar01');
        $this->makePendaftaran($user, 'baru');

        $this->actingAs($user)->get(route('profil'))->assertOk();
    }

    public function test_role_non_siswa_diarahkan_ke_login()
    {
        $guru = $this->makeUser('guru', 'guru01');

        $this->actingAs($guru)->get(route('profil'))->assertRedirect(route('login'));
        $this->actingAs($guru)->post(route('profil.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('foto.jpg'),
        ])->assertRedirect(route('login'));
    }

    public function test_profil_tanpa_pendaftaran_menampilkan_empty_state()
    {
        $user = $this->makeUser('siswa', 'siswa02');

        $response = $this->actingAs($user)->get(route('profil'));

        $response->assertOk();
        $response->assertSee('Belum Mendaftar');
        $response->assertSee('Isi Formulir Pendaftaran');
    }

    public function test_upload_foto_profil_disimpan_sebagai_base64_dan_diresize()
    {
        $user = $this->makeUser('siswa', 'siswa03');

        $response = $this->actingAs($user)->post(route('profil.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('foto-gedung.jpg', 1600, 1200),
        ]);

        $response->assertRedirect(route('profil'));
        $response->assertSessionHas('success');

        $avatar = $user->fresh()->avatar;

        $this->assertNotNull($avatar, 'Avatar harus tersimpan di kolom users.avatar');
        $this->assertStringStartsWith('data:image/jpeg;base64,', $avatar);

        $binary = base64_decode(substr($avatar, strlen('data:image/jpeg;base64,')), true);
        $this->assertNotFalse($binary, 'Base64 harus valid');
        $this->assertLessThanOrEqual(350000, strlen($avatar), 'Ukuran data URI harus terkendali');

        $size = getimagesizefromstring($binary);
        $this->assertNotFalse($size, 'Hasil harus gambar yang bisa dibaca');
        $this->assertSame(256, $size[0], 'Sisi terpanjang harus dikecilkan ke 256px');
        $this->assertSame(192, $size[1], 'Aspect ratio harus dipertahankan');
    }

    public function test_foto_ditampilkan_di_halaman_dan_topbar_setelah_disimpan()
    {
        $user = $this->makeUser('siswa', 'siswa04');
        $this->makePendaftaran($user, 'diterima');

        $this->actingAs($user)->post(route('profil.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('foto.jpg', 600, 600),
        ])->assertRedirect(route('profil'));

        $avatar = $user->fresh()->avatar;

        $this->actingAs($user)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee($avatar, false);
    }

    public function test_upload_file_terlalu_besar_ditolak()
    {
        $user = $this->makeUser('siswa', 'siswa05');

        $response = $this->actingAs($user)->post(route('profil.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('gedung-besar.jpg', 600, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar, 'Avatar tidak boleh tersimpan');
    }

    public function test_upload_file_bukan_gambar_ditolak()
    {
        $user = $this->makeUser('siswa', 'siswa06');

        $response = $this->actingAs($user)->post(route('profil.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('dokumen.pdf', 20, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_upload_gambar_rusak_tidak_disimpan()
    {
        $user = $this->makeUser('siswa', 'siswa07');

        $file = UploadedFile::fake()->createWithContent('rusak.jpg', 'bukan-gambar-sungguhan');

        $response = $this->actingAs($user)->post(route('profil.avatar.update'), ['avatar' => $file]);

        $this->assertTrue(
            $response->assertStatus(302) && ($response->getSession()->has('error') || $response->getSession()->hasErrors()),
            'Upload gambar rusak harus ditolak dengan pesan error'
        );
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_hapus_foto_profil_mengosongkan_kolom()
    {
        $user = $this->makeUser('siswa', 'siswa08');
        $user->forceFill(['avatar' => 'data:image/jpeg;base64,/9j/4AAQSkZJRg=='])->save();

        $response = $this->actingAs($user)->delete(route('profil.avatar.destroy'));

        $response->assertRedirect(route('profil'));
        $response->assertSessionHas('success');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_avatar_tidak_bocor_saat_user_diserialisasi()
    {
        $user = $this->makeUser('siswa', 'siswa09');
        $user->forceFill(['avatar' => 'data:image/jpeg;base64,/9j/4AAQSkZJRg=='])->save();

        $array = $user->fresh()->toArray();

        $this->assertArrayNotHasKey('avatar', $array, 'Avatar tidak ikut serialisasi JSON');
        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_auth_status_mengirim_avatar_ke_frontend()
    {
        $user = $this->makeUser('siswa', 'siswa9a');
        $this->makePendaftaran($user, 'diterima');
        $avatar = 'data:image/jpeg;base64,/9j/4AAQSkZJRg==';
        $user->forceFill(['avatar' => $avatar])->save();

        $response = $this->actingAs($user)->getJson('/auth-status');

        $response->assertOk()->assertJson([
            'logged_in' => true,
            'role' => 'siswa',
            'name' => 'Zakky Pratama',
            'avatar' => $avatar,
        ]);
    }

    public function test_auth_status_guest_avatar_null_konsisten()
    {
        $this->getJson('/auth-status')
            ->assertOk()
            ->assertJson([
                'logged_in' => false,
                'avatar' => null,
            ]);
    }

    public function test_frontend_auth_url_payload_menyertakan_avatar()
    {
        // redirect/frontendAuthUrl() membawa status via ?auth= untuk browser tanpa cookie
        $user = $this->makeUser('siswa', 'siswa9b');
        $this->makePendaftaran($user, 'diterima');
        $avatar = 'data:image/jpeg;base64,/9j/4AAQSkZJRg==';
        $user->forceFill(['avatar' => $avatar])->save();

        $this->actingAs($user);

        $url = frontendAuthUrl();
        $query = parse_url($url, PHP_URL_QUERY);
        $payload = $query ? json_decode(base64_decode(explode('auth=', $query)[1] ?? ''), true) : null;

        $this->assertNotNull($payload, 'Payload ?auth= harus ada');
        $this->assertSame($avatar, $payload['avatar']);
        $this->assertSame('siswa', $payload['role']);
    }

    public function test_link_profil_di_topbar_hanya_untuk_siswa_dan_pendaftar()
    {
        $siswa = $this->makeUser('siswa', 'siswa10');
        $guru = $this->makeUser('guru', 'guru10');

        // Halaman yang memakai topbar: dashboard siswa
        $this->actingAs($siswa)->get(route('dashboard.siswa'))
            ->assertOk()
            ->assertSee('href="' . route('profil') . '"', false);

        Auth::logout();
        $this->actingAs($guru)->get(route('spp.rekap'))
            ->assertOk()
            ->assertDontSee('href="' . route('profil') . '"', false);
    }
}
