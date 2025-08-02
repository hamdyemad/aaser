<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderReplacePhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderReplaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'replace_id' => $this->replace_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderReplacePhoneResource::collection(ProviderReplacePhone::where('provider_id', $this->id)->get()),
        ];
    }
}
