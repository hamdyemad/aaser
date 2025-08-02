<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardReplacePointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'replace_point_id' => $this->replace_point_id,
            'name' => $this->name,
            'point' => $this->point,
            'image' => $this->image ? asset('storage/'. $this->image) : null,
            'qty' => $this->qty,
            'available' => $this->available,
            'residual' => $this->residual,
            'appointment' => $this->appointment,
            'end_date' => $this->end_date,
        ];
    }
}
