<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'side' => $this->side,
            'description' => $this->description,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'website_url' => $this->website_url,
            'status' => $this->status,
            'view' => $this->view,
            'send_notification' => $this->send_notification,
            'phone' => PhoneParticipantResource::collection($this->whenLoaded('phone')),
            'image' => ImageParticipantResource::collection($this->whenLoaded('image')),
            'file' => FileParticipantResource::collection($this->whenLoaded('file')),
        ];
    }
}
