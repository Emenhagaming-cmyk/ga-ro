<?php

namespace App\Http\Controllers;

use App\Models\CareerMessage;
use Illuminate\Http\Request;

class CareerMessageController extends Controller
{
    /**
     * Percakapan siswa dengan BKK/admin. Balasan admin yang belum dibaca
     * ditandai terbaca saat percakapan dibuka.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $messages = CareerMessage::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        CareerMessage::where('user_id', $user->id)
            ->where('sender', CareerMessage::ADMIN)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:1|max:2000',
        ]);

        $message = CareerMessage::create([
            'user_id' => $request->user()->id,
            'sender' => CareerMessage::SISWA,
            'body' => $validated['body'],
        ]);

        return response()->json($message, 201);
    }
}
