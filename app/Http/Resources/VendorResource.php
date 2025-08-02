<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    protected $specialized_type;

    public function __construct($resource, $specialized_type)
    {
        parent::__construct($resource);
        $this->specialized_type = $specialized_type;
    }

    public function toArray(Request $request): array
    {
        // جلب مقدمي الخدمة بناءً على specialized_type و specialized_id
        $service_providers = ServiceProvider::where('specialized_type', $this->specialized_type)
            ->where('specialized_id', $this->id)
            ->where('specialized_provider', 1)
            ->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'vendors' => $service_providers->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'email' => $provider->email,
                    'unencrypted_password' => $provider->unencrypted_password,
                    'password' => $provider->password,
                    'phone' => $provider->phone,
                    'side' => $provider->side,
                    'active' => $provider->active,
                    'total_points' => $provider->total_points,
                    'last_active' => $provider->last_active,
                    'specialized_provider' => $provider->specialized_provider,
                    'specialized_type' => $provider->specialized_type,
                    'specialized_id' => $provider->specialized_id,
                ];
            }),
        ];
    }
}
