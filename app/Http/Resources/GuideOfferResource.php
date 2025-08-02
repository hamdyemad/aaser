<?php

namespace App\Http\Resources;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guide_id' => $this->guide_id,
            'name' => $this->name,
            'discount' => $this->discount,
            'points' => $this->points,
            'date' => $this->date,
            'num_customers' => $this->num_customers,
            'num_every_customer' => $this->num_every_customer,
            'image' => asset('storage/'. $this->image),
        ];
    }
}
