<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pendaftaran;

class AnjarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'anjar'],
            [
                'name' => 'Anjar Wicaksono',
                'email' => 'sampbernad@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'siswa',
            ]
        );

        Pendaftaran::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => 'Anjar Wicaksono',
                'nama_panggilan' => 'Anjar',
                'nisn' => '0071234567',
                'nik' => '3578010101080001',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2008-01-01',
                'umur' => 18,
                'agama' => 'Islam',
                'kewarnegaraan' => 'WNI',
                'kategori_pendaftar' => 'Siswa Baru',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Bahrul Ulum No. 12',
                'rt_rw' => '02/03',
                'kode_pos' => '60115',
                'asal_sekolah' => 'SMP Negeri 1 Surabaya',
                'gelombang' => 'Gelombang 1',
                'tahun_lulus' => 2026,
                'rata_rata_nilai' => '85.50',
                'no_hp' => '081234567890',
                'email' => 'sampbernad@gmail.com',
                'jurusan_pilihan' => 'RPL',
                'nama_orang_tua' => 'Bapak Wicaksono',
                'no_hp_orang_tua' => '081234567891',
                'status' => 'diterima',
                'data_confirmed' => true,
                'confirmed_at' => now(),
                'status_updated_at' => now(),
            ]
        );
    }
}
