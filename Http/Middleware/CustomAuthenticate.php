<?php

namespace App\Http\Middleware;

use App\Traits\Res;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class CustomAuthenticate extends BaseAuthenticate
{
    protected function redirectTo($request)
    {
        return null;
    }

    public function handle($request, Closure $next, ...$guards)
    {
        if ($request->header('Authorization') == null) {
            return response()->json([
                'status' => 'Unauthenticated',
                'data' => [],
                'message' => 'You Should add Authorization header with Bearer token',
            ], 422);
        }

        try {
            $this->authenticate($request, $guards);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'status' => 'Unauthenticated',
                'data' => [],
                'message' => 'Please check your token or login again',
            ], 422);
        }

        return $next($request);
    }
}
