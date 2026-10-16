<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LamaranStatusHistory extends Model
{
    protected $fillable = [
        'lamaran_id',
        'status_from',
        'status_to',
        'action_by',
        'catatan',
    ];

    public function lamaran(): BelongsTo
    {
        return $this->belongsTo(Lamaran::class);
    }

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
