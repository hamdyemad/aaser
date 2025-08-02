<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reward_id' => $this->reward_id,
            'visitor_exhibition_conference_id' => $this->visitor_exhibition_conference_id,
            'participant_exhibition_conference_id' => $this->participant_exhibition_conference_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateString(),
            'done_date' => $this->done_date ?? null,
            'request_id' => $this->request_id,
            'done_by_service_provider' => $this->done_by_service_provider,
            'reward' => new RewardResource($this->whenLoaded('reward')),
            'visitorExhibitionConference' => new VisitorExhibitionConferenceResource($this->whenLoaded('visitorExhibitionConference')),
            'participantExhibitionConference' => new ParticipantExhibitionConferenceResource($this->whenLoaded('participantExhibitionConference')),
            'user' => new UserResource($this->whenLoaded('user')),
            'provider' => new ServiceProviderResource($this->whenLoaded('provider')),
            'offers' => RequestGuideResource::collection($this->whenLoaded('requestGuide')),
            'stock_point' => RequestStockPointResource::collection($this->whenLoaded('requestStockPoint')),
            'replace_point' => RequestReplacePointResource::collection($this->whenLoaded('requestReplacePoint')),
            'touriste' => RequestTouristeResource::collection($this->whenLoaded('requestTouriste')),
            'activity' => RequestActivityResource::collection($this->whenLoaded('requestActivity')),
        ];
    }
}
