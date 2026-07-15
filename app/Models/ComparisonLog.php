<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonLog extends Model
{
    protected $fillable = [
        'user_id',
        'country_one_id',
        'country_two_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function countryOne()
    {
        return $this->belongsTo(Country::class, 'country_one_id');
    }

    public function countryTwo()
    {
        return $this->belongsTo(Country::class, 'country_two_id');
    }
}