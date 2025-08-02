<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function notifications () {
        return $this->hasMany(Notification::class, 'product_id')->where('page', 'episodes');
    }

}
