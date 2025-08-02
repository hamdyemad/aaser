<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExhibitionConferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'location' => $this->location,
            'location_link' => $this->location_link,
            'country' => $this->country,
            'status' => $this->status,
            'view' => $this->view,
            'website_url' => $this->website_url,
            'earn_points' => $this->earn_points,
            'appointment' => $this->appointment,
            'apper_appointment' => $this->apper_appointment,
            'phone' => PhoneExhibitionConferenceResource::collection($this->whenLoaded('phone')),
            'email' => EmailExhibitionConferenceResource::collection($this->whenLoaded('email')),
            'file' => FileExhibitionConferenceResource::collection($this->whenLoaded('file')),
            'image' => ImageExhibitionConferenceResource::collection($this->whenLoaded('image')),
            'visitor' => VisitorExhibitionConferenceResource::collection($this->whenLoaded('visitor')),
            'participant' => ParticipantExhibitionConferenceResource::collection($this->whenLoaded('participant')),
            'provider' => new ProviderConferenceResource($this->whenLoaded('provider')),
            'vendors' => new VendorResource($this, 'exhibition-conference'),
        ];
    }
}
