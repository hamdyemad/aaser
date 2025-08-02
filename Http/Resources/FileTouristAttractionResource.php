<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileTouristAttractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tourist_attraction_id' => $this->tourist_attraction_id,
            'file' => asset('storage/'.$this->file),
        ];
    }
}
