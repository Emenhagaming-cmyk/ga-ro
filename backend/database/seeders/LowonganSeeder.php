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
            ['title' => 'Database Programmer', 'company' => 'Fascinate Team', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'description' => 'Magang backend development, bekerja dengan MySQL dan Laravel untuk mengembangkan sistem manajemen data perusahaan.', 'deadline' => '2026-09-30'],
            ['title' => 'Intern Programmer', 'company' => 'Cute Cute Studio', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Magang', 'description' => 'Programmer magang untuk proyek web development, belajar framework modern dan deployment.', 'deadline' => '2026-09-15'],
            ['title' => 'Multimedia Creator', 'company' => 'Kreatif Studio', 'location' => 'Sidoarjo', 'jurusan' => 'RPL', 'type' => 'Magang', 'description' => 'Magang pembuatan konten visual, video editing, dan desain grafis untuk media sosial perusahaan.', 'deadline' => '2026-09-25'],
            ['title' => 'Mobile App Developer', 'company' => 'Inovasi Digital', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Magang', 'description' => 'Magang pengembangan aplikasi mobile Android/iOS menggunakan Flutter, belajar UI/UX design dan API integration.', 'deadline' => '2026-10-10'],
            ['title' => 'Frontend Developer', 'company' => 'Velocity Corp', 'location' => 'Gresik', 'jurusan' => 'RPL', 'type' => 'Kerja', 'description' => 'Develop tampilan website menggunakan Vue.js dan Tailwind CSS, kolaborasi dengan tim desain UI/UX.', 'deadline' => '2026-08-30'],
            ['title' => 'Junior Web Developer', 'company' => 'Digital Nusantara', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'description' => 'Web developer junior untuk proyek e-commerce, React.js dan Node.js, fresh graduate dipersilakan.', 'deadline' => '2026-08-25'],
            ['title' => 'Backend Developer', 'company' => 'Arkana Solusi', 'location' => 'Surabaya', 'jurusan' => 'RPL', 'type' => 'Kerja', 'description' => 'Backend developer Laravel/PHP untuk sistem ERP perusahaan manufaktur, pengalaman REST API lebih disukai.', 'deadline' => '2026-09-20'],
            ['title' => 'Fullstack Developer', 'company' => 'Nexa Tech', 'location' => 'Malang', 'jurusan' => 'RPL', 'type' => 'Kerja', 'description' => 'Fullstack developer Next.js + PostgreSQL, mengerjakan fitur marketplace dari frontend sampai backend.', 'deadline' => '2026-09-18'],
            ['title' => 'Helpdesk Support', 'company' => 'TechZone ID', 'location' => 'Surabaya', 'jurusan' => 'TKJ', 'type' => 'Magang', 'description' => 'Magang helpdesk support, melayani troubleshooting jaringan dan software internal perusahaan.', 'deadline' => '2026-09-20'],
            ['title' => 'CNC Operator', 'company' => 'PT Sinar Maju', 'location' => 'Gresik', 'jurusan' => 'TKJ', 'type' => 'Magang', 'description' => 'Operator mesin CNC untuk produksi komponen presisi, dilatih penggunaan mesin industri modern.', 'deadline' => '2026-09-12'],
            ['title' => 'IT Support Intern', 'company' => 'Prima Komputama', 'location' => 'Surabaya', 'jurusan' => 'TKJ', 'type' => 'Magang', 'description' => 'Magang IT support: instalasi OS, perawatan hardware, dan dokumentasi inventaris TI perusahaan.', 'deadline' => '2026-10-01'],
            ['title' => 'Network Admin Intern', 'company' => 'Inti Jaringan', 'location' => 'Sidoarjo', 'jurusan' => 'TKJ', 'type' => 'Magang', 'description' => 'Magang admin jaringan, konfigurasi MikroTik/UniFi, monitoring bandwidth, dan eliminasi gangguan jaringan.', 'deadline' => '2026-10-08'],
            ['title' => 'Network Technician', 'company' => 'CyberNet Solutions', 'location' => 'Surabaya', 'jurusan' => 'TKJ', 'type' => 'Kerja', 'description' => 'Teknisi jaringan untuk instalasi, konfigurasi, dan maintenance jaringan LAN/WAN di kantor pusat.', 'deadline' => '2026-08-28'],
            ['title' => 'System Administrator', 'company' => 'Bintang Teknologi', 'location' => 'Gresik', 'jurusan' => 'TKJ', 'type' => 'Kerja', 'description' => 'Admin sistem untuk manage server Linux, backup data, dan monitoring uptime layanan cloud perusahaan.', 'deadline' => '2026-09-22'],
            ['title' => 'CCTV & Security System', 'company' => 'Paramount Security', 'location' => 'Malang', 'jurusan' => 'TKJ', 'type' => 'Kerja', 'description' => 'Teknisi pemasangan dan pemeliharaan sistem CCTV, access control, dan alarm untuk proyek gedung perkantoran.', 'deadline' => '2026-09-28'],
            ['title' => 'Teknisi Komputer', 'company' => 'Makmur Jaya', 'location' => 'Surabaya', 'jurusan' => 'TKJ', 'type' => 'BKK', 'description' => 'Teknisi komputer untuk service hardware, instalasi software, dan perakitan di toko elektronik.', 'deadline' => '2026-09-14'],
            ['title' => 'Staff Admin Jaringan', 'company' => 'Lestari Net', 'location' => 'Sidoarjo', 'jurusan' => 'TKJ', 'type' => 'BKK', 'description' => 'Staff admin jaringan untuk monitoring, dokumentasi, dan pelaporan gangguan jaringan pelanggan.', 'deadline' => '2026-09-16'],
            ['title' => 'Accounting Intern', 'company' => 'Harapan Bangsa', 'location' => 'Surabaya', 'jurusan' => 'AKL', 'type' => 'Magang', 'description' => 'Magang bagian accounting, membantu pencatatan jurnal, rekonsiliasi bank, dan persiapan laporan keuangan.', 'deadline' => '2026-10-03'],
            ['title' => 'Admin Perkantoran', 'company' => 'PT Sinar Maju', 'location' => 'Surabaya', 'jurusan' => 'AKL', 'type' => 'BKK', 'description' => 'Admin perkantoran untuk pengelolaan dokumen, surat-menyurat, dan koordinasi internal kantor.', 'deadline' => '2026-09-05'],
            ['title' => 'Akuntansi Staff', 'company' => 'Mitra Jaya', 'location' => 'Malang', 'jurusan' => 'AKL', 'type' => 'BKK', 'description' => 'Staff akuntansi untuk pembukuan, pencatatan transaksi, dan laporan keuangan bulanan perusahaan.', 'deadline' => '2026-09-10'],
            ['title' => 'Staff Kasir', 'company' => 'Toko Berkah', 'location' => 'Sidoarjo', 'jurusan' => 'AKL', 'type' => 'Kerja', 'description' => 'Staff kasir untuk pengelolaan transaksi harian, pencatatan kas, dan pelaporan penjualan.', 'deadline' => '2026-09-08'],
            ['title' => 'Admin Gudang', 'company' => 'Sentosa Logistik', 'location' => 'Gresik', 'jurusan' => 'AKL', 'type' => 'Kerja', 'description' => 'Admin gudang untuk pencatatan stok barang, input data inventory, dan koordinasi pengiriman.', 'deadline' => '2026-09-15'],
            ['title' => 'Staff Administrasi', 'company' => 'Berkah Motor', 'location' => 'Surabaya', 'jurusan' => 'AKL', 'type' => 'Kerja', 'description' => 'Staff administrasi untuk pengelolaan data penjualan, faktur, dan laporan keuangan dealer motor.', 'deadline' => '2026-10-02'],
            ['title' => 'Finance Admin', 'company' => 'Cahaya Kencana', 'location' => 'Malang', 'jurusan' => 'AKL', 'type' => 'Magang', 'description' => 'Magang bagian keuangan, membantu proses invoice, pencatatan piutang, dan rekonsiliasi data.', 'deadline' => '2026-10-12'],
            ['title' => 'Operator Komputer', 'company' => 'Sumber Rejeki', 'location' => 'Gresik', 'jurusan' => 'AKL', 'type' => 'BKK', 'description' => 'Operator komputer untuk input data, pengolahan spreadsheet, dan pencetakan dokumen kantor.', 'deadline' => '2026-09-18'],
        ];

        foreach ($lowongan as $item) {
            Lowongan::create($item);
        }
    }
}
