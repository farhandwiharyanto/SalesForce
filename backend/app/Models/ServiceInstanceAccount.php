<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ServiceInstanceAccount extends Model
{
    use Auditable;
    protected $fillable = ['sia_number', 'deal_id', 'customer_id', 'company_name', 'status', 'billing_account_number', 'contract_id'];

    public function opty()
    {
        return $this->belongsTo(Opty::class, 'deal_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
