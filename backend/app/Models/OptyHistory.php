<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptyHistory extends Model
{
    protected $fillable = ['opty_id', 'user_id', 'action', 'description'];

    public function opty()
    {
        return $this->belongsTo(Opty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
