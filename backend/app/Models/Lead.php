<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\FilterByUserRoleTrait;

class Lead extends Model
{
    use HasFactory, FilterByUserRoleTrait;

    protected $fillable = [
        'lead_number', 'first_name', 'last_name', 'email', 'status', 
        'customer_id', 'owner_id', 'product_id'
    ];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function updates()
    {
        return $this->hasMany(LeadUpdate::class)->orderBy('created_at', 'desc');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->lead_number)) {
                $lastLead = self::orderBy('id', 'desc')->first();
                $nextId = $lastLead ? $lastLead->id + 1 : 1;
                // Format: LEA + 100010293 + nextId (so it starts at LEA100010294 if id is 1)
                $baseNumber = 100010293 + $nextId;
                $model->lead_number = 'LEA' . $baseNumber;
            }
        });
    }
}
