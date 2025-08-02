<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceStockPoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stockPoint()
    {
        return $this->belongsTo(StockPoint::class, 'stock_point_id');
    }


    public function specialized_provider($all = false)
    {
        $query =$this->belongsTo(ServiceProvider::class, 'stock_point_id', 'specialized_id')
        ->where('specialized_type', 'stoke-points');
        $query->where('specialized_provider', 1);
        return $query;
    }


}
