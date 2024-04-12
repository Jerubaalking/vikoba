<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ValidateTokenController extends Controller
{
    /**
     * Invoke method
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        return response()->json(auth()->user()->load([
            'roles.permissions',
            'notificationPreferences',
        ]));
    }
}
