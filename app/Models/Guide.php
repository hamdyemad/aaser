<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guide extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderGuide::class, 'guide_id');
    }

    public function type()
    {
        return $this->belongsTo(GuideType::class, 'type_id');
    }

    public function offers()
    {
        return $this->hasMany(GuideOffer::class, 'guide_id');
    }

    public function terms()
    {
        return $this->hasMany(GuideTerm::class, 'guide_id');
    }

    public function rate()
    {
        return $this->hasMany(GuideRate::class, 'guide_id');
    }

    public function phone()
    {
        return $this->hasMany(PhoneGuide::class, 'guide_id');
    }

    public function file()
    {
        return $this->hasMany(FileGuide::class, 'guide_id');
    }

    public function image()
    {
        return $this->hasMany(ImageGuide::class, 'guide_id');
    }
}
