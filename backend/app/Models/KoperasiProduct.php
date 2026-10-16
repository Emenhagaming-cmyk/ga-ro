<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KoperasiProduct extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category', 'price', 'stock', 'image', 'emoji', 'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }
}
