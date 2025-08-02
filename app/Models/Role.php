<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rolePermission()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }
}
