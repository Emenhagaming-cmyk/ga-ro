<?php

namespace App\Http\Controllers;

use App\Models\KoperasiOrder;
use App\Models\User;
use Illuminate\Http\Request;

class KoperasiController extends Controller
{
    public function adminIndex()
    {
        $orders = KoperasiOrder::with('user:id,name,username,email')
            ->latest()
            ->get();

        $totalPendapatan = $orders->where('status', 'lunas')->sum('total');
        $totalPending = $orders->where('status', 'pending')->sum('total');

        return view('koperasi.index', [
            'orders' => $orders,
            'totalPendapatan' => $totalPendapatan,
            'totalPending' => $totalPending,
        ]);
    }

    public function adminShow(int $orderId)
    {
        $order = KoperasiOrder::with('user:id,name,username,email')->findOrFail($orderId);

        return view('koperasi.show', [
            'order' => $order,
        ]);
    }
}
