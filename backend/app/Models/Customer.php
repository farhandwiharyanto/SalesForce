<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_customer',
        'nomor_sia',
        'customer_name',
        'status',
        'email',
        'initial',
        'owner_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->nomor_customer)) {
                $lastCustomer = self::orderBy('id', 'desc')->first();
                $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;
                $model->nomor_customer = str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
