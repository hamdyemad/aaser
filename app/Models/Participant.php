<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function phone()
    {
        return $this->hasMany(PhoneParticipant::class,'participant_id');
    }

    public function image()
    {
        return $this->hasMany(ImageParticipant::class,'participant_id');
    }

    public function file()
    {
        return $this->hasMany(FileParticipant::class,'participant_id');
    }
}
