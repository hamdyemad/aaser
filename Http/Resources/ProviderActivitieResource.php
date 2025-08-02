<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderActivitiePhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderActivitieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'activitie_id' => $this->activitie_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderActivitiePhoneResource::collection(ProviderActivitiePhone::where('provider_id', $this->id)->get()),
        ];
    }
}
