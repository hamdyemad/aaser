<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EpisodeResource extends JsonResource
{
public function toArray(Request $request): array
{
    $appointmentDate = $this->appointment ? Carbon::parse($this->appointment) : null;
    $now = Carbon::now();

    return [
        'id' => $this->id,
        'name' => $this->name,
        'title' => $this->title,
        'description' => $this->description,
        'file' => $this->file ? asset('storage/' . $this->file) : null,
        'image' => $this->image ? asset('storage/' . $this->image) : null,
        'appointment' => $this->appointment,
        'available' => $appointmentDate && $appointmentDate->lte($now) ? 1 : 0,
        'status' => $this->status,
        'view' => $this->view,
        'send_notification' => $this->send_notification,
        'earn_points' => $this->earn_points,
    ];
}
}
