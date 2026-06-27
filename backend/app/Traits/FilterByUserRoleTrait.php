<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterByUserRoleTrait
{
    protected static function bootFilterByUserRoleTrait()
    {
        static::addGlobalScope('role_filter', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $ownerColumn = defined('static::OWNER_COLUMN') ? static::OWNER_COLUMN : 'owner_id';

                if ($user->role === 'sales') {
                    $builder->where($builder->getQuery()->from . '.' . $ownerColumn, $user->id);
                } elseif ($user->role === 'pimpinan_sales') {
                    $subordinateIds = $user->subordinates()->pluck('id')->toArray();
                    $allowedIds = array_merge([$user->id], $subordinateIds);
                    $builder->whereIn($builder->getQuery()->from . '.' . $ownerColumn, $allowedIds);
                }
                // admin has no filter
            }
        });
    }
}
