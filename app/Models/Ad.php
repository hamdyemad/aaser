<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ad extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function locations()
    {
        return $this->hasMany(AdLocation::class, 'ad_id');
    }

    public function terms()
    {
        return $this->hasMany(AdTerm::class, 'ad_id');
    }

    public function image()
    {
        return $this->hasMany(ImageAd::class, 'ad_id');
    }

    public function file()
    {
        return $this->hasMany(FileAd::class, 'ad_id');
    }
}
