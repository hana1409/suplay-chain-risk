<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'image',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Published' => '#10B981',
            'Draft'     => '#F59E0B',
            default     => '#6B7280',
        };
    }

    // =====================
    // BOOT
    // =====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
