<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResendVerificationLinkController extends Controller
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
            'email' => 'required|email',
        ]);

        $user = User::whereEmail($request['email'])->firstOrFail();

        Mail::to($request['email'])->later(5, new VerifyEmail($user));

        return response()->json(null, 202);
    }
}
