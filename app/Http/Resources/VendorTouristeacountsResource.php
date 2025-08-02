<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorTouristeacountsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'specialized_type' => 'tourist-attraction',
            'specialized_id' => $this->specialized_id,
            'email' => $this->provider_id,
            'password' => $this->phone,
        ];
    }
}
