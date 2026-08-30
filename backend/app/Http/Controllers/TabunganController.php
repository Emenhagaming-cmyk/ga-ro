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

        $saldo = $tabungans->reduce(function ($carry, $t) {
            return $carry + ($t->type === 'setor' ? $t->amount : -$t->amount);
        }, 0);

        return response()->json([
            'saldo' => $saldo,
            'transaksi' => $tabungans->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => $t->amount,
                'description' => $t->description,
                'created_at' => $t->created_at,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $isAdmin = $request->user()->role === 'admin';

        $data = $request->validate([
            'type' => 'required|in:setor,tarik',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'user_id' => $isAdmin ? 'required|exists:users,id' : 'nullable|exists:users,id',
        ]);

        $user = $data['user_id'] ?? $request->user()->id;
        $targetUser = User::findOrFail($user);

        if ($targetUser->role !== 'siswa') {
            return response()->json(['message' => 'Tabungan hanya untuk akun siswa.'], 422);
        }

        $saldoSaatIni = Tabungan::where('user_id', $user)->get()->reduce(function ($carry, $t) {
            return $carry + ($t->type === 'setor' ? $t->amount : -$t->amount);
        }, 0);

        if ($data['type'] === 'tarik' && $data['amount'] > $saldoSaatIni) {
            return response()->json(['message' => 'Saldo tidak cukup untuk penarikan.'], 422);
        }

        $tabungan = Tabungan::create([
            'user_id' => $user,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
        ]);

        $saldoBaru = $saldoSaatIni + ($data['type'] === 'setor' ? $data['amount'] : -$data['amount']);

        return response()->json([
            'message' => $data['type'] === 'setor' ? 'Setoran berhasil.' : 'Penarikan berhasil.',
            'tabungan' => $tabungan,
            'saldo' => $saldoBaru,
        ], 201);
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