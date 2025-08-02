<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shepherd extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function phone()
    {
        return $this->hasMany(PhoneShepherd::class,'shepherd_id');
    }

    public function image()
    {
        return $this->hasMany(ImageShepherd::class,'shepherd_id');
    }

    public function file()
    {
        return $this->hasMany(FileShepherd::class,'shepherd_id');
    }
}
