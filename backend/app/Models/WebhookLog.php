<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = ['event_type', 'target_url', 'status_code', 'payload', 'response'];

    protected $casts = [
        'payload' => 'array',
    ];
}
