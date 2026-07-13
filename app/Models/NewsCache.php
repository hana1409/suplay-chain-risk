<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $fillable = [
        'country_id',
        'title',
        'description',
        'content',
        'url',
        'image',
        'source',
        'published_at',
        'sentiment',
        'sentiment_score',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'sentiment_score' => 'float',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getSentimentColorAttribute(): string
    {
        return match ($this->sentiment) {
            'Positive' => '#10B981',
            'Negative' => '#EF4444',
            default    => '#6B7280',
        };
    }

    public function getSentimentIconAttribute(): string
    {
        return match ($this->sentiment) {
            'Positive' => '↑',
            'Negative' => '↓',
            default    => '→',
        };
    }
}