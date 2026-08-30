<?php

namespace App\Console\Commands;

use App\Models\SppBill;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateSppBills extends Command
{
    protected $signature = 'spp:generate {--periode=} {--nominal=}';

    protected $description = 'Membuat tagihan SPP bulanan untuk semua siswa';

    public function handle(): int
    {
        $periode = $this->option('periode') ?? now()->format('Y-m');
        $nominal = (int) ($this->option('nominal') ?? 150000);

        if ($nominal < 1) {
            $this->error('Nominal harus lebih dari 0.');
            return self::FAILURE;
        }

        $created = 0;
        $skipped = 0;

        foreach (User::where('role', 'siswa')->cursor() as $siswa) {
            $bill = SppBill::firstOrCreate(
                ['user_id' => $siswa->id, 'periode' => $periode],
                ['nominal' => $nominal, 'status' => 'belum'],
            );

            $bill->wasRecentlyCreated ? $created++ : $skipped++;
        }

        $this->info("Periode {$periode}: {$created} tagihan dibuat, {$skipped} sudah ada.");

        return self::SUCCESS;
    }
}
