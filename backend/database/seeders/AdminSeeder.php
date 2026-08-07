<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smkbahrululum.sch.id'],
            [
                'name' => 'Admin SMK Bahrul Ulum',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'siswa@demo.com'],
            [
                'name' => 'Siswa Demo',
                'username' => 'siswa',
                'password' => bcrypt('siswa123'),
                'role' => 'siswa',
            ]
        );
    }
}
