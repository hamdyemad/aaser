<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'points' => $this->points,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'send_notification' => $this->send_notification,
            'have_count' => $this->have_count,
            'count_people' => $this->count_people,
            'image' => asset('storage/'. $this->image),
            'requests' => RewardRequestResource::collection($this->whenLoaded('requests')),
            'terms' => RewardTermResource::collection($this->whenLoaded('terms'))
        ];
    }
}
