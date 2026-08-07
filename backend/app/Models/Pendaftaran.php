<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    protected $fillable = [
        'user_id', 'nama_lengkap', 'nama_panggilan', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir',
        'umur', 'agama', 'kewarnegaraan', 'kategori_pendaftar', 'jenis_kelamin', 'alamat',
        'rt_rw', 'kode_pos', 'asal_sekolah', 'gelombang', 'tahun_lulus', 'rata_rata_nilai',
        'no_hp', 'email', 'jurusan_pilihan', 'jumlah_saudara', 'anak_ke', 'status_keluarga',
        'nama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'alamat_ayah', 'hp_ayah',
        'nama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'alamat_ibu', 'hp_ibu',
        'nama_wali', 'hubungan_wali', 'email_orang_tua', 'jenis_pembayaran', 'berkas_tambahan',
        'foto_3x4', 'kk_file', 'ijazah_file', 'sktm_file',
        'nama_orang_tua', 'no_hp_orang_tua', 'status',
        'data_confirmed', 'confirmed_at', 'status_updated_at'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
        'data_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
