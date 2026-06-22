<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['nomor_sia', 'nomor_customer', 'customer_name', 'status', 'email', 'initial'];

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
