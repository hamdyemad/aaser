<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TouristeAttractionRate extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function booted()
    {
        parent::boot();
        static::created(function ($tourist_attraction_rate) {
            $sum_rate = TouristeAttractionRate::where('tourist_attraction_id', $tourist_attraction_rate->tourist_attraction_id)->sum('rate');
            $count_rate = TouristeAttractionRate::where('tourist_attraction_id', $tourist_attraction_rate->tourist_attraction_id)->count();
            TouristAttraction::where('id', $tourist_attraction_rate->tourist_attraction_id)->update([
                'rate' => $sum_rate / $count_rate,
            ]);
        });
    }
}
