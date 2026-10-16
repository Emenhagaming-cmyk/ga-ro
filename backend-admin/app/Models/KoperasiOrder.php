<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KoperasiOrder extends Model
{
    public const PAID_STATUSES = ['dibayar', 'diambil'];

    protected $fillable = [
        'user_id', 'kode', 'items', 'total', 'pickup_code', 'metode', 'status',
        'snap_token', 'midtrans_order_id', 'paid_at', 'picked_up_at',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'integer',
        'paid_at' => 'datetime',
        'picked_up_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateKode(): string
    {
        return 'KOP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }

    public static function generatePickupCode(): string
    {
        return strtoupper(Str::random(6));
    }
}
