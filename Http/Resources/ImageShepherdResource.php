<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageShepherdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shepherd_id' => $this->shepherd_id,
            'image' => asset('storage/'. $this->image),
        ];
    }
}
