<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorTouristeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'side' => $this->side,
            'active' => $this->active,
            'total_points' => $this->total_points,
            'last_active' => $this->last_active,
            'specialized_provider' => $this->specialized_provider,
            'specialized_type' => $this->specialized_type,
            'specialized_id' => $this->specialized_id,
        ];
    }
}
