<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadUpdate extends Model
{
    protected $fillable = ['lead_id', 'user_id', 'action', 'description'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
