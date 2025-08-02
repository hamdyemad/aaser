<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExhibitionConference extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider()
    {
        return $this->hasOne(ProviderConference::class, 'conference_id');
    }

    public function phone()
    {
        return $this->hasMany(PhoneExhibitionConference::class, 'exhibition_conference_id');
    }

    public function email()
    {
        return $this->hasMany(EmailExhibitionConference::class, 'exhibition_conference_id');
    }

    public function file()
    {
        return $this->hasMany(FileExhibitionConference::class, 'exhibition_conference_id');
    }

    public function image()
    {
        return $this->hasMany(ImageExhibitionConference::class, 'exhibition_conference_id');
    }

    public function visitor()
    {
        return $this->hasMany(VisitorExhibitionConference::class, 'conference_id');
    }

    public function participant()
    {
        return $this->hasMany(ParticipantExhibitionConference::class, 'conference_id');
    }
}
