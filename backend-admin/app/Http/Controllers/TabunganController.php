<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\User;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    public function adminIndex()
    {
        $siswa = User::where('role', 'siswa')
            ->withSum(['tabungans as total_setor' => fn ($q) => $q->where('type', 'setor')], 'amount')
            ->withSum(['tabungans as total_tarik' => fn ($q) => $q->where('type', 'tarik')], 'amount')
            ->orderBy('name')
            ->get();

        $totalSaldo = 0;
        foreach ($siswa as $s) {
            $totalSaldo += ($s->total_setor ?? 0) - ($s->total_tarik ?? 0);
        }

        return view('tabungan.index', [
            'siswa' => $siswa,
            'totalSaldo' => $totalSaldo,
        ]);
    }

    public function adminShow(int $userId)
    {
        $user = User::findOrFail($userId);
        if ($user->role !== 'siswa') {
            abort(404);
        }

        $tabungans = Tabungan::where('user_id', $userId)
            ->with('inputter:id,name,username')
            ->latest()
            ->get();

        return view('tabungan.show', [
            'siswa' => $user,
            'saldo' => self::saldoFor($userId),
            'tabungans' => $tabungans,
        ]);
    }

    public function adminStore(Request $request)
    {
        $adminId = $request->user()->id;

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:setor,tarik',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($data['user_id']);
        if ($user->role !== 'siswa') {
            return back()->with('error', 'Tabungan hanya untuk akun siswa.');
        }

        $saldoSaatIni = self::saldoFor($user->id);
        if ($data['type'] === 'tarik' && $data['amount'] > $saldoSaatIni) {
            return back()->with('error', 'Saldo tidak cukup untuk penarikan.');
        }

        Tabungan::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'input_by' => $adminId,
        ]);

        return back()->with('success', 'Transaksi tabungan berhasil ditambahkan.');
    }

    private static function saldoFor(int $userId): int
    {
        return (int) Tabungan::where('user_id', $userId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'setor' THEN amount ELSE -amount END), 0) as saldo")
            ->value('saldo');
    }
}
