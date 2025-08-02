<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TermReplacePoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function replacePoint()
    {
        return $this->belongsTo(ReplacePoint::class, 'replace_point_id');
    }
}
