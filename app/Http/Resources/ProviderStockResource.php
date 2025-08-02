<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ProviderStockPhone;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_id' => $this->stock_id,
            'name' => $this->name,
            'address' => $this->address,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'num_hours' => $this->num_hours,
            'phones' => ProviderStockPhoneResource::collection(ProviderStockPhone::where('provider_id', $this->id)->get()),
        ];
    }
}
