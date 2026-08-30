<?php

namespace App\Http\Controllers;

use App\Models\SppBill;
use App\Models\SppPayment;
use App\Models\User;
use Illuminate\Http\Request;

class SppController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $bills = SppBill::with('payments')
            ->where('user_id', $user->id)
            ->orderBy('periode')
            ->get();

        return response()->json([
            'bills' => $bills->map(fn (SppBill $bill) => [
                'id' => $bill->id,
                'periode' => $bill->periode,
                'nominal' => $bill->nominal,
                'status' => $bill->status,
                'jatuh_tempo' => $bill->jatuh_tempo,
                'terbayar' => $bill->payments->sum('amount'),
                'sisa' => max($bill->nominal - $bill->payments->sum('amount'), 0),
                'payments' => $bill->payments->map(fn (SppPayment $p) => [
                    'id' => $p->id,
                    'metode' => $p->metode,
                    'amount' => $p->amount,
                    'paid_at' => $p->paid_at,
                ]),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bill_id' => 'required|exists:spp_bills,id',
            'metode' => 'required|in:tunai,transfer',
            'amount' => 'required|integer|min:1',
        ]);

        $bill = SppBill::findOrFail($data['bill_id']);

        if ($bill->user->role !== 'siswa') {
            return response()->json(['message' => 'Tagihan SPP hanya untuk akun siswa.'], 422);
        }

        $payment = SppPayment::create([
            'bill_id' => $bill->id,
            'metode' => $data['metode'],
            'amount' => $data['amount'],
            'paid_at' => now(),
            'input_by' => $request->user()->id,
        ]);

        $terbayar = $bill->payments()->sum('amount');
        if ($terbayar >= $bill->nominal) {
            $bill->update(['status' => 'lunas']);
        }

        return response()->json([
            'message' => 'Pembayaran SPP tercatat.',
            'payment' => $payment,
            'bill' => [
                'id' => $bill->id,
                'periode' => $bill->periode,
                'status' => $bill->status,
                'sisa' => max($bill->nominal - $terbayar, 0),
            ],
        ], 201);
    }

    public function adminIndex()
    {
        $siswa = User::where('role', 'siswa')
            ->with(['sppBills' => fn ($q) => $q->orderBy('periode')])
            ->orderBy('name')
            ->get();

        return response()->json($siswa->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
            'bills' => $u->sppBills->map(fn (SppBill $b) => [
                'id' => $b->id,
                'periode' => $b->periode,
                'nominal' => $b->nominal,
                'status' => $b->status,
            ]),
        ]));
    }
}
