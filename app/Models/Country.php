<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [

        'country_code',

        'country_code3',

        'country_name',

        'official_name',

        'capital',

        'region',

        'subregion',

        'currency_code',

        'currency_name',

        'language',

        'population',

        'latitude',

        'longitude',

        'timezone',

        'flag',

        'flag_png',

        'flag_svg',

    ];
}