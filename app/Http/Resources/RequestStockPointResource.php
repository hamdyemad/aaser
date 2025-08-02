<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ServiceStockPoint;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestStockPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'service_id' => $this->service_id,
            'products_count' => $this->products_count,
            'service' => new ServiceStockPointResource(ServiceStockPoint::findorFail($this->service_id)),
        ];
    }
}
