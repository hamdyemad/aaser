<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'message' => $this->message,
            'episode_id' => $this->episode_id,
            'page' => $this->page,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'link' => $this->link,
            'image' => $this->image ? asset('storage/'. $this->image) : null,
            'file' => $this->file ? asset('storage/'. $this->file) : null,
            'read_at' => $this->read_at,
        ];
    }
}
