<?php

namespace App\Http\Controllers;

use App\Models\KoperasiOrder;
use App\Models\User;
use Illuminate\Http\Request;

class KoperasiController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Hanya siswa yang bisa berbelanja.'], 422);
        }

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.title' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'metode' => 'nullable|in:qris,transfer',
        ]);

        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $order = KoperasiOrder::create([
            'user_id' => $user->id,
            'items' => $data['items'],
            'total' => $total,
            'metode' => $data['metode'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'order' => $order,
        ], 201);
    }

    public function myOrders(Request $request)
    {
        $user = $request->user();
        $orders = KoperasiOrder::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'items', 'total', 'metode', 'status', 'created_at']);

        return response()->json(['orders' => $orders]);
    }
}
