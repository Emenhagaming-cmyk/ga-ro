<?php

namespace Database\Seeders;

use App\Models\Lowongan;
use Illuminate\Database\Seeder;

class LowonganSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Lowongan::query()->delete();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $lowongan = [
            ['title' => 'Database Programmer', 'company' => 'Fascinate Team', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang backend development, bekerja dengan MySQL dan Laravel untuk mengembangkan sistem manajemen data perusahaan.', 'deadline' => '2026-09-30'],
            ['title' => 'Intern Programmer', 'company' => 'Cute Cute Studio', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'hybrid', 'description' => 'Programmer magang untuk proyek web development, belajar framework modern dan deployment.', 'deadline' => '2026-09-15'],
            ['title' => 'Multimedia Creator', 'company' => 'Kreatif Studio', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang pembuatan konten visual, video editing, dan desain grafis untuk media sosial perusahaan.', 'deadline' => '2026-09-25'],
            ['title' => 'Mobile App Developer', 'company' => 'Inovasi Digital', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'remote', 'description' => 'Magang pengembangan aplikasi mobile Android/iOS menggunakan Flutter, belajar UI/UX design dan API integration.', 'deadline' => '2026-10-10'],
            ['title' => 'Frontend Developer', 'company' => 'Velocity Corp', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'hybrid', 'description' => 'Develop tampilan website menggunakan Vue.js dan Tailwind CSS, kolaborasi dengan tim desain UI/UX.', 'deadline' => '2026-08-30'],
            ['title' => 'Junior Web Developer', 'company' => 'Digital Nusantara', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Web developer junior untuk proyek e-commerce, React.js dan Node.js, fresh graduate dipersilakan.', 'deadline' => '2026-08-25'],
            ['title' => 'Backend Developer', 'company' => 'Arkana Solusi', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Backend developer Laravel/PHP untuk sistem ERP perusahaan manufaktur, pengalaman REST API lebih disukai.', 'deadline' => '2026-09-20'],
            ['title' => 'Fullstack Developer', 'company' => 'Nexa Tech', 'location' => 'Malang', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'remote', 'description' => 'Fullstack developer Next.js + PostgreSQL, mengerjakan fitur marketplace dari frontend sampai backend.', 'deadline' => '2026-09-18'],
            ['title' => 'Helpdesk Support', 'company' => 'TechZone ID', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang helpdesk support, melayani troubleshooting jaringan dan software internal perusahaan.', 'deadline' => '2026-09-20'],
            ['title' => 'QA Tester', 'company' => 'PT Sinar Maju', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang quality assurance, menyusun skenario uji manual dan otomasi untuk aplikasi internal perusahaan.', 'deadline' => '2026-09-12'],
            ['title' => 'IT Support Intern', 'company' => 'Prima Komputama', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang IT support: instalasi OS, perawatan hardware, dan dokumentasi inventaris TI perusahaan.', 'deadline' => '2026-10-01'],
            ['title' => 'IT Infrastructure Intern', 'company' => 'Inti Jaringan', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'hybrid', 'description' => 'Magang infrastruktur TI, konfigurasi jaringan kantor, monitoring server, dan penanganan gangguan koneksi.', 'deadline' => '2026-10-08'],
            ['title' => 'Junior DevOps Engineer', 'company' => 'CyberNet Solutions', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Deployment aplikasi dengan Docker, penyiapan CI/CD, dan monitoring server produksi.', 'deadline' => '2026-08-28'],
            ['title' => 'System Administrator', 'company' => 'Bintang Teknologi', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Admin sistem untuk manage server Linux, backup data, dan monitoring uptime layanan cloud perusahaan.', 'deadline' => '2026-09-22'],
            ['title' => 'IT Security Analyst', 'company' => 'Paramount Security', 'location' => 'Malang', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Analis keamanan TI untuk monitoring ancaman, audit akses, dan penerapan kebijakan keamanan jaringan.', 'deadline' => '2026-09-28'],
            ['title' => 'Teknisi Komputer', 'company' => 'Makmur Jaya', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'BKK', 'work_arrangement' => 'onsite', 'description' => 'Teknisi komputer untuk service hardware, instalasi software, dan perakitan di toko elektronik.', 'deadline' => '2026-09-14'],
            ['title' => 'Web Admin', 'company' => 'Lestari Net', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'BKK', 'work_arrangement' => 'onsite', 'description' => 'Pengelolaan konten dan data website klien, input produk, serta pemantauan performa situs.', 'deadline' => '2026-09-16'],
            ['title' => 'Data Entry Intern', 'company' => 'Harapan Bangsa', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'onsite', 'description' => 'Magang pengolahan data, input transaksi ke sistem, dan penyusunan rekap laporan dengan spreadsheet.', 'deadline' => '2026-10-03'],
            ['title' => 'Admin Data Digital', 'company' => 'PT Sinar Maju', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'BKK', 'work_arrangement' => 'onsite', 'description' => 'Pengelolaan arsip digital, input data ke sistem, dan dukungan administrasi berbasis aplikasi.', 'deadline' => '2026-09-05'],
            ['title' => 'Junior Data Analyst', 'company' => 'Mitra Jaya', 'location' => 'Malang', 'jurusan' => 'RPL', 'type' => 'BKK', 'work_arrangement' => 'onsite', 'description' => 'Mengolah data penjualan, membuat query SQL sederhana, dan menyusun laporan bulanan.', 'deadline' => '2026-09-10'],
            ['title' => 'Kasir & Operator POS', 'company' => 'Toko Berkah', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Melayani transaksi dengan aplikasi POS, memastikan stok akurat, dan penanganan kendala perangkat kasir.', 'deadline' => '2026-09-08'],
            ['title' => 'Admin Inventory', 'company' => 'Sentosa Logistik', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Input data stok ke sistem gudang, pencocokan inventory, dan penyusunan laporan pengiriman.', 'deadline' => '2026-09-15'],
            ['title' => 'Staff Administrasi Digital', 'company' => 'Berkah Motor', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'work_arrangement' => 'onsite', 'description' => 'Pengelolaan data penjualan dan faktur melalui sistem, serta penyusunan laporan dealer.', 'deadline' => '2026-10-02'],
            ['title' => 'Frontend Developer Intern', 'company' => 'Cahaya Kencana', 'location' => 'Malang', 'jurusan' => 'RPL', 'type' => 'Magang', 'work_arrangement' => 'hybrid', 'description' => 'Magang pengembangan antarmuka web dengan Vue.js dan integrasi REST API.', 'deadline' => '2026-10-12'],
            ['title' => 'Operator Data', 'company' => 'Sumber Rejeki', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'BKK', 'work_arrangement' => 'onsite', 'description' => 'Input data ke sistem, pengolahan spreadsheet, dan penyusunan dokumen digital kantor.', 'deadline' => '2026-09-18'],
        ];

        foreach ($lowongan as $item) {
            Lowongan::create($item);
        }
    }
}
