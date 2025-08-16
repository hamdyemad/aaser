<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\RewardReplacePoint;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestReplacePointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'replace_reward_id' => $this->replace_reward_id,
            'rewardRequest' => new RewardRequestResource($this->whenLoaded('rewardRequest')),
            'products_count' => $this->products_count,
            'replace_date' => $this->replace_date,
            'replace_reward' => new RewardReplacePointResource(RewardReplacePoint::findorFail($this->replace_reward_id)),

        ];
    }
}
