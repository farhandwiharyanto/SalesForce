<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByUserRoleTrait;

class Opty extends Model
{
    use FilterByUserRoleTrait;

    protected $table = 'opty';
    protected $fillable = [
        'opportunity_number',
        'name',
        'assigned_to',
        'owner_id',
        'customer_id',
        'product_id',
        'target_close_date',
        'customer_expected_rfs',
        'request_type',
        'stage',
        'confidence_level',
        'estimated_value_otc',
        'estimated_value_mrc',
        'discount_amount',
        'discount_status',
        'is_converted_from_lead',
        'contract_id',
        'contract_document_path',
        'pimpinan_approval_status',
        'director_approval_status',
        'verificator_approval_status',
        'rejection_note'
    ];

    protected $casts = [
        'target_close_date' => 'date',
        'customer_expected_rfs' => 'date',
        'is_converted_from_lead' => 'boolean',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function histories()
    {
        return $this->hasMany(OptyHistory::class)->latest();
    }

    public function serviceInstanceAccount()
    {
        return $this->hasOne(ServiceInstanceAccount::class, 'deal_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
