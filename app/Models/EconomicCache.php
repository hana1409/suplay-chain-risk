<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicCache extends Model
{
    protected $fillable = [

        'country_id',

        'gdp',

        'inflation',

        'exports',

        'imports',

        'population',

        'year'

    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}