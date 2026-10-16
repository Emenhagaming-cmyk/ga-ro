<?php

namespace Tests\Feature;

use App\Models\Lowongan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LowonganControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('jurusan');
            $table->enum('type', ['Magang', 'Kerja', 'BKK']);
            $table->string('work_arrangement')->nullable();
            $table->text('description');
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('lowongans');

        parent::tearDown();
    }

    private function makeLowongan(array $overrides = []): Lowongan
    {
        return Lowongan::create(array_merge([
            'title' => 'Frontend Developer',
            'company' => 'Tech Corp',
            'location' => 'Surabaya',
            'jurusan' => 'RPL',
            'type' => 'Kerja',
            'work_arrangement' => 'onsite',
            'description' => 'Deskripsi lowongan',
            'is_active' => true,
        ], $overrides));
    }

    public function test_index_mengembalikan_paginasi()
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->makeLowongan(['title' => 'Job ' . $i]);
        }

        $response = $this->getJson('/lowongan');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['next', 'prev'],
            ]);

        $response->assertJsonPath('meta.per_page', 12)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonPath('meta.last_page', 2);

        $this->assertCount(12, $response->json('data'));
    }

    public function test_filter_search()
    {
        $this->makeLowongan(['title' => 'Frontend Developer']);
        $this->makeLowongan(['title' => 'Backend Developer']);

        $response = $this->getJson('/lowongan?search=Frontend');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Frontend Developer', $response->json('data.0.title'));
    }

    public function test_filter_type()
    {
        $this->makeLowongan(['title' => 'Magang 1', 'type' => 'Magang']);
        $this->makeLowongan(['title' => 'Kerja 1', 'type' => 'Kerja']);

        $response = $this->getJson('/lowongan?type=Magang');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Magang 1', $response->json('data.0.title'));
    }

    public function test_filter_jurusan()
    {
        $this->makeLowongan(['title' => 'RPL Job', 'jurusan' => 'RPL']);
        $this->makeLowongan(['title' => 'TKJ Job', 'jurusan' => 'TKJ']);

        $response = $this->getJson('/lowongan?jurusan=TJK');
        // Bug: filter jurusan TKJ harusnya TKJ
        $response = $this->getJson('/lowongan?jurusan=TKJ');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('TKJ Job', $response->json('data.0.title'));
    }

    public function test_filter_work_arrangement()
    {
        $this->makeLowongan(['title' => 'Remote Job', 'work_arrangement' => 'remote']);
        $this->makeLowongan(['title' => 'Onsite Job', 'work_arrangement' => 'onsite']);

        $response = $this->getJson('/lowongan?work_arrangement=remote');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Remote Job', $response->json('data.0.title'));
    }

    public function test_filter_lokasi()
    {
        $this->makeLowongan(['title' => 'Surabaya Job', 'location' => 'Surabaya']);
        $this->makeLowongan(['title' => 'Malang Job', 'location' => 'Malang']);

        $response = $this->getJson('/lowongan?location=Malang');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Malang Job', $response->json('data.0.title'));
    }

    public function test_sort_by_company()
    {
        $this->makeLowongan(['title' => 'B Job', 'company' => 'B Company']);
        $this->makeLowongan(['title' => 'A Job', 'company' => 'A Company']);

        $response = $this->getJson('/lowongan?sort=company');

        $response->assertOk();
        $this->assertEquals('A Company', $response->json('data.0.company'));
    }

    public function test_count_lowongan_aktif()
    {
        $this->makeLowongan(['title' => 'Active 1']);
        $this->makeLowongan(['title' => 'Active 2']);
        $this->makeLowongan(['title' => 'Inactive', 'is_active' => false]);

        $response = $this->getJson('/lowongan/count');

        $response->assertOk()
            ->assertJsonPath('total', 2);
    }

    public function test_show_lowongan()
    {
        $lowongan = $this->makeLowongan();

        $response = $this->getJson('/lowongan/' . $lowongan->id);

        $response->assertOk()
            ->assertJsonPath('title', 'Frontend Developer')
            ->assertJsonPath('work_arrangement', 'onsite');
    }
}
