<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lamaran extends Model
{
    public const STATUSES = ['pending', 'diterima', 'ditolak', 'dibatalkan'];

    protected $fillable = [
        'user_id', 'lowongan_id', 'cv_path',
        'cover_letter', 'status', 'catatan_admin',
        'nama_lengkap', 'email', 'nisn', 'jurusan_pilihan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LamaranStatusHistory::class)->orderByDesc('created_at');
    }
}
