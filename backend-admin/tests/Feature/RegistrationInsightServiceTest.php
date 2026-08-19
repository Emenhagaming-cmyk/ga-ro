<?php

namespace Tests\Feature;

use App\Services\RegistrationInsightService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationInsightServiceTest extends TestCase
{
    public function test_it_uses_ninerouter_to_generate_ai_summary(): void
    {
        putenv('NINEROUTER_URL=http://localhost:20128');
        putenv('NINEROUTER_MODEL=openai/gpt-5');
        putenv('NINEROUTER_KEY=demo-key');

        Http::fake([
            'http://localhost:20128/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Ringkasan AI berhasil dibuat']]
                ]
            ], 200),
        ]);

        $service = new RegistrationInsightService();
        $summary = $service->generateSummary([
            'total' => 12,
            'baru' => 3,
            'diproses' => 2,
            'diterima' => 5,
            'ditolak' => 2,
            'jurusan' => ['RPL' => 7, 'TKJ' => 3, 'AKL' => 2],
        ]);

        $this->assertSame('Ringkasan AI berhasil dibuat', $summary);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://localhost:20128/v1/chat/completions'
                && $request['model'] === 'openai/gpt-5'
                && str_contains($request['messages'][1]['content'], 'Total pendaftar');
        });
    }
}
