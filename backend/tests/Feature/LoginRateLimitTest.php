<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    private const PESAN_THROTTLE = 'Terlalu banyak percobaan login. Coba lagi sebentar lagi.';

    protected function setUp(): void
    {
        parent::setUp();

        // Cukup tabel users (query login menyeleksi username/email). Session uji = array.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('pendaftar');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    private function attempt(string $username, array $headers = [])
    {
        return $this->withHeaders($headers)->post('/login', [
            'username' => $username,
            'password' => 'kata-sandi-salah',
        ]);
    }

    public function test_percobaan_keenam_dari_ip_sama_diblokir(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->attempt('penyerang')
                ->assertRedirect()
                ->assertSessionHasErrors('username');
        }

        $this->attempt('penyerang')
            ->assertRedirect()
            ->assertSessionHasErrors(['username' => self::PESAN_THROTTLE]);
    }

    public function test_limit_per_akun_tetap_berlaku_walau_ip_berbeda(): void
    {
        // 10 percobaan akun sama dari IP berbeda-beda (spoof XFF, trustProxies '*').
        for ($i = 0; $i < 10; $i++) {
            $this->attempt('korban', ['X-Forwarded-For' => '10.0.0.'.$i])
                ->assertRedirect();
        }

        // Ke-11 dari IP baru tetap kena limit per-akun.
        $this->attempt('korban', ['X-Forwarded-For' => '10.0.0.200'])
            ->assertRedirect()
            ->assertSessionHasErrors(['username' => self::PESAN_THROTTLE]);
    }

    public function test_username_berganti_dari_ip_sama_tetap_kena_limit_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->attempt('akun'.$i, ['X-Forwarded-For' => '10.9.9.9'])
                ->assertRedirect();
        }

        $this->attempt('akun-lain', ['X-Forwarded-For' => '10.9.9.9'])
            ->assertRedirect()
            ->assertSessionHasErrors(['username' => self::PESAN_THROTTLE]);
    }
}
