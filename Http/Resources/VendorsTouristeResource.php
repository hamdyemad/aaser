<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorsTouristeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'touriste_id' => $this->touriste_id,
            'service_vendor_email' => $this->service_vendor_email,
            'service_vendor_password' => $this->service_vendor_password,
        ];
    }
}
