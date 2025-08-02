<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ServiceEntertainmentActivity;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'service_activity_id' => $this->service_activity_id,
            'qty' => $this->qty,
            'date' => $this->date,
            'service_activity' => new ServiceEntertainmentActivityResource(ServiceEntertainmentActivity::findorFail($this->service_activity_id)),
        ];
    }
}
