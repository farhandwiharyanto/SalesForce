<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiaContract extends Model
{
    protected $fillable = ['sia_number', 'opty_id', 'customer_id', 'company_name', 'status'];

    public function opty()
    {
        return $this->belongsTo(Opty::class);
    }
}
