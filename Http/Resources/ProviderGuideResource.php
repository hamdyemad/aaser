<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderGuidePhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderGuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guide_id' => $this->guide_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderGuidePhoneResource::collection(ProviderGuidePhone::where('provider_id', $this->id)->get()),
        ];
    }
}
