<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('nama_panggilan')->nullable()->after('nama_lengkap');
            $table->unsignedTinyInteger('umur')->nullable()->after('tanggal_lahir');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->string('kewarnegaraan')->nullable()->after('agama');
            $table->string('kategori_pendaftar')->nullable()->after('kewarnegaraan');
            $table->string('rt_rw')->nullable()->after('alamat');
            $table->string('kode_pos')->nullable()->after('rt_rw');
            $table->string('gelombang')->nullable()->after('asal_sekolah');
            $table->unsignedSmallInteger('tahun_lulus')->nullable()->after('gelombang');
            $table->string('rata_rata_nilai')->nullable()->after('tahun_lulus');
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
            $table->string('jenis_pembayaran')->nullable();
            $table->text('berkas_tambahan')->nullable();
            $table->string('foto_3x4')->nullable();
            $table->string('kk_file')->nullable();
            $table->string('ijazah_file')->nullable();
            $table->string('sktm_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_panggilan', 'umur', 'agama', 'kewarnegaraan', 'kategori_pendaftar',
                'rt_rw', 'kode_pos', 'gelombang', 'tahun_lulus', 'rata_rata_nilai',
                'jumlah_saudara', 'anak_ke', 'status_keluarga', 'nama_ayah',
                'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'alamat_ayah', 'hp_ayah',
                'nama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'alamat_ibu', 'hp_ibu',
                'nama_wali', 'hubungan_wali', 'email_orang_tua', 'jenis_pembayaran', 'berkas_tambahan',
                'foto_3x4', 'kk_file', 'ijazah_file', 'sktm_file',
            ]);
        });
    }
};
