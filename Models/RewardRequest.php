<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reward()
    {
        return $this->belongsTo(Reward::class,'reward_id');
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class,'done_by_service_provider');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function visitorExhibitionConference()
    {
        return $this->belongsTo(VisitorExhibitionConference::class,'visitor_exhibition_conference_id');
    }

    public function participantExhibitionConference()
    {
        return $this->belongsTo(ParticipantExhibitionConference::class,'participant_exhibition_conference_id');
    }

    public function requestGuide()
    {
        return $this->hasMany(RequestGuide::class,'request_id');
    }
    public function requestStockPoint()
    {
        return $this->hasMany(RequestStockPoint::class,'request_id');
    }
    public function requestReplacePoint()
    {
        return $this->hasMany(RequestReplacePoint::class,'request_id');
    }
    public function requestTouriste()
    {
        return $this->hasMany(RequestTouriste::class,'request_id');
    }
    public function requestActivity()
    {
        return $this->hasMany(RequestActivity::class,'request_id');
    }
}
