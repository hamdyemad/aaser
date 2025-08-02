<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileEntertainmentActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'activitie_id' => $this->activitie_id,
            'file' => asset('storage/'. $this->file),
        ];
    }
}
