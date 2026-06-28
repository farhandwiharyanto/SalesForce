<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByUserRoleTrait;
use App\Traits\Auditable;

class Contract extends Model
{
    use HasFactory, FilterByUserRoleTrait, Auditable;

    const OWNER_COLUMN = 'assigned_to';

    protected $fillable = [
        'contract_number',
        'subject',
        'status',
        'customer_id',
        'assigned_to',
        'start_date',
        'due_date',
        'contract_division',
        'last_modified_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lastModifiedBy()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }
}
