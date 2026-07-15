<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshLog extends Model
{
    protected $fillable = [
        'type',
        'status',
        'message',
        'duration_ms',
    ];

    // =====================
    // ACCESSORS
    // =====================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'success' => '#10B981',
            'error'   => '#EF4444',
            default   => '#F59E0B',
        };
    }
}
