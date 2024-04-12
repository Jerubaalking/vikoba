<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppleToken;
use Illuminate\Http\Request;
use App\Models\UserSocialAccount as SocialAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{

    public function __invoke(Request $request)
    {

        write('Social Login');

        $validator = Validator::make($request->all(),
            [
                'provider' => ['required', Rule::in(['facebook', 'google'])],
                'access_token' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response(['messages' => $validator->errors()], 422);
        }

        $provider = $request->get('provider');

        /**
         * @var $social_user
         */

        switch ($provider) {
            case SocialAccount::SERVICE_FACEBOOK:
                $social_user = Socialite::driver(SocialAccount::SERVICE_FACEBOOK)
                    ->fields([
                        'name',
                        'first_name',
                        'last_name',
                        'email'
                    ]);
                break;
            case SocialAccount::SERVICE_GOOGLE:
                $social_user = Socialite::driver(SocialAccount::SERVICE_GOOGLE)
                    ->scopes(['profile', 'email']);
                break;
            case SocialAccount::SERVICE_TWITTER:
                $social_user = Socialite::driver(SocialAccount::SERVICE_TWITTER)
                    ->scopes(['profile', 'email']);
                break;
            case SocialAccount::SERVICE_APPLE:
                $social_user = Socialite::driver(SocialAccount::SERVICE_APPLE)
                    ->scopes(['profile', 'email']);
                break;
            default :
                $social_user = null;
        }

        if ($social_user == null) {
            response(['message' => 'Social account not found'], 422);
        }
        $social_user_details = null;

        try {

            $social_user_details = $social_user->userFromToken($request->get('access_token'));

            info('Got a user from token', [
                'user' => $social_user_details
            ]);

            if ($social_user_details == null) {
                response(['message' => 'Invalid credentials'], 422);
            }
        } catch (\Exception $e) {

            \Log::error(__CLASS__ . '::' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            response(['message' => 'Invalid credentials'], 422);
        }

        $account = SocialAccount::where("provider_user_id", $social_user_details->id)
            ->where("provider", $provider)
            ->with('user')
            ->first();

        if ($account) {
            return $this->issueToken($account->user);
        } else {
            // create new user and social login if user with social id not found.
            $user = User::where("email", $social_user_details->getEmail())->first();
            if (!$user) {
                // create new social login if user already exist.
                $user = new User();
                switch ($provider) {
                    case SocialAccount::SERVICE_FACEBOOK:
                        $user->first_name = $social_user_details->user['first_name'];
                        $user->last_name = $social_user_details->user['last_name'];
                        break;
                    case SocialAccount::SERVICE_GOOGLE:
                        $user->first_name = $social_user_details->user['given_name'];
                        $user->last_name = @$social_user_details->user['family_name'];
                        break;
                    default :
                }

                $user->email = $social_user_details->getEmail();
                $user->phone = $request->get('phone');
                $user->password = Hash::make(rand_crypto());
                $user->save();
            }


            $user->social_accounts()
                ->firstOrCreate([
                    'provider' => $provider,
                    'provider_user_id' => $social_user_details->id,
                ]);
            return $this->issueToken($user);
        }
    }

    protected function handleSocialiteUserForApple(AppleToken  $appleToken) {

    }

    /**
     * @param Request $request
     */
    public function callback(Request $request)
    {
        info('Request logged in wih social network');
    }


    private function issueToken(User $user)
    {
        $userToken = $user->createToken('socialLogin');

        return response([
            'success' => true,
            'token' => $userToken,
            'user' => $user->fresh()->load(['notificationPreferences', 'notifications', 'interests'])
        ]);
    }
}
