<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestGuide extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rewardRequest()
    {
        return $this->belongsTo(RewardRequest::class, 'request_id');
    }

    public function offer()
    {
        return $this->belongsTo(GuideOffer::class,'offer_id');
    }
}
