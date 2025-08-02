<?php

namespace App\Http\Middleware;

use App\Traits\Res;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class CustomAuthenticate extends BaseAuthenticate
{
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'Unauthorized.',
            ], 401));
    }
}
