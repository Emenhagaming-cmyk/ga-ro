<?php

namespace App\Http\Controllers;

use App\Models\CareerMessage;
use App\Models\User;
use Illuminate\Http\Request;

class CareerMessageController extends Controller
{
    public function index()
    {
        // Urut dari percakapan terbaru. Satu siswa = satu thread.
        $userIds = CareerMessage::selectRaw('user_id, MAX(created_at) as last_at')
            ->groupBy('user_id')
            ->orderByDesc('last_at')
            ->pluck('user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $unread = CareerMessage::where('sender', CareerMessage::SISWA)
            ->whereNull('read_at')
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $lastMessages = CareerMessage::whereIn('user_id', $userIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->last());

        $conversations = $userIds->map(fn ($id) => [
            'user' => $users->get($id),
            'last' => $lastMessages->get($id),
            'unread' => (int) ($unread[$id] ?? 0),
        ])->filter(fn ($row) => $row['user'] !== null)->values();

        return view('career-pesan.index', compact('conversations'));
    }

    public function show(User $user)
    {
        $messages = CareerMessage::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        // Dibaca admin → pesan siswa hilang dari hitungan belum dibaca.
        CareerMessage::where('user_id', $user->id)
            ->where('sender', CareerMessage::SISWA)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('career-pesan.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:1|max:2000',
        ]);

        CareerMessage::create([
            'user_id' => $user->id,
            'sender' => CareerMessage::ADMIN,
            'body' => $validated['body'],
        ]);

        return redirect()->route('career.pesan.show', $user)->with('success', 'Balasan terkirim.');
    }
}
