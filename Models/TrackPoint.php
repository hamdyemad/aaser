<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackPoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function point()
    {
        return $this->belongsTo(Point::class,'point_id');
    }

    public static function booted()
    {
        parent::boot();
        static::created(function ($track_point) {
            $amount_count = TrackPoint::where('point_id', $track_point->point_id)->sum('point');
            Point::where('id', $track_point->point_id)->update([
                'points' => $amount_count,
            ]);
        });
    }
}
