<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'api_name',
        'endpoint',
        'status_code',
        'response_time',
        'requested_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'status_code'  => 'integer',
        'response_time'=> 'integer',
    ];

    // =====================
    // ACCESSORS
    // =====================

    public function getIsSuccessAttribute(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_success ? '#10B981' : '#EF4444';
    }
}
