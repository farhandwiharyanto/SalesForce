<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opty extends Model
{
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
        'is_converted_from_lead'
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
}
