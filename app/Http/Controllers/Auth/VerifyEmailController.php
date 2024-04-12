<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Verifies an email for a registered user
     */
    public function __invoke()
    {
        $user = User::where([
            ['email', decrypt(urldecode(request('value')))],
            ['phone', decrypt(urldecode(request('caller')))],
        ])->first();

        $wasAlreadyVerified = $user && ! is_null($user->email_verified_at) ? true : false;

        if (! $wasAlreadyVerified)
            $user->fill(['email_verified_at' => now()])->save();

        return view('auth.verify_email_result', compact('user', 'wasAlreadyVerified'));
    }
}
