<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TouristAttractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'website_url' => $this->website_url,
            'country' => $this->country,
            'tax' => $this->tax,
            'address' => $this->address,
            'view' => $this->view,
            'rate' => $this->rate,
            'num_rate' => $this->rate()->count(),
            'send_notification' => $this->send_notification,
            'hours_work' => $this->hours_work,
            'phone' => PhoneTouristAttractionResource::collection($this->whenLoaded('phone')),
            'service' => ServiceTouristAttractionResource::collection($this->whenLoaded('service')),
            'term' => TermTouristAttractionResource::collection($this->whenLoaded('term')),
            'image' => ImageTouristAttractionResource::collection($this->whenLoaded('image')),
            'file' => FileTouristAttractionResource::collection($this->whenLoaded('file')),
            'provider' => new ProviderTouristeResource($this->whenLoaded('provider')),
            'vendors' => new VendorResource($this, 'tourist-attraction'),
            // 'vendors' => VendorResource($this),
            // 'vendors' => VendorResource::collection($this->whenLoaded('vendors')),
            // 'vendors' => VendorResource::collection(
            //     VendorResource::where('specialized_id', $this->id)->get()
            // ),
        ];
    }
}
