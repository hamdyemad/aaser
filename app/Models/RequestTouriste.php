<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestTouriste extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rewardRequest()
    {
        return $this->belongsTo(RewardRequest::class, 'request_id');
    }

    public function serviceTouristAttraction()
    {
        return $this->belongsTo(ServiceTouristAttraction::class,'service_tourist_attraction_id');
    }
}
