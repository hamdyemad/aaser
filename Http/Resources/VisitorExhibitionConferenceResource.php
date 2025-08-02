<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorExhibitionConferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exhibition_conference_id' => $this->conference_id,
            'exhibition_conference_name' => $this->exhibition_conference->name,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'jop' => $this->jop,
        ];
    }
}
