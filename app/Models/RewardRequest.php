<?php

namespace App\Models;

use App\Http\Resources\ServiceProviderResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'done_by_service_provider');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visitorExhibitionConference()
    {
        return $this->belongsTo(VisitorExhibitionConference::class, 'visitor_exhibition_conference_id');
    }

    public function participantExhibitionConference()
    {
        return $this->belongsTo(ParticipantExhibitionConference::class, 'participant_exhibition_conference_id');
    }

    public function requestGuide()
    {
        return $this->hasMany(RequestGuide::class, 'request_id');
    }
    public function requestStockPoint()
    {
        return $this->hasMany(RequestStockPoint::class, 'request_id');
    }
    public function requestReplacePoint()
    {
        return $this->hasMany(RequestReplacePoint::class, 'request_id');
    }
    public function requestTouriste()
    {
        return $this->hasMany(RequestTouriste::class, 'request_id');
    }
    public function requestActivity()
    {
        return $this->hasMany(RequestActivity::class, 'request_id');
    }


    public function getServiceProvidersAttribute()
    {
        $otherProviders = ServiceProvider::where('specialized_provider', 0)->get()->toArray();
        $providers = [];
        // RequestTouriste > serviceTouristAttraction > specialized_provider
        foreach ($this->requestTouriste as $touriste) {
            if (
                $touriste->serviceTouristAttraction &&
                $touriste->serviceTouristAttraction->specialized_provider
            ) {
                $provider = $touriste->serviceTouristAttraction->specialized_provider;
                $data = new ServiceProviderResource($provider);
                array_push($providers, $data);
            }
        }

        // visitorExhibitionConference
        if (
            $this->visitorExhibitionConference &&
            $this->visitorExhibitionConference->specialized_provider
        ) {
            $provider = $this->visitorExhibitionConference->specialized_provider;
            $data = new ServiceProviderResource($provider);
            array_push($providers, $data);
        }
        // participantExhibitionConference
        if (
            $this->participantExhibitionConference &&
            $this->participantExhibitionConference->specialized_provider
        ) {
            $provider = $this->participantExhibitionConference->specialized_provider;
            $data = new ServiceProviderResource($provider);
            array_push($providers, $data);
        }

        // RequestGuide > offer > specialized_provider
        foreach ($this->requestGuide as $guide) {
            if (
                $guide->offer &&
                $guide->offer->specialized_provider
            ) {
                $provider = $guide->offer->specialized_provider;
                $data = new ServiceProviderResource($provider);
                array_push($providers, $data);
            }
        }

        // RequestActivity > serviceActivity > specialized_provider
        foreach ($this->requestActivity as $activity) {
            if (
                $activity->serviceActivity &&
                $activity->serviceActivity->specialized_provider
            ) {
                $provider = $activity->serviceActivity->specialized_provider;
                $data = new ServiceProviderResource($provider);
                array_push($providers, $data);
            }
        }

        // RequestStockPoint > service > specialized_provider
        foreach ($this->requestStockPoint as $stockPoint) {
            if (
                $stockPoint->service &&
                $stockPoint->service->specialized_provider
            ) {
                $provider = $stockPoint->service->specialized_provider;
                $data = new ServiceProviderResource($provider);
                array_push($providers, $data);
            }
        }

        // RequestReplacePoint > replaceReward > specialized_provider
        foreach ($this->requestReplacePoint as $replacePoint) {
            if (
                $replacePoint->replaceReward &&
                $replacePoint->replaceReward->specialized_provider
            ) {
                $provider = $replacePoint->replaceReward->specialized_provider;
                $data = new ServiceProviderResource($provider);
                array_push($providers, $data);
            }
        }
        $providers = array_merge($providers, $otherProviders);

        return $providers;
    }
}
