<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTouristAttractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tourist_attraction_id' => $this->tourist_attraction_id,
            'name' => $this->name,
            'image' => asset('storage/'.$this->image),
            'before_tax' => $this->before_tax,
            'tax' => $this->tourist_attraction->tax,
            'price' => $this->price,
            'appointment' => $this->appointment,
            'date' => $this->date,
            'earn_points' => $this->earn_points,
            'count' => $this->count,
            'available_count' => $this->available_count,
        ];
    }
}
