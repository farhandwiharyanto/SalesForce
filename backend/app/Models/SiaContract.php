<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiaContract extends Model
{
    protected $fillable = ['sia_number', 'deal_id', 'customer_id', 'company_name', 'status'];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
