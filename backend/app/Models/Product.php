<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_number', 'name', 'description', 'price'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->product_number)) {
                $lastProduct = self::orderBy('id', 'desc')->first();
                $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
                // Format: PRO + 100000000 + nextId
                $baseNumber = 100000000 + $nextId;
                $model->product_number = 'PRO' . $baseNumber;
            }
        });
    }
}
