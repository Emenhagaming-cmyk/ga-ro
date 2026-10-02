<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardSiswaHierarchyTest extends TestCase
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
    }

    protected function tearDown(): void
    {
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

    private function assertAnnounceAfterBanner(string $content): void
    {
        $banner = strpos($content, 'class="ds-banner"');
        $announce = strpos($content, 'class="ds-announce');

        $this->assertNotFalse($announce, 'Card pengumuman (ds-announce) harus ada');
        $this->assertNotFalse($banner, 'Banner sambutan harus ada');
        $this->assertLessThan($announce, $banner, 'Pengumuman harus tampil TEPAT SETELAH banner sambutan');
    }

    public function test_pengumuman_diterima_tampil_paling_atas_dengan_tombol_unduh()
    {
        $user = $this->makeUser('siswa', 'ds_siswa1');
        $this->makePendaftaran($user, 'diterima');

        $response = $this->actingAs($user)->get('/dashboard-siswa');
        $response->assertOk();
        $content = $response->getContent();

        $this->assertAnnounceAfterBanner($content);
        $response->assertSee('ds-announce--diterima');
        $response->assertSee('Pengumuman Hasil Seleksi');
        $response->assertSee('Unduh Bukti Diterima', false);
    }

    public function test_pengumuman_ditolak_tampil_atas_dan_teks_menyampaikan_penolakan()
    {
        $user = $this->makeUser('siswa', 'ds_siswa2');
        $this->makePendaftaran($user, 'ditolak');

        $response = $this->actingAs($user)->get('/dashboard-siswa');
        $response->assertOk();
        $content = $response->getContent();

        $this->assertAnnounceAfterBanner($content);
        $response->assertSee('ds-announce--ditolak');
        $response->assertSee('tidak diterima');
        $response->assertDontSee('Unduh Bukti Diterima');
    }

    public function test_status_diproses_naik_ke_atas()
    {
        $user = $this->makeUser('siswa', 'ds_siswa3');
        $this->makePendaftaran($user, 'diproses');

        $response = $this->actingAs($user)->get('/dashboard-siswa');
        $response->assertOk();
        $content = $response->getContent();

        $this->assertAnnounceAfterBanner($content);
        $response->assertSee('ds-announce--diproses');
        $response->assertSee('sedang diproses');
    }

    public function test_status_baru_lewat_deadline_tampil_informasi_penting_di_atas()
    {
        $user = $this->makeUser('siswa', 'ds_siswa4');
        $p = $this->makePendaftaran($user, 'baru');
        $p->forceFill(['created_at' => now()->subDays(4)])->save();

        $response = $this->actingAs($user)->get('/dashboard-siswa');
        $response->assertOk();
        $content = $response->getContent();

        $this->assertAnnounceAfterBanner($content);
        $response->assertSee('ds-announce--baru');
        $response->assertSee('Informasi Penting');
        $response->assertSee('Batas waktu edit telah berakhir');
    }

    public function test_baru_yang_masih_bisa_diedit_tidak_menampilkan_pengumuman()
    {
        $user = $this->makeUser('siswa', 'ds_siswa5');
        $this->makePendaftaran($user, 'baru');

        $response = $this->actingAs($user)->get('/dashboard-siswa');
        $response->assertOk();

        $response->assertDontSee('class="ds-announce', false);
        $response->assertSee('Edit Formulir');
        $response->assertSee('id="edit-section"', false);
    }
}