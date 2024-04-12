<?php

namespace App\Http\Middleware\V2;

use App\Http\V2_StandardApiResponse;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthRequiredMiddleware
{
    public function handle($request, Closure $next)
    {
        $accessToken = $request->header('Authorization');

        if (!$accessToken || !preg_match('/^Bearer\s+(.*)$/i', $accessToken, $matches)) {
            $messages =[trans('auth.auth_error')];
            return V2_StandardApiResponse::generate(false,$messages,401);
        }

        $accessToken = $matches[1];

        if (!Auth::guard('api')->check()) {

            $messages =[trans('auth.auth_error')];
            return V2_StandardApiResponse::generate(true,$messages,401);
        }

        return $next($request);
    }
}
