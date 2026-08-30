<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoperasiOrder extends Model
{
    protected $fillable = ['user_id', 'items', 'total', 'metode', 'status', 'paid_at'];

    protected $casts = [
        'items' => 'array',
        'total' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
