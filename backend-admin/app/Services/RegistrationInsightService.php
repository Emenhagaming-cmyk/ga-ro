<?php

namespace App\Services;

class RegistrationInsightService
{
    public function generateSummary(array $stats): string
    {
        return $this->buildFallbackSummary($stats);
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