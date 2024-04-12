<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
// use App\Notifications\MiniApp\PasswordNotification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\LoginUserRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;

class LoginController extends Controller
{
    /**
     * Login the user to the platform
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {

        info('Login in ', [
            'request' => $request->except(['password']),
        ]);

        // if ($request->header('x-request-origin-miniapp')) {
        //     try {
        //         return $this->miniapp($request);
        //     } catch (\Exception $e) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => $e->getMessage(),
        //         ], 422);
        //     }
        // }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            event('user.login_failed');
            info('Login failed', [
                'errors' => $validator->errors(),
            ]);
            return response()->json(
                ['success' => false, 'messages' => $validator->errors()],
                422
            );
        }

        $user = User::where('email', $request->username)
            ->orWhere('phone', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($user->full_name)->accessToken;

            event('user.logged_in', $user);
            return response()->json([
                'success' => true,
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => $user,
            ]);
        }

        event('user.login_failed');
        return response()->json([
            'success' => false,
            'message' => $user ? trans('auth.login_failed') : trans('auth.wrong_email'),
        ], 422);
    }

    /**
     *  Logouts the user
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function logout(Request $request)
    {
        $user = $request->user(); // Get the logged-in user
        $token = $user->token();

        // Clear user-specific cached data
        $cacheKey = 'user_' . $user->id . '_orders_'; // Update this with your cache key pattern

        Cache::forget($cacheKey);

        event('user.logged_out', $user);
        $token->revoke();

        $response = ['message' => trans('auth.logged_out')];
        return response($response, 200);
    }


    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function refreshToken(Request $request)
    {
        /**
         * @var User $user
         */
        if ($user = $request->user()) {
            $token = $user->createToken($user->full_name)->accessToken;
            return response([
                'success' => true,
                'token' => $token,
                'user' => $user
                    ->fresh()
                    ->load([
                        'notificationPreferences',
                        'notifications',
                        'interests',
                    ]),
            ]);
        } else {
            return response(
                [
                    'success' => false,
                ],
                422
            );
        }
    }

    /**
     * Login the miniapp user to the platform
     *
     * @param Request $request
     * @return JsonResponse
     */
    private static function miniapp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validator->errors(),
            ], 400);
        }

        $user = User::where('phone', get_international_number($request->username))->first();

        if(!$user){

        $nameValidator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|min:3',
            'last_name' => 'nullable|string|min:3',
        ]);

        if ($nameValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $nameValidator->errors(),
            ], 400);
        }

            $password = Str::random(8);

            $user = User::create([
                'phone' =>  get_international_number($request->username),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'registration_channel_id' => 3,
                'password' => bcrypt($password),
            ]);

            $user->notify(new PasswordNotification($password));
        }

        $token = $user->createToken($user->full_name)->accessToken;


        return response()->json([
            'success' => true,
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user->fresh()->load(['interests']),
        ]);
    }
}
