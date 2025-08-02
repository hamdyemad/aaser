<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntertainmentActivity extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderActivitie::class, 'activitie_id');
    }

    public function phone()
    {
        return $this->hasMany(PhoneEntertainmentActivity::class, 'activitie_id');
    }

    public function term()
    {
        return $this->hasMany(TermEntertainmentActivity::class, 'activitie_id');
    }

    public function file()
    {
        return $this->hasMany(FileEntertainmentActivity::class, 'activitie_id');
    }

    public function image()
    {
        return $this->hasMany(ImageEntertainmentActivity::class, 'activitie_id');
    }

    public function service()
    {
        return $this->hasMany(ServiceEntertainmentActivity::class, 'activitie_id');
    }
}
