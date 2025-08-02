<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntertainmentActivityResource extends JsonResource
{
    public function toArray(Request $request): array
{
    $now = now(); // الوقت الحالي باستخدام Carbon

    // نحاول تحويل التواريخ إلى كائنات Carbon
    $appointment = $this->appointment ? \Carbon\Carbon::parse($this->appointment) : null;
    $apperAppointment = $this->apper_appointment ? \Carbon\Carbon::parse($this->apper_appointment) : null;

    // تحديد القيمة بناءً على المقارنة بالتواريخ
    $available = 0;
    if ($appointment && $now->between($appointment, $apperAppointment)) {
        $available = 1;
    }

    return [
        'id' => $this->id,
        'name' => $this->name,
        'address' => $this->address,
        'tax' => $this->tax,
        'appointment' => $this->appointment,
        'apper_appointment' => $this->apper_appointment,
        'email' => $this->email,
        'location' => $this->location,
        'location_link' => $this->location_link,
        'status' => $this->status,
        'view' => $this->view,
        'country' => $this->country,
        'website_url' => $this->website_url,
        'description' => $this->description,
        'place' => $this->place,
        'send_notification' => $this->send_notification,
        'available' => $available, // السطر المضاف
        'file' => FileEntertainmentActivityResource::collection($this->whenLoaded('file')),
        'image' => ImageEntertainmentActivityResource::collection($this->whenLoaded('image')),
        'term' => TermEntertainmentActivityResource::collection($this->whenLoaded('term')),
        'phone' => PhoneEntertainmentActivityResource::collection($this->whenLoaded('phone')),
        'service' => ServiceEntertainmentActivityResource::collection($this->whenLoaded('service')),
        'provider' => new ProviderActivitieResource($this->whenLoaded('provider')),
        'vendors' => new VendorResource($this, 'entertainment-activity'),
    ];
}
}
