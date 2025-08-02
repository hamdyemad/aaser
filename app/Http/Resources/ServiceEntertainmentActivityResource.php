<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceEntertainmentActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'activitie_id' => $this->activitie_id,
            'service_type' => $this->service_type,
            'amount' => $this->amount,
            'from' => $this->from,
            'to' => $this->to,
            'earn_points' => $this->earn_points,
            'num_tickets' => $this->num_tickets,
            'available_num_tickets' => $this->available_num_tickets,
            'image' => asset('storage/'. $this->image),
        ];
    }
}
