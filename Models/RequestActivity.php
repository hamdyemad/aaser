<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestActivity extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rewardRequest()
    {
        return $this->belongsTo(RewardRequest::class, 'request_id');
    }

    public function serviceActivity()
    {
        return $this->belongsTo(ServiceEntertainmentActivity::class,'service_activity_id');
    }
}
