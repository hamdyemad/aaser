<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ServiceTouristAttraction;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestTouristeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'service_tourist_attraction_id' => $this->service_tourist_attraction_id,
            'qty' => $this->qty,
            'date' => $this->date,
            'service_tourist_attraction' => new ServiceTouristAttractionResource(ServiceTouristAttraction::findorFail($this->service_tourist_attraction_id)),
        ];
    }
}
