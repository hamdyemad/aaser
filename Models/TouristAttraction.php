<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TouristAttraction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderTouriste::class, 'touriste_id');
    }

    public function rate()
    {
        return $this->hasMany(TouristeAttractionRate::class, 'tourist_attraction_id');
    }

    public function phone()
    {
        return $this->hasMany(PhoneTouristAttraction::class, 'tourist_attraction_id');
    }

    public function service()
    {
        return $this->hasMany(ServiceTouristAttraction::class, 'tourist_attraction_id');
    }

    public function term()
    {
        return $this->hasMany(TermTouristAttraction::class, 'tourist_attraction_id');
    }

    public function image()
    {
        return $this->hasMany(ImageTouristAttraction::class, 'tourist_attraction_id');
    }

    public function file()
    {
        return $this->hasMany(FileTouristAttraction::class, 'tourist_attraction_id');
    }
}
