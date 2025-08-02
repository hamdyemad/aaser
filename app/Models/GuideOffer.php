<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuideOffer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function guide()
    {
        return $this->belongsTo(Guide::class, 'guide_id');
    }

    public function specialized_provider()
    {
        $query = $this->belongsTo(ServiceProvider::class, 'guide_id', 'specialized_id')
        ->where('specialized_type', 'guide');
        $query->where('specialized_provider', 1);
        return $query;
    }

}
