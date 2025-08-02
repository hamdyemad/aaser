<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderConferencePhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderConferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conference_id' => $this->conference_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderConferencePhoneResource::collection(ProviderConferencePhone::where('provider_id', $this->id)->get()),
        ];
    }
}
