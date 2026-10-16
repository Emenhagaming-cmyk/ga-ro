<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Lamaran;
use App\Models\LamaranStatusHistory;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CareerCenterSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'sampbernad@gmail.com')->first();
        if (!$user) {
            return;
        }

        $lowonganIds = Lowongan::where('is_active', true)->pluck('id')->toArray();
        if (empty($lowonganIds)) {
            return;
        }

        // Bookmark 5 lowongan
        $bookmarkCount = 0;
        foreach (array_slice($lowonganIds, 0, 5) as $lowonganId) {
            if (Bookmark::where('user_id', $user->id)->where('lowongan_id', $lowonganId)->exists()) {
                continue;
            }
            Bookmark::create([
                'user_id' => $user->id,
                'lowongan_id' => $lowonganId,
            ]);
            $bookmarkCount++;
        }

        // Lamaran ke 3 lowongan berbeda
        $lamaranCount = 0;
        foreach (array_slice($lowonganIds, 5, 3) as $lowonganId) {
            if (Lamaran::where('user_id', $user->id)->where('lowongan_id', $lowonganId)->exists()) {
                continue;
            }
            $lamaran = Lamaran::create([
                'user_id' => $user->id,
                'lowongan_id' => $lowonganId,
                'cover_letter' => 'Saya tertarik dengan posisi ini karena pengalaman dan minat saya selama di SMK Bahrul Ulum.',
                'nama_lengkap' => 'Anjar Wicaksono',
                'email' => 'sampbernad@gmail.com',
                'nisn' => '0071234567',
                'jurusan_pilihan' => 'RPL',
                'status' => 'pending',
            ]);
            $lamaranCount++;
        }

        // Buat lamaran dengan status diterima + history
        if (count($lowonganIds) > 8) {
            $acceptedLowonganId = $lowonganIds[8];
            if (!Lamaran::where('user_id', $user->id)->where('lowongan_id', $acceptedLowonganId)->exists()) {
                $lamaran = Lamaran::create([
                    'user_id' => $user->id,
                    'lowongan_id' => $acceptedLowonganId,
                    'cover_letter' => 'Saya bersemangat untuk bergabung dan berkontribusi di perusahaan ini.',
                    'nama_lengkap' => 'Anjar Wicaksono',
                    'email' => 'sampbernad@gmail.com',
                    'nisn' => '0071234567',
                    'jurusan_pilihan' => 'RPL',
                    'status' => 'diterima',
                ]);

                LamaranStatusHistory::create([
                    'lamaran_id' => $lamaran->id,
                    'status_from' => null,
                    'status_to' => 'pending',
                    'action_by' => null,
                ]);

                $admin = User::where('role', 'admin')->first();
                LamaranStatusHistory::create([
                    'lamaran_id' => $lamaran->id,
                    'status_from' => 'pending',
                    'status_to' => 'diterima',
                    'action_by' => $admin?->id,
                    'catatan' => 'Profil cocok, kemampuan teknis baik.',
                ]);
            }
        }
    }
}
