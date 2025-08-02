<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function answer()
    {
        return $this->hasMany(ContactAnswer::class, 'contact_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
