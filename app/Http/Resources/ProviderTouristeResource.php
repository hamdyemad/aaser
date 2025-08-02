<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderTouristePhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderTouristeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'touriste_id' => $this->touriste_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderTouristePhoneResource::collection(ProviderTouristePhone::where('provider_id', $this->id)->get()),
        ];
    }
}
