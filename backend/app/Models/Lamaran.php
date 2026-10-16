<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lamaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lowongan_id', 'cv_path',
        'cover_letter', 'status', 'catatan_admin',
        'nama_lengkap', 'email', 'nisn', 'jurusan_pilihan',
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
        return $this->hasMany(LamaranStatusHistory::class)->orderBy('created_at', 'desc');
    }
}
