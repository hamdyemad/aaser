<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdLocation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ad()
    {
        return $this->belongsTo(Ad::class,'ad_id');
    }
}
