<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReplacePoint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderReplace::class, 'replace_id');
    }

    public function phones()
    {
        return $this->hasMany(PhoneReplacePoint::class, 'replace_point_id');
    }

    public function terms()
    {
        return $this->hasMany(TermReplacePoint::class, 'replace_point_id');
    }

    public function rewards()
    {
        return $this->hasMany(RewardReplacePoint::class, 'replace_point_id');
    }
}
