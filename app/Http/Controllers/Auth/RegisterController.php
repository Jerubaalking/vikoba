<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Jobs\Auth\SendSmsVerificationCode;
use Illuminate\Notifications\Notification;
use App\Jobs\Auth\SendEmailVerificationLink;
use App\Jobs\Auth\HandleUnverifiedPhoneNumber;
use Illuminate\Foundation\Bus\PendingDispatch;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Notifications\MiniApp\PasswordNotification;
use App\Notifications\SendSignUpSuccessNotification;
use Illuminate\Foundation\Bus\PendingClosureDispatch;
use App\Notifications\DeliveryPartner\AgreementNotification;

class RegisterController extends Controller
{
/**
 * Invoke method
 *
 * @param Request $request
 * @return Application|ResponseFactory|JsonResponse|Response
 */
public function __invoke(Request $request)
{
    // write('Post registering user');

    if ($request->header('x-request-origin-miniapp')) {
        // return $this->miniapp($request);
    } else {
        $validator = Validator::make($request->all(), [
            'firstname' => 'nullable|string|min:3',
            'sirname' => 'nullable|string|min:3',
            'username' => 'nullable|string|min:3',
            'email' => ['nullable', Rule::unique('users', 'email')],
            'phone' => ['required', Rule::unique('users', 'phone')],
            'password' => 'required|min:5',
            'user_type' => 'sometimes|required|in:customer,business-owner',
        ])->after(function ($v) use ($request) {

            if ($request->filled('phone') && User::where('phone', Str::remove('+', $request->phone))->exists()) {
                $v->errors()->add('phone', trans('validation.unique', ['attribute' => $request->phone]));
            }
        });
        $validator->sometimes(['firstname', 'sirname'], ['required', 'string', 'min:3'], function ($input) {
           
            return $input->name == null;
        });

        if ($validator->fails()) {
            return response($validator->errors()->toArray(), 422);
        }

        try {
            $registerData = collect($validator->validated())
                ->reject(function ($item, $key) {
                    return $key === 'name';
                })->toArray();

            if ($request->filled('name')) {
                [$registerData['firstname'], $registerData['sirname']] = explode(' ', $request->name);
            }

            $registerData += [
                'phone' => Str::remove('+', $request->phone),
                'phone_code' => random_int(1000, 9999),
                'phone_code_at' => now('Africa/Dar_es_Salaam')->toDateTimeString(),
            ];

            $registerData['password'] = bcrypt($registerData['password']);

            return $this->register($registerData);
        } catch (\Exception $e) {
            info('Error :' . $e->getMessage(), [
                'error' => $e->getTrace()
            ]);

            return response(['success' => false, 'message' => trans('auth.server_error')], 422);
        }
    }

    return response(['success' => false]);
}

    /**
     * Registers a user into the system
     *
     * @param $data
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    private function register($data)
    {
        /**
         * @var User $user
         * @var Role $role
         */
        // dd($data);
        // $data['registration_channel_id'] = agent()->browser() != false ? 2 : 1;
        // dd(Arr::except($data, 'user_type'));
        $user = new User();
        $user->firstname = $data["firstname"];
        $user->sirname = $data["sirname"];
        $user->phone = $data["phone"];
        $user->email = $data["email"];
        $user->password = $data["password"];
        $user->username = $data["username"];
        $user->phone_code = $data["phone_code"];
        $user->phone_code_at = $data["phone_code_at"];
        $user->save();
        // dd($user);

        // $user->notificationPreferences()->sync([1]);

        $token = $user->createToken($user->full_name)->accessToken;

        // event('user.created', $user);

        // event('user_logged_in', $user);


        return response([
            'success' => true,
            'token' => $token,
            'user' => $user
            // 'user' => $user->fresh()->load(['notificationPreferences', 'notifications', 'interests'])
        ], 201);
    }

    /**
     * @param $user
     * @param $id
     * @return PendingClosureDispatch|PendingDispatch
     */
    private function verifyContacts($user, $id)
    {
        return dispatch(
            fn () => SendSmsVerificationCode::withChain([
                (new HandleUnverifiedPhoneNumber($user, 0))->delay(now()->addMinute()),
                new SendEmailVerificationLink($id),
            ])->dispatch($user)
        )->afterResponse();
    }

    /**
     * Processes a miniapp request.
     *
     * @param Request $request The request object containing the input data.
     * @return Response The response object containing the result of the request.
     */
    public static function miniapp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'messages' => $validator->errors()
            ], 422);
        }

        $userExists = User::where('phone', $request->phone)->exists();

        if ($userExists) {
            return response([
                'success' => false,
                'message' => trans('auth.user_exists')
            ], 422);
        }

        $password = Str::random(8);

        $user = User::create([
            'phone' => $request->phone,
            'firstname' => $request->firstname,
            'sirname' => $request->sirname,
            'registration_channel_id' => 3,
            'password' => bcrypt($password),
        ]);

        $token = $user->createToken($user->full_name)->accessToken;

        // $user->notify(new PasswordNotification($password));

        return response([
            'success' => true,
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
