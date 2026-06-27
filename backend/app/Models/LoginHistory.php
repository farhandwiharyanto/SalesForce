<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'sign_in_time',
        'sign_out_time',
        'status',
    ];

    protected $casts = [
        'sign_in_time' => 'datetime',
        'sign_out_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
