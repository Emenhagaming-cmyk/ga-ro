<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'category_color',
        'excerpt',
        'content',
        'image_path',
        'author',
        'published_at',
        'featured',
        'read_time',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'date',
        'featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    // Auto-generate slug dari title
    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // URL gambar (publik)
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        // Jika path mulai dengan http = URL eksternal
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return '/storage/' . $this->image_path;
    }

    // Format untuk API frontend (NewsView.vue)
    public function toApiArray(): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->title,
            'excerpt'       => $this->excerpt,
            'content'       => $this->content,
            'category'      => $this->category,
            'categoryColor' => $this->category_color,
            'image'         => $this->image_url,
            'author'        => $this->author,
            'publishedAt'   => $this->published_at?->format('Y-m-d'),
            'featured'      => $this->featured,
            'readTime'      => $this->read_time,
        ];
    }
}
