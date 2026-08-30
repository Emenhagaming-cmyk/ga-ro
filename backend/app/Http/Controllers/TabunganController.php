<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\User;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tabungans = Tabungan::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'saldo' => self::saldoFor($user->id),
            'transaksi' => $tabungans->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => $t->amount,
                'description' => $t->description,
                'created_at' => $t->created_at,
            ]),
        ]);
    }

    // ponytail: saldo via 1 aggregate query, bukan load semua transaksi ke PHP
    // lalu reduce (over-fetch). Berhenti jika index datanya membesar.
    private static function saldoFor(int $userId): int
    {
        return (int) Tabungan::where('user_id', $userId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'setor' THEN amount ELSE -amount END), 0) as saldo")
            ->value('saldo');
    }

    public function store(Request $request)
    {
        $isAdmin = $request->user()->role === 'admin';

        $data = $request->validate([
            'type' => 'required|in:setor,tarik',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'prohibited',
        ]);

        // IDOR fix: user non-admin tidak boleh menentukan user_id (selalu diri sendiri)
        $user = $isAdmin ? $data['user_id'] : $request->user()->id;
        $targetUser = User::findOrFail($user);

        if ($targetUser->role !== 'siswa') {
            return response()->json(['message' => 'Tabungan hanya untuk akun siswa.'], 422);
        }

        $saldoSaatIni = self::saldoFor($user);
        $saldoBaru = $saldoSaatIni + ($data['type'] === 'setor' ? $data['amount'] : -$data['amount']);

        if ($data['type'] === 'tarik' && $data['amount'] > $saldoSaatIni) {
            return response()->json(['message' => 'Saldo tidak cukup untuk penarikan.'], 422);
        }

        $tabungan = Tabungan::create([
            'user_id' => $user,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'input_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => $data['type'] === 'setor' ? 'Setoran berhasil.' : 'Penarikan berhasil.',
            'tabungan' => $tabungan,
            'saldo' => $saldoBaru,
        ], 201);
    }

    public function adminShow(int $userId)
    {
        $user = User::findOrFail($userId);
        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Tabungan hanya untuk akun siswa.'], 422);
        }

        $tabungans = Tabungan::where('user_id', $userId)
            ->with('inputter:id,name,username')
            ->latest()
            ->get();

        return response()->json([
            'siswa' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'saldo' => self::saldoFor($userId),
            'transaksi' => $tabungans->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => $t->amount,
                'description' => $t->description,
                'created_at' => $t->created_at,
                'inputter' => $t->inputter ? [
                    'name' => $t->inputter->name,
                    'username' => $t->inputter->username,
                ] : null,
            ]),
        ]);
    }

    public function adminIndex()
    {
        $siswa = User::where('role', 'siswa')
            ->withSum(['tabungans as total_setor' => fn ($q) => $q->where('type', 'setor')], 'amount')
            ->withSum(['tabungans as total_tarik' => fn ($q) => $q->where('type', 'tarik')], 'amount')
            ->orderBy('name')
            ->get();

        return response()->json($siswa->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'username' => $u->username,
            'saldo' => ($u->total_setor ?? 0) - ($u->total_tarik ?? 0),
        ]));
    }
}