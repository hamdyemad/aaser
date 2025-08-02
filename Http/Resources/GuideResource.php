<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'country' => $this->country,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'website_url' => $this->website_url,
            'rate' => $this->rate,
            'num_rate' => $this->rate()->count(),
            'send_notification' => $this->send_notification,
            'type' => new GuideTypeResource($this->whenLoaded('type')),
            'offers' => GuideOfferResource::collection($this->whenLoaded('offers')),
            'terms' => GuideTermResource::collection($this->whenLoaded('terms')),
            'phone' => PhoneGuideResource::collection($this->whenLoaded('phone')),
            'file' => FileGuideResource::collection($this->whenLoaded('file')),
            'image' => ImageGuideResource::collection($this->whenLoaded('image')),
            'provider' => new ProviderGuideResource($this->whenLoaded('provider')),
            'vendors' => new VendorResource($this, 'guide'),
        ];
    }
}
