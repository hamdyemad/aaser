<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceEntertainmentActivity extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function specialized_provider()
    {
        $query = $this->belongsTo(ServiceProvider::class, 'activitie_id', 'specialized_id')
        ->where('specialized_type', 'entertainment-activity');
        $query->where('specialized_provider', 1);
        return $query;
    }

}
