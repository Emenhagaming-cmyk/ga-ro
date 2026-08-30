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
            return $request->expectsJson()
                ? response()->json(['message' => 'Tagihan SPP hanya untuk akun siswa.'], 422)
                : back()->withErrors(['bill_id' => 'Tagihan SPP hanya untuk akun siswa.']);
        }

        SppPayment::create([
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

        if (!$request->expectsJson()) {
            return back()->with('success', "Pembayaran SPP {$bill->periode} sebesar Rp" . number_format($data['amount'], 0, ',', '.') . " tercatat.");
        }

        return response()->json([
            'message' => 'Pembayaran SPP tercatat.',
            'bill' => [
                'id' => $bill->id,
                'periode' => $bill->periode,
                'status' => $bill->status,
                'sisa' => max($bill->nominal - $terbayar, 0),
            ],
        ], 201);
    }

    public function kasirIndex(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $bills = SppBill::with(['user', 'payments'])
            ->whereHas('user', fn ($u) => $u->where('role', 'siswa')
                ->when($q !== '', fn ($u) => $u->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")->orWhere('username', 'like', "%{$q}%");
                })))
            ->whereRaw('(SELECT COALESCE(SUM(spp_payments.amount),0) FROM spp_payments WHERE spp_payments.bill_id = spp_bills.id) < nominal')
            ->orderByDesc('periode')
            ->get();

        return view('spp.kasir', [
            'bills' => $bills,
            'q' => $q,
        ]);
    }

    public function rekapIndex()
    {
        $siswa = User::where('role', 'siswa')
            ->with(['sppBills' => fn ($q) => $q->with('payments')->orderBy('periode')])
            ->orderBy('name')
            ->get();

        return view('spp.rekap', ['siswa' => $siswa]);
    }

    public function ortuIndex(Request $request, User $user)
    {
        abort_unless($request->hasValidSignature(), 403, 'Link orang tua tidak valid atau kedaluwarsa.');

        abort_if($user->role !== 'siswa', 404);

        $bills = SppBill::with('payments')
            ->where('user_id', $user->id)
            ->orderBy('periode')
            ->get();

        return view('spp.ortu', [
            'siswa' => $user,
            'bills' => $bills,
        ]);
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
