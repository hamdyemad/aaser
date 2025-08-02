<?php

namespace App\Http\Resources;

use App\Models\GuideOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestGuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'offer_id' => $this->offer_id,
            'offer' => new GuideOfferResource(GuideOffer::findorFail($this->offer_id))
        ];
    }
}
