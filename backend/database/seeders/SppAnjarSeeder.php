<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SppBill;
use App\Models\SppPayment;

class SppAnjarSeeder extends Seeder
{
    /**
     * Tagihan SPP dummy untuk Anjar Wicaksono (id 4) — uji alur bayar dari
     * saldo tabungan. Idempoten: bill di-upsert per periode, pembayaran lunas
     * hanya dibuat kalau belum ada (aman dijalankan berulang).
     */
    public function run(): void
    {
        $user = User::where('email', 'sampbernad@gmail.com')->firstOrFail();
        $nominal = 350000;

        // [periode, jatuh_tempo, apakah sudah dibayar penuh]
        $tagihan = [
            ['2026-06', '2026-06-10', true],
            ['2026-07', '2026-07-10', true],
            ['2026-08', '2026-08-10', false], // parsial 150rb → sisa 200rb
            ['2026-09', '2026-09-10', false],
            ['2026-10', '2026-10-10', false],
        ];

        foreach ($tagihan as [$periode, $jatuhTempo, $lunas]) {
            $bill = SppBill::updateOrCreate(
                ['user_id' => $user->id, 'periode' => $periode],
                [
                    'nominal' => $nominal,
                    'status' => 'belum',
                    'jatuh_tempo' => $jatuhTempo,
                ]
            );

            if ($periode === '2026-08') {
                // Parsial: tunai 150rb oleh kasir → sisa tagihan 200rb
                SppPayment::firstOrCreate(
                    ['bill_id' => $bill->id, 'metode' => 'tunai', 'amount' => 150000],
                    ['paid_at' => $jatuhTempo, 'input_by' => $user->id]
                );
                continue;
            }

            if ($lunas) {
                SppPayment::firstOrCreate(
                    ['bill_id' => $bill->id, 'metode' => 'tunai', 'amount' => $nominal],
                    ['paid_at' => $jatuhTempo, 'input_by' => $user->id]
                );
                $bill->update(['status' => 'lunas']);
            }
        }
    }
}
