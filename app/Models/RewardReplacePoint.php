<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardReplacePoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function replacePoint()
    {
        return $this->belongsTo(ReplacePoint::class, 'replace_point_id');
    }


    public function specialized_provider()
    {
        $query = $this->belongsTo(ServiceProvider::class, 'replace_point_id', 'specialized_id')
        ->where('specialized_type', 'replace-point');
        $query->where('specialized_provider', 1);
        return $query;
    }
}
