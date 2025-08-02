<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceStockPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock_point_id' => $this->stock_point_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'point' => $this->point,
            'count' => $this->count,
            'available_count' => $this->available_count,
            'before_price' => $this->before_price,
            'after_price' => $this->after_price,
            'appointment' => $this->appointment,
            'date' => $this->date,
            'image' => $this->image ? asset('storage/'. $this->image) : null,
        ];
    }
}
