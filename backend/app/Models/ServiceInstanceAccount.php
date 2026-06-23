<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceInstanceAccount extends Model
{
    protected $fillable = ['sia_number', 'opty_id', 'customer_id', 'company_name', 'status', 'billing_account_number', 'contract_id'];

    public function opty()
    {
        return $this->belongsTo(Opty::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
