<?php

namespace App\Observers;

use App\Models\Lamaran;
use App\Models\LamaranStatusHistory;
use Illuminate\Support\Facades\Auth;

class LamaranObserver
{
    public function created(Lamaran $lamaran): void
    {
        LamaranStatusHistory::create([
            'lamaran_id' => $lamaran->id,
            'status_from' => null,
            'status_to' => $lamaran->status,
            'action_by' => Auth::id(),
        ]);
    }

    public function updated(Lamaran $lamaran): void
    {
        if ($lamaran->wasChanged('status')) {
            LamaranStatusHistory::create([
                'lamaran_id' => $lamaran->id,
                'status_from' => $lamaran->getOriginal('status'),
                'status_to' => $lamaran->status,
                'action_by' => Auth::id(),
            ]);
        }
    }
}
