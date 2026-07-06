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

        'published_at'

    ];

    protected $casts = [

        'published_at' => 'datetime'

    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}