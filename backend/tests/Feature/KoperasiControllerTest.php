<?php

namespace Tests\Feature;

use App\Models\KoperasiProduct;
use App\Models\Tabungan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KoperasiControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Koperasi hanya buka Senin–Sabtu 07–15 WIB → uji selalu "jam buka".
        $this->travelTo(Carbon::create(2026, 8, 10, 9, 0, 0, 'Asia/Jakarta')); // Senin

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('pendaftar');
            $table->timestamps();
        });

        Schema::create('tabungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['setor', 'tarik']);
            $table->unsignedBigInteger('amount');
            $table->string('description')->nullable();
            $table->foreignId('input_by')->nullable();
            $table->timestamps();
        });

        Schema::create('koperasi_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('Lainnya');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->string('emoji')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('koperasi_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('kode')->nullable()->unique();
            $table->json('items');
            $table->unsignedBigInteger('total');
            $table->string('pickup_code', 8)->nullable();
            $table->string('metode')->nullable();
            $table->string('status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('koperasi_orders');
        Schema::dropIfExists('koperasi_products');
        Schema::dropIfExists('tabungans');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    private function makeUser(string $role, string $uname): User
    {
        return User::create([
            'name' => ucfirst($role),
            'username' => $uname,
            'email' => $uname . '@test.com',
            'password' => bcrypt('secret'),
            'role' => $role,
        ]);
    }

    private function makeProduct(int $price = 10000, int $stock = 5): KoperasiProduct
    {
        return KoperasiProduct::create([
            'name' => 'Produk Uji',
            'category' => 'Alat Tulis',
            'price' => $price,
            'stock' => $stock,
        ]);
    }

    private function topup(User $siswa, int $amount): void
    {
        Tabungan::create(['user_id' => $siswa->id, 'type' => 'setor', 'amount' => $amount, 'description' => 'Top Up']);
    }

    public function test_harga_diambil_dari_db_bukan_kiriman_client(): void
    {
        $siswa = $this->makeUser('siswa', 'siswa1');
        $product = $this->makeProduct(10000, 5);

        $this->actingAs($siswa)->postJson('/koperasi/checkout', [
            'items' => [['product_id' => $product->id, 'qty' => 2, 'price' => 1]],
        ])->assertStatus(201)
            ->assertJsonPath('order.total', 20000)
            ->assertJsonPath('order.status', 'pending');

        $this->assertNotNull(\App\Models\KoperasiOrder::first()->kode);
    }

    public function test_bayar_dari_saldo_mengurangi_stok_dan_menulis_ledger(): void
    {
        $siswa = $this->makeUser('siswa', 'siswa1');
        $product = $this->makeProduct(10000, 5);
        $this->topup($siswa, 50000);

        $kode = $this->actingAs($siswa)->postJson('/koperasi/checkout', [
            'items' => [['product_id' => $product->id, 'qty' => 2]],
        ])->json('order.kode');

        $this->actingAs($siswa)->postJson("/koperasi/orders/{$kode}/pay-tabungan")
            ->assertStatus(201)
            ->assertJsonPath('order.status', 'dibayar')
            ->assertJsonPath('saldo', 30000);

        $this->assertSame(3, $product->fresh()->stock);
        $last = Tabungan::latest('id')->first();
        $this->assertSame('tarik', $last->type);
        $this->assertSame(20000, $last->amount);
    }

    public function test_bayar_gagal_kalau_saldo_kurang_dan_tidak_mengurangi_stok(): void
    {
        $siswa = $this->makeUser('siswa', 'siswa1');
        $product = $this->makeProduct(10000, 5);
        $this->topup($siswa, 5000);

        $kode = $this->actingAs($siswa)->postJson('/koperasi/checkout', [
            'items' => [['product_id' => $product->id, 'qty' => 2]],
        ])->json('order.kode');

        $this->actingAs($siswa)->postJson("/koperasi/orders/{$kode}/pay-tabungan")
            ->assertStatus(422);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, Tabungan::where('user_id', $siswa->id)->where('type', 'tarik')->count());
    }

    public function test_stok_habis_saat_bayar_ditolak_tanpa_debit(): void
    {
        $siswa = $this->makeUser('siswa', 'siswa1');
        $product = $this->makeProduct(10000, 1);
        $this->topup($siswa, 50000);

        $kode = $this->actingAs($siswa)->postJson('/koperasi/checkout', [
            'items' => [['product_id' => $product->id, 'qty' => 1]],
        ])->json('order.kode');

        // Stok diambil orang lain sebelum siswa membayar.
        $product->update(['stock' => 0]);

        $this->actingAs($siswa)->postJson("/koperasi/orders/{$kode}/pay-tabungan")
            ->assertStatus(422);

        $this->assertSame(0, Tabungan::where('user_id', $siswa->id)->where('type', 'tarik')->count());
        $this->assertSame('pending', \App\Models\KoperasiOrder::where('kode', $kode)->first()->status);
    }

    public function test_admin_tidak_bisa_checkout(): void
    {
        $admin = $this->makeUser('admin', 'admin');
        $product = $this->makeProduct();

        $this->actingAs($admin)->postJson('/koperasi/checkout', [
            'items' => [['product_id' => $product->id, 'qty' => 1]],
        ])->assertStatus(403);
    }
}
