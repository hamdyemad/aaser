<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'localtion_url' => $this->localtion_url,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'phone' => $this->phone,
            'user' => new UserResource($this->whenLoaded('user')),
            'answer' => ContactAnswerResource::collection($this->whenLoaded('answer')),
        ];
    }
}
