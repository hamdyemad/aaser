<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorTouristeacount extends Model
{
    // ربط الموديل بجدول service_providers
    protected $table = 'service_providers';

    // إيقاف استخدام created_at و updated_at لو الجدول ما فيهمش
    public $timestamps = false;

    // الأعمدة القابلة للتعديل
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'side',
        'active',
        'total_points',
        'last_active',
        'specialized_provider',
        'specialized_type',
        'specialized_id',
    ];
}
