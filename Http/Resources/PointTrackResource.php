<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointTrackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'point_id' => $this->point_id,
            'episode_id' => $this->episode_id,
            'point' => $this->point,
            'comment' => $this->comment,
        ];
    }
}
