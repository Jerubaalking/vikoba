<?php

namespace App\Http\Middleware\V2;

use Closure;
use Illuminate\Http\Request;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('lang')) {
            app()->setLocale($request->input('lang'));
        } else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
