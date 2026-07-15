<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'system_name',
        'system_email',
        'refresh_interval',
    ];

    protected $casts = [
        'refresh_interval' => 'integer',
    ];
}
