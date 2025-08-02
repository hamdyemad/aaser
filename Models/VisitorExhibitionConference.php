<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorExhibitionConference extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function exhibition_conference()
    {
        return $this->belongsTo(ExhibitionConference::class,'conference_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
