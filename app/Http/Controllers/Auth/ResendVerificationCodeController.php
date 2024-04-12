<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Auth\SendSmsVerificationCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResendVerificationCodeController extends Controller
{
    /**
     * Invoke method
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
        ]);

        $user = User::wherePhone(Str::remove('+', $request['phone']))->first();

        $user->update([
            'phone_code' => rand(1000, 9999),
            'phone_code_at' => now(),
        ]);

        // SendSmsVerificationCode::dispatchAfterResponse($user);

        return response()->json(null, 202);
    }
}
