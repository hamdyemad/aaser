<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileExhibitionConferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exhibition_conference_id' => $this->exhibition_conference_id,
            'file' => asset('storage/'. $this->file),
        ];
    }
}
