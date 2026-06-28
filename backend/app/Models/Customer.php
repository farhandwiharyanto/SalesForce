<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\FilterByUserRoleTrait;
use App\Traits\Auditable;

class Customer extends Model
{
    use HasFactory, FilterByUserRoleTrait, Auditable;

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

    public function sias()
    {
        return $this->hasMany(ServiceInstanceAccount::class, 'customer_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'customer_id');
    }

    public function syncStatus()
    {
        $hasActiveSia = $this->sias()->where('status', 'Active')->exists();
        $hasActiveContract = $this->contracts()->whereIn('status', ['Active', 'In Use'])->exists();

        if ($hasActiveSia || $hasActiveContract) {
            $this->update(['status' => 'Active']);
            return;
        }

        $hasRegisteredSia = $this->sias()->whereIn('status', ['Registered', 'Isolir', 'Suspended'])->exists();
        $hasCreatedContract = $this->contracts()->whereIn('status', ['Created'])->exists();

        if ($hasRegisteredSia || $hasCreatedContract) {
            $this->update(['status' => 'Registered']);
            return;
        }

        $hasTerminatedSia = $this->sias()->whereIn('status', ['Terminated', 'Deactivated'])->exists();
        $hasCompletedContract = $this->contracts()->whereIn('status', ['Completed', 'Terminate'])->exists();

        if ($hasTerminatedSia || $hasCompletedContract) {
            $this->update(['status' => 'Deactivated']);
            return;
        }
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
