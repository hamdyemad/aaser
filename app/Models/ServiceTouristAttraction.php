<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceTouristAttraction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tourist_attraction()
    {
        return $this->belongsTo(TouristAttraction::class, 'tourist_attraction_id');
    }



    public function specialized_provider()
    {
        $query = $this->belongsTo(ServiceProvider::class, 'tourist_attraction_id', 'specialized_id')
        ->where('specialized_type', 'tourist-attraction');
        $query->where('specialized_provider', 1);
        return $query;
    }



}
