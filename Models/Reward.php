<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function requests()
    {
        return $this->hasMany(RewardRequest::class, 'reward_id');
    }

    public function terms()
    {
        return $this->hasMany(RewardTerm::class, 'reward_id');
    }
}
