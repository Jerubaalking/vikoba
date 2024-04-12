<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Auth\PasswordReset;
use App\Jobs\Auth\SendSmsResetCode;
use App\Http\Controllers\Controller;
use App\Mail\Auth\PasswordResetCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Contracts\Routing\ResponseFactory;

class PasswordResetController extends Controller
{
    /**
     * Request to obtain password reset code by email/SMS
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function store(Request $request)
    {
        info('Requesting account reset code ', $request->toArray());

        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|exists:users,phone',
            'email' => 'required_without:phone|exists:users,email',
        ], [
            'phone.required_without' => 'Phone number is not valid',
            'phone.exists' => 'Phone number does not exists',
            'email.required_without' => 'Email address is not correct',
            'email.exists' => 'Email address does not exists'
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 422);
        }

        $key = $request->filled('phone') ? 'phone': 'email';
        $value = $request[$key];

        $user = User::where($key, $request->filled('phone') ? get_international_number($value) : $value)->first();

        if (!$user) {
            return response()->json([
                'error' => 'verification_error',
                'message' => "Your " . $key . " was not verified, please contact support to help you recover your account.",
            ], 422);
        }

        PasswordReset::where($key, $value)->forceDelete();

        $reset = PasswordReset::create([
            $key => $value,
            'code' => rand(10000, 99999),
            'code_at' => now(),
        ]);

        info('Sending Reset code to ' . $value, [
            'code' => $reset->code,
            'key' => $key,
            'value' => $value
        ]);


        activity('user_password')
            ->on($user)
            ->withProperties([
                'code' => $reset->code
            ])
            ->log('Password reset requested');

        if ($request->header('x-request-origin-miniapp')) {
            // request is coming from miniapp
            return response(['status' => true, 'code' => $reset->code]);
        }

        $key === 'phone'
            ? SendSmsResetCode::dispatchAfterResponse($reset)
            : Mail::to($value)->later(5, new PasswordResetCode($reset));

        return response(['status' => true, 'code' => $reset->code]);
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function update(Request $request)
    {

        info('Resetting password', $request->all());

        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:5',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 422);
        }

        //$key = isset($request['phone']) ? 'phone' : 'email';
       // $value = $request[$key];
        $reset = PasswordReset::whereCode($request['code'])
            ->firstOrFail();
        if (now()->diffInMinutes($reset->code_at) > 10) {
            return response()->json([
                'error' => 'code_expiry',
                'message' => 'Your code has expired',
            ], 422);
        }
        $user = User::where('phone', $reset->phone)
            ->orWhere(function($query) use ($reset) {
                $query->where('email', $reset->email)
                      ->whereNotNull('email');
            })
            ->firstOrFail();
        $user->update(['password' => bcrypt($request['password'])]);
        $user->deleteOauthTokens();
        $reset->delete();

        return response(['status' => true]);
    }

    /**
     * Change password
     * @authenticated
     * @group Auth
     * @bodyParam current_password string required
     * @bodyParam password string required
     * @bodyParam password_confirmation string required
     * @response {
     * "status": true
     * }
     * @response 422 {
     * "message": "The given data was invalid.",
     * "errors": {
     * "current_password": [
     * "The current password field is required."
     * ],
     * "password": [
     * "The password field is required."
     * ],
     * "password_confirmation": [
     * "The password confirmation field is required."
     * ]
     * }
     * }
     *
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function changePassword(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->all()], 422);
        }
        $user = $request->user();
        if (Hash::check($request->input('current_password'), $user->password)) {
           $user->update(['password' => bcrypt($request->input('password'))]);
           $user->deleteOauthTokens();
        }else{
            return response()->json([
                'error' => 'password_mismatch',
            ], 400);
        }
        return response(['status' => 'Password changed successfully']);
    }

}
