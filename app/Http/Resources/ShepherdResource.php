<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShepherdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'side' => $this->side,
            'send_notification' => $this->send_notification,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'website_url' => $this->website_url,
            'status' => $this->status,
            'view' => $this->view,
            'phone' => PhoneShepherdResource::collection($this->whenLoaded('phone')),
            'image' => ImageShepherdResource::collection($this->whenLoaded('image')),
            'file' => FileShepherdResource::collection($this->whenLoaded('file')),
        ];
    }
}
