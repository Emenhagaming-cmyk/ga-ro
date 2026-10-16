<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lowongan extends Model
{
    protected $fillable = [
        'title', 'company', 'location', 'jurusan',
        'type', 'work_arrangement', 'description', 'deadline', 'is_active',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class);
    }
}
