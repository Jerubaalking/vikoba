<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\OtpCode;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberUtil;

class VerificationCodeController extends Controller
{

    /**
     *  Sends a verification code to user
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function send(Request $request)
    {

        info('Receiving request for verification', [
            'request' => $request->toArray()
        ]);

        $validator = Validator::make($request->all(), [
            'username' => ['required'],
        ]);

        $validator->after(function ($v) use ($request) {

           if(User::where('phone', Str::remove('+', $request->username))
                ->orWhere('email', $request->username)->exists()) {
               $v->errors()->add('username', 'Namba hii tayari ina akaunti Tunzaa. Tafadhali sajili kwa namba nyingine au Ingia kuendelea');
           }

           if($request->filled('username') && !filter_var($request->username, FILTER_VALIDATE_EMAIL)) {

               try {
                   $phoneUtil = PhoneNumberUtil::getInstance();
                   $phoneNumber = Str::startsWith($request->username, '+') ? $request->username : '+'.$request->username;
                   $phoneNumberInstance = $phoneUtil->parse($phoneNumber);
                   info('Parsed the number', [
                       'instance' => $phoneNumberInstance
                   ]);

                   if (!$phoneUtil->isValidNumber($phoneNumberInstance)) {
                       $v->errors()->add('username', 'Phone number is not valid');
                   }

               }catch (Exception $exception) {

                   Log::error('Phone Number Parse Failed:: '.$exception->getMessage(), [
                       'trace' => $exception->getTraceAsString()
                   ]);

                    $v->errors()->add('username', 'Phone number is not valid or un-formatted');

               }
           }

        });

        if ($validator->fails()) {
            return response(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $isEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL);

        $phoneNumber = !$isEmail ? get_international_number($request->username) : $request->username;

        info('Sending verification code to '. $phoneNumber);

        try {

            DB::table('otp_codes')
                ->where('phone_number', $phoneNumber)
                ->orWhere('email', $request->username)
                ->delete();

        }catch (Exception $e) {

            info('Error while deleting otp code');
        }

        /**
         * @var OtpCode $otp
         */
        if ($otp = OtpCode::firstOrCreate([
            'phone_number' => $isEmail ? null : $phoneNumber,
            'email' => $isEmail ? $request->username : null,
            'code' => rand(pow(10, 4 - 1) - 1, pow(10, 4) - 1),
        ])) {

            info('Sending OTP code', [
                'code' => $otp->code,
                'data' => $request->all(),
            ]);

            if ($request->header('x-request-origin-miniapp')) {
                // request is coming from miniapp
                return response(['status' => true, 'code' =>  $otp->code]);
            }

            $otp->notify((new VerificationCodeNotification())->delay(now()->addSecond()));
            
            return response(['success' => true]);
        }

        return response(['success' => false], Response::HTTP_BAD_REQUEST);
    }
    /**
     * @param $phoneNumber
     * @return int|string|null
     */
    protected function getPhoneNumber($phoneNumber) {

        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneNumberInstance = $phoneUtil->parse($phoneNumber);
            return $phoneNumberInstance->getCountryCode() + $phoneNumberInstance->getNationalNumber();

        }catch (Exception $exception) {

            return Str::remove('+', $phoneNumber);
        }
    }

    /**
     *  Sends a verification code to user
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function verify(Request $request)
    {
        info('Verifying OTP code', ['data' => $request->all()]);

        $phoneNumber = Str::remove('+', $request->username);

        if ($otp = OtpCode::where('code', $request->otp_code)
            //->whereNull('verified_at')
            ->where(function ($query) use ($request, $phoneNumber) {
               return $query->where('phone_number', $phoneNumber)
                    ->orWhere('email', $request->username);
            })
            ->first()) {

            info('Found OTP code', ['data' => $request->all(), 'user' => $otp->user]);

            $otp->update(['verified_at', now()]);

            if($otp->user != null) {

                info('Verifying user account ', ['data' => $request->all(), 'user' => $otp->user]);

                $otp->user->update(['code_verified_at' => now(), 'phone_verified_at' => now()]);
            }
            return response(['success' => true, 'user_exists' => $otp->user_id != null]);
        }

        return response(['success' => false], Response::HTTP_BAD_REQUEST);
    }
}
