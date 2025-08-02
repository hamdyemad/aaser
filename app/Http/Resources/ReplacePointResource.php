<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplacePointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reward_address' => $this->reward_address,
            'reward_description' => $this->reward_description,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'website_url' => $this->website_url,
            'file' => $this->file ? asset('storage/'. $this->file) : null,
            'image' => $this->image ? asset('storage/'. $this->image) : null,
            'send_notification' => $this->send_notification,
            'have_count' => $this->have_count,
            'count_people' => $this->count_people,
            'phones' => PhoneReplacePointResource::collection($this->whenLoaded('phones')),
            'terms' => TermReplacePointResource::collection($this->whenLoaded('terms')),
            'rewards' => RewardReplacePointResource::collection($this->whenLoaded('rewards')),
            'provider' => new ProviderReplaceResource($this->whenLoaded('provider')),
            'vendors' => new VendorResource($this, 'replace-point'),
        ];
    }
}
