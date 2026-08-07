<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RegistrationInsightService
{
    public function generateSummary(array $stats): string
    {
        $url = env('NINEROUTER_URL');
        $model = env('NINEROUTER_MODEL', 'openai/gpt-5');
        $apiKey = env('NINEROUTER_KEY');

        if (empty($url)) {
            return $this->buildFallbackSummary($stats);
        }

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah asisten admin sekolah. Berikan ringkasan singkat dan berguna tentang data pendaftaran siswa.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildPrompt($stats),
                ],
            ],
            'temperature' => 0.2,
        ];

        $response = Http::withToken($apiKey)
            ->timeout(20)
            ->post($url . '/v1/chat/completions', $payload);

        if ($response->failed()) {
            return $this->buildFallbackSummary($stats);
        }

        return trim(data_get($response->json(), 'choices.0.message.content') ?: $this->buildFallbackSummary($stats));
    }

    private function buildPrompt(array $stats): string
    {
        $jurusan = $stats['jurusan'] ?? [];
        $rpl = $jurusan['RPL'] ?? 0;
        $tkj = $jurusan['TKJ'] ?? 0;
        $akl = $jurusan['AKL'] ?? 0;

        return <<<TEXT
Berikut data pendaftaran sekolah:
- Total pendaftar: {$stats['total']}
- Baru: {$stats['baru']}
- Diproses: {$stats['diproses']}
- Diterima: {$stats['diterima']}
- Ditolak: {$stats['ditolak']}
- Jurusan: RPL {$rpl}, TKJ {$tkj}, AKL {$akl}

Buat ringkasan singkat 3-4 kalimat dalam Bahasa Indonesia yang membantu admin memahami kondisi pendaftaran saat ini.
TEXT;
    }

    private function buildFallbackSummary(array $stats): string
    {
        return "Saat ini ada {$stats['total']} pendaftar. Status {$stats['baru']} masih baru, {$stats['diproses']} sedang diproses, dan {$stats['diterima']} sudah diterima. Jurusan yang paling banyak diminati adalah " . $this->topJurusan($stats['jurusan']) . ".";
    }

    private function topJurusan(array $jurusan): string
    {
        if (empty($jurusan)) {
            return 'belum ada data';
        }

        $max = max($jurusan);
        $top = array_filter($jurusan, fn ($value) => $value === $max);
        $name = array_key_first($top);

        return $name ?? 'belum ada data';
    }
}
