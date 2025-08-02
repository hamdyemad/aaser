<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuideRate extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function booted()
    {
        parent::boot();
        static::created(function ($guide_rate) {
            $sum_rate = GuideRate::where('guide_id', $guide_rate->guide_id)->sum('rate');
            $count_rate = GuideRate::where('guide_id', $guide_rate->guide_id)->count();
            Guide::where('id', $guide_rate->guide_id)->update([
                'rate' => $sum_rate / $count_rate,
            ]);
        });
    }
}
