<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneVerificationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class VerifyPhoneController extends Controller
{
    /**
     * Invoke method
     *
     * @param PhoneVerificationRequest $request
     * @return JsonResponse
     */
    public function __invoke(PhoneVerificationRequest $request)
    {
        $user = User::wherePhone($request['phone'])->first();

        if ((int) $user->phone_code === $request['code']) {
            $user->update(['phone_verified_at' => now()]);
            return response()->json();
        }

        return response()->json([
            'error' => 'invalid_code',
            'message' => 'The verification code is incorrect',
        ], 400);
    }
}
