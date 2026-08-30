<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SppBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'periode', 'nominal', 'status', 'jatuh_tempo',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'jatuh_tempo' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SppPayment::class, 'bill_id');
    }
}
