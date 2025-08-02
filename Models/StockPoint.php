<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockPoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderStock::class, 'stock_id');
    }

    public function phones()
    {
        return $this->hasMany(PhoneStockPoint::class, 'stock_point_id');
    }

    public function terms()
    {
        return $this->hasMany(TermStockPoint::class, 'stock_point_id');
    }

    public function services()
    {
        return $this->hasMany(ServiceStockPoint::class, 'stock_point_id');
    }

    public function image()
    {
        return $this->hasMany(ImageStockPoint::class, 'stock_point_id');
    }

    public function file()
    {
        return $this->hasMany(FileStockPoint::class, 'stock_point_id');
    }
}
