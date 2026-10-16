<?php

namespace Database\Seeders;

use App\Models\KoperasiProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KoperasiProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Seragam Putih', 'category' => 'Seragam', 'price' => 75000, 'stock' => 48, 'emoji' => '👕', 'image' => '/produk/pppp.jpg', 'description' => 'Kaos seragam putih lengan pendek bahan katun premium. Bordir logo sekolah di dada kiri, tersedia S-XXL.'],
            ['name' => 'Rok/Celana Biru Tua', 'category' => 'Seragam', 'price' => 85000, 'stock' => 42, 'emoji' => '👖', 'image' => '/produk/bajuPraktek.jpeg', 'description' => 'Rok plisket putri dan celana panjang putra warna navy, bahan polyester wol premium.'],
            ['name' => 'Jaket Almamater', 'category' => 'Seragam', 'price' => 185000, 'stock' => 15, 'emoji' => '🧥', 'image' => '/produk/bis.jpg', 'description' => 'Jaket bomber almamater bordir logo sekolah, doublé fleece hangat. Warna hitam/navy.'],
            ['name' => 'Buku Tulis Sekolah', 'category' => 'Alat Tulis', 'price' => 8000, 'stock' => 120, 'emoji' => '📓', 'image' => '/produk/buku.jpg', 'description' => 'Buku tulis 38 lembar, kertas HVS 70gsm, cover logo sekolah.'],
            ['name' => 'Pensil 2B & Penghapus', 'category' => 'Alat Tulis', 'price' => 5000, 'stock' => 200, 'emoji' => '✏️', 'image' => '/produk/pensil.jpg', 'description' => 'Set 3 pensil kayu 2B SNI + 1 penghapus karet vinyl non-debu.'],
            ['name' => 'Pulpen Gel Hitam 0.5mm', 'category' => 'Alat Tulis', 'price' => 3000, 'stock' => 300, 'emoji' => '🖊️', 'image' => '/produk/prak.jpeg', 'description' => 'Pulpen gel tipe jarum 0.5mm, tinta quick-dry, grip karet ergonomis.'],
            ['name' => 'Sabuk Sekolah Hitam', 'category' => 'Aksesoris', 'price' => 25000, 'stock' => 60, 'emoji' => '🪢', 'image' => '/produk/sabuk.jpg', 'description' => 'Sabuk sekolah lebar 3cm, bahan PU premium, buckle anti karat.'],
            ['name' => 'Dasi Sekolah Merah', 'category' => 'Aksesoris', 'price' => 20000, 'stock' => 55, 'emoji' => '👔', 'image' => '/produk/dasi.jpeg', 'description' => 'Dasi polyester merah marun dengan motif logo sekolah, panjang 140cm.'],
            ['name' => 'Topi Sekolah', 'category' => 'Aksesoris', 'price' => 35000, 'stock' => 40, 'emoji' => '🧢', 'image' => '/produk/topi.jpg', 'description' => 'Topi baseball 6 panel, bordir logo sekolah, strap adjustable.'],
            ['name' => 'Kaos Kaki Putih Sekolah', 'category' => 'Aksesoris', 'price' => 12000, 'stock' => 90, 'emoji' => '🧦', 'image' => '/produk/kaoskaki.jpg', 'description' => 'Kaos kaki putih cotton combed + spandex, tinggi mata kaki 15cm.'],
            ['name' => 'Hasduk Pramuka', 'category' => 'Aksesoris', 'price' => 18000, 'stock' => 50, 'emoji' => '🔺', 'image' => '/produk/hasduk.jpg', 'description' => 'Set hasduk pramuka + ring, bahan satin, ukuran standar siswa.'],
            ['name' => 'Air Mineral 600ml', 'category' => 'Minuman', 'price' => 4000, 'stock' => 100, 'emoji' => '💧', 'image' => '/produk/air.jpg', 'description' => 'Air mineral botol PET 600ml BPA-free, segar dan higienis.'],
            ['name' => 'Teh Botol 350ml', 'category' => 'Minuman', 'price' => 5000, 'stock' => 80, 'emoji' => '🍵', 'image' => '/produk/teh.jpg', 'description' => 'Teh manis botol 350ml rasa jasmine, gula aren asli.'],
            ['name' => 'Roti Sekolah', 'category' => 'Makanan', 'price' => 6000, 'stock' => 60, 'emoji' => '🍞', 'image' => '/produk/roti.jpeg', 'description' => 'Roti isi praktis untuk bekal istirahat, tersedia aneka rasa.'],
        ];

        // DatabaseSeeder pakai WithoutModelEvents → hook `saving` model tidak jalan,
        // jadi slug diisi manual (kalau kosong, MySQL strict menolak: slug tanpa default).
        foreach ($products as $product) {
            KoperasiProduct::updateOrCreate(
                ['name' => $product['name']],
                $product + ['slug' => Str::slug($product['name']), 'is_active' => true]
            );
        }

        $this->command?->info('KoperasiProductSeeder: ' . count($products) . ' produk siap.');
    }
}
