<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneStockPoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stockPoint()
    {
        return $this->belongsTo(StockPoint::class, 'stock_point_id');
    }
}
