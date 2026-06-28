<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'menus'
    ];

    protected $casts = [
        'menus' => 'array',
    ];
}
