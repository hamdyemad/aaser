<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = Carbon::now()->toDateString();
        if($this->start_date <= $now && $this->end_date >= $now) {
            $available = true;
        } else {
            $available = false;
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'ad_link_type' => $this->ad_link_type,
            'ad_link' => $this->ad_link,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'timer' => $this->timer,
            'available' => $available,
            'status' => $this->start_date <= $now && $this->end_date >= $now ? 1 : 0,
            'locations' => AdLocationResource::collection($this->whenLoaded('locations')),
            'terms' => AdTermResource::collection($this->whenLoaded('terms')),
            'image' => ImageAdResource::collection($this->whenLoaded('image')),
            'file' => FileAdResource::collection($this->whenLoaded('file')),
        ];
    }
}
