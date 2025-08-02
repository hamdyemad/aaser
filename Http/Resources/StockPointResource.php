<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'tax' => $this->tax,
            'website_url' => $this->website_url,
            'send_notification' => $this->send_notification,
            'have_count' => $this->have_count,
            'count_people' => $this->count_people,
            'phones' => PhoneStockPointResource::collection($this->whenLoaded('phones')),
            'terms' => TermStockPointResource::collection($this->whenLoaded('terms')),
            'services' => ServiceStockPointResource::collection($this->whenLoaded('services')),
            'file' => FileStockPointResource::collection($this->whenLoaded('file')),
            'image' => ImageStockPointResource::collection($this->whenLoaded('image')),
            'provider' => new ProviderStockResource($this->whenLoaded('provider')),
            'vendors' => new VendorResource($this, 'stoke-points'),
        ];
    }
}
